<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientContact;
use Modules\NexcoreClientManager\Models\NexcoreSystemContactType;
use Modules\NexcoreClientManager\Models\NexcoreSystemTitle;

class ContactController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $contacts = NexcoreClientContact::where('client_id', $clientId)
            ->with(['contactType', 'title'])
            ->orderByDesc('is_primary')
            ->orderBy('last_name')
            ->get();

        return view('nexcore_client_manager::contacts.index', compact('client', 'contacts'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $contactTypes = NexcoreSystemContactType::where('is_active', true)->orderBy('name')->get();
        $titles = NexcoreSystemTitle::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::contacts.form', compact('client', 'contactTypes', 'titles'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'contact_type_id' => 'required|integer',
            'title_id' => 'nullable|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'id_number' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'office_number' => 'nullable|string|max:20',
            'fax_number' => 'nullable|string|max:20',
            'contact_photo' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($request->boolean('is_primary')) {
            NexcoreClientContact::where('client_id', $clientId)->update(['is_primary' => false]);
        }

        $photoPath = null;
        if ($request->hasFile('contact_photo')) {
            $photoPath = $this->uploadPhoto($request->file('contact_photo'), $clientId);
        }

        NexcoreClientContact::create([
            'client_id' => $clientId,
            'contact_type_id' => $request->contact_type_id,
            'title_id' => $request->title_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
            'designation' => $request->designation,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'office_number' => $request->office_number,
            'fax_number' => $request->fax_number,
            'contact_photo' => $photoPath,
            'is_primary' => $request->boolean('is_primary'),
            'is_active' => true,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('nexcore.clients.show.contacts', $clientId)
            ->with('success', 'Contact added successfully.');
    }

    public function edit($clientId, $contactId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $contact = NexcoreClientContact::where('client_id', $clientId)->findOrFail($contactId);
        $contactTypes = NexcoreSystemContactType::where('is_active', true)->orderBy('name')->get();
        $titles = NexcoreSystemTitle::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::contacts.form', compact('client', 'contact', 'contactTypes', 'titles'));
    }

    public function update(Request $request, $clientId, $contactId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $contact = NexcoreClientContact::where('client_id', $clientId)->findOrFail($contactId);

        $request->validate([
            'contact_type_id' => 'required|integer',
            'title_id' => 'nullable|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'id_number' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'office_number' => 'nullable|string|max:20',
            'fax_number' => 'nullable|string|max:20',
            'contact_photo' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($request->boolean('is_primary')) {
            NexcoreClientContact::where('client_id', $clientId)
                ->where('id', '!=', $contactId)
                ->update(['is_primary' => false]);
        }

        $data = $request->only([
            'contact_type_id', 'title_id', 'first_name', 'last_name',
            'id_number', 'designation', 'email', 'mobile_number',
            'office_number', 'fax_number', 'notes',
        ]);
        $data['is_primary'] = $request->boolean('is_primary');
        $data['updated_by'] = auth()->id();

        if ($request->boolean('remove_photo')) {
            if ($contact->contact_photo && file_exists(base_path('../' . $contact->contact_photo))) {
                @unlink(base_path('../' . $contact->contact_photo));
            }
            $data['contact_photo'] = null;
        }

        if ($request->hasFile('contact_photo')) {
            if ($contact->contact_photo && file_exists(base_path('../' . $contact->contact_photo))) {
                @unlink(base_path('../' . $contact->contact_photo));
            }
            $data['contact_photo'] = $this->uploadPhoto($request->file('contact_photo'), $clientId);
        }

        $contact->update($data);

        return redirect()->route('nexcore.clients.show.contacts', $clientId)
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy($clientId, $contactId)
    {
        $contact = NexcoreClientContact::where('client_id', $clientId)->findOrFail($contactId);
        $contact->delete();

        return redirect()->route('nexcore.clients.show.contacts', $clientId)
            ->with('success', 'Contact deleted successfully.');
    }

    public function toggle($clientId, $contactId)
    {
        $contact = NexcoreClientContact::where('client_id', $clientId)->findOrFail($contactId);
        $contact->update(['is_active' => !$contact->is_active]);

        return redirect()->back()->with('success', 'Contact status updated.');
    }

    private function uploadPhoto($file, $clientId)
    {
        $dir = base_path('../uploads/persons');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'contact_' . $clientId . '_' . time() . '.' . $ext;
        $file->move($dir, $filename);
        return 'uploads/persons/' . $filename;
    }
}
