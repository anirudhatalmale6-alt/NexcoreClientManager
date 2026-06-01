<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientPayeReturn extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_paye_returns';

    protected $fillable = [
        'client_id', 'return_type', 'tax_year', 'period', 'due_date',
        'submission_date', 'amount_due', 'amount_paid', 'reference_number',
        'status', 'document_path', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submission_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
