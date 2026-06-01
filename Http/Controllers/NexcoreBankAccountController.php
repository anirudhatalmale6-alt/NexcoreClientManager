<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreBankAccount;
use Modules\NexcoreClientManager\Models\NexcoreBankTransaction;
use Modules\NexcoreClientManager\Models\NexcoreBankStatement;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;

class NexcoreBankAccountController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccounts = NexcoreBankAccount::where('company_id', $companyId)
            ->with('glAccount', 'statements', 'systemBank')
            ->orderBy('bank_name')
            ->get();

        foreach ($bankAccounts as $ba) {
            $ba->unallocated_count = NexcoreBankTransaction::where('bank_account_id', $ba->id)->where('status', 'unallocated')->count();
            $ba->total_transactions = NexcoreBankTransaction::where('bank_account_id', $ba->id)->count();
            $ba->posted_count = NexcoreBankTransaction::where('bank_account_id', $ba->id)->where('status', 'posted')->count();
        }

        return view('nexcore_client_manager::accounting.bank.accounts', compact('client', 'bankAccounts'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $bankGlAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('account_level', 3)
            ->where('account_type', 'asset')
            ->where(function ($q) {
                $q->where('account_name', 'LIKE', '%Bank%')
                  ->orWhere('account_name', 'LIKE', '%bank%')
                  ->orWhere('account_name', 'LIKE', '%Current Account%')
                  ->orWhere('account_name', 'LIKE', '%Savings Account%')
                  ->orWhere('account_name', 'LIKE', '%Cheque Account%');
            })
            ->orderBy('account_code')
            ->get();

        if ($bankGlAccounts->isEmpty()) {
            $bankGlAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
                ->where('account_level', 3)
                ->where('account_type', 'asset')
                ->where('is_active', true)
                ->orderBy('account_code')
                ->get();
        }

        $bankNames = \DB::table('nexcore_system_banks')->where('is_active', 1)->where('is_deleted', 0)->orderBy('name')->get();
        $accountTypes = \DB::table('nexcore_system_bank_account_types')->where('is_active', 1)->where('is_deleted', 0)->orderBy('name')->get();

        $bankAccount = null;

        return view('nexcore_client_manager::accounting.bank.account-form', compact('client', 'bankGlAccounts', 'bankNames', 'accountTypes', 'bankAccount'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $request->validate([
            'account_id' => 'required|exists:cims_gl_chart_of_accounts_master,id',
            'bank_id' => 'required|exists:nexcore_system_banks,id',
            'account_number' => 'required|string|max:50',
        ]);

        $existing = NexcoreBankAccount::where('company_id', $companyId)
            ->where('account_id', $request->account_id)
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', 'This GL account is already linked to another bank account (' . $existing->bank_name . ' - ' . $existing->account_number . '). Each GL account can only be linked to one bank account.');
        }

        $systemBank = \DB::table('nexcore_system_banks')->where('id', $request->bank_id)->first();

        $bankAccount = NexcoreBankAccount::create([
            'company_id' => $companyId,
            'account_id' => $request->account_id,
            'bank_id' => $request->bank_id,
            'bank_name' => $systemBank ? $systemBank->name : '',
            'account_number' => $request->account_number,
            'branch_code' => $request->branch_code,
            'account_type' => $request->account_type ?? 'cheque',
            'is_active' => 1,
            'opening_balance_date' => $request->opening_balance_date ?: null,
            'opening_balance_amount' => $request->opening_balance_amount ?: 0,
        ]);

        $obAmount = floatval($request->opening_balance_amount ?: 0);
        if ($obAmount != 0 && $request->opening_balance_date) {
            $this->createOpeningBalanceJournal($companyId, $bankAccount, $obAmount, $request->opening_balance_date);
        }

        return redirect()->route('nexcore.clients.show.accounting.bank.accounts', $clientId)
            ->with('success', 'Bank account linked successfully.' . ($obAmount != 0 ? ' Opening balance journal created.' : ''));
    }

    public function edit($clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        $bankGlAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('account_level', 3)
            ->where('account_type', 'asset')
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $bankNames = \DB::table('nexcore_system_banks')->where('is_active', 1)->where('is_deleted', 0)->orderBy('name')->get();
        $accountTypes = \DB::table('nexcore_system_bank_account_types')->where('is_active', 1)->where('is_deleted', 0)->orderBy('name')->get();

        return view('nexcore_client_manager::accounting.bank.account-form', compact('client', 'bankGlAccounts', 'bankNames', 'accountTypes', 'bankAccount'));
    }

    public function update(Request $request, $clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        $request->validate([
            'account_id' => 'required|exists:cims_gl_chart_of_accounts_master,id',
            'bank_id' => 'required|exists:nexcore_system_banks,id',
            'account_number' => 'required|string|max:50',
        ]);

        $existing = NexcoreBankAccount::where('company_id', $companyId)
            ->where('account_id', $request->account_id)
            ->where('id', '!=', $bankId)
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', 'This GL account is already linked to another bank account (' . $existing->bank_name . ' - ' . $existing->account_number . '). Each GL account can only be linked to one bank account.');
        }

        $systemBank = \DB::table('nexcore_system_banks')->where('id', $request->bank_id)->first();

        $bankAccount->update([
            'account_id' => $request->account_id,
            'bank_id' => $request->bank_id,
            'bank_name' => $systemBank ? $systemBank->name : $bankAccount->bank_name,
            'account_number' => $request->account_number,
            'branch_code' => $request->branch_code,
            'account_type' => $request->account_type ?? 'cheque',
            'is_active' => $request->has('is_active') ? 1 : $bankAccount->is_active,
            'opening_balance_date' => $request->opening_balance_date ?: $bankAccount->opening_balance_date,
            'opening_balance_amount' => $request->opening_balance_amount !== null ? $request->opening_balance_amount : $bankAccount->opening_balance_amount,
        ]);

        return redirect()->route('nexcore.clients.show.accounting.bank.accounts', $clientId)
            ->with('success', 'Bank account updated.');
    }

    public function toggle($clientId, $bankId)
    {
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);
        $bankAccount->update(['is_active' => !$bankAccount->is_active]);
        return back()->with('success', 'Bank account ' . ($bankAccount->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy($clientId, $bankId)
    {
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        $txnCount = NexcoreBankTransaction::where('bank_account_id', $bankId)->count();
        $stmtCount = NexcoreBankStatement::where('bank_account_id', $bankId)->count();

        NexcoreBankTransaction::where('bank_account_id', $bankId)->delete();
        NexcoreBankStatement::where('bank_account_id', $bankId)->delete();
        $bankAccount->delete();

        $msg = 'Bank account removed.';
        if ($txnCount > 0 || $stmtCount > 0) {
            $msg = 'Bank account removed along with ' . $stmtCount . ' statement(s) and ' . $txnCount . ' transaction(s).';
        }

        return redirect()->route('nexcore.clients.show.accounting.bank.accounts', $clientId)
            ->with('success', $msg);
    }

    private function createOpeningBalanceJournal($companyId, $bankAccount, $amount, $date)
    {
        $equityAccount = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('account_level', 3)
            ->where('account_type', 'equity')
            ->where(function ($q) {
                $q->where('account_name', 'LIKE', '%Opening Balance%')
                  ->orWhere('account_name', 'LIKE', '%Retained Earnings%');
            })
            ->first();

        if (!$equityAccount) {
            $equityParent = NexcoreGlChartOfAccount::where('company_id', $companyId)
                ->where('account_level', 2)
                ->where('account_type', 'equity')
                ->first();

            if (!$equityParent) {
                $equityMain = NexcoreGlChartOfAccount::where('company_id', $companyId)
                    ->where('account_level', 1)
                    ->where('account_type', 'equity')
                    ->first();

                if (!$equityMain) {
                    return;
                }

                $maxL2Code = NexcoreGlChartOfAccount::where('company_id', $companyId)
                    ->where('parent_id', $equityMain->id)
                    ->where('account_level', 2)
                    ->max('account_code');

                $nextL2 = $maxL2Code ? intval($maxL2Code) + 100 : intval($equityMain->account_code) * 100 + 100;

                $equityParent = NexcoreGlChartOfAccount::create([
                    'company_id' => $companyId,
                    'account_code' => str_pad($nextL2, strlen($maxL2Code ?: '3100'), '0', STR_PAD_LEFT),
                    'account_level' => 2,
                    'account_name' => 'Opening Balances',
                    'account_type' => 'equity',
                    'normal_balance' => 'credit',
                    'is_active' => true,
                    'is_system' => false,
                    'is_header' => true,
                    'parent_id' => $equityMain->id,
                ]);
            }

            $maxL3Code = NexcoreGlChartOfAccount::where('company_id', $companyId)
                ->where('parent_id', $equityParent->id)
                ->where('account_level', 3)
                ->max('account_code');

            $nextL3 = $maxL3Code ? intval($maxL3Code) + 10 : intval($equityParent->account_code) * 10 + 10;

            $equityAccount = NexcoreGlChartOfAccount::create([
                'company_id' => $companyId,
                'account_code' => str_pad($nextL3, strlen($maxL3Code ?: '31010'), '0', STR_PAD_LEFT),
                'account_level' => 3,
                'account_name' => 'Opening Balance Equity',
                'account_type' => 'equity',
                'normal_balance' => 'credit',
                'is_active' => true,
                'is_system' => true,
                'is_header' => false,
                'parent_id' => $equityParent->id,
            ]);
        }

        $allJournalNums = NexcoreGlJournal::where('company_id', $companyId)
            ->where('journal_number', 'like', 'JNL%')
            ->pluck('journal_number')
            ->toArray();
        $maxNum = 0;
        foreach ($allJournalNums as $jn) {
            $num = intval(preg_replace('/[^0-9]/', '', $jn));
            if ($num > $maxNum) {
                $maxNum = $num;
            }
        }
        $journalNumber = 'JNL' . str_pad($maxNum + 1, 6, '0', STR_PAD_LEFT);

        $absAmount = abs($amount);

        $journal = NexcoreGlJournal::create([
            'company_id'     => $companyId,
            'journal_number' => $journalNumber,
            'journal_date'   => $date,
            'period_id'      => null,
            'reference'      => 'OB-' . $bankAccount->bank_name,
            'description'    => 'Opening balance for ' . $bankAccount->bank_name . ' ' . $bankAccount->account_number,
            'source'         => 'opening',
            'status'         => 'posted',
            'total_debit'    => $absAmount,
            'total_credit'   => $absAmount,
            'created_by'     => auth()->id(),
            'posted_by'      => auth()->id(),
            'posted_at'      => now(),
        ]);

        if ($amount > 0) {
            NexcoreGlJournalLine::create([
                'journal_id'    => $journal->id,
                'account_id'    => $bankAccount->account_id,
                'description'   => 'Opening balance - Bank',
                'debit_amount'  => $absAmount,
                'credit_amount' => 0,
                'vat_amount'    => 0,
                'vat_type'      => 'none',
                'line_order'    => 1,
            ]);
            NexcoreGlJournalLine::create([
                'journal_id'    => $journal->id,
                'account_id'    => $equityAccount->id,
                'description'   => 'Opening balance - Equity',
                'debit_amount'  => 0,
                'credit_amount' => $absAmount,
                'vat_amount'    => 0,
                'vat_type'      => 'none',
                'line_order'    => 2,
            ]);
        } else {
            NexcoreGlJournalLine::create([
                'journal_id'    => $journal->id,
                'account_id'    => $equityAccount->id,
                'description'   => 'Opening balance - Equity (overdraft)',
                'debit_amount'  => $absAmount,
                'credit_amount' => 0,
                'vat_amount'    => 0,
                'vat_type'      => 'none',
                'line_order'    => 1,
            ]);
            NexcoreGlJournalLine::create([
                'journal_id'    => $journal->id,
                'account_id'    => $bankAccount->account_id,
                'description'   => 'Opening balance - Bank (overdraft)',
                'debit_amount'  => 0,
                'credit_amount' => $absAmount,
                'vat_amount'    => 0,
                'vat_type'      => 'none',
                'line_order'    => 2,
            ]);
        }
    }
}
