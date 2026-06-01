<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;
use Carbon\Carbon;

class ManagementPackController extends Controller
{
    protected array $typeLabels = [
        'revenue' => 'Revenue',
        'cost_of_sales' => 'Cost of Sales',
        'expense' => 'Operating Expenses',
        'asset' => 'Assets',
        'liability' => 'Liabilities',
        'equity' => 'Equity',
        'other' => 'Other',
    ];

    protected array $typeIcons = [
        'revenue' => 'fa-arrow-trend-up',
        'cost_of_sales' => 'fa-receipt',
        'expense' => 'fa-arrow-down',
        'asset' => 'fa-coins',
        'liability' => 'fa-hand-holding-usd',
        'equity' => 'fa-balance-scale-right',
        'other' => 'fa-ellipsis-h',
    ];

    protected array $typeColors = [
        'revenue' => 'var(--accent-green)',
        'cost_of_sales' => '#f59e0b',
        'expense' => 'var(--accent-red)',
        'asset' => 'var(--accent-blue)',
        'liability' => '#a855f7',
        'equity' => 'var(--accent-cyan)',
        'other' => '#94a3b8',
    ];

    public function index(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $now = Carbon::now();
        $m = $now->month;
        $fyYear = $m >= 3 ? $now->year : $now->year - 1;
        if ($m >= 3 && $m <= 5) {
            $defPreset = 'q1';
            $defFrom = Carbon::create($fyYear, 3, 1)->format('Y-m-d');
            $defTo = Carbon::create($fyYear, 5, 31)->format('Y-m-d');
        } elseif ($m >= 6 && $m <= 8) {
            $defPreset = 'q2';
            $defFrom = Carbon::create($fyYear, 6, 1)->format('Y-m-d');
            $defTo = Carbon::create($fyYear, 8, 31)->format('Y-m-d');
        } elseif ($m >= 9 && $m <= 11) {
            $defPreset = 'q3';
            $defFrom = Carbon::create($fyYear, 9, 1)->format('Y-m-d');
            $defTo = Carbon::create($fyYear, 11, 30)->format('Y-m-d');
        } else {
            $defPreset = 'q4';
            $defFrom = Carbon::create($fyYear, 12, 1)->format('Y-m-d');
            $defTo = Carbon::create($fyYear + 1, 2, 1)->endOfMonth()->format('Y-m-d');
        }
        $fromDate = $request->get('from_date', $defFrom);
        $toDate = $request->get('to_date', $defTo);
        $preset = $request->get('preset', $defPreset);

        $postedJournalIds = NexcoreGlJournal::where('company_id', $clientId)
            ->where('status', 'posted')
            ->whereBetween('journal_date', [$fromDate, $toDate])
            ->pluck('id');

        $allAccounts = NexcoreGlChartOfAccount::where('company_id', $clientId)
            ->orderBy('account_code')
            ->get()
            ->keyBy('id');

        $balances = NexcoreGlJournalLine::whereIn('journal_id', $postedJournalIds)
            ->select('account_id')
            ->selectRaw('COALESCE(SUM(debit_amount), 0) as total_debit')
            ->selectRaw('COALESCE(SUM(credit_amount), 0) as total_credit')
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $tbAccounts = NexcoreGlChartOfAccount::where('company_id', $clientId)
            ->whereIn('account_level', [2, 3])
            ->orderBy('account_code')
            ->get();

        // === INCOME STATEMENT DATA ===
        $isAccounts = $allAccounts->whereIn('account_type', ['revenue', 'cost_of_sales', 'expense']);
        $revenueGroups = $this->buildISHierarchy($isAccounts, $balances, 'revenue');
        $cosGroups = $this->buildISHierarchy($isAccounts, $balances, 'cost_of_sales');
        $expenseGroups = $this->buildISHierarchy($isAccounts, $balances, 'expense');

        $revenueTotal = collect($revenueGroups)->sum('total');
        $cosTotal = collect($cosGroups)->sum('total');
        $expenseTotal = collect($expenseGroups)->sum('total');

        $revenueCount = $this->countHierarchyAccounts($revenueGroups);
        $cosCount = $this->countHierarchyAccounts($cosGroups);
        $expenseCount = $this->countHierarchyAccounts($expenseGroups);

        $grossProfit = $revenueTotal - $cosTotal;
        $netProfit = $grossProfit - $expenseTotal;

        // === BALANCE SHEET DATA ===
        $bsAccounts = $allAccounts->whereIn('account_type', ['asset', 'liability', 'equity']);
        $assetGroups = $this->buildBSHierarchy($bsAccounts, $balances, 'asset');
        $liabilityGroups = $this->buildBSHierarchy($bsAccounts, $balances, 'liability');
        $equityGroups = $this->buildBSHierarchy($bsAccounts, $balances, 'equity');

        $assetTotal = collect($assetGroups)->sum('total');
        $liabilityTotal = collect($liabilityGroups)->sum('total');
        $equityTotal = collect($equityGroups)->sum('total');

        $assetCount = $this->countHierarchyAccounts($assetGroups);
        $liabilityCount = $this->countHierarchyAccounts($liabilityGroups);
        $equityCount = $this->countHierarchyAccounts($equityGroups);

        $totalLiabilitiesEquity = $liabilityTotal + $equityTotal + $netProfit;
        $isBalanced = abs($assetTotal - $totalLiabilitiesEquity) < 0.01;

        // === TRIAL BALANCE DATA ===
        $tbGrouped = [];
        $totalDebits = 0;
        $totalCredits = 0;
        $typeOrder = ['revenue', 'cost_of_sales', 'expense', 'asset', 'liability', 'equity', 'other'];

        foreach ($tbAccounts as $account) {
            $bal = $balances->get($account->id);
            $debit = $bal ? (float) $bal->total_debit : 0;
            $credit = $bal ? (float) $bal->total_credit : 0;

            if ($debit == 0 && $credit == 0) continue;

            $closingDebit = 0;
            $closingCredit = 0;

            if ($account->normal_balance === 'debit') {
                $net = $debit - $credit;
                if ($net >= 0) { $closingDebit = $net; } else { $closingCredit = abs($net); }
            } else {
                $net = $credit - $debit;
                if ($net >= 0) { $closingCredit = $net; } else { $closingDebit = abs($net); }
            }

            $type = $account->account_type;
            if (!isset($tbGrouped[$type])) {
                $tbGrouped[$type] = ['accounts' => [], 'subtotal_debit' => 0, 'subtotal_credit' => 0];
            }

            $tbGrouped[$type]['accounts'][] = [
                'account' => $account,
                'debit' => $closingDebit,
                'credit' => $closingCredit,
            ];

            $tbGrouped[$type]['subtotal_debit'] += $closingDebit;
            $tbGrouped[$type]['subtotal_credit'] += $closingCredit;

            $totalDebits += $closingDebit;
            $totalCredits += $closingCredit;
        }

        $sortedGroups = [];
        foreach ($typeOrder as $type) {
            if (isset($tbGrouped[$type])) {
                $sortedGroups[$type] = $tbGrouped[$type];
            }
        }

        $tbIsBalanced = round($totalDebits, 2) === round($totalCredits, 2);
        $accountCount = array_sum(array_map(fn($g) => count($g['accounts']), $sortedGroups));

        $typeLabels = $this->typeLabels;
        $typeIcons = $this->typeIcons;
        $typeColors = $this->typeColors;

        return view('nexcore_client_manager::accounting.management-pack', compact(
            'client', 'fromDate', 'toDate', 'preset',
            // Income Statement
            'revenueGroups', 'revenueTotal', 'revenueCount',
            'cosGroups', 'cosTotal', 'cosCount',
            'expenseGroups', 'expenseTotal', 'expenseCount',
            'grossProfit', 'netProfit',
            // Balance Sheet
            'assetGroups', 'assetTotal', 'assetCount',
            'liabilityGroups', 'liabilityTotal', 'liabilityCount',
            'equityGroups', 'equityTotal', 'equityCount',
            'totalLiabilitiesEquity', 'isBalanced',
            // Trial Balance
            'sortedGroups', 'totalDebits', 'totalCredits', 'tbIsBalanced',
            'accountCount', 'typeLabels', 'typeIcons', 'typeColors'
        ));
    }

    private function buildISHierarchy($allAccounts, $balances, $type)
    {
        $groups = [];
        $typeAccounts = $allAccounts->where('account_type', $type);
        $mainAccounts = $typeAccounts->where('account_level', 1)->sortBy('account_code');

        foreach ($mainAccounts as $main) {
            $subGroups = [];
            $subAccounts = $typeAccounts->where('parent_id', $main->id)->where('account_level', 2)->sortBy('account_code');

            foreach ($subAccounts as $sub) {
                $details = [];
                $subAmount = 0;

                $detailAccounts = $typeAccounts->where('parent_id', $sub->id)->where('account_level', 3)->sortBy('account_name');

                foreach ($detailAccounts as $detail) {
                    $bal = $balances->get($detail->id);
                    if (!$bal) continue;
                    $debit = (float) $bal->total_debit;
                    $credit = (float) $bal->total_credit;
                    if ($debit == 0 && $credit == 0) continue;
                    $amount = ($type === 'revenue') ? ($credit - $debit) : ($debit - $credit);
                    $details[] = ['account' => $detail, 'amount' => $amount];
                    $subAmount += $amount;
                }

                $subBal = $balances->get($sub->id);
                if ($subBal) {
                    $d = (float) $subBal->total_debit;
                    $c = (float) $subBal->total_credit;
                    if ($d != 0 || $c != 0) {
                        $amt = ($type === 'revenue') ? ($c - $d) : ($d - $c);
                        if (empty($details)) {
                            $details[] = ['account' => $sub, 'amount' => $amt, 'is_sub_leaf' => true];
                        }
                        $subAmount += $amt;
                    }
                }

                if (!empty($details) || $subAmount != 0) {
                    $subGroups[] = [
                        'account' => $sub,
                        'details' => $details,
                        'subtotal' => $subAmount,
                    ];
                }
            }

            if (!empty($subGroups)) {
                $groups[] = [
                    'account' => $main,
                    'sub_groups' => $subGroups,
                    'total' => collect($subGroups)->sum('subtotal'),
                ];
            }
        }

        return $groups;
    }

    private function buildBSHierarchy($allAccounts, $balances, $type)
    {
        $groups = [];
        $typeAccounts = $allAccounts->where('account_type', $type);
        $mainAccounts = $typeAccounts->where('account_level', 1)->sortBy('account_code');

        foreach ($mainAccounts as $main) {
            $subGroups = [];
            $subAccounts = $typeAccounts->where('parent_id', $main->id)->where('account_level', 2)->sortBy('account_code');

            foreach ($subAccounts as $sub) {
                $details = [];
                $subAmount = 0;

                $detailAccounts = $typeAccounts->where('parent_id', $sub->id)->where('account_level', 3)->sortBy('account_name');

                foreach ($detailAccounts as $detail) {
                    $bal = $balances->get($detail->id);
                    if (!$bal) continue;
                    $debit = (float) $bal->total_debit;
                    $credit = (float) $bal->total_credit;
                    if ($debit == 0 && $credit == 0) continue;
                    $amount = ($type === 'asset') ? ($debit - $credit) : ($credit - $debit);
                    $details[] = ['account' => $detail, 'amount' => $amount];
                    $subAmount += $amount;
                }

                $subBal = $balances->get($sub->id);
                if ($subBal) {
                    $d = (float) $subBal->total_debit;
                    $c = (float) $subBal->total_credit;
                    if ($d != 0 || $c != 0) {
                        $amt = ($type === 'asset') ? ($d - $c) : ($c - $d);
                        if (empty($details)) {
                            $details[] = ['account' => $sub, 'amount' => $amt, 'is_sub_leaf' => true];
                        }
                        $subAmount += $amt;
                    }
                }

                if (!empty($details) || $subAmount != 0) {
                    $subGroups[] = [
                        'account' => $sub,
                        'details' => $details,
                        'subtotal' => $subAmount,
                    ];
                }
            }

            if (!empty($subGroups)) {
                $groups[] = [
                    'account' => $main,
                    'sub_groups' => $subGroups,
                    'total' => collect($subGroups)->sum('subtotal'),
                ];
            }
        }

        return $groups;
    }

    private function countHierarchyAccounts($groups)
    {
        $count = 0;
        foreach ($groups as $group) {
            foreach ($group['sub_groups'] as $sub) {
                $count += count($sub['details']);
            }
        }
        return $count;
    }
}
