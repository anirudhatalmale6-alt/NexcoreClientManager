<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreClientAuditTrail extends Model
{
    protected $table = 'nexcore_client_audit_trail';

    // Only created_at exists — no updated_at column on this table
    public $timestamps = false;

    protected $fillable = [
        'client_id', 'user_id', 'user_name', 'action', 'module',
        'record_id', 'description', 'old_values', 'new_values',
        'ip_address', 'created_at',
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'created_at'  => 'datetime',
    ];

    // -----------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    // -----------------------------------------------------------------
    // Static helper — call from any controller / observer
    // -----------------------------------------------------------------

    /**
     * Log an audit event.
     *
     * @param  int|null   $clientId
     * @param  string     $action      created | updated | deleted
     * @param  string     $module      clients | addresses | contacts | banking | directors | sars | cipc | financials | documents | tasks | meetings | alerts
     * @param  int|null   $recordId
     * @param  string     $description Human-readable summary of what changed
     * @param  array|null $oldValues   Previous field values (before change)
     * @param  array|null $newValues   New field values (after change)
     * @return static
     */
    public static function log($clientId, $action, $module, $recordId, $description, $oldValues = null, $newValues = null)
    {
        return static::create([
            'client_id'   => $clientId,
            'user_id'     => auth()->id(),
            'user_name'   => auth()->user() ? auth()->user()->name : 'System',
            'action'      => $action,
            'module'      => $module,
            'record_id'   => $recordId,
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
        ]);
    }
}
