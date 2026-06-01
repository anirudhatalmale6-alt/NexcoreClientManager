<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientCipcReturn;
use Modules\NexcoreClientManager\Models\NexcoreSystemCipcReturnType;
use Modules\NexcoreClientManager\Models\NexcoreSystemReturnStatus;

class CipcController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $returns = NexcoreClientCipcReturn::where('client_id', $clientId)
            ->with(['returnType', 'status'])
            ->orderByDesc('filing_year')
            ->orderBy('due_date')
            ->get();

        return view('nexcore_client_manager::cipc.index', compact('client', 'returns'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $returnTypes = NexcoreSystemCipcReturnType::where('is_active', true)->orderBy('name')->get();
        $statuses = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::cipc.form', compact('client', 'returnTypes', 'statuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'return_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'filing_year' => 'required|string|max:10',
            'due_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'approval_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:50',
            'amount_due' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        NexcoreClientCipcReturn::create(array_merge(
            $request->only([
                'return_type_id', 'status_id', 'filing_year',
                'due_date', 'submission_date', 'approval_date',
                'reference_number', 'amount_due', 'amount_paid', 'notes',
            ]),
            ['client_id' => $clientId, 'is_active' => true, 'created_by' => auth()->id(), 'updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.cipc', $clientId)
            ->with('success', 'CIPC return added successfully.');
    }

    public function edit($clientId, $returnId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $cipcReturn = NexcoreClientCipcReturn::where('client_id', $clientId)->findOrFail($returnId);
        $returnTypes = NexcoreSystemCipcReturnType::where('is_active', true)->orderBy('name')->get();
        $statuses = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::cipc.form', compact('client', 'cipcReturn', 'returnTypes', 'statuses'));
    }

    public function update(Request $request, $clientId, $returnId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $cipcReturn = NexcoreClientCipcReturn::where('client_id', $clientId)->findOrFail($returnId);

        $request->validate([
            'return_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'filing_year' => 'required|string|max:10',
            'due_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'approval_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:50',
            'amount_due' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cipcReturn->update(array_merge(
            $request->only([
                'return_type_id', 'status_id', 'filing_year',
                'due_date', 'submission_date', 'approval_date',
                'reference_number', 'amount_due', 'amount_paid', 'notes',
            ]),
            ['updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.cipc', $clientId)
            ->with('success', 'CIPC return updated successfully.');
    }

    public function destroy($clientId, $returnId)
    {
        $cipcReturn = NexcoreClientCipcReturn::where('client_id', $clientId)->findOrFail($returnId);
        $cipcReturn->delete();

        return redirect()->route('nexcore.clients.show.cipc', $clientId)
            ->with('success', 'CIPC return deleted successfully.');
    }
}
