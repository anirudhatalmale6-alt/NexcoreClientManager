<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientSarsReturn;
use Modules\NexcoreClientManager\Models\NexcoreSystemSarsReturnType;
use Modules\NexcoreClientManager\Models\NexcoreSystemReturnStatus;

class SarsController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $returns = NexcoreClientSarsReturn::where('client_id', $clientId)
            ->with(['returnType', 'status'])
            ->orderByDesc('tax_year')
            ->orderBy('due_date')
            ->get();

        $stats = [
            'total' => $returns->count(),
            'due' => $returns->filter(fn($r) => $r->status && in_array($r->status->name, ['Due', 'Overdue']))->count(),
            'submitted' => $returns->filter(fn($r) => $r->status && $r->status->name === 'Submitted')->count(),
            'overdue' => $returns->filter(fn($r) => $r->status && $r->status->name === 'Overdue')->count(),
        ];

        return view('nexcore_client_manager::sars.index', compact('client', 'returns', 'stats'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $returnTypes = NexcoreSystemSarsReturnType::where('is_active', true)->orderBy('name')->get();
        $statuses = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::sars.form', compact('client', 'returnTypes', 'statuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'return_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'tax_year' => 'required|string|max:10',
            'tax_period' => 'nullable|string|max:20',
            'due_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'assessment_date' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:50',
            'amount_due' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'interest_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        NexcoreClientSarsReturn::create(array_merge(
            $request->only([
                'return_type_id', 'status_id', 'tax_year', 'tax_period',
                'due_date', 'submission_date', 'assessment_date',
                'payment_due_date', 'payment_date', 'reference_number',
                'amount_due', 'amount_paid', 'penalty_amount', 'interest_amount', 'notes',
            ]),
            ['client_id' => $clientId, 'is_active' => true, 'created_by' => auth()->id(), 'updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.sars', $clientId)
            ->with('success', 'SARS return added successfully.');
    }

    public function edit($clientId, $returnId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $sarsReturn = NexcoreClientSarsReturn::where('client_id', $clientId)->findOrFail($returnId);
        $returnTypes = NexcoreSystemSarsReturnType::where('is_active', true)->orderBy('name')->get();
        $statuses = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::sars.form', compact('client', 'sarsReturn', 'returnTypes', 'statuses'));
    }

    public function update(Request $request, $clientId, $returnId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $sarsReturn = NexcoreClientSarsReturn::where('client_id', $clientId)->findOrFail($returnId);

        $request->validate([
            'return_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'tax_year' => 'required|string|max:10',
            'tax_period' => 'nullable|string|max:20',
            'due_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'assessment_date' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:50',
            'amount_due' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'interest_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $sarsReturn->update(array_merge(
            $request->only([
                'return_type_id', 'status_id', 'tax_year', 'tax_period',
                'due_date', 'submission_date', 'assessment_date',
                'payment_due_date', 'payment_date', 'reference_number',
                'amount_due', 'amount_paid', 'penalty_amount', 'interest_amount', 'notes',
            ]),
            ['updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.sars', $clientId)
            ->with('success', 'SARS return updated successfully.');
    }

    public function destroy($clientId, $returnId)
    {
        $sarsReturn = NexcoreClientSarsReturn::where('client_id', $clientId)->findOrFail($returnId);
        $sarsReturn->delete();

        return redirect()->route('nexcore.clients.show.sars', $clientId)
            ->with('success', 'SARS return deleted successfully.');
    }
}
