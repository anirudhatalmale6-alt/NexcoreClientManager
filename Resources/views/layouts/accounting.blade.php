@extends('nexcore_client_manager::layouts.nerve-centre')

@section('topbar_module', 'Accounting')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@push('styles')
<style>
.neon-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-decoration: none;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    font-family: var(--font-body);
    line-height: 1.4;
}
.neon-btn::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 6px;
    padding: 2px;
    background: linear-gradient(135deg, var(--neon-from), var(--neon-to));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0.8;
    transition: opacity 0.3s;
}
.neon-btn::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--neon-from), var(--neon-to));
    opacity: 0;
    filter: blur(12px);
    transition: opacity 0.4s;
    z-index: -1;
}
.neon-btn:hover::before { opacity: 1; }
.neon-btn:hover::after { opacity: 0.4; }
.neon-btn:hover { transform: translateY(-2px); }
.neon-btn:active { transform: translateY(0) scale(0.98); }
.neon-btn i { font-size: 12px; transition: transform 0.3s; }
.neon-btn:hover i { transform: scale(1.15); }

.neon-btn-green { --neon-from: #22c55e; --neon-to: #10b981; color: #22c55e; background: rgba(34, 197, 94, 0.08); }
.neon-btn-green:hover { color: #4ade80; background: rgba(34, 197, 94, 0.15); box-shadow: 0 0 20px rgba(34, 197, 94, 0.2), 0 0 40px rgba(34, 197, 94, 0.1); }
.neon-btn-cyan { --neon-from: #06b6d4; --neon-to: #0ea5e9; color: #06b6d4; background: rgba(6, 182, 212, 0.08); }
.neon-btn-cyan:hover { color: #22d3ee; background: rgba(6, 182, 212, 0.15); box-shadow: 0 0 20px rgba(6, 182, 212, 0.2), 0 0 40px rgba(6, 182, 212, 0.1); }
.neon-btn-blue { --neon-from: #3b82f6; --neon-to: #6366f1; color: #3b82f6; background: rgba(59, 130, 246, 0.08); }
.neon-btn-blue:hover { color: #60a5fa; background: rgba(59, 130, 246, 0.15); box-shadow: 0 0 20px rgba(59, 130, 246, 0.2), 0 0 40px rgba(59, 130, 246, 0.1); }
.neon-btn-amber { --neon-from: #f59e0b; --neon-to: #d97706; color: #f59e0b; background: rgba(245, 158, 11, 0.08); }
.neon-btn-amber:hover { color: #fbbf24; background: rgba(245, 158, 11, 0.15); box-shadow: 0 0 20px rgba(245, 158, 11, 0.2), 0 0 40px rgba(245, 158, 11, 0.1); }
.neon-btn-purple { --neon-from: #a855f7; --neon-to: #7c3aed; color: #a855f7; background: rgba(168, 85, 247, 0.08); }
.neon-btn-purple:hover { color: #c084fc; background: rgba(168, 85, 247, 0.15); box-shadow: 0 0 20px rgba(168, 85, 247, 0.2), 0 0 40px rgba(168, 85, 247, 0.1); }
.neon-btn-red { --neon-from: #ef4444; --neon-to: #dc2626; color: #ef4444; background: rgba(239, 68, 68, 0.08); }
.neon-btn-red:hover { color: #f87171; background: rgba(239, 68, 68, 0.15); box-shadow: 0 0 20px rgba(239, 68, 68, 0.2), 0 0 40px rgba(239, 68, 68, 0.1); }
.neon-btn-ghost { --neon-from: #64748b; --neon-to: #94a3b8; color: #94a3b8; background: rgba(148, 163, 184, 0.05); }
.neon-btn-ghost:hover { color: #cbd5e1; background: rgba(148, 163, 184, 0.1); box-shadow: 0 0 15px rgba(148, 163, 184, 0.15), 0 0 30px rgba(148, 163, 184, 0.08); }

.swal2-container { z-index: 99999 !important; }
.nc-swal-popup { border: 1px solid rgba(245,158,11,0.2) !important; border-radius: 16px !important; box-shadow: 0 20px 60px rgba(0,0,0,0.6), 0 0 40px rgba(245,158,11,0.05) !important; }
.nc-swal-title { color: #f1f5f9 !important; font-weight: 800 !important; font-size: 20px !important; letter-spacing: 0.5px !important; font-family: 'Montserrat', sans-serif !important; }
.nc-swal-html { color: #94a3b8 !important; font-size: 14px !important; line-height: 1.6 !important; }
.nc-swal-actions .swal2-confirm, .nc-swal-actions .swal2-cancel { font-family: 'Montserrat', sans-serif !important; font-weight: 700 !important; font-size: 13px !important; letter-spacing: 1px !important; text-transform: uppercase !important; border-radius: 8px !important; padding: 12px 28px !important; }
.swal2-popup .swal2-icon { border: none !important; }
</style>
@endpush

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@endpush
