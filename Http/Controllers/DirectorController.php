<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientDirector;
use Modules\NexcoreClientManager\Models\NexcoreSystemDirectorType;
use Modules\NexcoreClientManager\Models\NexcoreSystemTitle;

class DirectorController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $directors = NexcoreClientDirector::where('client_id', $clientId)
            ->with(['directorType', 'title'])
            ->orderBy('last_name')
            ->get();

        $totalShares = $directors->sum('shareholding_percentage');

        return view('nexcore_client_manager::directors.index', compact('client', 'directors', 'totalShares'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $directorTypes = NexcoreSystemDirectorType::where('is_active', true)->orderBy('name')->get();
        $titles = NexcoreSystemTitle::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::directors.form', compact('client', 'directorTypes', 'titles'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'director_type_id' => 'required|integer',
            'title_id' => 'nullable|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'id_number' => 'nullable|string|max:20',
            'passport_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'office_number' => 'nullable|string|max:20',
            'residential_address' => 'nullable|string',
            'appointment_date' => 'nullable|date',
            'resignation_date' => 'nullable|date',
            'shareholding_percentage' => 'nullable|numeric|min:0|max:100',
            'director_photo' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'notes' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->hasFile('director_photo')) {
            $photoPath = $this->uploadPhoto($request->file('director_photo'), $clientId);
        }

        NexcoreClientDirector::create([
            'client_id' => $clientId,
            'director_type_id' => $request->director_type_id,
            'title_id' => $request->title_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
            'passport_number' => $request->passport_number,
            'nationality' => $request->nationality ?? 'South African',
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'office_number' => $request->office_number,
            'residential_address' => $request->residential_address,
            'appointment_date' => $request->appointment_date,
            'resignation_date' => $request->resignation_date,
            'shareholding_percentage' => $request->shareholding_percentage,
            'director_photo' => $photoPath,
            'is_active' => true,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('nexcore.clients.show.directors', $clientId)
            ->with('success', 'Director added successfully.');
    }

    public function edit($clientId, $directorId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $director = NexcoreClientDirector::where('client_id', $clientId)->findOrFail($directorId);
        $directorTypes = NexcoreSystemDirectorType::where('is_active', true)->orderBy('name')->get();
        $titles = NexcoreSystemTitle::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::directors.form', compact('client', 'director', 'directorTypes', 'titles'));
    }

    public function update(Request $request, $clientId, $directorId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $director = NexcoreClientDirector::where('client_id', $clientId)->findOrFail($directorId);

        $request->validate([
            'director_type_id' => 'required|integer',
            'title_id' => 'nullable|integer',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'id_number' => 'nullable|string|max:20',
            'passport_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'office_number' => 'nullable|string|max:20',
            'residential_address' => 'nullable|string',
            'appointment_date' => 'nullable|date',
            'resignation_date' => 'nullable|date',
            'shareholding_percentage' => 'nullable|numeric|min:0|max:100',
            'director_photo' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only([
            'director_type_id', 'title_id', 'first_name', 'last_name',
            'id_number', 'passport_number', 'nationality', 'date_of_birth',
            'email', 'mobile_number', 'office_number', 'residential_address',
            'appointment_date', 'resignation_date', 'shareholding_percentage', 'notes',
        ]);
        $data['updated_by'] = auth()->id();

        if ($request->boolean('remove_photo')) {
            if ($director->director_photo && file_exists(base_path('../' . $director->director_photo))) {
                @unlink(base_path('../' . $director->director_photo));
            }
            $data['director_photo'] = null;
        }

        if ($request->hasFile('director_photo')) {
            if ($director->director_photo && file_exists(base_path('../' . $director->director_photo))) {
                @unlink(base_path('../' . $director->director_photo));
            }
            $data['director_photo'] = $this->uploadPhoto($request->file('director_photo'), $clientId);
        }

        $director->update($data);

        return redirect()->route('nexcore.clients.show.directors', $clientId)
            ->with('success', 'Director updated successfully.');
    }

    public function destroy($clientId, $directorId)
    {
        $director = NexcoreClientDirector::where('client_id', $clientId)->findOrFail($directorId);
        $director->delete();

        return redirect()->route('nexcore.clients.show.directors', $clientId)
            ->with('success', 'Director deleted successfully.');
    }

    public function toggle($clientId, $directorId)
    {
        $director = NexcoreClientDirector::where('client_id', $clientId)->findOrFail($directorId);
        $director->update(['is_active' => !$director->is_active]);

        return redirect()->back()->with('success', 'Director status updated.');
    }

    private function uploadPhoto($file, $clientId)
    {
        $dir = base_path('../uploads/persons');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'director_' . $clientId . '_' . time() . '.' . $ext;
        $file->move($dir, $filename);
        return 'uploads/persons/' . $filename;
    }
}
