@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($task) ? 'Edit' : 'New') . ' Task - ' . $client->company_name)
@section('page_heading', isset($task) ? 'EDIT TASK' : 'NEW TASK')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(217,119,6,0.15), rgba(217,119,6,0.05)); border:1px solid rgba(217,119,6,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-tasks" style="color:#d97706; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($task) ? 'Edit Task' : 'Add Task' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.tasks', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Tasks</a>
        </div>
    </div>
</div>

@if($errors->any())
<div class="sl-verdict reject sl-mb-md sl-animate d2" style="padding:14px 20px;">
    <div class="sl-verdict-icon" style="width:32px;height:32px;font-size:16px;"><i class="fas fa-exclamation-triangle"></i></div>
    <div>
        <div class="sl-verdict-text" style="font-size:15px;">Please correct the following errors:</div>
        <ul style="margin:6px 0 0; padding-left:20px; font-size:13px; color:var(--text-secondary);">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST"
      action="{{ isset($task) ? route('nexcore.clients.show.tasks.update', [$client->id, $task->id]) : route('nexcore.clients.show.tasks.store', $client->id) }}">
    @csrf
    @if(isset($task)) @method('PUT') @endif

    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#d97706;"><i class="fas fa-tasks"></i> Task Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr; gap:20px;">
                <div class="sl-field">
                    <label>Title <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}" required placeholder="Enter task title">
                </div>
            </div>

            <div style="margin-top:20px;">
                <div class="sl-field">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Optional description of the task..." style="width:100%;">{{ old('description', $task->description ?? '') }}</textarea>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Category <span style="color:var(--accent-red);">*</span></label>
                    <select name="category" class="ncm-select2" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $task->category ?? 'general') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Priority <span style="color:var(--accent-red);">*</span></label>
                    <select name="priority" class="ncm-select2" required>
                        <option value="">-- Select Priority --</option>
                        @foreach($priorities as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', $task->priority ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="task_status" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($taskStatuses as $key => $label)
                            <option value="{{ $key }}" {{ old('task_status', $task->task_status ?? 'pending') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Assigned To</label>
                    <input type="text" name="assigned_to" value="{{ old('assigned_to', $task->assigned_to ?? '') }}" placeholder="Person responsible">
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-calendar-alt"></i> Dates</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Due Date</label>
                    <input type="text" name="due_date" class="ncm-datepicker" value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div class="sl-field">
                    <label>Completed Date</label>
                    <input type="text" name="completed_date" class="ncm-datepicker" value="{{ old('completed_date', isset($task) && $task->completed_date ? $task->completed_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title"><i class="fas fa-sticky-note"></i> Notes</div>
        </div>
        <div style="padding:24px;">
            <div class="sl-field">
                <textarea name="notes" rows="3" placeholder="Optional internal notes..." style="width:100%;">{{ old('notes', $task->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="sl-animate d5" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($task) ? 'Update Task' : 'Save Task' }}</button>
        <a href="{{ route('nexcore.clients.show.tasks', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" rel="stylesheet">
<style>
.select2-container--default .select2-selection--single { background:var(--bg-raised) !important; border:1px solid var(--border-default) !important; border-radius:var(--radius-sm) !important; height:42px !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { color:var(--text-primary) !important; line-height:42px !important; padding-left:12px !important; font-size:15px !important; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height:42px !important; }
.select2-dropdown { background:var(--bg-surface) !important; border:1px solid var(--border-default) !important; border-radius:var(--radius-sm) !important; }
.select2-search--dropdown .select2-search__field { background:var(--bg-raised) !important; border:1px solid var(--border-default) !important; color:var(--text-primary) !important; border-radius:var(--radius-sm) !important; padding:8px 12px !important; }
.select2-results__option { color:var(--text-secondary) !important; padding:10px 14px !important; font-size:14px !important; }
.select2-results__option--highlighted { background:#d97706 !important; color:#fff !important; }
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:var(--text-muted) !important; }
.flatpickr-calendar { background:var(--bg-surface) !important; border:1px solid var(--border-default) !important; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(function() {
    $('.ncm-select2').select2({ width: '100%', placeholder: '-- Select --', allowClear: true });
    $('.ncm-datepicker').flatpickr({
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'j M Y',
        theme: 'dark',
        allowInput: false
    });
});
</script>
@endpush
