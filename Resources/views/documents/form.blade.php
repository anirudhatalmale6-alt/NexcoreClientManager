@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', (isset($document) ? 'Edit' : 'Upload') . ' Document - ' . $client->company_name)
@section('page_heading', isset($document) ? 'EDIT DOCUMENT' : 'UPLOAD DOCUMENT')

@section('content')
<div class="sl-animate d1">
    <div class="sl-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg, rgba(124,58,237,0.15), rgba(124,58,237,0.05)); border:1px solid rgba(124,58,237,0.3); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-folder-open" style="color:#7c3aed; font-size:16px;"></i>
            </div>
            <div>
                <h1 class="sl-page-title" style="margin:0;">{{ isset($document) ? 'Edit Document' : 'Upload Document' }}</h1>
                <span class="sl-page-subtitle">{{ $client->company_name }}</span>
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('nexcore.clients.show.documents', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-arrow-left"></i> Back to Documents</a>
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
      enctype="multipart/form-data"
      action="{{ isset($document) ? route('nexcore.clients.show.documents.update', [$client->id, $document->id]) : route('nexcore.clients.show.documents.store', $client->id) }}">
    @csrf
    @if(isset($document)) @method('PUT') @endif

    <div class="sl-card sl-animate d2">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:#7c3aed;"><i class="fas fa-folder-open"></i> Document Details</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Document Type <span style="color:var(--accent-red);">*</span></label>
                    <select name="document_type_id" class="ncm-select2" required>
                        <option value="">-- Select Type --</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('document_type_id', $document->document_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sl-field">
                    <label>Status <span style="color:var(--accent-red);">*</span></label>
                    <select name="status_id" class="ncm-select2" required>
                        <option value="">-- Select Status --</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $document->status_id ?? '') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top:20px;">
                <div class="sl-field">
                    <label>Title <span style="color:var(--accent-red);">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $document->title ?? '') }}" required placeholder="Document title or reference">
                </div>
            </div>

            <div style="margin-top:20px;">
                <div class="sl-field">
                    <label>Description</label>
                    <textarea name="description" rows="2" placeholder="Brief description of this document..." style="width:100%;">{{ old('description', $document->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-card sl-animate d3" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-cyan);"><i class="fas fa-file-upload"></i> File Upload</div>
        </div>
        <div style="padding:24px;">
            @if(isset($document) && $document->file_name)
                <div style="display:flex; align-items:center; gap:12px; padding:12px 16px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.25); border-radius:var(--radius-sm); margin-bottom:16px;">
                    <i class="fas fa-file" style="color:#7c3aed; font-size:18px;"></i>
                    <div>
                        <div style="font-size:13px; font-weight:600; color:var(--text-primary); font-family:var(--font-mono);">{{ $document->file_name }}</div>
                        @if($document->file_size)
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ number_format($document->file_size / 1024, 1) }} KB &mdash; {{ $document->file_type ?? '' }}</div>
                        @endif
                    </div>
                    @if($document->file_path)
                        <a href="{{ asset('uploads/documents/' . $document->file_path) }}" target="_blank" style="margin-left:auto; color:#7c3aed; font-size:13px; text-decoration:none; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-external-link-alt"></i> View current file
                        </a>
                    @endif
                </div>
                <div class="sl-field">
                    <label>Replace File <span style="font-size:12px; color:var(--text-muted); font-weight:400;">(optional — leave blank to keep current)</span></label>
                    <input type="file" name="document_file" class="ncm-file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.zip,.rar">
                </div>
            @else
                <div class="sl-field">
                    <label>File <span style="color:var(--accent-red);">*</span></label>
                    <input type="file" name="document_file" class="ncm-file-input" required accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.zip,.rar">
                    <div style="font-size:11px; color:var(--text-muted); margin-top:6px;">Max 20MB. Accepted: PDF, Word, Excel, Images, ZIP</div>
                </div>
            @endif
        </div>
    </div>

    <div class="sl-card sl-animate d4" style="margin-top:20px;">
        <div class="sl-card-header">
            <div class="sl-card-title" style="color:var(--accent-amber);"><i class="fas fa-calendar-alt"></i> Validity & Notes</div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="sl-field">
                    <label>Expiry Date</label>
                    <input type="text" name="expiry_date" class="ncm-datepicker" value="{{ old('expiry_date', isset($document) && $document->expiry_date ? $document->expiry_date->format('Y-m-d') : '') }}" placeholder="Select date..." readonly>
                </div>
                <div style="display:flex; align-items:flex-end;">
                    <div style="font-size:12px; color:var(--text-muted); padding-bottom:10px;">
                        <i class="fas fa-info-circle" style="color:var(--accent-amber);"></i>
                        Documents expiring within 30 days will be flagged in the dashboard.
                    </div>
                </div>
            </div>

            <div style="margin-top:20px;">
                <div class="sl-field">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional notes about this document..." style="width:100%;">{{ old('notes', $document->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="sl-animate d5" style="margin-top:24px; display:flex; gap:12px;">
        <button type="submit" class="neon-btn neon-btn-green neon-pulse"><i class="fas fa-save"></i> {{ isset($document) ? 'Update Document' : 'Upload Document' }}</button>
        <a href="{{ route('nexcore.clients.show.documents', $client->id) }}" class="neon-btn neon-btn-ghost"><i class="fas fa-times"></i> Cancel</a>
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
.select2-results__option--highlighted { background:var(--accent-cyan) !important; color:#fff !important; }
.select2-container--default .select2-selection--single .select2-selection__placeholder { color:var(--text-muted) !important; }
.flatpickr-calendar { background:var(--bg-surface) !important; border:1px solid var(--border-default) !important; }
.ncm-file-input {
    display:block;
    width:100%;
    padding:10px 14px;
    font-size:14px;
    font-family:var(--font-body);
    color:var(--text-secondary);
    background:var(--bg-raised);
    border:1px solid var(--border-default);
    border-radius:var(--radius-sm);
    cursor:pointer;
    transition:border-color 0.2s ease;
}
.ncm-file-input:hover,
.ncm-file-input:focus {
    border-color:rgba(124,58,237,0.5);
    outline:none;
}
.ncm-file-input::file-selector-button {
    padding:6px 14px;
    background:rgba(124,58,237,0.15);
    border:1px solid rgba(124,58,237,0.35);
    border-radius:4px;
    color:#a78bfa;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
    margin-right:12px;
    transition:background 0.2s ease;
}
.ncm-file-input::file-selector-button:hover {
    background:rgba(124,58,237,0.25);
}
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
