<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientCipcReturn extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_cipc_returns';

    protected $fillable = [
        'client_id', 'return_type_id', 'status_id',
        'filing_year', 'due_date', 'submission_date', 'approval_date',
        'reference_number', 'amount_due', 'amount_paid',
        'document_path', 'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_date' => 'date',
        'submission_date' => 'date',
        'approval_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function returnType()
    {
        return $this->belongsTo(NexcoreSystemCipcReturnType::class, 'return_type_id');
    }

    public function status()
    {
        return $this->belongsTo(NexcoreSystemReturnStatus::class, 'status_id');
    }
}
