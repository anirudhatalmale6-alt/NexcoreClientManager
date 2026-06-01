<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientSarsReturn extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_sars_returns';

    protected $fillable = [
        'client_id', 'return_type_id', 'status_id',
        'tax_year', 'tax_period',
        'due_date', 'submission_date', 'assessment_date',
        'payment_due_date', 'payment_date',
        'reference_number', 'amount_due', 'amount_paid',
        'penalty_amount', 'interest_amount',
        'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_date' => 'date',
        'submission_date' => 'date',
        'assessment_date' => 'date',
        'payment_due_date' => 'date',
        'payment_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function returnType()
    {
        return $this->belongsTo(NexcoreSystemSarsReturnType::class, 'return_type_id');
    }

    public function status()
    {
        return $this->belongsTo(NexcoreSystemReturnStatus::class, 'status_id');
    }
}
