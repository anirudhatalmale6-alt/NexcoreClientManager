<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientSdlReturn extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_sdl_returns';

    protected $fillable = [
        'client_id', 'sdl_number', 'period', 'tax_year', 'due_date',
        'submission_date', 'levy_amount', 'amount_paid', 'reference_number',
        'status', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submission_date' => 'date',
        'levy_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
