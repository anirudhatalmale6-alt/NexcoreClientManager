<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreBankAccount;
use Modules\NexcoreClientManager\Models\NexcoreBankTransaction;
use Modules\NexcoreClientManager\Models\NexcoreBankAllocationRule;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;

class BankAllocationController extends Controller
{
    public function index($clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        $transactions = NexcoreBankTransaction::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->whereIn('status', ['unallocated', 'allocated'])
            ->with(['bankAccount.glAccount', 'allocatedAccount'])
            ->orderBy('transaction_date')
            ->get();

        $accounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('account_level', 3)
            ->where('is_active', true)
            ->where('is_header', false)
            ->orderBy('account_name')
            ->get();

        $rules = NexcoreBankAllocationRule::where('company_id', $companyId)
            ->where('is_active', true)
            ->with('account')
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($transactions as $txn) {
            if ($txn->status === 'unallocated' && !$txn->allocated_account_id) {
                $suggested = $this->suggestAccount($rules, $txn->description);
                $txn->suggested_account_id = $suggested ? $suggested->account_id : null;
                $txn->suggested_account_name = $suggested ? $suggested->account->account_name : null;
                $txn->suggested_vat_type = $suggested ? $suggested->vat_type : null;
            }
        }

        $parentAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('account_level', 2)
            ->where('is_active', true)
            ->orderBy('account_name')
            ->get();

        return view('nexcore_client_manager::accounting.bank.allocate', compact(
            'client', 'bankAccount', 'transactions', 'accounts', 'rules', 'parentAccounts'
        ));
    }

    private function suggestAccount($rules, $description)
    {
        $desc = strtolower($description);
        foreach ($rules as $rule) {
            if (strpos($desc, strtolower($rule->keyword)) !== false) {
                return $rule;
            }
        }
        return null;
    }

    public function save(Request $request, $clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $vatRate = $client->vat_rate ?: 0;
        $vatDivisor = 1 + ($vatRate / 100);

        $allocations = $request->input('allocations', []);
        $count = 0;

        foreach ($allocations as $txnId => $data) {
            $accountId = $data['account_id'] ?? null;
            if (!$accountId) continue;

            $txn = NexcoreBankTransaction::where('company_id', $companyId)
                ->where('bank_account_id', $bankId)
                ->find($txnId);
            if (!$txn || $txn->status === 'posted') continue;

            $account = NexcoreGlChartOfAccount::find($accountId);
            $vatType = $data['vat_type'] ?? ($account ? $account->vat_type : 'none');
            if (!$client->is_vat_registered) $vatType = 'none';

            $absAmount = abs($txn->amount);
            $vatAmount = 0;
            $netAmount = $absAmount;
            if ($vatType === 'standard') {
                $vatAmount = round($absAmount - ($absAmount / $vatDivisor), 2);
                $netAmount = $absAmount - $vatAmount;
            }

            $txn->update([
                'allocated_account_id' => $accountId,
                'vat_type' => $vatType,
                'vat_amount' => $vatAmount,
                'net_amount' => $netAmount,
                'status' => 'allocated',
            ]);

            if (!empty($data['save_rule']) && !empty($data['rule_keyword'])) {
                $keyword = trim($data['rule_keyword']);
                if ($keyword) {
                    NexcoreBankAllocationRule::updateOrCreate(
                        ['company_id' => $companyId, 'keyword' => $keyword],
                        ['account_id' => $accountId, 'vat_type' => $vatType, 'is_active' => true]
                    );
                }
            }

            $count++;
        }

        return redirect()->route('nexcore.clients.show.accounting.bank.allocate', [$clientId, $bankId])
            ->with('success', "Allocated $count transactions.");
    }

    public function post(Request $request, $clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        $transactions = NexcoreBankTransaction::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->where('status', 'allocated')
            ->with(['bankAccount.glAccount', 'allocatedAccount'])
            ->get();

        if ($transactions->isEmpty()) {
            return back()->with('error', 'No allocated transactions to post.');
        }

        $posted = 0;

        $allJournalNums = NexcoreGlJournal::where('company_id', $companyId)
            ->where('journal_number', 'like', 'JNL%')
            ->pluck('journal_number')
            ->toArray();
        $maxNum = 0;
        foreach ($allJournalNums as $jn) {
            $num = intval(str_replace('JNL', '', $jn));
            if ($num > $maxNum) {
                $maxNum = $num;
            }
        }
        $nextNum = $maxNum + 1;

        foreach ($transactions as $txn) {
            $bankGlId = $txn->bankAccount->account_id;
            $contraId = $txn->allocated_account_id;
            $absAmount = abs($txn->amount);

            $journalNumber = 'JNL' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            $nextNum++;

            $journal = NexcoreGlJournal::create([
                'company_id' => $companyId,
                'journal_number' => $journalNumber,
                'journal_date' => $txn->transaction_date,
                'period_id' => null,
                'reference' => $txn->reference ?: $txn->batch_ref,
                'description' => $txn->description,
                'source' => 'bank_import',
                'status' => 'posted',
                'total_debit' => $absAmount,
                'total_credit' => $absAmount,
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            $lineOrder = 1;
            if ($txn->direction === 'debit') {
                $netAmount = $txn->net_amount ?: $absAmount;
                $vatAmount = $txn->vat_amount ?: 0;

                NexcoreGlJournalLine::create([
                    'journal_id' => $journal->id, 'account_id' => $contraId,
                    'description' => $txn->description,
                    'debit_amount' => $netAmount, 'credit_amount' => 0,
                    'vat_amount' => 0, 'vat_type' => 'none', 'line_order' => $lineOrder++,
                ]);

                if ($vatAmount > 0) {
                    $vatInput = NexcoreGlChartOfAccount::where('company_id', $companyId)
                        ->whereIn('account_name', ['VAT Input', 'VAT Input (VAT Receivable)'])
                        ->where('account_level', 3)->first();
                    if ($vatInput) {
                        NexcoreGlJournalLine::create([
                            'journal_id' => $journal->id, 'account_id' => $vatInput->id,
                            'description' => 'VAT Input - ' . $txn->description,
                            'debit_amount' => $vatAmount, 'credit_amount' => 0,
                            'vat_amount' => $vatAmount, 'vat_type' => 'standard', 'line_order' => $lineOrder++,
                        ]);
                    }
                }

                NexcoreGlJournalLine::create([
                    'journal_id' => $journal->id, 'account_id' => $bankGlId,
                    'description' => $txn->description,
                    'debit_amount' => 0, 'credit_amount' => $absAmount,
                    'vat_amount' => 0, 'vat_type' => 'none', 'line_order' => $lineOrder,
                ]);
            } else {
                NexcoreGlJournalLine::create([
                    'journal_id' => $journal->id, 'account_id' => $bankGlId,
                    'description' => $txn->description,
                    'debit_amount' => $absAmount, 'credit_amount' => 0,
                    'vat_amount' => 0, 'vat_type' => 'none', 'line_order' => $lineOrder++,
                ]);

                $netAmount = $txn->net_amount ?: $absAmount;
                $vatAmount = $txn->vat_amount ?: 0;

                if ($vatAmount > 0) {
                    $vatOutput = NexcoreGlChartOfAccount::where('company_id', $companyId)
                        ->whereIn('account_name', ['VAT Output', 'VAT Output (VAT Payable)'])
                        ->where('account_level', 3)->first();
                    if ($vatOutput) {
                        NexcoreGlJournalLine::create([
                            'journal_id' => $journal->id, 'account_id' => $vatOutput->id,
                            'description' => 'VAT Output - ' . $txn->description,
                            'debit_amount' => 0, 'credit_amount' => $vatAmount,
                            'vat_amount' => $vatAmount, 'vat_type' => 'standard', 'line_order' => $lineOrder++,
                        ]);
                    }
                }

                NexcoreGlJournalLine::create([
                    'journal_id' => $journal->id, 'account_id' => $contraId,
                    'description' => $txn->description,
                    'debit_amount' => 0, 'credit_amount' => $netAmount,
                    'vat_amount' => 0, 'vat_type' => 'none', 'line_order' => $lineOrder,
                ]);
            }

            $txn->update(['status' => 'posted', 'journal_id' => $journal->id]);
            $posted++;
        }

        return redirect()->route('nexcore.clients.show.accounting.journals', $clientId)
            ->with('success', "Posted $posted journal entries from bank transactions.");
    }

    public function exclude(Request $request, $clientId, $bankId)
    {
        $companyId = $clientId;
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['error' => 'No transactions selected.'], 422);
        }

        $count = NexcoreBankTransaction::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->whereIn('id', $ids)
            ->whereIn('status', ['unallocated', 'allocated'])
            ->update(['status' => 'excluded']);

        return response()->json(['success' => true, 'count' => $count]);
    }

    public function unexclude(Request $request, $clientId, $bankId)
    {
        $companyId = $clientId;
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['error' => 'No transactions selected.'], 422);
        }

        $count = NexcoreBankTransaction::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->whereIn('id', $ids)
            ->where('status', 'excluded')
            ->update(['status' => 'unallocated', 'allocated_account_id' => null, 'vat_type' => null, 'vat_amount' => 0, 'net_amount' => null]);

        return response()->json(['success' => true, 'count' => $count]);
    }

    public function autoAllocate(Request $request, $clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $vatRate = $client->vat_rate ?: 0;
        $vatDivisor = 1 + ($vatRate / 100);

        $rules = NexcoreBankAllocationRule::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        if ($rules->isEmpty()) {
            return response()->json(['error' => 'No allocation rules configured.'], 422);
        }

        $transactions = NexcoreBankTransaction::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->where('status', 'unallocated')
            ->get();

        $count = 0;
        \DB::beginTransaction();
        try {
            foreach ($transactions as $txn) {
                $desc = strtolower($txn->description);
                foreach ($rules as $rule) {
                    if (str_contains($desc, strtolower($rule->keyword))) {
                        $amount = abs((float) $txn->amount);
                        $vatAmount = 0;
                        $netAmount = $amount;
                        $vatType = $client->is_vat_registered ? $rule->vat_type : 'none';

                        if ($vatType === 'standard') {
                            $vatAmount = round($amount - ($amount / $vatDivisor), 2);
                            $netAmount = $amount - $vatAmount;
                        }

                        $txn->update([
                            'allocated_account_id' => $rule->account_id,
                            'vat_type' => $vatType,
                            'vat_amount' => $vatAmount,
                            'net_amount' => $netAmount,
                            'status' => 'allocated',
                        ]);

                        $rule->increment('match_count');
                        $count++;
                        break;
                    }
                }
            }
            \DB::commit();

            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function chartQuickAdd(Request $request, $clientId)
    {
        $companyId = $clientId;
        try {
            $request->validate([
                'parent_id' => 'required|exists:cims_gl_chart_of_accounts_master,id',
                'segment3' => 'required|string|size:4',
                'account_name' => 'required|string|max:255',
                'account_type' => 'required|in:asset,liability,equity,revenue,cost_of_sales,expense',
                'normal_balance' => 'required|in:debit,credit',
                'vat_type' => 'required|in:standard,zero_rated,exempt,none',
            ]);

            $parent = NexcoreGlChartOfAccount::where('company_id', $companyId)->findOrFail($request->parent_id);
            if ($parent->account_level != 2) {
                return response()->json(['success' => false, 'error' => 'Parent must be a level 2 account.'], 422);
            }

            $code = $parent->segment1 . '/' . $parent->segment2 . '/' . $request->segment3;
            if (NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_code', $code)->exists()) {
                return response()->json(['success' => false, 'error' => "Account code $code already exists."], 422);
            }

            $account = NexcoreGlChartOfAccount::create([
                'company_id' => $companyId,
                'account_code' => $code,
                'segment1' => $parent->segment1,
                'segment2' => $parent->segment2,
                'segment3' => $request->segment3,
                'account_level' => 3,
                'account_name' => $request->account_name,
                'account_type' => $request->account_type,
                'normal_balance' => $request->normal_balance,
                'vat_type' => $request->vat_type,
                'is_active' => true,
                'is_system' => false,
                'is_header' => false,
                'description' => $request->description,
                'parent_id' => $parent->id,
            ]);

            return response()->json([
                'success' => true,
                'account' => [
                    'id' => $account->id,
                    'name' => $account->account_name,
                    'vat' => $account->vat_type,
                    'code' => $code,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => implode(' ', $e->validator->errors()->all())], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Save failed: ' . $e->getMessage()], 500);
        }
    }

    public function saveRule(Request $request, $clientId)
    {
        $companyId = $clientId;
        $request->validate([
            'keyword' => 'required|string|max:255',
            'account_id' => 'required|integer',
            'vat_type' => 'required|in:standard,zero_rated,exempt,none',
        ]);

        $rule = NexcoreBankAllocationRule::updateOrCreate(
            [
                'company_id' => $companyId,
                'keyword' => $request->keyword,
            ],
            [
                'account_id' => $request->account_id,
                'vat_type' => $request->vat_type,
                'priority' => $request->priority ?? 50,
                'is_active' => true,
            ]
        );

        return response()->json(['success' => true, 'rule' => $rule]);
    }

    public function deleteRule(Request $request, $clientId, $ruleId)
    {
        $companyId = $clientId;
        NexcoreBankAllocationRule::where('company_id', $companyId)
            ->where('id', $ruleId)
            ->delete();

        return response()->json(['success' => true]);
    }
}
