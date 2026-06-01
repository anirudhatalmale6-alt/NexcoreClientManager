<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientTask extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_tasks';

    protected $fillable = [
        'client_id', 'title', 'description', 'category', 'priority', 'task_status',
        'assigned_to', 'due_date', 'completed_date', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'due_date'       => 'date',
        'completed_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
