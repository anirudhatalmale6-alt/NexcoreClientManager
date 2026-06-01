<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientBank;
use Modules\NexcoreClientManager\Models\NexcoreSystemAccountType;
class BankingController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $accounts = NexcoreClientBank::where('client_id', $clientId)
            ->with(['bank', 'accountType'])
            ->orderByDesc('is_primary')
            ->orderBy('account_label')
            ->get();

        return view('nexcore_client_manager::banking.index', compact('client', 'accounts'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $banks = \Modules\CIMS_PMPRO\Models\NexcorSystemBank::where('is_active', true)->orderBy('name')->get();
        $accountTypes = NexcoreSystemAccountType::where('is_active', true)->orderBy('name')->get();
        $glAccounts = $this->getGlAccounts($clientId);

        return view('nexcore_client_manager::banking.form', compact('client', 'banks', 'accountTypes', 'glAccounts'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'bank_id' => 'required|integer',
            'account_type_id' => 'required|integer',
            'gl_account_id' => 'nullable|integer',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch_code' => 'nullable|string|max:20',
            'swift_code' => 'nullable|string|max:20',
            'account_label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($request->boolean('is_primary')) {
            NexcoreClientBank::where('client_id', $clientId)->update(['is_primary' => false]);
        }

        NexcoreClientBank::create([
            'client_id' => $clientId,
            'bank_id' => $request->bank_id,
            'account_type_id' => $request->account_type_id,
            'gl_account_id' => $request->gl_account_id,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch_code' => $request->branch_code,
            'swift_code' => $request->swift_code,
            'account_label' => $request->account_label,
            'is_primary' => $request->boolean('is_primary'),
            'is_active' => true,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('nexcore.clients.show.banking', $clientId)
            ->with('success', 'Bank account added successfully.');
    }

    public function edit($clientId, $accountId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $account = NexcoreClientBank::where('client_id', $clientId)->findOrFail($accountId);
        $banks = \Modules\CIMS_PMPRO\Models\NexcorSystemBank::where('is_active', true)->orderBy('name')->get();
        $accountTypes = NexcoreSystemAccountType::where('is_active', true)->orderBy('name')->get();
        $glAccounts = $this->getGlAccounts($clientId);

        return view('nexcore_client_manager::banking.form', compact('client', 'account', 'banks', 'accountTypes', 'glAccounts'));
    }

    public function update(Request $request, $clientId, $accountId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $account = NexcoreClientBank::where('client_id', $clientId)->findOrFail($accountId);

        $request->validate([
            'bank_id' => 'required|integer',
            'account_type_id' => 'required|integer',
            'gl_account_id' => 'nullable|integer',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch_code' => 'nullable|string|max:20',
            'swift_code' => 'nullable|string|max:20',
            'account_label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($request->boolean('is_primary')) {
            NexcoreClientBank::where('client_id', $clientId)
                ->where('id', '!=', $accountId)
                ->update(['is_primary' => false]);
        }

        $account->update([
            'bank_id' => $request->bank_id,
            'account_type_id' => $request->account_type_id,
            'gl_account_id' => $request->gl_account_id,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch_code' => $request->branch_code,
            'swift_code' => $request->swift_code,
            'account_label' => $request->account_label,
            'is_primary' => $request->boolean('is_primary'),
            'notes' => $request->notes,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('nexcore.clients.show.banking', $clientId)
            ->with('success', 'Bank account updated successfully.');
    }

    public function destroy($clientId, $accountId)
    {
        $account = NexcoreClientBank::where('client_id', $clientId)->findOrFail($accountId);
        $account->delete();

        return redirect()->route('nexcore.clients.show.banking', $clientId)
            ->with('success', 'Bank account deleted successfully.');
    }

    public function toggle($clientId, $accountId)
    {
        $account = NexcoreClientBank::where('client_id', $clientId)->findOrFail($accountId);
        $account->update(['is_active' => !$account->is_active]);

        return redirect()->back()->with('success', 'Bank account status updated.');
    }

    private function getGlAccounts($clientId)
    {
        return \Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount::where('company_id', $clientId)
            ->where('is_active', true)
            ->where('is_header', false)
            ->where('account_type', 'asset')
            ->orderBy('account_code')
            ->get(['id', 'account_code', 'account_name', 'account_type']);
    }
}
