<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientMeeting extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_meetings';

    protected $fillable = [
        'client_id', 'meeting_type', 'title', 'description',
        'meeting_date', 'meeting_time', 'duration_minutes', 'location',
        'attendees', 'outcome', 'follow_up_date', 'follow_up_notes',
        'meeting_status', 'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'meeting_date'     => 'date',
        'follow_up_date'   => 'date',
        'duration_minutes' => 'integer',
        'is_active'        => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
