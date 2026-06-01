<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientMeeting;

class MeetingController extends Controller
{
    protected array $meetingTypes = [
        'in_person'  => 'In Person',
        'virtual'    => 'Virtual / Online',
        'phone'      => 'Phone Call',
        'site_visit' => 'Site Visit',
    ];

    protected array $meetingStatuses = [
        'scheduled'  => 'Scheduled',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
        'postponed'  => 'Postponed',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $meetings = NexcoreClientMeeting::where('client_id', $clientId)
            ->orderByDesc('meeting_date')
            ->orderByDesc('meeting_time')
            ->get();

        $meetingTypes    = $this->meetingTypes;
        $meetingStatuses = $this->meetingStatuses;

        return view('nexcore_client_manager::meetings.index', compact('client', 'meetings', 'meetingTypes', 'meetingStatuses'));
    }

    public function create($clientId)
    {
        $client          = NexcoreClient::findOrFail($clientId);
        $meetingTypes    = $this->meetingTypes;
        $meetingStatuses = $this->meetingStatuses;

        return view('nexcore_client_manager::meetings.form', compact('client', 'meetingTypes', 'meetingStatuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'title'            => 'required|string|max:255',
            'meeting_type'     => 'required|in:in_person,virtual,phone,site_visit',
            'meeting_status'   => 'required|in:scheduled,completed,cancelled,postponed',
            'meeting_date'     => 'required|date',
            'meeting_time'     => 'nullable|string|max:10',
            'duration_minutes' => 'nullable|integer|min:1',
            'location'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'attendees'        => 'nullable|string',
            'outcome'          => 'nullable|string',
            'follow_up_date'   => 'nullable|date',
            'follow_up_notes'  => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        NexcoreClientMeeting::create(array_merge(
            $request->only([
                'title', 'meeting_type', 'meeting_status',
                'meeting_date', 'meeting_time', 'duration_minutes', 'location',
                'description', 'attendees', 'outcome',
                'follow_up_date', 'follow_up_notes', 'notes',
            ]),
            ['client_id' => $clientId, 'is_active' => true, 'created_by' => auth()->id(), 'updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.meetings', $clientId)
            ->with('success', 'Meeting added successfully.');
    }

    public function edit($clientId, $meetingId)
    {
        $client          = NexcoreClient::findOrFail($clientId);
        $meeting         = NexcoreClientMeeting::where('client_id', $clientId)->findOrFail($meetingId);
        $meetingTypes    = $this->meetingTypes;
        $meetingStatuses = $this->meetingStatuses;

        return view('nexcore_client_manager::meetings.form', compact('client', 'meeting', 'meetingTypes', 'meetingStatuses'));
    }

    public function update(Request $request, $clientId, $meetingId)
    {
        $client  = NexcoreClient::findOrFail($clientId);
        $meeting = NexcoreClientMeeting::where('client_id', $clientId)->findOrFail($meetingId);

        $request->validate([
            'title'            => 'required|string|max:255',
            'meeting_type'     => 'required|in:in_person,virtual,phone,site_visit',
            'meeting_status'   => 'required|in:scheduled,completed,cancelled,postponed',
            'meeting_date'     => 'required|date',
            'meeting_time'     => 'nullable|string|max:10',
            'duration_minutes' => 'nullable|integer|min:1',
            'location'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'attendees'        => 'nullable|string',
            'outcome'          => 'nullable|string',
            'follow_up_date'   => 'nullable|date',
            'follow_up_notes'  => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $meeting->update(array_merge(
            $request->only([
                'title', 'meeting_type', 'meeting_status',
                'meeting_date', 'meeting_time', 'duration_minutes', 'location',
                'description', 'attendees', 'outcome',
                'follow_up_date', 'follow_up_notes', 'notes',
            ]),
            ['updated_by' => auth()->id()]
        ));

        return redirect()->route('nexcore.clients.show.meetings', $clientId)
            ->with('success', 'Meeting updated successfully.');
    }

    public function destroy($clientId, $meetingId)
    {
        $meeting = NexcoreClientMeeting::where('client_id', $clientId)->findOrFail($meetingId);
        $meeting->delete();

        return redirect()->route('nexcore.clients.show.meetings', $clientId)
            ->with('success', 'Meeting deleted successfully.');
    }
}
