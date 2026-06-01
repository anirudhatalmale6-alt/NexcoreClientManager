<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;
use Carbon\Carbon;

class IncomeStatementController extends Controller
{
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
            ->whereIn('account_type', ['revenue', 'cost_of_sales', 'expense'])
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

        $revenueGroups = $this->buildHierarchy($allAccounts, $balances, 'revenue');
        $cosGroups = $this->buildHierarchy($allAccounts, $balances, 'cost_of_sales');
        $expenseGroups = $this->buildHierarchy($allAccounts, $balances, 'expense');

        $revenueTotal = collect($revenueGroups)->sum('total');
        $cosTotal = collect($cosGroups)->sum('total');
        $expenseTotal = collect($expenseGroups)->sum('total');

        $revenueCount = $this->countAccounts($revenueGroups);
        $cosCount = $this->countAccounts($cosGroups);
        $expenseCount = $this->countAccounts($expenseGroups);

        $grossProfit = $revenueTotal - $cosTotal;
        $netProfit = $grossProfit - $expenseTotal;

        return view('nexcore_client_manager::accounting.income-statement', compact(
            'client',
            'revenueGroups', 'revenueTotal', 'revenueCount',
            'cosGroups', 'cosTotal', 'cosCount',
            'expenseGroups', 'expenseTotal', 'expenseCount',
            'grossProfit', 'netProfit',
            'fromDate', 'toDate', 'preset'
        ));
    }

    private function buildHierarchy($allAccounts, $balances, $type)
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

    private function countAccounts($groups)
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
