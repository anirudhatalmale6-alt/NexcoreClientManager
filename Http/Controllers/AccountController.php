<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreClientJournalLine;
use Modules\NexcoreClientManager\Models\NexcoreAccountTemplate;
use Modules\NexcoreClientManager\Models\NexcoreAccountTemplateItem;
use Modules\NexcoreClientManager\Models\NexcoreBankReconLine;
use Modules\NexcoreClientManager\Models\NexcoreBankReconciliation;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;
use Modules\NexcoreClientManager\Models\NexcoreBankTransaction;
use Modules\NexcoreClientManager\Models\NexcoreBankStatement;
use Modules\NexcoreClientManager\Models\NexcoreBankAllocationRule;
use Modules\NexcoreClientManager\Models\NexcoreBankAccount;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    protected array $accountTypes = [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'cost_of_sales' => 'Cost of Sales',
        'expense' => 'Expense',
        'other' => 'Other',
    ];

    protected array $normalBalances = [
        'debit' => 'Debit',
        'credit' => 'Credit',
    ];

    protected array $vatTypes = [
        'none' => 'None',
        'standard' => 'Standard',
        'exempt' => 'Exempt',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $accounts = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_level', '!=', 1)->orderBy('account_code')->get();
        $accountTypes = $this->accountTypes;

        return view('nexcore_client_manager::accounting.accounts.index', compact('client', 'accounts', 'accountTypes'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $accountTypes = $this->accountTypes;
        $normalBalances = $this->normalBalances;
        $vatTypes = $this->vatTypes;
        $parentAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('is_header', true)
            ->orderBy('account_code')
            ->get();

        return view('nexcore_client_manager::accounting.accounts.form', compact('client', 'accountTypes', 'normalBalances', 'vatTypes', 'parentAccounts'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $request->validate([
            'account_code' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|string|in:' . implode(',', array_keys($this->accountTypes)),
            'segment1' => 'nullable|string|max:20',
            'segment2' => 'nullable|string|max:20',
            'segment3' => 'nullable|string|max:20',
            'account_level' => 'required|integer|in:1,2,3',
            'normal_balance' => 'required|string|in:debit,credit',
            'vat_type' => 'nullable|string|in:none,standard,exempt',
            'parent_id' => 'nullable|exists:cims_gl_chart_of_accounts_master,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_header' => 'boolean',
        ]);

        NexcoreGlChartOfAccount::create(array_merge(
            $request->only([
                'account_code', 'account_name', 'account_type',
                'segment1', 'segment2', 'segment3',
                'account_level', 'normal_balance', 'vat_type',
                'parent_id', 'description',
            ]),
            [
                'company_id' => $companyId,
                'is_active' => $request->boolean('is_active', true),
                'is_header' => $request->boolean('is_header', false),
                'is_system' => false,
            ]
        ));

        return redirect()->route('nexcore.clients.show.accounting.accounts', $clientId)
            ->with('success', 'Account created successfully.');
    }

    public function edit($clientId, $accountId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $account = NexcoreGlChartOfAccount::findOrFail($accountId);
        $accountTypes = $this->accountTypes;
        $normalBalances = $this->normalBalances;
        $vatTypes = $this->vatTypes;
        $parentAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('is_header', true)
            ->where('id', '!=', $accountId)
            ->orderBy('account_code')
            ->get();

        return view('nexcore_client_manager::accounting.accounts.form', compact('client', 'account', 'accountTypes', 'normalBalances', 'vatTypes', 'parentAccounts'));
    }

    public function update(Request $request, $clientId, $accountId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $account = NexcoreGlChartOfAccount::findOrFail($accountId);

        $request->validate([
            'account_code' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|string|in:' . implode(',', array_keys($this->accountTypes)),
            'segment1' => 'nullable|string|max:20',
            'segment2' => 'nullable|string|max:20',
            'segment3' => 'nullable|string|max:20',
            'account_level' => 'required|integer|in:1,2,3',
            'normal_balance' => 'required|string|in:debit,credit',
            'vat_type' => 'nullable|string|in:none,standard,exempt',
            'parent_id' => 'nullable|exists:cims_gl_chart_of_accounts_master,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_header' => 'boolean',
        ]);

        $account->update(array_merge(
            $request->only([
                'account_code', 'account_name', 'account_type',
                'segment1', 'segment2', 'segment3',
                'account_level', 'normal_balance', 'vat_type',
                'parent_id', 'description',
            ]),
            [
                'is_active' => $request->boolean('is_active', true),
                'is_header' => $request->boolean('is_header', false),
            ]
        ));

        return redirect()->route('nexcore.clients.show.accounting.accounts', $clientId)
            ->with('success', 'Account updated successfully.');
    }

    public function destroy($clientId, $accountId)
    {
        $account = NexcoreGlChartOfAccount::findOrFail($accountId);

        if ($account->is_system) {
            return redirect()->back()
                ->with('error', 'System accounts cannot be deleted.');
        }

        $hasLines = NexcoreClientJournalLine::where('account_id', $accountId)->exists();
        if ($hasLines) {
            return redirect()->back()
                ->with('error', 'Cannot delete this account because it has journal entries linked to it.');
        }

        $account->delete();

        return redirect()->route('nexcore.clients.show.accounting.accounts', $clientId)
            ->with('success', 'Account deleted successfully.');
    }

    public function seedForm($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $templates = NexcoreAccountTemplate::active()->orderBy('industry_type')->orderBy('template_name')->get();
        $existingCount = NexcoreGlChartOfAccount::where('company_id', $companyId)->count();
        $allClients = NexcoreClient::where('is_active', true)->orderBy('company_name')->get();

        foreach ($allClients as $c) {
            $cCompanyId = $c->id;
            $c->account_count = NexcoreGlChartOfAccount::where('company_id', $cCompanyId)->count();
        }

        foreach ($templates as $t) {
            $t->item_count = NexcoreAccountTemplateItem::where('template_id', $t->id)->count();
            $t->level1_count = NexcoreAccountTemplateItem::where('template_id', $t->id)->where('account_level', 1)->count();
            $t->level2_count = NexcoreAccountTemplateItem::where('template_id', $t->id)->where('account_level', 2)->count();
            $t->level3_count = NexcoreAccountTemplateItem::where('template_id', $t->id)->where('account_level', 3)->count();
        }

        return view('nexcore_client_manager::accounting.setup-coa', compact('client', 'templates', 'existingCount', 'allClients'));
    }

    public function seed(Request $request, $clientId)
    {
        $request->validate([
            'target_client_id' => 'required|exists:nexcore_clients,id',
            'template_id' => 'required|exists:nexcore_account_templates,id',
            'vat_registered' => 'required|in:0,1',
        ]);

        $vatRegistered = (bool) $request->vat_registered;
        $targetClientId = $request->target_client_id;
        $targetClient = NexcoreClient::findOrFail($targetClientId);
        $targetCompanyId = $targetClientId;

        if (NexcoreGlChartOfAccount::where('company_id', $targetCompanyId)->count() > 0) {
            return back()->with('error', 'Cannot seed: ' . $targetClient->company_name . ' already has accounts in the chart. Setup from template is only for new/empty charts.');
        }

        $items = NexcoreAccountTemplateItem::where('template_id', $request->template_id)
            ->orderBy('account_code')
            ->get();

        $parentMap = [];
        $count = 0;

        foreach ($items as $item) {
            $parentId = null;
            if ($item->account_level == 2 && isset($parentMap[$item->segment1])) {
                $parentId = $parentMap[$item->segment1];
            } elseif ($item->account_level == 3 && isset($parentMap[$item->segment1 . '/' . $item->segment2])) {
                $parentId = $parentMap[$item->segment1 . '/' . $item->segment2];
            }

            $acc = NexcoreGlChartOfAccount::create([
                'company_id' => $targetCompanyId,
                'account_code' => $item->account_code,
                'segment1' => $item->segment1,
                'segment2' => $item->segment2,
                'segment3' => $item->segment3,
                'account_level' => $item->account_level,
                'account_name' => $item->account_name,
                'account_type' => $item->account_type,
                'normal_balance' => $item->normal_balance,
                'vat_type' => $vatRegistered ? $item->vat_type : 'none',
                'is_active' => true,
                'is_system' => $item->is_system,
                'is_header' => $item->is_header,
                'description' => $item->description,
                'parent_id' => $parentId,
                'sars_link_id' => $item->sars_link_id,
            ]);

            if ($item->account_level == 1) {
                $parentMap[$item->segment1] = $acc->id;
            } elseif ($item->account_level == 2) {
                $parentMap[$item->segment1 . '/' . $item->segment2] = $acc->id;
            }

            $count++;
        }

        $template = NexcoreAccountTemplate::find($request->template_id);

        return redirect()->route('nexcore.clients.show.accounting.accounts', $targetClientId)
            ->with('success', "Successfully seeded $count accounts for {$targetClient->company_name} from template: {$template->template_name}");
    }

    public function manageCoa()
    {
        $clients = NexcoreClient::orderBy('company_name')->get();

        foreach ($clients as $c) {
            $cCompanyId = $c->id;
            $c->account_count = NexcoreGlChartOfAccount::where('company_id', $cCompanyId)->count();
            $c->journal_count = NexcoreGlJournal::where('company_id', $cCompanyId)->count();
            $c->bank_account_count = NexcoreBankAccount::where('company_id', $cCompanyId)->count();
            $c->transaction_count = NexcoreBankTransaction::where('company_id', $cCompanyId)->count();
        }

        return view('nexcore_client_manager::accounting.manage-coa', compact('clients'));
    }

    public function resetClientAccounting(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $request->validate([
            'confirm_text' => 'required|in:DELETE',
        ]);

        DB::beginTransaction();
        try {
            $reconIds = NexcoreBankReconciliation::where('company_id', $companyId)->pluck('id');
            if ($reconIds->isNotEmpty()) {
                NexcoreBankReconLine::whereIn('reconciliation_id', $reconIds)->delete();
            }
            NexcoreBankReconciliation::where('company_id', $companyId)->delete();

            $journalIds = NexcoreGlJournal::where('company_id', $companyId)->pluck('id');
            if ($journalIds->isNotEmpty()) {
                NexcoreGlJournalLine::whereIn('journal_id', $journalIds)->delete();
            }
            NexcoreGlJournal::where('company_id', $companyId)->delete();

            NexcoreBankTransaction::where('company_id', $companyId)->delete();
            NexcoreBankStatement::where('company_id', $companyId)->delete();
            NexcoreBankAllocationRule::where('company_id', $companyId)->delete();
            NexcoreBankAccount::where('company_id', $companyId)->delete();
            NexcoreGlChartOfAccount::where('company_id', $companyId)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'All accounting data for ' . $client->company_name . ' has been deleted. The client is now ready for a fresh COA setup.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to reset: ' . $e->getMessage(),
            ], 500);
        }
    }
}
