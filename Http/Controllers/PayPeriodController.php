<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientPayPeriod;

class PayPeriodController extends Controller
{
    protected array $payFrequencies = [
        'monthly'     => 'Monthly',
        'weekly'      => 'Weekly',
        'fortnightly' => 'Fortnightly',
    ];

    protected array $statuses = [
        'draft'     => 'Draft',
        'processed' => 'Processed',
        'finalised' => 'Finalised',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $periods = NexcoreClientPayPeriod::where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->get();

        $payFrequencies = $this->payFrequencies;
        $statuses       = $this->statuses;

        return view('nexcore_client_manager::payroll.periods.index', compact('client', 'periods', 'payFrequencies', 'statuses'));
    }

    public function create($clientId)
    {
        $client         = NexcoreClient::findOrFail($clientId);
        $payFrequencies = $this->payFrequencies;
        $statuses       = $this->statuses;

        return view('nexcore_client_manager::payroll.periods.form', compact('client', 'payFrequencies', 'statuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'name'          => 'required|string|max:255',
            'pay_frequency' => 'required|in:monthly,weekly,fortnightly',
            'period_start'  => 'required|date',
            'period_end'    => 'required|date|after_or_equal:period_start',
            'payment_date'  => 'required|date',
            'status'        => 'required|in:draft,processed,finalised',
            'notes'         => 'nullable|string',
        ]);

        NexcoreClientPayPeriod::create(array_merge(
            $request->only(['name', 'pay_frequency', 'period_start', 'period_end', 'payment_date', 'status', 'notes']),
            [
                'client_id'           => $clientId,
                'total_gross'         => 0,
                'total_deductions'    => 0,
                'total_net'           => 0,
                'total_employer_cost' => 0,
                'is_active'           => true,
                'created_by'          => auth()->id(),
                'updated_by'          => auth()->id(),
            ]
        ));

        return redirect()->route('nexcore.clients.show.payroll.periods', $clientId)
            ->with('success', 'Pay period created successfully.');
    }

    public function edit($clientId, $periodId)
    {
        $client         = NexcoreClient::findOrFail($clientId);
        $period         = NexcoreClientPayPeriod::where('client_id', $clientId)->findOrFail($periodId);
        $payFrequencies = $this->payFrequencies;
        $statuses       = $this->statuses;

        return view('nexcore_client_manager::payroll.periods.form', compact('client', 'period', 'payFrequencies', 'statuses'));
    }

    public function update(Request $request, $clientId, $periodId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $period = NexcoreClientPayPeriod::where('client_id', $clientId)->findOrFail($periodId);

        $request->validate([
            'name'          => 'required|string|max:255',
            'pay_frequency' => 'required|in:monthly,weekly,fortnightly',
            'period_start'  => 'required|date',
            'period_end'    => 'required|date|after_or_equal:period_start',
            'payment_date'  => 'required|date',
            'status'        => 'required|in:draft,processed,finalised',
            'notes'         => 'nullable|string',
        ]);

        $data = $request->only(['name', 'pay_frequency', 'period_start', 'period_end', 'payment_date', 'status', 'notes']);
        $data['updated_by'] = auth()->id();

        $period->update($data);

        return redirect()->route('nexcore.clients.show.payroll.periods', $clientId)
            ->with('success', 'Pay period updated successfully.');
    }

    public function destroy($clientId, $periodId)
    {
        $period = NexcoreClientPayPeriod::where('client_id', $clientId)->findOrFail($periodId);
        $period->delete();

        return redirect()->route('nexcore.clients.show.payroll.periods', $clientId)
            ->with('success', 'Pay period deleted successfully.');
    }
}
