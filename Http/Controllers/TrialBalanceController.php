<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;
use Carbon\Carbon;

class TrialBalanceController extends Controller
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

        $accounts = NexcoreGlChartOfAccount::where('company_id', $clientId)
            ->whereIn('account_level', [2, 3])
            ->orderBy('account_code')
            ->get();

        $balances = NexcoreGlJournalLine::whereIn('journal_id', $postedJournalIds)
            ->select('account_id')
            ->selectRaw('COALESCE(SUM(debit_amount), 0) as total_debit')
            ->selectRaw('COALESCE(SUM(credit_amount), 0) as total_credit')
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $grouped = [];
        $totalDebits = 0;
        $totalCredits = 0;
        $typeOrder = ['revenue', 'cost_of_sales', 'expense', 'asset', 'liability', 'equity', 'other'];

        foreach ($accounts as $account) {
            $bal = $balances->get($account->id);
            $debit = $bal ? (float) $bal->total_debit : 0;
            $credit = $bal ? (float) $bal->total_credit : 0;

            if ($debit == 0 && $credit == 0) {
                continue;
            }

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
            if (!isset($grouped[$type])) {
                $grouped[$type] = ['accounts' => [], 'subtotal_debit' => 0, 'subtotal_credit' => 0];
            }

            $grouped[$type]['accounts'][] = [
                'account' => $account,
                'debit' => $closingDebit,
                'credit' => $closingCredit,
            ];

            $grouped[$type]['subtotal_debit'] += $closingDebit;
            $grouped[$type]['subtotal_credit'] += $closingCredit;

            $totalDebits += $closingDebit;
            $totalCredits += $closingCredit;
        }

        $sortedGroups = [];
        foreach ($typeOrder as $type) {
            if (isset($grouped[$type])) {
                $sortedGroups[$type] = $grouped[$type];
            }
        }

        $isBalanced = round($totalDebits, 2) === round($totalCredits, 2);
        $accountCount = array_sum(array_map(fn($g) => count($g['accounts']), $sortedGroups));

        $typeLabels = $this->typeLabels;
        $typeIcons = $this->typeIcons;
        $typeColors = $this->typeColors;

        return view('nexcore_client_manager::accounting.trial-balance', compact(
            'client', 'sortedGroups', 'totalDebits', 'totalCredits', 'isBalanced',
            'accountCount', 'typeLabels', 'typeIcons', 'typeColors',
            'fromDate', 'toDate', 'preset'
        ));
    }
}
