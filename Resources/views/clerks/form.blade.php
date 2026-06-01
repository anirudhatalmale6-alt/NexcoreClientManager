@extends('nexcore_client_manager::layouts.app')

@section('title', $clerk ? 'Edit Clerk' : 'New Clerk')
@section('page_heading', $clerk ? 'EDIT CLERK' : 'NEW CLERK')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <h1 class="sl-page-title">{{ $clerk ? 'Edit Clerk' : 'Create New Clerk' }}</h1>
        <span class="sl-page-subtitle">{{ $clerk ? 'Update clerk information' : 'Register a new practice clerk' }}</span>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.clerks.index') }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Clerks</a>
        </div>
    </div>
</div>

@if($errors->any())
<div class="sl-card sl-animate d2 sl-mb-md" style="border:1px solid var(--accent-red); background:rgba(239,68,68,0.06);">
    <div style="padding:12px 16px; display:flex; align-items:start; gap:12px;">
        <i class="fas fa-exclamation-triangle" style="color:var(--accent-red); margin-top:3px;"></i>
        <div>
            @foreach($errors->all() as $error)
                <div style="color:var(--accent-red); font-size:13px; margin-bottom:4px;">{{ $error }}</div>
            @endforeach
        </div>
    </div>
</div>
@endif

<form method="POST" action="{{ $clerk ? route('nexcore.clients.clerks.update', $clerk->id) : route('nexcore.clients.clerks.store') }}" enctype="multipart/form-data">
    @csrf
    @if($clerk) @method('PUT') @endif

    <div class="sl-card sl-animate d2 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-user"></i> Personal Details</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>First Name <span style="color:var(--accent-red);">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name', $clerk->first_name ?? '') }}" required style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Last Name <span style="color:var(--accent-red);">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name', $clerk->last_name ?? '') }}" required style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Known As (Nickname)</label>
                <input type="text" name="known_as" value="{{ old('known_as', $clerk->known_as ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>SA ID Number</label>
                <input type="text" name="id_number" value="{{ old('id_number', $clerk->id_number ?? '') }}" maxlength="13" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Profile Photo</label>
                <input type="file" name="profile_photo" accept="image/*" style="font-size:14px;">
                @if($clerk && $clerk->profile_photo)
                    <div style="margin-top:6px; font-size:12px; color:var(--text-muted);">Current: {{ $clerk->profile_photo }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-briefcase"></i> Employment Details</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Employee Number</label>
                <input type="text" name="employee_number" value="{{ old('employee_number', $clerk->employee_number ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation', $clerk->designation ?? '') }}" placeholder="e.g. CA(SA), AGA(SA), Bookkeeper" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Job Title</label>
                <input type="text" name="job_title" value="{{ old('job_title', $clerk->job_title ?? '') }}" placeholder="e.g. Senior Accountant, Tax Manager" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>System Role</label>
                <select name="role" style="font-size:15px; font-weight:600;">
                    <option value="clerk" {{ old('role', $clerk->role ?? 'clerk') == 'clerk' ? 'selected' : '' }}>Clerk</option>
                    <option value="practice_manager" {{ old('role', $clerk->role ?? '') == 'practice_manager' ? 'selected' : '' }}>Practice Manager</option>
                    <option value="administrator" {{ old('role', $clerk->role ?? '') == 'administrator' ? 'selected' : '' }}>Administrator</option>
                    <option value="super_admin" {{ old('role', $clerk->role ?? '') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            <div class="sl-field">
                <label>Date Joined</label>
                <input type="date" name="date_joined" value="{{ old('date_joined', $clerk->date_joined ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Date Left</label>
                <input type="date" name="date_left" value="{{ old('date_left', $clerk->date_left ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-phone"></i> Contact Details</div>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div class="sl-field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $clerk->email ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $clerk->phone ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
            <div class="sl-field">
                <label>Mobile</label>
                <input type="text" name="mobile" value="{{ old('mobile', $clerk->mobile ?? '') }}" style="font-size:15px; font-weight:600;">
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d4 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-key"></i> Login Account</div>
            @if($clerk && $clerk->user_id)
                <span style="font-size:12px; color:var(--accent-green); display:flex; align-items:center; gap:6px; margin-left:auto;"><i class="fas fa-check-circle"></i> Linked to User #{{ $clerk->user_id }}</span>
            @endif
        </div>
        <div style="padding:20px;">
            @if($clerk && $clerk->user_id && isset($linkedUser))
                <div style="background:var(--bg-raised); border:1px solid var(--border-default); border-radius:8px; padding:14px 18px; margin-bottom:16px; display:flex; align-items:center; gap:16px;">
                    <div style="width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px; color:#fff;">
                        {{ strtoupper(substr($linkedUser->first_name, 0, 1)) }}{{ strtoupper(substr($linkedUser->last_name, 0, 1)) }}
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:600; font-size:14px; color:var(--text-primary);">{{ $linkedUser->first_name }} {{ $linkedUser->last_name }}</div>
                        <div style="font-size:12px; color:var(--text-muted);">{{ $linkedUser->email }} &middot; Role ID: {{ $linkedUser->role_id }} &middot; Status: {{ $linkedUser->status }}</div>
                    </div>
                    <span style="font-size:11px; padding:4px 10px; border-radius:20px; background:rgba(16,185,129,0.12); color:var(--accent-green); font-weight:600;">ACTIVE</span>
                </div>
                <div style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">Update login credentials below. Leave password blank to keep the current password.</div>
            @else
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px; color:var(--text-primary);">
                        <input type="checkbox" name="create_login" id="nxCreateLogin" value="1" {{ old('create_login') ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--accent-cyan);">
                        Create a login account for this clerk
                    </label>
                </div>
            @endif

            <div id="nxLoginFields" style="{{ ($clerk && $clerk->user_id) ? '' : 'display:none;' }}">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="sl-field">
                        <label>Login Email <span style="color:var(--accent-red);">*</span></label>
                        <input type="email" name="login_email" value="{{ old('login_email', isset($linkedUser) ? $linkedUser->email : ($clerk->email ?? '')) }}" placeholder="user@practice.co.za" style="font-size:15px; font-weight:600;">
                    </div>
                    <div class="sl-field">
                        <label>Login Role</label>
                        <select name="login_role_id" style="font-size:15px; font-weight:600;">
                            <option value="1" {{ old('login_role_id', isset($linkedUser) ? $linkedUser->role_id : 1) == 1 ? 'selected' : '' }}>Administrator</option>
                            <option value="3" {{ old('login_role_id', isset($linkedUser) ? $linkedUser->role_id : 1) == 3 ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="sl-field">
                        <label>Password {{ ($clerk && $clerk->user_id) ? '' : '*' }}</label>
                        <input type="password" name="login_password" placeholder="{{ ($clerk && $clerk->user_id) ? 'Leave blank to keep current' : 'Min 6 characters' }}" style="font-size:15px; font-weight:600;" autocomplete="new-password">
                    </div>
                    <div class="sl-field">
                        <label>Confirm Password {{ ($clerk && $clerk->user_id) ? '' : '*' }}</label>
                        <input type="password" name="login_password_confirmation" placeholder="Repeat password" style="font-size:15px; font-weight:600;" autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d5 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-building"></i> Practice Assignments</div>
        </div>
        <div style="padding:20px;">
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">Select which practices this clerk belongs to:</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                @foreach($practices as $p)
                <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:6px; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.borderColor='var(--accent-cyan)'" onmouseout="this.style.borderColor='var(--border-default)'">
                    <input type="checkbox" name="practices[]" value="{{ $p->id }}" {{ in_array($p->id, $clerkPractices) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--accent-cyan);">
                    <div>
                        <div style="font-weight:600; font-size:14px; color:var(--text-primary);">{{ $p->practice_name }}</div>
                        @if($p->trading_name && $p->trading_name !== $p->practice_name)
                            <div style="font-size:11px; color:var(--text-muted);">t/a {{ $p->trading_name }}</div>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
            @if(count($practices) == 0)
                <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;">No active practices found. Create a practice first.</div>
            @endif
        </div>
    </div>

    <div class="sl-card sl-animate d6 sl-mb-md">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-building"></i> Linked Clients</div>
        </div>
        <div style="padding:20px;">
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">Select which clients this clerk can access:</div>
            @if(count($clients) > 0)
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                @foreach($clients as $c)
                <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--bg-raised); border:1px solid var(--border-default); border-radius:6px; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.borderColor='var(--accent-cyan)'" onmouseout="this.style.borderColor='var(--border-default)'">
                    <input type="checkbox" name="clients[]" value="{{ $c->id }}" {{ in_array($c->id, $clerkClients) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--accent-cyan);">
                    <div>
                        <div style="font-weight:600; font-size:14px; color:var(--text-primary);">{{ $c->company_name }}</div>
                        <div style="font-size:11px; color:var(--accent-amber); font-family:var(--font-mono);">{{ $c->client_code }}</div>
                        @if($c->trading_name && $c->trading_name !== $c->company_name)
                            <div style="font-size:11px; color:var(--text-muted);">t/a {{ $c->trading_name }}</div>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
            @else
                <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;">No active clients found.</div>
            @endif
        </div>
    </div>

    <div class="sl-animate d7" style="display:flex; gap:12px; justify-content:flex-end; padding-bottom:40px;">
        <a href="{{ route('nexcore.clients.clerks.index') }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ $clerk ? 'Update Clerk' : 'Create Clerk' }}</button>
    </div>
</form>

<script>
(function(){
    var cb = document.getElementById('nxCreateLogin');
    var fields = document.getElementById('nxLoginFields');
    if (cb && fields) {
        cb.addEventListener('change', function(){
            fields.style.display = this.checked ? '' : 'none';
        });
        if (cb.checked) fields.style.display = '';
    }
})();
</script>
@endsection