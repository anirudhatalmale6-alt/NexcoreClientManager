<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;

class DashboardPanelController extends Controller
{
    protected $panels = [
        'business-operations' => [
            'title' => 'Business Operations Hub',
            'icon' => 'fa-building',
            'color' => '#059669',
            'gradient_from' => 'rgba(5,150,105,0.25)',
            'gradient_to' => 'rgba(6,182,212,0.15)',
            'border_color' => 'rgba(5,150,105,0.5)',
            'description' => 'Centralised operational command centre for task queues, activity feeds, operational KPIs and team workload monitoring.',
            'features' => [
                ['icon' => 'fa-tasks', 'label' => 'Task Queue Management'],
                ['icon' => 'fa-stream', 'label' => 'Activity Feed'],
                ['icon' => 'fa-chart-bar', 'label' => 'Operational KPIs'],
                ['icon' => 'fa-users-cog', 'label' => 'Team Workload'],
            ],
        ],
        'compliance-command' => [
            'title' => 'Compliance Command Centre',
            'icon' => 'fa-shield-alt',
            'color' => '#8b5cf6',
            'gradient_from' => 'rgba(139,92,246,0.25)',
            'gradient_to' => 'rgba(167,139,250,0.15)',
            'border_color' => 'rgba(139,92,246,0.5)',
            'description' => 'Real-time compliance tracking with document expiry monitoring, regulatory deadlines, compliance checklists and full audit trails.',
            'features' => [
                ['icon' => 'fa-calendar-check', 'label' => 'Regulatory Deadlines'],
                ['icon' => 'fa-file-signature', 'label' => 'Document Expiry Tracking'],
                ['icon' => 'fa-clipboard-check', 'label' => 'Compliance Checklists'],
                ['icon' => 'fa-history', 'label' => 'Audit Trail Reports'],
            ],
        ],
        'revenue-billing' => [
            'title' => 'Revenue & Billing Intelligence',
            'icon' => 'fa-coins',
            'color' => '#f59e0b',
            'gradient_from' => 'rgba(245,158,11,0.25)',
            'gradient_to' => 'rgba(234,179,8,0.15)',
            'border_color' => 'rgba(245,158,11,0.5)',
            'description' => 'Revenue analytics and billing intelligence with trend analysis, cash flow projections, billing cycles and outstanding invoice aging.',
            'features' => [
                ['icon' => 'fa-chart-line', 'label' => 'Revenue Trends'],
                ['icon' => 'fa-money-bill-wave', 'label' => 'Cash Flow Projections'],
                ['icon' => 'fa-file-invoice-dollar', 'label' => 'Invoice Aging Analysis'],
                ['icon' => 'fa-sync-alt', 'label' => 'Billing Cycle Tracking'],
            ],
        ],
        'business-growth' => [
            'title' => 'Business Growth Insights',
            'icon' => 'fa-rocket',
            'color' => '#06b6d4',
            'gradient_from' => 'rgba(6,182,212,0.25)',
            'gradient_to' => 'rgba(59,130,246,0.15)',
            'border_color' => 'rgba(6,182,212,0.5)',
            'description' => 'Strategic growth intelligence with client acquisition trends, pipeline metrics, growth rate analysis and forward-looking business projections.',
            'features' => [
                ['icon' => 'fa-user-plus', 'label' => 'Client Acquisition Trends'],
                ['icon' => 'fa-funnel-dollar', 'label' => 'Pipeline Metrics'],
                ['icon' => 'fa-arrow-trend-up', 'label' => 'Growth Rate Analysis'],
                ['icon' => 'fa-bullseye', 'label' => 'Strategic Projections'],
            ],
        ],
        'executive-overview' => [
            'title' => 'Executive Client Overview',
            'icon' => 'fa-crown',
            'color' => '#ec4899',
            'gradient_from' => 'rgba(236,72,153,0.25)',
            'gradient_to' => 'rgba(168,85,247,0.15)',
            'border_color' => 'rgba(236,72,153,0.5)',
            'description' => 'Board-level executive snapshot with portfolio health scores, top clients by revenue, risk flags and relationship scoring across the client base.',
            'features' => [
                ['icon' => 'fa-heartbeat', 'label' => 'Portfolio Health Scores'],
                ['icon' => 'fa-trophy', 'label' => 'Top Clients by Revenue'],
                ['icon' => 'fa-exclamation-triangle', 'label' => 'Risk Flags & Alerts'],
                ['icon' => 'fa-star', 'label' => 'Relationship Scoring'],
            ],
        ],
        'task-add' => [
            'title' => 'Add Task',
            'icon' => 'fa-plus-circle',
            'color' => '#10b981',
            'gradient_from' => 'rgba(16,185,129,0.25)',
            'gradient_to' => 'rgba(5,150,105,0.15)',
            'border_color' => 'rgba(16,185,129,0.5)',
            'description' => 'Quick task creation with priority assignment, due dates, assignees, categories and attachment support.',
            'features' => [
                ['icon' => 'fa-flag', 'label' => 'Priority Assignment'],
                ['icon' => 'fa-calendar-day', 'label' => 'Due Date Scheduling'],
                ['icon' => 'fa-user-tag', 'label' => 'Assignee Selection'],
                ['icon' => 'fa-paperclip', 'label' => 'File Attachments'],
            ],
        ],
        'task-kanban' => [
            'title' => 'Kanban View',
            'icon' => 'fa-columns',
            'color' => '#6366f1',
            'gradient_from' => 'rgba(99,102,241,0.25)',
            'gradient_to' => 'rgba(139,92,246,0.15)',
            'border_color' => 'rgba(99,102,241,0.5)',
            'description' => 'Visual drag-and-drop board for managing tasks across workflow stages with real-time status tracking.',
            'features' => [
                ['icon' => 'fa-th-list', 'label' => 'Drag & Drop Columns'],
                ['icon' => 'fa-exchange-alt', 'label' => 'Status Transitions'],
                ['icon' => 'fa-filter', 'label' => 'Priority Filtering'],
                ['icon' => 'fa-eye', 'label' => 'Real-Time Updates'],
            ],
        ],
        'reminders' => [
            'title' => 'Reminders',
            'icon' => 'fa-bell',
            'color' => '#f59e0b',
            'gradient_from' => 'rgba(245,158,11,0.25)',
            'gradient_to' => 'rgba(251,191,36,0.15)',
            'border_color' => 'rgba(245,158,11,0.5)',
            'description' => 'Smart reminder system for deadlines, follow-ups, compliance dates and custom alerts with priority levels and notification scheduling.',
            'features' => [
                ['icon' => 'fa-clock', 'label' => 'Scheduled Reminders'],
                ['icon' => 'fa-exclamation-circle', 'label' => 'Priority Levels'],
                ['icon' => 'fa-redo', 'label' => 'Recurring Reminders'],
                ['icon' => 'fa-check-double', 'label' => 'Completion Tracking'],
            ],
        ],
        'appointments' => [
            'title' => 'Appointments',
            'icon' => 'fa-calendar-check',
            'color' => '#0891b2',
            'gradient_from' => 'rgba(8,145,178,0.25)',
            'gradient_to' => 'rgba(6,182,212,0.15)',
            'border_color' => 'rgba(8,145,178,0.5)',
            'description' => 'Client appointment scheduling and management with calendar views, availability tracking and meeting history.',
            'features' => [
                ['icon' => 'fa-calendar-alt', 'label' => 'Calendar View'],
                ['icon' => 'fa-user-clock', 'label' => 'Availability Tracking'],
                ['icon' => 'fa-history', 'label' => 'Meeting History'],
                ['icon' => 'fa-envelope', 'label' => 'Appointment Notifications'],
            ],
        ],
    ];

    public function show(Request $request, $clientId, $panel)
    {
        $client = NexcoreClient::findOrFail($clientId);

        if (!isset($this->panels[$panel])) {
            abort(404);
        }

        $config = $this->panels[$panel];

        return view('nexcore_client_manager::dashboards.under-construction', compact('client', 'config'));
    }
}
