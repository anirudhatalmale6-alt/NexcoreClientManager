@extends('nexcore_client_manager::layouts.nerve-centre')

@section('sidebar')
    @include('nexcore_client_manager::partials.nerve-centre-sidebar')
@endsection

@section('title', 'Task Command Centre - ' . $client->company_name)
@section('page_heading', 'TASK COMMAND CENTRE')

@section('content')

@php
    $today = \Carbon\Carbon::today();
    $endOfWeek = \Carbon\Carbon::now()->endOfWeek();

    $totalCount = $tasks->count();
    $overdueCount = 0;
    $dueWeekCount = 0;
    $inProgCount = 0;
    $completedCount = 0;
    $pendingCount = 0;
    $cancelledCount = 0;

    $catCounts = [];
    foreach ($categories as $ck => $cl) {
        $catCounts[$ck] = 0;
    }

    foreach ($tasks as $t) {
        $isActive = !in_array($t->task_status, ['completed', 'cancelled']);
        if ($t->task_status === 'pending') $pendingCount++;
        if ($t->task_status === 'in_progress') $inProgCount++;
        if ($t->task_status === 'completed') $completedCount++;
        if ($t->task_status === 'cancelled') $cancelledCount++;
        if ($t->due_date && $t->due_date->lt($today) && $isActive) $overdueCount++;
        if ($t->due_date && $t->due_date->gte($today) && $t->due_date->lte($endOfWeek) && $isActive) $dueWeekCount++;
        $cat = $t->category ?: 'general';
        if (isset($catCounts[$cat])) $catCounts[$cat]++;
    }
@endphp

{{-- Page Header --}}
<div class="tcc-hero sl-animate d1">
    <div class="tcc-hero-left">
        <div class="tcc-hero-icon">
            <i class="fas fa-bullseye"></i>
            <div class="tcc-hero-pulse"></div>
        </div>
        <div>
            <h1 class="tcc-hero-title">Task Command Centre</h1>
            <p class="tcc-hero-sub">{{ $client->company_name }} <span class="tcc-hero-dot"></span> {{ $totalCount }} {{ $totalCount === 1 ? 'task' : 'tasks' }}</p>
        </div>
    </div>
    <div class="tcc-hero-right">
        <a href="{{ route('nexcore.clients.show.tasks.create', $client->id) }}" class="tcc-btn-add">
            <i class="fas fa-plus"></i> New Task
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="tcc-stats sl-animate d2">
    <div class="tcc-stat" style="--stat-color:#94a3b8;">
        <div class="tcc-stat-icon"><i class="fas fa-layer-group"></i></div>
        <div class="tcc-stat-num">{{ $totalCount }}</div>
        <div class="tcc-stat-label">Total Tasks</div>
    </div>
    <div class="tcc-stat {{ $overdueCount > 0 ? 'tcc-stat-alert' : '' }}" style="--stat-color:#ef4444;">
        <div class="tcc-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="tcc-stat-num">{{ $overdueCount }}</div>
        <div class="tcc-stat-label">Overdue</div>
        @if($overdueCount > 0)<div class="tcc-stat-pulse-ring"></div>@endif
    </div>
    <div class="tcc-stat" style="--stat-color:#f59e0b;">
        <div class="tcc-stat-icon"><i class="fas fa-clock"></i></div>
        <div class="tcc-stat-num">{{ $dueWeekCount }}</div>
        <div class="tcc-stat-label">Due This Week</div>
    </div>
    <div class="tcc-stat" style="--stat-color:#3b82f6;">
        <div class="tcc-stat-icon"><i class="fas fa-spinner"></i></div>
        <div class="tcc-stat-num">{{ $inProgCount }}</div>
        <div class="tcc-stat-label">In Progress</div>
    </div>
    <div class="tcc-stat" style="--stat-color:#10b981;">
        <div class="tcc-stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="tcc-stat-num">{{ $completedCount }}</div>
        <div class="tcc-stat-label">Completed</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="tcc-filters sl-animate d3">
    <div class="tcc-filter-row">
        <div class="tcc-filter-label"><i class="fas fa-folder"></i> Category</div>
        <div class="tcc-pills" id="tccCatPills">
            <button class="tcc-pill active" data-cat="all" onclick="tccFilterCat('all', this)">
                <span class="tcc-pill-dot" style="background:#94a3b8;"></span> All <span class="tcc-pill-count">{{ $totalCount }}</span>
            </button>
            @foreach($categories as $catKey => $catLabel)
                @if($catCounts[$catKey] > 0 || true)
                <button class="tcc-pill" data-cat="{{ $catKey }}" onclick="tccFilterCat('{{ $catKey }}', this)">
                    <span class="tcc-pill-dot" style="background:{{ $categoryColors[$catKey] }};"></span> {{ $catLabel }} <span class="tcc-pill-count">{{ $catCounts[$catKey] }}</span>
                </button>
                @endif
            @endforeach
        </div>
    </div>
    <div class="tcc-filter-row">
        <div class="tcc-filter-label"><i class="fas fa-filter"></i> Status</div>
        <div class="tcc-pills" id="tccStatusPills">
            <button class="tcc-pill active" data-status="all" onclick="tccFilterStatus('all', this)">All</button>
            <button class="tcc-pill" data-status="pending" onclick="tccFilterStatus('pending', this)"><i class="fas fa-clock" style="font-size:10px;"></i> Pending</button>
            <button class="tcc-pill" data-status="in_progress" onclick="tccFilterStatus('in_progress', this)"><i class="fas fa-spinner" style="font-size:10px;"></i> In Progress</button>
            <button class="tcc-pill" data-status="completed" onclick="tccFilterStatus('completed', this)"><i class="fas fa-check" style="font-size:10px;"></i> Completed</button>
            <button class="tcc-pill" data-status="overdue" onclick="tccFilterStatus('overdue', this)"><i class="fas fa-exclamation" style="font-size:10px;"></i> Overdue</button>
        </div>
    </div>
    <div class="tcc-filter-row">
        <div class="tcc-filter-label"><i class="fas fa-search"></i> Search</div>
        <input type="text" class="tcc-search" id="tccSearch" placeholder="Search tasks..." oninput="tccApplyFilters()">
    </div>
</div>

{{-- Task Cards --}}
<div class="tcc-list" id="tccTaskList">
    @forelse($tasks as $task)
    @php
        $cat = $task->category ?: 'general';
        $catColor = $categoryColors[$cat] ?? '#94a3b8';
        $catIcon = $categoryIcons[$cat] ?? 'fa-tasks';
        $catLabel = $categories[$cat] ?? 'General';
        $isOverdue = $task->due_date && $task->due_date->lt($today) && !in_array($task->task_status, ['completed', 'cancelled']);
        $daysLeft = null;
        $dueLabel = '';
        if ($task->due_date) {
            $diff = $today->diffInDays($task->due_date, false);
            if ($task->task_status === 'completed' || $task->task_status === 'cancelled') {
                $dueLabel = $task->due_date->format('j M Y');
            } elseif ($diff < 0) {
                $dueLabel = abs($diff) . ' day' . (abs($diff) > 1 ? 's' : '') . ' overdue';
            } elseif ($diff === 0) {
                $dueLabel = 'Due today';
            } elseif ($diff === 1) {
                $dueLabel = 'Due tomorrow';
            } elseif ($diff <= 7) {
                $dueLabel = $diff . ' days left';
            } else {
                $dueLabel = $task->due_date->format('j M Y');
            }
        }
    @endphp
    <div class="tcc-card sl-animate d4 {{ $isOverdue ? 'tcc-card-overdue' : '' }}"
         data-cat="{{ $cat }}"
         data-status="{{ $task->task_status }}"
         data-overdue="{{ $isOverdue ? '1' : '0' }}"
         data-search="{{ strtolower($task->title . ' ' . ($task->description ?? '') . ' ' . ($task->assigned_to ?? '')) }}"
         style="--card-accent:{{ $catColor }};">
        <div class="tcc-card-accent"></div>
        <div class="tcc-card-body">
            <div class="tcc-card-top">
                <div class="tcc-card-title-wrap">
                    <h3 class="tcc-card-title">{{ $task->title }}</h3>
                    @if($task->description)
                        <p class="tcc-card-desc">{{ \Illuminate\Support\Str::limit($task->description, 120) }}</p>
                    @endif
                </div>
                <div class="tcc-card-actions">
                    <a href="{{ route('nexcore.clients.show.tasks.edit', [$client->id, $task->id]) }}" class="tcc-action-btn tcc-action-edit" title="Edit"><i class="fas fa-pen"></i></a>
                    <form method="POST" action="{{ route('nexcore.clients.show.tasks.destroy', [$client->id, $task->id]) }}" style="display:inline;" onsubmit="return confirm('Delete this task?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="tcc-action-btn tcc-action-del" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </div>
            </div>
            <div class="tcc-card-meta">
                <div class="tcc-badge tcc-badge-cat" style="--badge-color:{{ $catColor }};">
                    <i class="fas {{ $catIcon }}"></i> {{ $catLabel }}
                </div>
                <div class="tcc-badge tcc-badge-priority tcc-priority-{{ $task->priority }}">
                    <span class="tcc-priority-dot"></span> {{ ucfirst($task->priority) }}
                </div>
                <div class="tcc-badge tcc-badge-status tcc-status-{{ $task->task_status }}">
                    @switch($task->task_status)
                        @case('pending')
                            <i class="fas fa-clock"></i> Pending
                            @break
                        @case('in_progress')
                            <i class="fas fa-spinner"></i> In Progress
                            @break
                        @case('completed')
                            <i class="fas fa-check-circle"></i> Completed
                            @break
                        @case('cancelled')
                            <i class="fas fa-ban"></i> Cancelled
                            @break
                    @endswitch
                </div>
                @if($task->due_date)
                <div class="tcc-badge tcc-badge-due {{ $isOverdue ? 'tcc-badge-overdue' : '' }}">
                    <i class="far fa-calendar-alt"></i>
                    {{ $task->due_date->format('j M Y') }}
                    @if($dueLabel && ($isOverdue || (isset($diff) && $diff >= 0 && $diff <= 7)))
                        <span class="tcc-due-countdown {{ $isOverdue ? 'tcc-due-red' : 'tcc-due-amber' }}">{{ $dueLabel }}</span>
                    @endif
                </div>
                @endif
                @if($task->assigned_to)
                <div class="tcc-badge tcc-badge-assign">
                    <i class="fas fa-user"></i> {{ $task->assigned_to }}
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="tcc-empty" id="tccEmpty">
        <div class="tcc-empty-icon"><i class="fas fa-bullseye"></i></div>
        <h3>No Tasks Yet</h3>
        <p>Create your first task to get started. Organise by category — SARS, CIPC, COIDA, Compliance, Birthdays, Events and more.</p>
        <a href="{{ route('nexcore.clients.show.tasks.create', $client->id) }}" class="tcc-btn-add" style="margin-top:16px;">
            <i class="fas fa-plus"></i> Create First Task
        </a>
    </div>
    @endforelse
</div>

<div class="tcc-empty" id="tccNoResults" style="display:none;">
    <div class="tcc-empty-icon" style="opacity:0.3;"><i class="fas fa-search"></i></div>
    <h3>No Matching Tasks</h3>
    <p>Try adjusting your filters or search terms.</p>
</div>

@endsection

@push('styles')
<style>
/* ═══ TASK COMMAND CENTRE ═══ */

.tcc-hero {
    display:flex; align-items:center; justify-content:space-between; gap:16px;
    padding:20px 24px; margin-bottom:20px;
    background:linear-gradient(135deg, rgba(16,185,129,0.06), rgba(59,130,246,0.04), rgba(139,92,246,0.03));
    border:1px solid rgba(255,255,255,0.06);
    border-radius:16px;
    position:relative; overflow:hidden;
}
.tcc-hero::before {
    content:''; position:absolute; top:-50%; right:-20%; width:300px; height:300px;
    background:radial-gradient(circle, rgba(16,185,129,0.08), transparent 70%);
    pointer-events:none;
}
.tcc-hero-left { display:flex; align-items:center; gap:16px; position:relative; z-index:1; }
.tcc-hero-icon {
    width:52px; height:52px; border-radius:14px;
    background:linear-gradient(135deg, rgba(16,185,129,0.2), rgba(59,130,246,0.15));
    border:1px solid rgba(16,185,129,0.3);
    display:flex; align-items:center; justify-content:center;
    font-size:20px; color:#10b981; position:relative;
}
.tcc-hero-pulse {
    position:absolute; inset:-4px; border-radius:18px;
    border:2px solid rgba(16,185,129,0.3);
    animation:tccPulse 2.5s ease-in-out infinite;
}
.tcc-hero-title { margin:0; font-size:22px; font-weight:800; color:#fff; letter-spacing:0.5px; font-family:'Montserrat',sans-serif; }
.tcc-hero-sub { margin:4px 0 0; font-size:13px; color:rgba(255,255,255,0.45); font-weight:500; }
.tcc-hero-dot { display:inline-block; width:4px; height:4px; border-radius:50%; background:rgba(255,255,255,0.25); vertical-align:middle; margin:0 6px; }
.tcc-hero-right { position:relative; z-index:1; }

.tcc-btn-add {
    display:inline-flex; align-items:center; gap:8px;
    padding:11px 22px; border-radius:10px;
    background:linear-gradient(135deg, #059669, #10b981);
    color:#fff; font-size:13px; font-weight:700;
    text-decoration:none; border:none; cursor:pointer;
    box-shadow:0 0 20px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.1);
    transition:all 0.3s ease; letter-spacing:0.3px;
}
.tcc-btn-add:hover {
    transform:translateY(-1px);
    box-shadow:0 0 30px rgba(16,185,129,0.4), inset 0 1px 0 rgba(255,255,255,0.15);
}

/* ── Summary Cards ── */
.tcc-stats { display:grid; grid-template-columns:repeat(5, 1fr); gap:12px; margin-bottom:20px; }
.tcc-stat {
    padding:18px 16px; border-radius:14px;
    background:rgba(255,255,255,0.02);
    border:1px solid rgba(255,255,255,0.06);
    text-align:center; position:relative; overflow:hidden;
    transition:all 0.3s ease;
}
.tcc-stat::after {
    content:''; position:absolute; bottom:0; left:0; right:0; height:3px;
    background:var(--stat-color); opacity:0.6;
}
.tcc-stat:hover { background:rgba(255,255,255,0.04); transform:translateY(-2px); }
.tcc-stat-icon { font-size:16px; color:var(--stat-color); margin-bottom:8px; opacity:0.8; }
.tcc-stat-num { font-size:28px; font-weight:800; color:#fff; font-family:'Montserrat',sans-serif; line-height:1; }
.tcc-stat-label { font-size:11px; font-weight:600; color:rgba(255,255,255,0.4); text-transform:uppercase; letter-spacing:0.8px; margin-top:6px; }
.tcc-stat-alert { border-color:rgba(239,68,68,0.3); background:rgba(239,68,68,0.04); }
.tcc-stat-alert .tcc-stat-num { color:#ef4444; }
.tcc-stat-pulse-ring {
    position:absolute; top:8px; right:8px; width:8px; height:8px;
    border-radius:50%; background:#ef4444;
    animation:tccStatPulse 1.5s ease-in-out infinite;
}

/* ── Filter Bar ── */
.tcc-filters {
    padding:16px 20px; border-radius:14px;
    background:rgba(255,255,255,0.02);
    border:1px solid rgba(255,255,255,0.06);
    margin-bottom:20px;
    display:flex; flex-direction:column; gap:12px;
}
.tcc-filter-row { display:flex; align-items:center; gap:12px; }
.tcc-filter-label {
    font-size:11px; font-weight:700; color:rgba(255,255,255,0.35);
    text-transform:uppercase; letter-spacing:0.5px; min-width:72px;
    display:flex; align-items:center; gap:6px;
}
.tcc-filter-label i { font-size:10px; opacity:0.6; }
.tcc-pills { display:flex; flex-wrap:wrap; gap:6px; }
.tcc-pill {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; border-radius:20px;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.08);
    color:rgba(255,255,255,0.5); font-size:12px; font-weight:600;
    cursor:pointer; transition:all 0.25s ease;
    font-family:'Poppins',sans-serif;
}
.tcc-pill:hover { background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.8); }
.tcc-pill.active {
    background:rgba(16,185,129,0.12); border-color:rgba(16,185,129,0.3);
    color:#10b981;
}
.tcc-pill-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.tcc-pill-count {
    font-size:10px; font-weight:700; font-family:var(--font-mono);
    background:rgba(255,255,255,0.06); padding:1px 6px; border-radius:8px;
    min-width:18px; text-align:center;
}
.tcc-pill.active .tcc-pill-count { background:rgba(16,185,129,0.2); }
.tcc-search {
    flex:1; padding:8px 14px; border-radius:10px;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.08);
    color:#fff; font-size:13px; font-family:'Poppins',sans-serif;
    outline:none; transition:all 0.25s ease;
}
.tcc-search:focus { border-color:rgba(16,185,129,0.4); background:rgba(255,255,255,0.05); }
.tcc-search::placeholder { color:rgba(255,255,255,0.25); }

/* ── Task Cards ── */
.tcc-list { display:flex; flex-direction:column; gap:10px; }
.tcc-card {
    display:flex; border-radius:12px;
    background:rgba(255,255,255,0.02);
    border:1px solid rgba(255,255,255,0.06);
    overflow:hidden; transition:all 0.3s ease;
    position:relative;
}
.tcc-card:hover {
    background:rgba(255,255,255,0.04);
    border-color:rgba(255,255,255,0.1);
    transform:translateX(4px);
    box-shadow:0 4px 24px rgba(0,0,0,0.15);
}
.tcc-card-accent {
    width:4px; min-width:4px;
    background:linear-gradient(180deg, var(--card-accent), color-mix(in srgb, var(--card-accent) 60%, transparent));
    flex-shrink:0;
}
.tcc-card-body { flex:1; padding:16px 20px; min-width:0; }
.tcc-card-top { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
.tcc-card-title-wrap { flex:1; min-width:0; }
.tcc-card-title { margin:0; font-size:15px; font-weight:700; color:#fff; line-height:1.3; }
.tcc-card-desc { margin:4px 0 0; font-size:12px; color:rgba(255,255,255,0.35); line-height:1.5; }
.tcc-card-actions { display:flex; gap:6px; flex-shrink:0; opacity:0; transition:opacity 0.25s ease; }
.tcc-card:hover .tcc-card-actions { opacity:1; }
.tcc-action-btn {
    width:30px; height:30px; border-radius:8px;
    display:flex; align-items:center; justify-content:center;
    font-size:12px; border:none; cursor:pointer; transition:all 0.2s ease;
}
.tcc-action-edit { background:rgba(59,130,246,0.1); color:#3b82f6; text-decoration:none; }
.tcc-action-edit:hover { background:rgba(59,130,246,0.2); }
.tcc-action-del { background:rgba(239,68,68,0.1); color:#ef4444; }
.tcc-action-del:hover { background:rgba(239,68,68,0.2); }

.tcc-card-meta { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; align-items:center; }

/* ── Badges ── */
.tcc-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:6px;
    font-size:11px; font-weight:600; font-family:'Poppins',sans-serif;
}
.tcc-badge i { font-size:10px; }
.tcc-badge-cat {
    background:color-mix(in srgb, var(--badge-color) 12%, transparent);
    color:var(--badge-color);
    border:1px solid color-mix(in srgb, var(--badge-color) 25%, transparent);
}
.tcc-badge-priority { border:1px solid rgba(255,255,255,0.08); }
.tcc-priority-dot { width:6px; height:6px; border-radius:50%; }
.tcc-priority-low { color:rgba(255,255,255,0.4); background:rgba(255,255,255,0.03); }
.tcc-priority-low .tcc-priority-dot { background:#94a3b8; }
.tcc-priority-medium { color:#3b82f6; background:rgba(59,130,246,0.08); border-color:rgba(59,130,246,0.2); }
.tcc-priority-medium .tcc-priority-dot { background:#3b82f6; }
.tcc-priority-high { color:#f59e0b; background:rgba(245,158,11,0.08); border-color:rgba(245,158,11,0.2); }
.tcc-priority-high .tcc-priority-dot { background:#f59e0b; }
.tcc-priority-urgent { color:#ef4444; background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.2); }
.tcc-priority-urgent .tcc-priority-dot { background:#ef4444; animation:tccDotPulse 1.2s ease-in-out infinite; }

.tcc-badge-status { border:1px solid rgba(255,255,255,0.08); }
.tcc-status-pending { color:#f59e0b; background:rgba(245,158,11,0.08); border-color:rgba(245,158,11,0.2); }
.tcc-status-in_progress { color:#3b82f6; background:rgba(59,130,246,0.08); border-color:rgba(59,130,246,0.2); }
.tcc-status-completed { color:#10b981; background:rgba(16,185,129,0.08); border-color:rgba(16,185,129,0.2); }
.tcc-status-cancelled { color:#ef4444; background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.2); }

.tcc-badge-due { color:rgba(255,255,255,0.5); background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); font-family:var(--font-mono); font-size:11px; }
.tcc-badge-overdue { color:#ef4444; background:rgba(239,68,68,0.06); border-color:rgba(239,68,68,0.2); }
.tcc-due-countdown { font-weight:700; margin-left:4px; padding-left:6px; border-left:1px solid rgba(255,255,255,0.1); }
.tcc-due-red { color:#ef4444; }
.tcc-due-amber { color:#f59e0b; }

.tcc-badge-assign { color:rgba(255,255,255,0.45); background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); }

/* ── Overdue Card ── */
.tcc-card-overdue {
    border-color:rgba(239,68,68,0.2);
    box-shadow:inset 0 0 30px rgba(239,68,68,0.03);
}
.tcc-card-overdue:hover { border-color:rgba(239,68,68,0.3); }
.tcc-card-overdue .tcc-card-accent { background:linear-gradient(180deg, #ef4444, #dc2626) !important; }

/* ── Empty State ── */
.tcc-empty {
    text-align:center; padding:60px 20px;
    background:rgba(255,255,255,0.01);
    border:1px dashed rgba(255,255,255,0.08);
    border-radius:16px;
}
.tcc-empty-icon { font-size:48px; color:rgba(255,255,255,0.08); margin-bottom:20px; }
.tcc-empty h3 { margin:0; font-size:18px; font-weight:700; color:rgba(255,255,255,0.5); }
.tcc-empty p { margin:8px auto 0; font-size:13px; color:rgba(255,255,255,0.3); max-width:400px; line-height:1.6; }

/* ── Hidden ── */
.tcc-card-hidden { display:none !important; }

/* ── Animations ── */
@@keyframes tccPulse {
    0%, 100% { opacity:0.5; transform:scale(1); }
    50% { opacity:0; transform:scale(1.15); }
}
@@keyframes tccStatPulse {
    0%, 100% { opacity:1; transform:scale(1); }
    50% { opacity:0.5; transform:scale(1.5); }
}
@@keyframes tccDotPulse {
    0%, 100% { opacity:1; }
    50% { opacity:0.3; }
}

/* ── Responsive ── */
@@media (max-width: 1100px) {
    .tcc-stats { grid-template-columns:repeat(3, 1fr); }
}
@@media (max-width: 768px) {
    .tcc-hero { flex-direction:column; align-items:flex-start; gap:12px; }
    .tcc-stats { grid-template-columns:repeat(2, 1fr); }
    .tcc-filter-row { flex-direction:column; align-items:flex-start; gap:8px; }
    .tcc-card-top { flex-direction:column; }
    .tcc-card-actions { opacity:1; }
    .tcc-card-meta { gap:6px; }
}
</style>
@endpush

@push('scripts')
<script>
var _tccCatFilter = 'all';
var _tccStatusFilter = 'all';

function tccFilterCat(cat, btn) {
    _tccCatFilter = cat;
    var pills = document.querySelectorAll('#tccCatPills .tcc-pill');
    for (var i = 0; i < pills.length; i++) { pills[i].classList.remove('active'); }
    btn.classList.add('active');
    tccApplyFilters();
}

function tccFilterStatus(status, btn) {
    _tccStatusFilter = status;
    var pills = document.querySelectorAll('#tccStatusPills .tcc-pill');
    for (var i = 0; i < pills.length; i++) { pills[i].classList.remove('active'); }
    btn.classList.add('active');
    tccApplyFilters();
}

function tccApplyFilters() {
    var cards = document.querySelectorAll('.tcc-card');
    var searchVal = (document.getElementById('tccSearch').value || '').toLowerCase();
    var visibleCount = 0;

    for (var i = 0; i < cards.length; i++) {
        var card = cards[i];
        var cat = card.getAttribute('data-cat') || '';
        var status = card.getAttribute('data-status') || '';
        var overdue = card.getAttribute('data-overdue') === '1';
        var searchText = card.getAttribute('data-search') || '';

        var showCat = (_tccCatFilter === 'all' || cat === _tccCatFilter);
        var showStatus = true;
        if (_tccStatusFilter === 'overdue') {
            showStatus = overdue;
        } else if (_tccStatusFilter !== 'all') {
            showStatus = (status === _tccStatusFilter);
        }
        var showSearch = (!searchVal || searchText.indexOf(searchVal) !== -1);

        if (showCat && showStatus && showSearch) {
            card.classList.remove('tcc-card-hidden');
            visibleCount++;
        } else {
            card.classList.add('tcc-card-hidden');
        }
    }

    var noResults = document.getElementById('tccNoResults');
    var emptyState = document.getElementById('tccEmpty');
    if (noResults) {
        if (visibleCount === 0 && cards.length > 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
}
</script>
@endpush
