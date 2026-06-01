<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientAlert;

class AlertController extends Controller
{
    protected array $severities = [
        'info'     => 'Information',
        'warning'  => 'Warning',
        'critical' => 'Critical',
    ];

    protected array $alertTypes = [
        'manual' => 'Manual',
        'auto'   => 'Auto-Generated',
    ];

    protected array $modules = [
        'general'    => 'General',
        'sars'       => 'SARS Returns',
        'cipc'       => 'CIPC Returns',
        'financials' => 'Financials',
        'documents'  => 'Documents',
        'tasks'      => 'Tasks',
        'meetings'   => 'Meetings',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $alerts = NexcoreClientAlert::where('client_id', $clientId)
            ->orderByRaw('is_read ASC')
            ->orderByRaw("FIELD(severity, 'critical', 'warning', 'info') ASC")
            ->orderByRaw('due_date IS NULL ASC')
            ->orderBy('due_date', 'asc')
            ->get();

        $severities = $this->severities;
        $alertTypes = $this->alertTypes;
        $modules    = $this->modules;

        return view('nexcore_client_manager::alerts.index', compact('client', 'alerts', 'severities', 'alertTypes', 'modules'));
    }

    public function create($clientId)
    {
        $client     = NexcoreClient::findOrFail($clientId);
        $severities = $this->severities;
        $alertTypes = $this->alertTypes;
        $modules    = $this->modules;

        return view('nexcore_client_manager::alerts.form', compact('client', 'severities', 'alertTypes', 'modules'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'severity'       => 'required|in:info,warning,critical',
            'alert_type'     => 'required|in:manual,auto',
            'related_module' => 'nullable|in:general,sars,cipc,financials,documents,tasks,meetings',
            'related_id'     => 'nullable|integer',
            'due_date'       => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        NexcoreClientAlert::create(array_merge(
            $request->only([
                'title', 'description', 'severity', 'alert_type',
                'related_module', 'related_id', 'due_date', 'notes',
            ]),
            [
                'client_id'    => $clientId,
                'is_read'      => false,
                'is_dismissed' => false,
                'is_active'    => true,
                'created_by'   => auth()->id(),
                'updated_by'   => auth()->id(),
            ]
        ));

        return redirect()->route('nexcore.clients.show.alerts', $clientId)
            ->with('success', 'Alert added successfully.');
    }

    public function edit($clientId, $alertId)
    {
        $client     = NexcoreClient::findOrFail($clientId);
        $alert      = NexcoreClientAlert::where('client_id', $clientId)->findOrFail($alertId);
        $severities = $this->severities;
        $alertTypes = $this->alertTypes;
        $modules    = $this->modules;

        return view('nexcore_client_manager::alerts.form', compact('client', 'alert', 'severities', 'alertTypes', 'modules'));
    }

    public function update(Request $request, $clientId, $alertId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $alert  = NexcoreClientAlert::where('client_id', $clientId)->findOrFail($alertId);

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'severity'       => 'required|in:info,warning,critical',
            'alert_type'     => 'required|in:manual,auto',
            'related_module' => 'nullable|in:general,sars,cipc,financials,documents,tasks,meetings',
            'related_id'     => 'nullable|integer',
            'due_date'       => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'description', 'severity', 'alert_type',
            'related_module', 'related_id', 'due_date', 'notes',
        ]);

        $data['updated_by'] = auth()->id();

        $alert->update($data);

        return redirect()->route('nexcore.clients.show.alerts', $clientId)
            ->with('success', 'Alert updated successfully.');
    }

    public function destroy($clientId, $alertId)
    {
        $alert = NexcoreClientAlert::where('client_id', $clientId)->findOrFail($alertId);
        $alert->delete();

        return redirect()->route('nexcore.clients.show.alerts', $clientId)
            ->with('success', 'Alert deleted successfully.');
    }

    public function dismiss($clientId, $alertId)
    {
        $alert = NexcoreClientAlert::where('client_id', $clientId)->findOrFail($alertId);
        $alert->update([
            'is_dismissed' => true,
            'updated_by'   => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Alert dismissed.');
    }

    public function toggleRead($clientId, $alertId)
    {
        $alert = NexcoreClientAlert::where('client_id', $clientId)->findOrFail($alertId);
        $alert->update([
            'is_read'    => !$alert->is_read,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', $alert->is_read ? 'Alert marked as unread.' : 'Alert marked as read.');
    }
}
