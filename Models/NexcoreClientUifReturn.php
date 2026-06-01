<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientUifReturn extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_uif_returns';

    protected $fillable = [
        'client_id', 'uif_number', 'period', 'declaration_date', 'due_date',
        'amount_due', 'amount_paid', 'reference_number', 'status', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'declaration_date' => 'date',
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
