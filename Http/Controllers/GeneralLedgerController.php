<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;
use Carbon\Carbon;

class GeneralLedgerController extends Controller
{
    public function index(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

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

        $allAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->whereIn('account_level', [2, 3])
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $fromAccountId = $request->get('from_account', '');
        $toAccountId = $request->get('to_account', '');

        $fromAccountCode = null;
        $toAccountCode = null;
        if ($fromAccountId) {
            $fromAcc = $allAccounts->firstWhere('id', $fromAccountId);
            if ($fromAcc) $fromAccountCode = $fromAcc->account_code;
        }
        if ($toAccountId) {
            $toAcc = $allAccounts->firstWhere('id', $toAccountId);
            if ($toAcc) $toAccountCode = $toAcc->account_code;
        }

        $rangeAccounts = $allAccounts;
        if ($fromAccountCode) {
            $rangeAccounts = $rangeAccounts->filter(fn($a) => $a->account_code >= $fromAccountCode);
        }
        if ($toAccountCode) {
            $rangeAccounts = $rangeAccounts->filter(fn($a) => $a->account_code <= $toAccountCode);
        }
        $rangeAccountIds = $rangeAccounts->pluck('id');

        $postedJournals = NexcoreGlJournal::where('company_id', $companyId)
            ->where('status', 'posted')
            ->whereBetween('journal_date', [$fromDate, $toDate])
            ->orderBy('journal_date')
            ->orderBy('journal_number')
            ->get()
            ->keyBy('id');

        $postedJournalIds = $postedJournals->pluck('id');

        $lines = NexcoreGlJournalLine::whereIn('journal_id', $postedJournalIds)
            ->whereIn('account_id', $rangeAccountIds)
            ->orderBy('journal_id')
            ->get();

        $accountsById = $rangeAccounts->keyBy('id');

        $accountTransactions = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($lines as $line) {
            $accId = $line->account_id;
            if (!isset($accountTransactions[$accId])) {
                $accountTransactions[$accId] = [];
            }

            $journal = $postedJournals->get($line->journal_id);
            if (!$journal) continue;

            $debit = (float) $line->debit_amount;
            $credit = (float) $line->credit_amount;
            $totalDebits += $debit;
            $totalCredits += $credit;

            $accountTransactions[$accId][] = [
                'date' => $journal->journal_date,
                'journal_number' => $journal->journal_number,
                'journal_id' => $journal->id,
                'reference' => $journal->reference,
                'description' => $line->description ?: $journal->description,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        $ledgerAccounts = [];
        foreach ($rangeAccounts->sortBy('account_code') as $acc) {
            if (!isset($accountTransactions[$acc->id])) continue;

            $txns = $accountTransactions[$acc->id];

            usort($txns, function ($a, $b) {
                $dateCompare = $a['date']->timestamp <=> $b['date']->timestamp;
                if ($dateCompare !== 0) return $dateCompare;
                return ($a['journal_number'] ?? '') <=> ($b['journal_number'] ?? '');
            });

            $running = 0;
            foreach ($txns as &$txn) {
                if ($acc->normal_balance === 'debit') {
                    $running += ($txn['debit'] - $txn['credit']);
                } else {
                    $running += ($txn['credit'] - $txn['debit']);
                }
                $txn['balance'] = $running;
            }
            unset($txn);

            $accDebit = array_sum(array_column($txns, 'debit'));
            $accCredit = array_sum(array_column($txns, 'credit'));

            $ledgerAccounts[] = [
                'account' => $acc,
                'transactions' => $txns,
                'total_debit' => $accDebit,
                'total_credit' => $accCredit,
                'closing_balance' => $running,
            ];
        }

        $accountCount = count($ledgerAccounts);
        $transactionCount = 0;
        foreach ($ledgerAccounts as $la) {
            $transactionCount += count($la['transactions']);
        }

        return view('nexcore_client_manager::accounting.general-ledger', compact(
            'client', 'ledgerAccounts', 'totalDebits', 'totalCredits',
            'accountCount', 'transactionCount',
            'allAccounts', 'fromAccountId', 'toAccountId',
            'fromDate', 'toDate', 'preset'
        ));
    }
}
