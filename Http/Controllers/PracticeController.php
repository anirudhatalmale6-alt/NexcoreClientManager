<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PracticeController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('nexcore_practices')->whereNull('deleted_at');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('practice_name', 'like', $s)
                  ->orWhere('trading_name', 'like', $s)
                  ->orWhere('practice_number', 'like', $s)
                  ->orWhere('registration_number', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('principal_name', 'like', $s);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        if ($request->filled('body')) {
            $query->where('professional_body', $request->body);
        }

        $total = (clone $query)->count();
        $active = (clone $query)->where('is_active', 1)->count();
        $inactive = $total - $active;

        $practices = $query->orderBy('practice_name')->paginate(25)->appends($request->query());

        $bodies = DB::table('nexcore_practices')
            ->whereNull('deleted_at')
            ->whereNotNull('professional_body')
            ->distinct()
            ->pluck('professional_body');

        return view('nexcore_client_manager::practices.index', compact('practices', 'total', 'active', 'inactive', 'bodies'));
    }

    public function create()
    {
        $practice = null;
        return view('nexcore_client_manager::practices.form', compact('practice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'practice_name' => 'required|string|max:255',
        ]);

        $data = $request->only([
            'practice_name', 'trading_name', 'practice_number', 'registration_number',
            'tax_number', 'vat_number', 'is_vat_registered',
            'professional_body', 'professional_body_number', 'bbbee_level', 'bbbee_certificate_expiry',
            'phone_number', 'mobile_number', 'fax_number', 'email', 'website',
            'physical_address_line1', 'physical_address_line2', 'physical_city', 'physical_province', 'physical_postal_code', 'physical_country',
            'postal_address_line1', 'postal_address_line2', 'postal_city', 'postal_province', 'postal_postal_code', 'postal_country',
            'bank_name', 'bank_branch', 'bank_branch_code', 'bank_account_number', 'bank_account_type',
            'principal_name', 'principal_designation', 'principal_email', 'principal_mobile',
        ]);

        $data['is_vat_registered'] = $request->input('is_vat_registered', 0) ? 1 : 0;
        $data['is_active'] = 1;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        if ($request->hasFile('practice_logo')) {
            $path = $request->file('practice_logo')->store('practice_logos', 'public');
            $data['practice_logo'] = 'storage/' . $path;
        }

        if ($request->hasFile('watermark_logo')) {
            $path = $request->file('watermark_logo')->store('practice_logos', 'public');
            $data['watermark_logo'] = 'storage/' . $path;
        }

        DB::table('nexcore_practices')->insert($data);

        return redirect()->route('nexcore.clients.practices.index')->with('success', 'Practice created successfully.');
    }

    public function edit($id)
    {
        $practice = DB::table('nexcore_practices')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$practice) {
            return redirect()->route('nexcore.clients.practices.index')->with('error', 'Practice not found.');
        }

        return view('nexcore_client_manager::practices.form', compact('practice'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'practice_name' => 'required|string|max:255',
        ]);

        $practice = DB::table('nexcore_practices')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$practice) {
            return redirect()->route('nexcore.clients.practices.index')->with('error', 'Practice not found.');
        }

        $data = $request->only([
            'practice_name', 'trading_name', 'practice_number', 'registration_number',
            'tax_number', 'vat_number', 'is_vat_registered',
            'professional_body', 'professional_body_number', 'bbbee_level', 'bbbee_certificate_expiry',
            'phone_number', 'mobile_number', 'fax_number', 'email', 'website',
            'physical_address_line1', 'physical_address_line2', 'physical_city', 'physical_province', 'physical_postal_code', 'physical_country',
            'postal_address_line1', 'postal_address_line2', 'postal_city', 'postal_province', 'postal_postal_code', 'postal_country',
            'bank_name', 'bank_branch', 'bank_branch_code', 'bank_account_number', 'bank_account_type',
            'principal_name', 'principal_designation', 'principal_email', 'principal_mobile',
        ]);

        $data['is_vat_registered'] = $request->input('is_vat_registered', 0) ? 1 : 0;
        $data['updated_at'] = now();

        if ($request->hasFile('practice_logo')) {
            $path = $request->file('practice_logo')->store('practice_logos', 'public');
            $data['practice_logo'] = 'storage/' . $path;
        }

        if ($request->hasFile('watermark_logo')) {
            $path = $request->file('watermark_logo')->store('practice_logos', 'public');
            $data['watermark_logo'] = 'storage/' . $path;
        }

        DB::table('nexcore_practices')->where('id', $id)->update($data);

        return redirect()->route('nexcore.clients.practices.index')->with('success', 'Practice updated successfully.');
    }

    public function toggle($id)
    {
        $practice = DB::table('nexcore_practices')->where('id', $id)->first();
        if ($practice) {
            DB::table('nexcore_practices')->where('id', $id)->update([
                'is_active' => $practice->is_active ? 0 : 1,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('nexcore.clients.practices.index')->with('success', 'Practice status updated.');
    }

    public function destroy($id)
    {
        DB::table('nexcore_practices')->where('id', $id)->update([
            'deleted_at' => now(),
        ]);

        return redirect()->route('nexcore.clients.practices.index')->with('success', 'Practice deleted.');
    }
}
