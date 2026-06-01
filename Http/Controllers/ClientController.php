<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\CIMS_PMPRO\Models\PmproAddressLink;
use Modules\NexcoreClientManager\Models\NexcoreClientContact;
use Modules\NexcoreClientManager\Models\NexcoreClientBank;
use Modules\NexcoreClientManager\Models\NexcoreBankAccount;
use Modules\NexcoreClientManager\Models\NexcoreBankTransaction;
use Modules\NexcoreClientManager\Models\NexcoreClientDirector;
use Modules\NexcoreClientManager\Models\NexcoreClientSarsReturn;
use Modules\NexcoreClientManager\Models\NexcoreClientCipcReturn;
use Modules\NexcoreClientManager\Models\NexcoreClientFinancial;
use Modules\NexcoreClientManager\Models\NexcoreClientDocument;
use Modules\NexcoreClientManager\Models\NexcoreClientBee;
use Modules\NexcoreClientManager\Models\NexcoreClientCoida;
use Modules\NexcoreClientManager\Models\NexcoreClientUifReturn;
use Modules\NexcoreClientManager\Models\NexcoreClientPayeReturn;
use Modules\NexcoreClientManager\Models\NexcoreClientSdlReturn;
use Modules\NexcoreClientManager\Models\NexcoreClientUnion;
use Modules\NexcoreClientManager\Models\NexcoreClientTask;
use Modules\NexcoreClientManager\Models\NexcoreClientAuditTrail;
use Modules\CIMSDocManager\Models\DocumentCategory;
use Modules\CIMSDocManager\Models\DocumentType;
use Modules\CIMS_PMPRO\Models\NexcorSystemCompanyType;
use Modules\CIMS_PMPRO\Models\NexcorSystemCompanyStatus;
use Modules\CIMS_PMPRO\Models\NexcorSystemIndustry;
use Modules\CIMS_PMPRO\Models\NexcorSicCode;
use Modules\CIMS_PMPRO\Models\NexcorSystemBeeStatusLevel;
use Modules\CIMS_PMPRO\Models\PmproAddressType;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = NexcoreClient::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name', 'like', "%{$s}%")
                  ->orWhere('trading_name', 'like', "%{$s}%")
                  ->orWhere('client_code', 'like', "%{$s}%")
                  ->orWhere('registration_number', 'like', "%{$s}%")
                  ->orWhere('tax_number', 'like', "%{$s}%")
                  ->orWhere('vat_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('company_type')) {
            $query->where('company_type_id', $request->company_type);
        }

        $clients = $query->with(['companyType', 'companyStatus', 'industry'])
            ->orderBy('company_name')
            ->paginate(25);

        $stats = [
            'total' => NexcoreClient::count(),
            'active' => NexcoreClient::where('is_active', true)->count(),
            'inactive' => NexcoreClient::where('is_active', false)->count(),
        ];

        $companyTypes = NexcorSystemCompanyType::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::clients.index', compact('clients', 'stats', 'companyTypes'));
    }

    public function create()
    {
        $companyTypes = NexcorSystemCompanyType::where('is_active', true)->orderBy('name')->get();
        $companyStatuses = NexcorSystemCompanyStatus::where('is_active', true)->orderBy('name')->get();
        $industries = NexcorSystemIndustry::where('is_active', true)->orderBy('name')->get();
        $sicCodes = NexcorSicCode::where('is_active', true)->orderBy('sic_code')->get();
        $beeLevels = NexcorSystemBeeStatusLevel::where('is_active', true)->orderBy('name')->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $practices = \DB::table('nexcore_practices')->whereNull('deleted_at')->where('is_active', 1)->orderBy('practice_name')->get();
        $allClerks = \DB::table('nexcore_clerks')->whereNull('deleted_at')->where('is_active', 1)->orderBy('first_name')->get();
        $practiceClerkLinks = \DB::table('nexcore_practice_clerks')->where('is_active', 1)->get();
        $clerkAssignments = collect();

        return view('nexcore_client_manager::clients.form', compact(
            'companyTypes', 'companyStatuses', 'industries', 'sicCodes', 'beeLevels', 'months',
            'practices', 'allClerks', 'practiceClerkLinks', 'clerkAssignments'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'company_type_id' => 'nullable|integer',
            'company_status_id' => 'nullable|integer',
            'industry_id' => 'nullable|integer',
            'sic_code_id' => 'nullable|integer',
            'bee_status_id' => 'nullable|integer',
            'tax_number' => 'nullable|string|max:20',
            'vat_number' => 'nullable|string|max:20',
            'paye_number' => 'nullable|string|max:20',
            'sdl_number' => 'nullable|string|max:20',
            'uif_number' => 'nullable|string|max:20',
            'coida_number' => 'nullable|string|max:20',
            'financial_year_end' => 'nullable|integer|min:1|max:12',
            'date_incorporated' => 'nullable|date',
            'date_commenced_trading' => 'nullable|date',
            'client_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'watermark_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        $clientCode = $this->generateClientCode($request->company_name);

        $logoPath = null;
        if ($request->hasFile('client_logo')) {
            $logoPath = $this->uploadLogo($request->file('client_logo'), $clientCode);
        }

        $watermarkPath = null;
        if ($request->hasFile('watermark_logo')) {
            $watermarkPath = $this->uploadLogo($request->file('watermark_logo'), $clientCode, 'watermark');
        }

        $client = NexcoreClient::create([
            'client_code' => $clientCode,
            'company_name' => $request->company_name,
            'trading_name' => $request->trading_name,
            'registration_number' => $request->registration_number,
            'company_type_id' => $request->company_type_id,
            'company_status_id' => $request->company_status_id,
            'industry_id' => $request->industry_id,
            'sic_code_id' => $request->sic_code_id,
            'bee_status_id' => $request->bee_status_id,
            'tax_number' => $request->tax_number,
            'vat_number' => $request->vat_number,
            'is_vat_registered' => $request->input('is_vat_registered', 0) ? 1 : 0,
            'paye_number' => $request->paye_number,
            'sdl_number' => $request->sdl_number,
            'uif_number' => $request->uif_number,
            'coida_number' => $request->coida_number,
            'financial_year_end' => $request->financial_year_end,
            'date_incorporated' => $request->date_incorporated,
            'date_commenced_trading' => $request->date_commenced_trading,
            'client_logo' => $logoPath,
            'watermark_logo' => $watermarkPath,
            'description' => $request->description,
            'practice_id' => $request->practice_id,
            'profit_code' => $request->profit_code,
            'loss_code' => $request->loss_code,
            'is_active' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->saveClerkAssignments($client->id, $request);

        return redirect()->route('nexcore.clients.show.dashboard', $client->id)
            ->with('success', 'Client created successfully.');
    }

    public function dashboard($client)
    {
        $client = NexcoreClient::with(['companyType', 'companyStatus', 'industry', 'sicCode', 'beeStatus'])
            ->findOrFail($client);

        $addresses = PmproAddressLink::where('linkable_type', NexcoreClient::class)
            ->where('linkable_id', $client->id)
            ->with(['address.province', 'address.suburb', 'addressType'])
            ->orderByDesc('is_primary')->orderBy('address_label')->get();

        $contacts = NexcoreClientContact::where('client_id', $client->id)
            ->with(['contactType', 'title'])
            ->orderByDesc('is_primary')->orderBy('last_name')->get();

        $bankAccounts = NexcoreBankAccount::where('company_id', $client->id)
            ->with('glAccount')
            ->orderBy('bank_name')
            ->get();
        foreach ($bankAccounts as $ba) {
            $ba->total_transactions = NexcoreBankTransaction::where('bank_account_id', $ba->id)->count();
            $ba->unallocated_count = NexcoreBankTransaction::where('bank_account_id', $ba->id)->where('status', 'unallocated')->count();
            $ba->posted_count = NexcoreBankTransaction::where('bank_account_id', $ba->id)->where('status', 'posted')->count();
        }

        $directors = NexcoreClientDirector::where('client_id', $client->id)
            ->with(['directorType', 'title'])
            ->orderBy('last_name')->get();
        $totalShares = $directors->sum('shareholding_percentage');

        $sarsReturns = NexcoreClientSarsReturn::where('client_id', $client->id)
            ->with(['returnType', 'status'])
            ->orderByDesc('tax_year')->orderBy('due_date')->get();

        $cipcReturns = NexcoreClientCipcReturn::where('client_id', $client->id)
            ->with(['returnType', 'status'])
            ->orderByDesc('filing_year')->orderBy('due_date')->get();

        $financials = NexcoreClientFinancial::where('client_id', $client->id)
            ->with(['financialType', 'status'])
            ->orderByDesc('financial_year')->orderBy('period_end')->get();

        $beeRecords = NexcoreClientBee::where('client_id', $client->id)
            ->orderByDesc('issued_date')->get();

        $coidaRecords = NexcoreClientCoida::where('client_id', $client->id)
            ->orderByDesc('assessment_year')->get();

        $uifReturns = NexcoreClientUifReturn::where('client_id', $client->id)
            ->orderByDesc('due_date')->get();

        $payeReturns = NexcoreClientPayeReturn::where('client_id', $client->id)
            ->orderByDesc('tax_year')->orderByDesc('due_date')->get();

        $sdlReturns = NexcoreClientSdlReturn::where('client_id', $client->id)
            ->orderByDesc('tax_year')->orderByDesc('due_date')->get();

        $unionRecords = NexcoreClientUnion::where('client_id', $client->id)
            ->orderBy('union_name')->get();

        $documents = NexcoreClientDocument::where('client_id', $client->id)
            ->with(['documentType', 'status', 'uploader', 'creator'])
            ->orderByDesc('created_at')->get();

        $docCategories = DocumentCategory::where('is_active', true)
            ->where('show_in_tab', true)
            ->orderBy('display_order')
            ->get();

        $docTypesByCategory = DocumentType::where('is_active', true)
            ->where('show_types_in_tab', true)
            ->where('category_id', '>', 0)
            ->orderBy('display_order')
            ->get()
            ->groupBy('category_id');

        $tasks = NexcoreClientTask::where('client_id', $client->id)
            ->orderByRaw("FIELD(task_status, 'in_progress', 'pending', 'completed', 'cancelled')")
            ->orderBy('due_date')
            ->get();

        $auditTrail = NexcoreClientAuditTrail::where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        $addressTypes = PmproAddressType::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::clients.dashboard', compact(
            'client', 'addresses', 'addressTypes', 'contacts', 'bankAccounts', 'directors', 'totalShares',
            'sarsReturns', 'cipcReturns', 'financials',
            'beeRecords', 'coidaRecords', 'uifReturns', 'payeReturns', 'sdlReturns', 'unionRecords',
            'documents', 'docCategories', 'docTypesByCategory', 'tasks', 'auditTrail'
        ));
    }

    public function edit($client)
    {
        $client = NexcoreClient::findOrFail($client);

        $companyTypes = NexcorSystemCompanyType::where('is_active', true)->orderBy('name')->get();
        $companyStatuses = NexcorSystemCompanyStatus::where('is_active', true)->orderBy('name')->get();
        $industries = NexcorSystemIndustry::where('is_active', true)->orderBy('name')->get();
        $sicCodes = NexcorSicCode::where('is_active', true)->orderBy('sic_code')->get();
        $beeLevels = NexcorSystemBeeStatusLevel::where('is_active', true)->orderBy('name')->get();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        $practices = \DB::table('nexcore_practices')->whereNull('deleted_at')->where('is_active', 1)->orderBy('practice_name')->get();
        $allClerks = \DB::table('nexcore_clerks')->whereNull('deleted_at')->where('is_active', 1)->orderBy('first_name')->get();
        $practiceClerkLinks = \DB::table('nexcore_practice_clerks')->where('is_active', 1)->get();
        $clerkAssignments = \DB::table('nexcore_client_clerk_assignments')->where('client_id', $client->id)->get();

        return view('nexcore_client_manager::clients.form', compact(
            'client', 'companyTypes', 'companyStatuses', 'industries', 'sicCodes', 'beeLevels', 'months',
            'practices', 'allClerks', 'practiceClerkLinks', 'clerkAssignments'
        ));
    }

    public function update(Request $request, $client)
    {
        $client = NexcoreClient::findOrFail($client);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'company_type_id' => 'nullable|integer',
            'company_status_id' => 'nullable|integer',
            'industry_id' => 'nullable|integer',
            'sic_code_id' => 'nullable|integer',
            'bee_status_id' => 'nullable|integer',
            'tax_number' => 'nullable|string|max:20',
            'vat_number' => 'nullable|string|max:20',
            'is_vat_registered' => 'nullable',
            'paye_number' => 'nullable|string|max:20',
            'sdl_number' => 'nullable|string|max:20',
            'uif_number' => 'nullable|string|max:20',
            'coida_number' => 'nullable|string|max:20',
            'financial_year_end' => 'nullable|integer|min:1|max:12',
            'date_incorporated' => 'nullable|date',
            'date_commenced_trading' => 'nullable|date',
            'client_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'watermark_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->only([
            'company_name', 'trading_name', 'registration_number',
            'company_type_id', 'company_status_id', 'industry_id',
            'sic_code_id', 'bee_status_id',
            'tax_number', 'vat_number', 'is_vat_registered', 'paye_number', 'sdl_number',
            'uif_number', 'coida_number', 'financial_year_end',
            'date_incorporated', 'date_commenced_trading', 'description',
            'practice_id', 'profit_code', 'loss_code',
        ]);
        $data['is_vat_registered'] = $request->input('is_vat_registered', 0) ? 1 : 0;
        $data['updated_by'] = auth()->id();

        if ($request->boolean('remove_logo')) {
            if ($client->client_logo && file_exists(base_path('../' . $client->client_logo))) {
                @unlink(base_path('../' . $client->client_logo));
            }
            $data['client_logo'] = null;
        }

        if ($request->hasFile('client_logo')) {
            if ($client->client_logo && file_exists(base_path('../' . $client->client_logo))) {
                @unlink(base_path('../' . $client->client_logo));
            }
            $data['client_logo'] = $this->uploadLogo($request->file('client_logo'), $client->client_code);
        }

        if ($request->boolean('remove_watermark')) {
            if ($client->watermark_logo && file_exists(base_path('../' . $client->watermark_logo))) {
                @unlink(base_path('../' . $client->watermark_logo));
            }
            $data['watermark_logo'] = null;
        }

        if ($request->hasFile('watermark_logo')) {
            if ($client->watermark_logo && file_exists(base_path('../' . $client->watermark_logo))) {
                @unlink(base_path('../' . $client->watermark_logo));
            }
            $data['watermark_logo'] = $this->uploadLogo($request->file('watermark_logo'), $client->client_code, 'watermark');
        }

        $client->update($data);

        $this->saveClerkAssignments($client->id, $request);

        return redirect()->route('nexcore.clients.show.dashboard', $client->id)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy($client)
    {
        $client = NexcoreClient::findOrFail($client);
        $client->delete();

        return redirect()->route('nexcore.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function toggle($client)
    {
        $client = NexcoreClient::findOrFail($client);
        $client->update(['is_active' => !$client->is_active]);

        return redirect()->back()->with('success', 'Client status updated.');
    }

    private function generateClientCode($companyName)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $companyName), 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        $lastCode = NexcoreClient::where('client_code', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(client_code, 4) AS UNSIGNED) DESC')
            ->value('client_code');

        if ($lastCode) {
            $num = (int) substr($lastCode, 3) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    private function uploadLogo($file, $code, $prefix = 'logo')
    {
        $dir = base_path('../uploads/clients');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = $file->getClientOriginalExtension() ?: 'png';
        $filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $code)) . '_' . $prefix . '_' . time() . '.' . $ext;
        $file->move($dir, $filename);

        return 'uploads/clients/' . $filename;
    }

    private function saveClerkAssignments($clientId, Request $request)
    {
        $departments = $request->input('assign_department', []);
        $clerkIds = $request->input('assign_clerk', []);
        $primaries = $request->input('assign_primary', []);
        $practiceId = $request->input('practice_id');

        if (!$practiceId) {
            return;
        }

        \DB::table('nexcore_client_clerk_assignments')->where('client_id', $clientId)->delete();

        $now = now();
        foreach ($departments as $idx => $dept) {
            $clerkId = isset($clerkIds[$idx]) ? $clerkIds[$idx] : null;
            if (!$dept || !$clerkId) {
                continue;
            }
            \DB::table('nexcore_client_clerk_assignments')->insert([
                'client_id' => $clientId,
                'clerk_id' => $clerkId,
                'practice_id' => $practiceId,
                'department' => $dept,
                'is_primary' => isset($primaries[$idx]) ? 1 : 0,
                'is_active' => 1,
                'assigned_date' => $now->toDateString(),
                'created_by' => auth()->id(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}