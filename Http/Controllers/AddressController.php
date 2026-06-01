<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\CIMS_PMPRO\Models\PmproAddressMain;
use Modules\CIMS_PMPRO\Models\PmproAddressLink;
use Modules\CIMS_PMPRO\Models\PmproAddressSecondary;
use Modules\CIMS_PMPRO\Models\PmproAddressType;
use Modules\CIMS_PMPRO\Models\PmproProvince;

class AddressController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $addressLinks = PmproAddressLink::where('linkable_type', NexcoreClient::class)
            ->where('linkable_id', $clientId)
            ->with(['address.province', 'address.suburb', 'addressType'])
            ->orderByDesc('is_primary')
            ->orderBy('address_label')
            ->get();

        return view('nexcore_client_manager::addresses.index', compact('client', 'addressLinks'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $addressTypes = PmproAddressType::where('is_active', true)->orderBy('name')->get();
        $provinces = PmproProvince::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::addresses.form', compact('client', 'addressTypes', 'provinces'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'address_type_id' => 'required|integer',
            'address_label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($request->filled('existing_address_id')) {
            $address = PmproAddressMain::findOrFail($request->existing_address_id);
        } else {
            $request->validate([
                'street_number' => 'required|string|max:20',
                'street_name' => 'required|string|max:255',
                'city' => 'required|string|max:200',
                'postal_code' => 'required|string|max:10',
                'province_id' => 'required|integer',
                'unit_number' => 'nullable|string|max:20',
                'complex_name' => 'nullable|string|max:200',
                'suburb_id' => 'nullable|integer',
                'municipality_id' => 'nullable|integer',
                'ward_id' => 'nullable|integer',
                'address_category' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'google_formatted_address' => 'nullable|string',
            ]);

            $address = PmproAddressMain::create([
                'unit_number' => $request->unit_number,
                'complex_name' => $request->complex_name,
                'street_number' => $request->street_number,
                'street_name' => $request->street_name,
                'suburb_id' => $request->suburb_id ?: null,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'province_id' => $request->province_id,
                'municipality_id' => $request->municipality_id ?: null,
                'ward_id' => $request->ward_id ?: null,
                'country' => $request->country ?? 'ZA',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'google_formatted_address' => $request->google_formatted_address,
                'address_category' => $request->address_category,
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $secondaryFields = array_filter($request->only([
                'floor_level', 'building_name', 'estate_name', 'section_number',
                'farm_name', 'farm_number', 'stand_number',
                'erf_number', 'sg_code', 'municipal_account_number',
                'plus_code', 'what3words', 'google_place_id', 'map_url', 'address_source',
            ]));
            if (!empty($secondaryFields)) {
                $secondaryFields['address_id'] = $address->id;
                PmproAddressSecondary::create($secondaryFields);
            }
        }

        if ($request->boolean('is_primary')) {
            PmproAddressLink::where('linkable_type', NexcoreClient::class)
                ->where('linkable_id', $clientId)
                ->update(['is_primary' => false]);
        }

        PmproAddressLink::create([
            'address_id' => $address->id,
            'linkable_type' => NexcoreClient::class,
            'linkable_id' => $clientId,
            'address_type_id' => $request->address_type_id,
            'address_label' => $request->address_label,
            'notes' => $request->notes,
            'is_primary' => $request->boolean('is_primary'),
            'is_active' => true,
        ]);

        return redirect()->to(route('nexcore.clients.show.dashboard', $clientId) . '?tab=addresses')
            ->with('success', 'Address added successfully.');
    }

    public function edit($clientId, $linkId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $link = PmproAddressLink::where('linkable_type', NexcoreClient::class)
            ->where('linkable_id', $clientId)
            ->with(['address.province', 'address.suburb', 'address.secondary'])
            ->findOrFail($linkId);

        $addressTypes = PmproAddressType::where('is_active', true)->orderBy('name')->get();
        $provinces = PmproProvince::where('is_active', true)->orderBy('name')->get();

        return view('nexcore_client_manager::addresses.form', compact('client', 'link', 'addressTypes', 'provinces'));
    }

    public function update(Request $request, $clientId, $linkId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $link = PmproAddressLink::where('linkable_type', NexcoreClient::class)
            ->where('linkable_id', $clientId)
            ->findOrFail($linkId);

        $request->validate([
            'address_type_id' => 'required|integer',
            'address_label' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'street_number' => 'required|string|max:20',
            'street_name' => 'required|string|max:255',
            'city' => 'required|string|max:200',
            'postal_code' => 'required|string|max:10',
            'province_id' => 'required|integer',
            'unit_number' => 'nullable|string|max:20',
            'complex_name' => 'nullable|string|max:200',
            'suburb_id' => 'nullable|integer',
            'municipality_id' => 'nullable|integer',
            'ward_id' => 'nullable|integer',
            'address_category' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_formatted_address' => 'nullable|string',
        ]);

        $link->address->update([
            'unit_number' => $request->unit_number,
            'complex_name' => $request->complex_name,
            'street_number' => $request->street_number,
            'street_name' => $request->street_name,
            'suburb_id' => $request->suburb_id ?: null,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'province_id' => $request->province_id,
            'municipality_id' => $request->municipality_id ?: null,
            'ward_id' => $request->ward_id ?: null,
            'country' => $request->country ?? 'ZA',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'google_formatted_address' => $request->google_formatted_address,
            'address_category' => $request->address_category,
            'updated_by' => auth()->id(),
        ]);

        $secondaryFields = array_filter($request->only([
            'floor_level', 'building_name', 'estate_name', 'section_number',
            'farm_name', 'farm_number', 'stand_number',
            'erf_number', 'sg_code', 'municipal_account_number',
            'plus_code', 'what3words', 'google_place_id', 'map_url', 'address_source',
        ]));
        if (!empty($secondaryFields)) {
            PmproAddressSecondary::updateOrCreate(
                ['address_id' => $link->address_id],
                $secondaryFields
            );
        }

        if ($request->boolean('is_primary')) {
            PmproAddressLink::where('linkable_type', NexcoreClient::class)
                ->where('linkable_id', $clientId)
                ->where('id', '!=', $linkId)
                ->update(['is_primary' => false]);
        }

        $link->update([
            'address_type_id' => $request->address_type_id,
            'address_label' => $request->address_label,
            'notes' => $request->notes,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return redirect()->to(route('nexcore.clients.show.dashboard', $clientId) . '?tab=addresses')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy($clientId, $linkId)
    {
        $link = PmproAddressLink::where('linkable_type', NexcoreClient::class)
            ->where('linkable_id', $clientId)
            ->findOrFail($linkId);

        $dashUrl = route('nexcore.clients.show.dashboard', $clientId) . '?tab=addresses';

        $totalLinks = PmproAddressLink::where('linkable_type', NexcoreClient::class)
            ->where('linkable_id', $clientId)
            ->count();

        if ($totalLinks <= 1) {
            return redirect()->to($dashUrl)
                ->with('error', 'Cannot unlink the only address. Please add a new address before unlinking this one.');
        }

        $wasPrimary = $link->is_primary;
        $link->delete();

        if ($wasPrimary) {
            $nextLink = PmproAddressLink::where('linkable_type', NexcoreClient::class)
                ->where('linkable_id', $clientId)
                ->where('is_active', true)
                ->first();

            if (!$nextLink) {
                $nextLink = PmproAddressLink::where('linkable_type', NexcoreClient::class)
                    ->where('linkable_id', $clientId)
                    ->first();
            }

            if ($nextLink) {
                $nextLink->update(['is_primary' => true]);
                return redirect()->to($dashUrl)
                    ->with('success', 'Address unlinked. Primary switched to "' . ($nextLink->address_label ?? 'Address') . '".');
            }
        }

        return redirect()->to($dashUrl)
            ->with('success', 'Address unlinked successfully.');
    }

    public function toggle($clientId, $linkId)
    {
        $link = PmproAddressLink::where('linkable_type', NexcoreClient::class)
            ->where('linkable_id', $clientId)
            ->findOrFail($linkId);

        $dashUrl = route('nexcore.clients.show.dashboard', $clientId) . '?tab=addresses';

        if ($link->is_active && $link->is_primary) {
            $otherActive = PmproAddressLink::where('linkable_type', NexcoreClient::class)
                ->where('linkable_id', $clientId)
                ->where('id', '!=', $linkId)
                ->where('is_active', true)
                ->first();

            if (!$otherActive) {
                return redirect()->to($dashUrl)
                    ->with('error', 'This is the only address — it cannot be deactivated.');
            }

            $link->update(['is_active' => false, 'is_primary' => false]);
            $otherActive->update(['is_primary' => true]);

            return redirect()->to($dashUrl)
                ->with('success', 'Address deactivated. Primary switched to "' . ($otherActive->address_label ?? 'Address') . '".');
        }

        $link->update(['is_active' => !$link->is_active]);

        return redirect()->to($dashUrl)->with('success', 'Address status updated.');
    }

    public function searchRegistry(Request $request)
    {
        $q = $request->get('q', '');

        $query = PmproAddressMain::where('is_active', true);

        if (strlen($q) >= 2) {
            $query->where(function ($qb) use ($q) {
                $qb->where('street_name', 'like', "%{$q}%")
                    ->orWhere('street_number', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('complex_name', 'like', "%{$q}%")
                    ->orWhere('postal_code', 'like', "%{$q}%");
            });
        }

        $addresses = $query->with(['province', 'suburb'])
            ->orderBy('street_name')
            ->limit(50)
            ->get()
            ->map(function ($addr) {
                $line1 = trim(($addr->unit_number ? 'Unit ' . $addr->unit_number . ', ' : '') . ($addr->complex_name ? $addr->complex_name . ', ' : '') . $addr->street_number . ' ' . $addr->street_name);
                $line2 = trim(($addr->suburb ? $addr->suburb->name . ', ' : '') . $addr->city . ', ' . ($addr->province ? $addr->province->name : '') . ', ' . $addr->postal_code);
                return [
                    'id' => $addr->id,
                    'line1' => $line1,
                    'line2' => $line2,
                    'category' => $addr->address_category,
                ];
            });

        return response()->json($addresses);
    }
}
