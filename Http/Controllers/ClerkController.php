<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClerkController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('nexcore_clerks')->whereNull('deleted_at');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', $s)
                  ->orWhere('last_name', 'like', $s)
                  ->orWhere('known_as', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('employee_number', 'like', $s)
                  ->orWhere('designation', 'like', $s)
                  ->orWhere('job_title', 'like', $s);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('practice')) {
            $clerkIds = DB::table('nexcore_practice_clerks')
                ->where('practice_id', $request->practice)
                ->pluck('clerk_id');
            $query->whereIn('id', $clerkIds);
        }

        $total = (clone $query)->count();
        $active = (clone $query)->where('is_active', 1)->count();
        $inactive = $total - $active;

        $clerks = $query->orderBy('first_name')->paginate(25)->appends($request->query());

        $practices = DB::table('nexcore_practices')->whereNull('deleted_at')->where('is_active', 1)->orderBy('practice_name')->get();

        $practiceMap = array();
        $pcLinks = DB::table('nexcore_practice_clerks')->get();
        foreach ($pcLinks as $link) {
            if (!isset($practiceMap[$link->clerk_id])) {
                $practiceMap[$link->clerk_id] = array();
            }
            $practiceMap[$link->clerk_id][] = $link->practice_id;
        }

        $practiceNames = array();
        foreach ($practices as $p) {
            $practiceNames[$p->id] = $p->trading_name ? $p->trading_name : $p->practice_name;
        }

        return view('nexcore_client_manager::clerks.index', compact('clerks', 'total', 'active', 'inactive', 'practices', 'practiceMap', 'practiceNames'));
    }

    public function create()
    {
        $clerk = null;
        $linkedUser = null;
        $practices = DB::table('nexcore_practices')->whereNull('deleted_at')->where('is_active', 1)->orderBy('practice_name')->get();
        $clerkPractices = array();
        $clients = DB::table('nexcore_clients')->where('is_active', 1)->orderBy('company_name')->get();
        $clerkClients = array();
        return view('nexcore_client_manager::clerks.form', compact('clerk', 'practices', 'clerkPractices', 'linkedUser', 'clients', 'clerkClients'));
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ];

        if ($request->input('create_login')) {
            $rules['login_email'] = 'required|email';
            $rules['login_password'] = 'required|min:6|confirmed';
        }

        $request->validate($rules);

        if ($request->input('create_login') && $request->input('login_email')) {
            $exists = DB::table('users')->where('email', $request->input('login_email'))->first();
            if ($exists) {
                return back()->withInput()->withErrors(['login_email' => 'This email is already registered as a user.']);
            }
        }

        $data = $request->only([
            'first_name', 'last_name', 'known_as', 'id_number',
            'designation', 'job_title', 'employee_number',
            'email', 'phone', 'mobile', 'role',
            'date_joined', 'date_left',
        ]);

        $data['is_active'] = 1;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('clerk_photos', 'public');
            $data['profile_photo'] = 'storage/' . $path;
        }

        if ($request->input('create_login') && $request->input('login_email')) {
            $userId = $this->createUserAccount($request);
            $data['user_id'] = $userId;
        }

        $clerkId = DB::table('nexcore_clerks')->insertGetId($data);

        $practiceIds = $request->input('practices', array());
        $now = now();
        foreach ($practiceIds as $pid) {
            DB::table('nexcore_practice_clerks')->insert([
                'practice_id' => $pid,
                'clerk_id' => $clerkId,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $clientIds = $request->input('clients', array());
        foreach ($clientIds as $cid) {
            DB::table('nexcore_clerk_clients')->insert([
                'clerk_id' => $clerkId,
                'client_id' => $cid,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return redirect()->route('nexcore.clients.clerks.index')->with('success', 'Clerk created successfully' . ($request->input('create_login') ? ' with login account.' : '.'));
    }

    public function edit($id)
    {
        $clerk = DB::table('nexcore_clerks')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$clerk) {
            return redirect()->route('nexcore.clients.clerks.index')->with('error', 'Clerk not found.');
        }

        $linkedUser = null;
        if ($clerk->user_id) {
            $linkedUser = DB::table('users')->where('id', $clerk->user_id)->first();
        }

        $practices = DB::table('nexcore_practices')->whereNull('deleted_at')->where('is_active', 1)->orderBy('practice_name')->get();
        $clerkPractices = DB::table('nexcore_practice_clerks')->where('clerk_id', $id)->pluck('practice_id')->toArray();
        $clients = DB::table('nexcore_clients')->where('is_active', 1)->orderBy('company_name')->get();
        $clerkClients = DB::table('nexcore_clerk_clients')->where('clerk_id', $id)->pluck('client_id')->toArray();

        return view('nexcore_client_manager::clerks.form', compact('clerk', 'practices', 'clerkPractices', 'linkedUser', 'clients', 'clerkClients'));
    }

    public function update(Request $request, $id)
    {
        $clerk = DB::table('nexcore_clerks')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$clerk) {
            return redirect()->route('nexcore.clients.clerks.index')->with('error', 'Clerk not found.');
        }

        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ];

        if ($request->input('create_login') && !$clerk->user_id) {
            $rules['login_email'] = 'required|email';
            $rules['login_password'] = 'required|min:6|confirmed';
        }

        if ($clerk->user_id && $request->filled('login_password')) {
            $rules['login_password'] = 'min:6|confirmed';
        }

        $request->validate($rules);

        if ($request->filled('login_email')) {
            $emailCheck = DB::table('users')->where('email', $request->input('login_email'));
            if ($clerk->user_id) {
                $emailCheck->where('id', '!=', $clerk->user_id);
            }
            if ($emailCheck->first()) {
                return back()->withInput()->withErrors(['login_email' => 'This email is already registered as a user.']);
            }
        }

        $data = $request->only([
            'first_name', 'last_name', 'known_as', 'id_number',
            'designation', 'job_title', 'employee_number',
            'email', 'phone', 'mobile', 'role',
            'date_joined', 'date_left',
        ]);

        $data['updated_at'] = now();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('clerk_photos', 'public');
            $data['profile_photo'] = 'storage/' . $path;
        }

        if ($clerk->user_id) {
            $this->updateUserAccount($clerk->user_id, $request);
        } elseif ($request->input('create_login') && $request->input('login_email')) {
            $userId = $this->createUserAccount($request);
            $data['user_id'] = $userId;
        }

        DB::table('nexcore_clerks')->where('id', $id)->update($data);

        DB::table('nexcore_practice_clerks')->where('clerk_id', $id)->delete();
        $practiceIds = $request->input('practices', array());
        $now = now();
        foreach ($practiceIds as $pid) {
            DB::table('nexcore_practice_clerks')->insert([
                'practice_id' => $pid,
                'clerk_id' => $id,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('nexcore_clerk_clients')->where('clerk_id', $id)->delete();
        $clientIds = $request->input('clients', array());
        foreach ($clientIds as $cid) {
            DB::table('nexcore_clerk_clients')->insert([
                'clerk_id' => $id,
                'client_id' => $cid,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return redirect()->route('nexcore.clients.clerks.index')->with('success', 'Clerk updated successfully.');
    }

    public function toggle($id)
    {
        $clerk = DB::table('nexcore_clerks')->where('id', $id)->first();
        if ($clerk) {
            DB::table('nexcore_clerks')->where('id', $id)->update([
                'is_active' => $clerk->is_active ? 0 : 1,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('nexcore.clients.clerks.index')->with('success', 'Clerk status updated.');
    }

    public function destroy($id)
    {
        DB::table('nexcore_clerks')->where('id', $id)->update([
            'deleted_at' => now(),
        ]);

        return redirect()->route('nexcore.clients.clerks.index')->with('success', 'Clerk deleted.');
    }

    private function createUserAccount(Request $request)
    {
        $uniqueId = uniqid() . bin2hex(random_bytes(5));
        $now = now();

        $userId = DB::table('users')->insertGetId([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('login_email'),
            'password' => Hash::make($request->input('login_password')),
            'type' => 'team',
            'role_id' => intval($request->input('login_role_id', 1)),
            'status' => 'active',
            'unique_id' => $uniqueId,
            'account_owner' => 'no',
            'creatorid' => auth()->check() ? auth()->id() : 1,
            'created' => $now,
            'updated' => $now,
        ]);

        return $userId;
    }

    private function updateUserAccount($userId, Request $request)
    {
        $userData = array();
        $userData['updated'] = now();

        if ($request->filled('login_email')) {
            $userData['email'] = $request->input('login_email');
        }

        if ($request->filled('login_role_id')) {
            $userData['role_id'] = intval($request->input('login_role_id'));
        }

        if ($request->filled('login_password')) {
            $userData['password'] = Hash::make($request->input('login_password'));
        }

        $userData['first_name'] = $request->input('first_name');
        $userData['last_name'] = $request->input('last_name');

        DB::table('users')->where('id', $userId)->update($userData);
    }
}
