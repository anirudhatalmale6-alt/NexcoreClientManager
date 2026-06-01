<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientFinancial;
use Modules\NexcoreClientManager\Models\NexcoreSystemFinancialType;
use Modules\NexcoreClientManager\Models\NexcoreSystemReturnStatus;

class FinancialController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $financials = NexcoreClientFinancial::where('client_id', $clientId)
            ->with(['financialType', 'status'])
            ->orderByDesc('financial_year')
            ->orderBy('period_end')
            ->get();

        return view('nexcore_client_manager::financials.index', compact('client', 'financials'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $financialTypes = NexcoreSystemFinancialType::where('is_active', true)->orderBy('name')->get();
        $statuses = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::financials.form', compact('client', 'financialTypes', 'statuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'financial_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'financial_year' => 'required|string|max:10',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date',
            'prepared_by' => 'nullable|string|max:255',
            'reviewed_by' => 'nullable|string|max:255',
            'approved_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        NexcoreClientFinancial::create(array_merge(
            $request->only([
                'financial_type_id', 'status_id', 'financial_year',
                'period_start', 'period_end', 'prepared_by', 'reviewed_by',
                'approved_date', 'notes',
            ]),
            ['client_id' => $clientId, 'is_active' => true, 'created_by' => auth()->id(), 'updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.financials', $clientId)
            ->with('success', 'Financial record added successfully.');
    }

    public function edit($clientId, $financialId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $financial = NexcoreClientFinancial::where('client_id', $clientId)->findOrFail($financialId);
        $financialTypes = NexcoreSystemFinancialType::where('is_active', true)->orderBy('name')->get();
        $statuses = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::financials.form', compact('client', 'financial', 'financialTypes', 'statuses'));
    }

    public function update(Request $request, $clientId, $financialId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $financial = NexcoreClientFinancial::where('client_id', $clientId)->findOrFail($financialId);

        $request->validate([
            'financial_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'financial_year' => 'required|string|max:10',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date',
            'prepared_by' => 'nullable|string|max:255',
            'reviewed_by' => 'nullable|string|max:255',
            'approved_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $financial->update(array_merge(
            $request->only([
                'financial_type_id', 'status_id', 'financial_year',
                'period_start', 'period_end', 'prepared_by', 'reviewed_by',
                'approved_date', 'notes',
            ]),
            ['updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.financials', $clientId)
            ->with('success', 'Financial record updated successfully.');
    }

    public function destroy($clientId, $financialId)
    {
        $financial = NexcoreClientFinancial::where('client_id', $clientId)->findOrFail($financialId);
        $financial->delete();

        return redirect()->route('nexcore.clients.show.financials', $clientId)
            ->with('success', 'Financial record deleted successfully.');
    }
}
