<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientTask;

class TaskController extends Controller
{
    protected array $priorities = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    protected array $taskStatuses = [
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
    ];

    protected array $categories = [
        'sars'       => 'SARS',
        'cipc'       => 'CIPC',
        'coida'      => 'COIDA',
        'compliance' => 'Compliance',
        'birthdays'  => 'Birthdays',
        'events'     => 'Events',
        'general'    => 'General',
    ];

    protected array $categoryIcons = [
        'sars'       => 'fa-file-invoice-dollar',
        'cipc'       => 'fa-clipboard-check',
        'coida'      => 'fa-hard-hat',
        'compliance' => 'fa-shield-alt',
        'birthdays'  => 'fa-birthday-cake',
        'events'     => 'fa-calendar-day',
        'general'    => 'fa-tasks',
    ];

    protected array $categoryColors = [
        'sars'       => '#ef4444',
        'cipc'       => '#8b5cf6',
        'coida'      => '#f59e0b',
        'compliance' => '#06b6d4',
        'birthdays'  => '#ec4899',
        'events'     => '#3b82f6',
        'general'    => '#94a3b8',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        if (!Schema::hasColumn('nexcore_client_tasks', 'category')) {
            Schema::table('nexcore_client_tasks', function ($table) {
                $table->string('category', 50)->default('general')->after('task_status');
            });
        }

        $tasks = NexcoreClientTask::where('client_id', $clientId)
            ->orderByRaw('due_date IS NULL ASC')
            ->orderBy('due_date', 'asc')
            ->orderByDesc('created_at')
            ->get();

        $priorities     = $this->priorities;
        $taskStatuses   = $this->taskStatuses;
        $categories     = $this->categories;
        $categoryIcons  = $this->categoryIcons;
        $categoryColors = $this->categoryColors;

        return view('nexcore_client_manager::tasks.index', compact(
            'client', 'tasks', 'priorities', 'taskStatuses',
            'categories', 'categoryIcons', 'categoryColors'
        ));
    }

    public function create($clientId)
    {
        $client         = NexcoreClient::findOrFail($clientId);
        $priorities     = $this->priorities;
        $taskStatuses   = $this->taskStatuses;
        $categories     = $this->categories;
        $categoryIcons  = $this->categoryIcons;
        $categoryColors = $this->categoryColors;

        return view('nexcore_client_manager::tasks.form', compact(
            'client', 'priorities', 'taskStatuses',
            'categories', 'categoryIcons', 'categoryColors'
        ));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|in:sars,cipc,coida,compliance,birthdays,events,general',
            'priority'       => 'required|in:low,medium,high,urgent',
            'task_status'    => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to'    => 'nullable|string|max:255',
            'due_date'       => 'nullable|date',
            'completed_date' => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        NexcoreClientTask::create(array_merge(
            $request->only([
                'title', 'description', 'category', 'priority', 'task_status',
                'assigned_to', 'due_date', 'completed_date', 'notes',
            ]),
            [
                'client_id'  => $clientId,
                'is_active'  => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        ));

        return redirect()->route('nexcore.clients.show.tasks', $clientId)
            ->with('success', 'Task added successfully.');
    }

    public function edit($clientId, $taskId)
    {
        $client         = NexcoreClient::findOrFail($clientId);
        $task           = NexcoreClientTask::where('client_id', $clientId)->findOrFail($taskId);
        $priorities     = $this->priorities;
        $taskStatuses   = $this->taskStatuses;
        $categories     = $this->categories;
        $categoryIcons  = $this->categoryIcons;
        $categoryColors = $this->categoryColors;

        return view('nexcore_client_manager::tasks.form', compact(
            'client', 'task', 'priorities', 'taskStatuses',
            'categories', 'categoryIcons', 'categoryColors'
        ));
    }

    public function update(Request $request, $clientId, $taskId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $task   = NexcoreClientTask::where('client_id', $clientId)->findOrFail($taskId);

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|in:sars,cipc,coida,compliance,birthdays,events,general',
            'priority'       => 'required|in:low,medium,high,urgent',
            'task_status'    => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to'    => 'nullable|string|max:255',
            'due_date'       => 'nullable|date',
            'completed_date' => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'description', 'category', 'priority', 'task_status',
            'assigned_to', 'due_date', 'completed_date', 'notes',
        ]);

        if ($request->task_status === 'completed' && empty($data['completed_date']) && !$task->completed_date) {
            $data['completed_date'] = Carbon::today()->format('Y-m-d');
        }

        $data['updated_by'] = auth()->id();

        $task->update($data);

        return redirect()->route('nexcore.clients.show.tasks', $clientId)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy($clientId, $taskId)
    {
        $task = NexcoreClientTask::where('client_id', $clientId)->findOrFail($taskId);
        $task->delete();

        return redirect()->route('nexcore.clients.show.tasks', $clientId)
            ->with('success', 'Task deleted successfully.');
    }
}
