<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;
use Carbon\Carbon;

class BalanceSheetController extends Controller
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
            ->whereIn('account_type', ['asset', 'liability', 'equity', 'revenue', 'cost_of_sales', 'expense'])
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

        $assetGroups = $this->buildHierarchy($allAccounts, $balances, 'asset');
        $liabilityGroups = $this->buildHierarchy($allAccounts, $balances, 'liability');
        $equityGroups = $this->buildHierarchy($allAccounts, $balances, 'equity');

        $assetTotal = collect($assetGroups)->sum('total');
        $liabilityTotal = collect($liabilityGroups)->sum('total');
        $equityTotal = collect($equityGroups)->sum('total');

        $assetCount = $this->countAccounts($assetGroups);
        $liabilityCount = $this->countAccounts($liabilityGroups);
        $equityCount = $this->countAccounts($equityGroups);

        $revenueTotal = $this->calcTypeTotal($allAccounts, $balances, 'revenue', 'credit');
        $cosTotal = $this->calcTypeTotal($allAccounts, $balances, 'cost_of_sales', 'debit');
        $expenseTotal = $this->calcTypeTotal($allAccounts, $balances, 'expense', 'debit');
        $netProfit = $revenueTotal - $cosTotal - $expenseTotal;

        $totalLiabilitiesEquity = $liabilityTotal + $equityTotal + $netProfit;
        $isBalanced = abs($assetTotal - $totalLiabilitiesEquity) < 0.01;

        return view('nexcore_client_manager::accounting.balance-sheet', compact(
            'client',
            'assetGroups', 'assetTotal', 'assetCount',
            'liabilityGroups', 'liabilityTotal', 'liabilityCount',
            'equityGroups', 'equityTotal', 'equityCount',
            'netProfit', 'totalLiabilitiesEquity', 'isBalanced',
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

    private function calcTypeTotal($allAccounts, $balances, $type, $normalSide)
    {
        $total = 0;
        $typeAccounts = $allAccounts->where('account_type', $type);
        foreach ($typeAccounts as $account) {
            $bal = $balances->get($account->id);
            if (!$bal) continue;
            $debit = (float) $bal->total_debit;
            $credit = (float) $bal->total_credit;
            if ($normalSide === 'credit') {
                $total += ($credit - $debit);
            } else {
                $total += ($debit - $credit);
            }
        }
        return $total;
    }
}
