<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientAuditTrail;

class AuditController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $auditTrail = NexcoreClientAuditTrail::where('client_id', $clientId)
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();

        $modules = [
            'all'        => 'All Activity',
            'clients'    => 'Client',
            'addresses'  => 'Addresses',
            'contacts'   => 'Contacts',
            'banking'    => 'Banking',
            'directors'  => 'Directors',
            'sars'       => 'SARS',
            'cipc'       => 'CIPC',
            'financials' => 'Financials',
            'documents'  => 'Documents',
            'tasks'      => 'Tasks',
            'meetings'   => 'Meetings',
            'alerts'     => 'Alerts',
        ];

        return view('nexcore_client_manager::audit.index', compact('client', 'auditTrail', 'modules'));
    }
}
