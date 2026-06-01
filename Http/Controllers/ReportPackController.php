<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;
use Carbon\Carbon;
use DB;

class ReportPackController extends Controller
{
    // ================================================================
    // REPORT PACK - Original CIMS Management Accounts (v1)
    // Ported to NexCore tables - CIMS-ORIGINAL logic preserved
    // ================================================================

    public function index(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $period = $request->input('period', 'this_year');
        $fyEnd = $client->financial_year_end ?: 2;
        $fyMonth = ($fyEnd % 12) + 1;

        list($dateFrom, $dateTo) = $this->resolvePeriodDates(
            $period, $request->input('date_from'), $request->input('date_to'), $fyMonth, $clientId
        );

        $compDateFrom = null;
        $compDateTo = null;
        $compPeriodLabel = '';

        $from = Carbon::parse($dateFrom);
        $to = Carbon::parse($dateTo);
        $monthsInPeriod = $from->diffInMonths($to) + 1;

        if ($monthsInPeriod <= 1) {
            $compDateFrom = $from->copy()->subMonth()->startOfMonth()->toDateString();
            $compDateTo = $from->copy()->subMonth()->endOfMonth()->toDateString();
            $compPeriodLabel = Carbon::parse($compDateFrom)->format('M Y');
        } elseif ($monthsInPeriod <= 3) {
            $compDateFrom = $from->copy()->subMonths(3)->toDateString();
            $compDateTo = $to->copy()->subMonths(3)->toDateString();
            $compPeriodLabel = Carbon::parse($compDateFrom)->format('M Y') . ' - ' . Carbon::parse($compDateTo)->format('M Y');
        } else {
            $compDateFrom = $from->copy()->subYear()->toDateString();
            $compDateTo = $to->copy()->subYear()->toDateString();
            $compPeriodLabel = Carbon::parse($compDateFrom)->format('M Y') . ' - ' . Carbon::parse($compDateTo)->format('M Y');
        }

        $pnlData = $this->calcPnlData($clientId, $dateFrom, $dateTo);
        $compPnlData = $this->calcPnlData($clientId, $compDateFrom, $compDateTo);

        $buildGroup = function($current, $comparison) {
            $merged = [];
            foreach ($current as $acc) {
                $key = $acc['code'];
                $compVal = 0;
                foreach ($comparison as $cAcc) {
                    if ($cAcc['code'] === $key) { $compVal = $cAcc['total']; break; }
                }
                $variance = $acc['total'] - $compVal;
                $merged[] = [
                    'id' => $acc['id'], 'name' => $acc['name'], 'code' => $acc['code'], 'group' => $acc['group'],
                    'current' => $acc['total'], 'comparison' => $compVal,
                    'variance' => $variance,
                    'variance_pct' => $compVal != 0 ? round(($variance / abs($compVal)) * 100, 1) : 0,
                ];
            }
            return collect($merged)->groupBy('group');
        };

        $revenueByGroup = $buildGroup($pnlData['revenue'], $compPnlData['revenue']);
        $cosByGroup = $buildGroup($pnlData['cos'], $compPnlData['cos']);
        $expensesByGroup = $buildGroup($pnlData['expenses'], $compPnlData['expenses']);

        $buildTotal = function($cur, $comp) {
            $variance = $cur - $comp;
            return (object)[
                'current' => $cur, 'comparison' => $comp,
                'variance' => $variance,
                'variance_pct' => $comp != 0 ? round(($variance / abs($comp)) * 100, 1) : 0,
            ];
        };

        $totalRevenue = $buildTotal($pnlData['totalRevenue'], $compPnlData['totalRevenue']);
        $totalCos = $buildTotal($pnlData['totalCos'], $compPnlData['totalCos']);
        $grossProfit = $buildTotal($pnlData['grossProfit'], $compPnlData['grossProfit']);
        $totalExpenses = $buildTotal($pnlData['totalExpenses'], $compPnlData['totalExpenses']);
        $netProfit = $buildTotal($pnlData['netProfit'], $compPnlData['netProfit']);

        $bsData = $this->calcBsData($clientId, $dateTo);
        $compBsData = $this->calcBsData($clientId, $compDateTo);

        $buildBsGroup = function($current, $comparison) {
            $merged = [];
            foreach ($current as $acc) {
                $compVal = 0;
                foreach ($comparison as $cAcc) {
                    if ($cAcc['code'] === $acc['code']) { $compVal = $cAcc['balance']; break; }
                }
                $merged[] = [
                    'id' => $acc['id'], 'name' => $acc['name'], 'code' => $acc['code'], 'group' => $acc['group'],
                    'balance' => $acc['balance'], 'comp_balance' => $compVal,
                ];
            }
            return collect($merged)->groupBy('group');
        };

        $assetsByGroup = $buildBsGroup($bsData['assets'], $compBsData['assets']);
        $liabilitiesByGroup = $buildBsGroup($bsData['liabilities'], $compBsData['liabilities']);
        $equityByGroup = $buildBsGroup($bsData['equity'], $compBsData['equity']);

        $totalAssets = (object)['current' => $bsData['totalAssets'], 'comparison' => $compBsData['totalAssets']];
        $totalLiabilities = (object)['current' => $bsData['totalLiabilities'], 'comparison' => $compBsData['totalLiabilities']];
        $totalEquity = (object)['current' => $bsData['totalEquity'], 'comparison' => $compBsData['totalEquity']];
        $retainedEarnings = (object)['current' => $bsData['retainedEarnings'], 'comparison' => $compBsData['retainedEarnings']];
        $totalLiabilitiesAndEquity = (object)['current' => $bsData['totalLiabilitiesAndEquity'], 'comparison' => $compBsData['totalLiabilitiesAndEquity']];

        $calcRatios = function($pnl, $bs) {
            $gm = $pnl['totalRevenue'] != 0 ? round(($pnl['grossProfit'] / $pnl['totalRevenue']) * 100, 1) : 0;
            $nm = $pnl['totalRevenue'] != 0 ? round(($pnl['netProfit'] / $pnl['totalRevenue']) * 100, 1) : 0;
            $currentAssets = 0; $currentLiab = 0;
            foreach ($bs['assets'] as $a) {
                if (strpos($a['code'], '1100') === 0 || strpos($a['code'], '11') === 0) $currentAssets += $a['balance'];
                elseif (substr($a['code'], 0, 2) <= '13') $currentAssets += $a['balance'];
            }
            foreach ($bs['liabilities'] as $l) {
                if (strpos($l['code'], '2100') === 0 || strpos($l['code'], '21') === 0) $currentLiab += $l['balance'];
                elseif (substr($l['code'], 0, 2) <= '22') $currentLiab += $l['balance'];
            }
            $cr = $currentLiab != 0 ? round($currentAssets / $currentLiab, 2) : 0;
            $equityTotal = $bs['totalEquity'] + $bs['retainedEarnings'];
            $de = $equityTotal != 0 ? round($bs['totalLiabilities'] / $equityTotal, 2) : 0;
            $er = $pnl['totalRevenue'] != 0 ? round(($pnl['totalExpenses'] / $pnl['totalRevenue']) * 100, 1) : 0;
            return ['gross_margin' => $gm, 'net_margin' => $nm, 'current_ratio' => $cr, 'debt_to_equity' => $de, 'expense_ratio' => $er];
        };

        $ratios = $calcRatios($pnlData, $bsData);
        $compRatios = $calcRatios($compPnlData, $compBsData);

        $bankRecons = DB::table('cims_gl_bank_recon_master_header as r')
            ->leftJoin('cims_gl_bank_accounts_linked_to_coa as ba', 'r.bank_account_id', '=', 'ba.id')
            ->where('r.company_id', $clientId)
            ->where('r.status', 'completed')
            ->select('ba.bank_name', 'ba.account_number', 'r.statement_date', 'r.status', 'r.statement_balance', 'r.reconciled_balance', 'r.difference')
            ->orderBy('r.statement_date', 'desc')
            ->get();

        $practiceRow = DB::table('nexcore_practices')->where('is_active', 1)->first();
        $companySettings = [
            'settings_company_name' => $practiceRow->practice_name ?? 'Practice',
            'settings_address' => '',
        ];
        if ($practiceRow) {
            $parts = array_filter([
                $practiceRow->physical_address_line1 ?? '',
                $practiceRow->physical_city ?? '',
                $practiceRow->physical_province ?? '',
                $practiceRow->physical_postal_code ?? '',
            ]);
            $companySettings['settings_address'] = implode(', ', $parts);
        }

        $preparedDate = Carbon::now()->format('j F Y');

        return view('nexcore_client_manager::reporting.report-pack', compact(
            'client', 'period', 'dateFrom', 'dateTo', 'compPeriodLabel',
            'revenueByGroup', 'cosByGroup', 'expensesByGroup',
            'totalRevenue', 'totalCos', 'grossProfit', 'totalExpenses', 'netProfit',
            'assetsByGroup', 'liabilitiesByGroup', 'equityByGroup',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'retainedEarnings', 'totalLiabilitiesAndEquity',
            'ratios', 'compRatios', 'bankRecons',
            'companySettings', 'preparedDate'
        ));
    }

    // ----------------------------------------------------------------
    // API: Account Transactions (drill-down)
    // ----------------------------------------------------------------
    public function accountTransactions(Request $request, $clientId, $accountId)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = DB::table('cims_gl_journal_header_linked_entries as jl')
            ->join('cims_gl_journal_master_header as j', 'jl.journal_id', '=', 'j.id')
            ->where('j.company_id', $clientId)
            ->where('j.status', 'posted')
            ->where('jl.account_id', $accountId);

        if ($dateFrom && $dateTo) {
            $query->whereBetween('j.journal_date', [$dateFrom, $dateTo]);
        } elseif ($dateTo) {
            $query->where('j.journal_date', '<=', $dateTo);
        }

        $rows = $query->select(
                'jl.id as line_id', 'jl.journal_id', 'jl.description',
                'jl.debit_amount as debit', 'jl.credit_amount as credit',
                'jl.ma_hidden', 'jl.note',
                'j.journal_date as date', 'j.journal_number as journal'
            )
            ->orderBy('j.journal_date')
            ->orderBy('j.id')
            ->get()
            ->map(function($row) {
                $row->date = Carbon::parse($row->date)->format('j M Y');
                $row->debit = (float)$row->debit;
                $row->credit = (float)$row->credit;
                $row->ma_hidden = (bool)$row->ma_hidden;
                return $row;
            });

        return response()->json(['rows' => $rows]);
    }

    // ----------------------------------------------------------------
    // API: Chart Tree (for reallocation search)
    // ----------------------------------------------------------------
    public function chartTree($clientId)
    {
        $accounts = NexcoreGlChartOfAccount::where('company_id', $clientId)
            ->where('is_active', 1)
            ->orderBy('account_code')
            ->get();

        $tree = [];
        $byId = [];
        foreach ($accounts as $acc) {
            $byId[$acc->id] = $acc;
        }

        foreach ($accounts as $acc) {
            if ($acc->account_level == 1) {
                $tree[$acc->id] = ['id' => $acc->id, 'code' => $acc->account_code, 'name' => $acc->account_name, 'children' => []];
            }
        }

        foreach ($accounts as $acc) {
            if ($acc->account_level == 2 && isset($tree[$acc->parent_id])) {
                $tree[$acc->parent_id]['children'][$acc->id] = [
                    'id' => $acc->id, 'code' => $acc->account_code, 'name' => $acc->account_name, 'children' => []
                ];
            }
        }

        foreach ($accounts as $acc) {
            if ($acc->account_level == 3 && isset($byId[$acc->parent_id])) {
                $parent = $byId[$acc->parent_id];
                if (isset($tree[$parent->parent_id]['children'][$parent->id])) {
                    $tree[$parent->parent_id]['children'][$parent->id]['children'][] = [
                        'id' => $acc->id, 'code' => $acc->account_code, 'name' => $acc->account_name, 'vat_type' => $acc->vat_type ?: 'none',
                    ];
                }
            }
        }

        return response()->json(['tree' => $tree]);
    }

    // ----------------------------------------------------------------
    // API: MA Reallocation
    // ----------------------------------------------------------------
    public function maRealloc(Request $request, $clientId)
    {
        $journalId = $request->input('journal_id');
        $newAccountId = $request->input('new_account_id');

        if (!$journalId || !$newAccountId) {
            return response()->json(['success' => false, 'error' => 'Missing journal_id or new_account_id']);
        }

        $journal = NexcoreGlJournal::where('id', $journalId)
            ->where('company_id', $clientId)
            ->where('status', 'posted')
            ->first();

        if (!$journal) {
            return response()->json(['success' => false, 'error' => 'Journal not found']);
        }

        $line = NexcoreGlJournalLine::where('journal_id', $journalId)->first();
        if (!$line) {
            return response()->json(['success' => false, 'error' => 'Journal line not found']);
        }

        $oldAccountId = $line->account_id;
        $line->account_id = $newAccountId;
        $line->save();

        $amount = max((float)$line->debit_amount, (float)$line->credit_amount);

        return response()->json([
            'success' => true,
            'journal_number' => $journal->journal_number,
            'description' => $line->description,
            'amount' => $amount,
        ]);
    }

    // ----------------------------------------------------------------
    // API: MA Hide/Unhide
    // ----------------------------------------------------------------
    public function maHide(Request $request, $clientId)
    {
        $lineId = $request->input('line_id');
        $hide = $request->input('hide', 1);

        if (!$lineId) {
            return response()->json(['success' => false, 'error' => 'Missing line_id']);
        }

        $line = NexcoreGlJournalLine::find($lineId);
        if (!$line) {
            return response()->json(['success' => false, 'error' => 'Line not found']);
        }

        $line->ma_hidden = $hide ? 1 : 0;
        $line->save();

        return response()->json(['success' => true]);
    }

    // ================================================================
    // PRIVATE HELPERS - Same logic as CIMS original
    // ================================================================

    private function resolvePeriodDates($period, $dateFrom, $dateTo, $fyMonth, $clientId)
    {
        $now = Carbon::now();
        $fyStart = $now->month >= $fyMonth
            ? $now->copy()->startOfYear()->addMonths($fyMonth - 1)
            : $now->copy()->subYear()->startOfYear()->addMonths($fyMonth - 1);

        if ($period === 'custom' && $dateFrom && $dateTo) {
            return [$dateFrom, $dateTo];
        }

        switch ($period) {
            case 'this_month':
                return [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()];
            case 'last_month':
                return [$now->copy()->subMonth()->startOfMonth()->toDateString(), $now->copy()->subMonth()->endOfMonth()->toDateString()];
            case 'q1':
                return [$fyStart->copy()->toDateString(), $fyStart->copy()->addMonths(2)->endOfMonth()->toDateString()];
            case 'q2':
                return [$fyStart->copy()->addMonths(3)->toDateString(), $fyStart->copy()->addMonths(5)->endOfMonth()->toDateString()];
            case 'q3':
                return [$fyStart->copy()->addMonths(6)->toDateString(), $fyStart->copy()->addMonths(8)->endOfMonth()->toDateString()];
            case 'q4':
                return [$fyStart->copy()->addMonths(9)->toDateString(), $fyStart->copy()->addMonths(11)->endOfMonth()->toDateString()];
            case 'h1':
                return [$fyStart->copy()->toDateString(), $fyStart->copy()->addMonths(5)->endOfMonth()->toDateString()];
            case 'h2':
                return [$fyStart->copy()->addMonths(6)->toDateString(), $fyStart->copy()->addMonths(11)->endOfMonth()->toDateString()];
            case 'full_year':
                return [$fyStart->copy()->toDateString(), $fyStart->copy()->addMonths(11)->endOfMonth()->toDateString()];
            case '6_months':
                return [$now->copy()->subMonths(5)->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()];
            case 'last_year':
                $prevFy = $fyStart->copy()->subYear();
                return [$prevFy->toDateString(), $prevFy->copy()->addMonths(11)->endOfMonth()->toDateString()];
            case 'all':
                $earliest = DB::table('cims_gl_journal_master_header')
                    ->where('company_id', $clientId)->where('status', 'posted')
                    ->min('journal_date');
                $latest = DB::table('cims_gl_journal_master_header')
                    ->where('company_id', $clientId)->where('status', 'posted')
                    ->max('journal_date');
                if ($earliest && $latest) {
                    return [Carbon::parse($earliest)->startOfMonth()->toDateString(), Carbon::parse($latest)->endOfMonth()->toDateString()];
                }
                return [$fyStart->toDateString(), $fyStart->copy()->addMonths(11)->endOfMonth()->toDateString()];
            case 'this_year':
            default:
                return [$fyStart->toDateString(), $fyStart->copy()->addMonths(11)->endOfMonth()->toDateString()];
        }
    }

    private function calcPnlData($companyId, $dateFrom, $dateTo)
    {
        $lines = DB::table('cims_gl_journal_header_linked_entries as jl')
            ->join('cims_gl_journal_master_header as j', 'jl.journal_id', '=', 'j.id')
            ->join('cims_gl_chart_of_accounts_master as c', 'jl.account_id', '=', 'c.id')
            ->where('j.company_id', $companyId)
            ->where('j.status', 'posted')
            ->where('jl.ma_hidden', 0)
            ->whereBetween('j.journal_date', [$dateFrom, $dateTo])
            ->whereIn('c.account_type', ['revenue', 'cost_of_sales', 'expense'])
            ->select(
                'c.id as account_id', 'c.account_code', 'c.account_name',
                'c.account_type', 'c.parent_id',
                DB::raw('SUM(jl.debit_amount) as total_debit'),
                DB::raw('SUM(jl.credit_amount) as total_credit')
            )
            ->groupBy('c.id', 'c.account_code', 'c.account_name', 'c.account_type', 'c.parent_id')
            ->orderBy('c.account_code')
            ->get();

        $parentNames = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('account_level', 2)->pluck('account_name', 'id');

        $revenue = []; $cos = []; $expenses = [];
        $totalRevenue = 0; $totalCos = 0; $totalExpenses = 0;

        foreach ($lines as $line) {
            $bal = $line->account_type === 'revenue'
                ? (float)$line->total_credit - (float)$line->total_debit
                : (float)$line->total_debit - (float)$line->total_credit;

            $item = [
                'id' => $line->account_id, 'code' => $line->account_code, 'name' => $line->account_name,
                'group' => $parentNames[$line->parent_id] ?? 'Other', 'total' => $bal,
            ];

            if ($line->account_type === 'revenue') { $revenue[] = $item; $totalRevenue += $bal; }
            elseif ($line->account_type === 'cost_of_sales') { $cos[] = $item; $totalCos += $bal; }
            else { $expenses[] = $item; $totalExpenses += $bal; }
        }

        return [
            'revenue' => $revenue, 'cos' => $cos, 'expenses' => $expenses,
            'totalRevenue' => round($totalRevenue, 2), 'totalCos' => round($totalCos, 2),
            'grossProfit' => round($totalRevenue - $totalCos, 2),
            'totalExpenses' => round($totalExpenses, 2),
            'netProfit' => round($totalRevenue - $totalCos - $totalExpenses, 2),
        ];
    }

    private function calcBsData($companyId, $asAt)
    {
        $lines = DB::table('cims_gl_journal_header_linked_entries as jl')
            ->join('cims_gl_journal_master_header as j', 'jl.journal_id', '=', 'j.id')
            ->join('cims_gl_chart_of_accounts_master as c', 'jl.account_id', '=', 'c.id')
            ->where('j.company_id', $companyId)
            ->where('j.status', 'posted')
            ->where('jl.ma_hidden', 0)
            ->where('j.journal_date', '<=', $asAt)
            ->whereIn('c.account_type', ['asset', 'liability', 'equity'])
            ->select(
                'c.id as account_id', 'c.account_code', 'c.account_name',
                'c.account_type', 'c.parent_id',
                DB::raw('SUM(jl.debit_amount) as total_debit'),
                DB::raw('SUM(jl.credit_amount) as total_credit')
            )
            ->groupBy('c.id', 'c.account_code', 'c.account_name', 'c.account_type', 'c.parent_id')
            ->orderBy('c.account_code')
            ->get();

        $pnl = DB::table('cims_gl_journal_header_linked_entries as jl')
            ->join('cims_gl_journal_master_header as j', 'jl.journal_id', '=', 'j.id')
            ->join('cims_gl_chart_of_accounts_master as c', 'jl.account_id', '=', 'c.id')
            ->where('j.company_id', $companyId)
            ->where('j.status', 'posted')
            ->where('jl.ma_hidden', 0)
            ->where('j.journal_date', '<=', $asAt)
            ->whereIn('c.account_type', ['revenue', 'cost_of_sales', 'expense'])
            ->select(DB::raw('SUM(jl.debit_amount) as total_debit'), DB::raw('SUM(jl.credit_amount) as total_credit'))
            ->first();

        $retainedEarnings = $pnl ? round((float)$pnl->total_credit - (float)$pnl->total_debit, 2) : 0;

        $level2Names = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('account_level', 2)->pluck('account_name', 'id');

        $assets = []; $liabilities = []; $equity = [];
        $totalAssets = 0; $totalLiabilities = 0; $totalEquity = 0;

        foreach ($lines as $line) {
            $bal = $line->account_type === 'asset'
                ? (float)$line->total_debit - (float)$line->total_credit
                : (float)$line->total_credit - (float)$line->total_debit;

            if (abs($bal) < 0.01) continue;

            $item = [
                'id' => $line->account_id, 'code' => $line->account_code, 'name' => $line->account_name,
                'group' => $level2Names[$line->parent_id] ?? 'Other', 'balance' => $bal,
            ];

            if ($line->account_type === 'asset') { $assets[] = $item; $totalAssets += $bal; }
            elseif ($line->account_type === 'liability') { $liabilities[] = $item; $totalLiabilities += $bal; }
            else { $equity[] = $item; $totalEquity += $bal; }
        }

        return [
            'assets' => $assets, 'liabilities' => $liabilities, 'equity' => $equity,
            'totalAssets' => round($totalAssets, 2), 'totalLiabilities' => round($totalLiabilities, 2),
            'totalEquity' => round($totalEquity, 2), 'retainedEarnings' => $retainedEarnings,
            'totalLiabilitiesAndEquity' => round($totalLiabilities + $totalEquity + $retainedEarnings, 2),
        ];
    }
}
