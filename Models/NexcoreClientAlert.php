<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientAlert extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_alerts';

    protected $fillable = [
        'client_id', 'alert_type', 'title', 'description', 'severity',
        'due_date', 'is_read', 'is_dismissed', 'related_module', 'related_id',
        'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'is_read'      => 'boolean',
        'is_dismissed' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
