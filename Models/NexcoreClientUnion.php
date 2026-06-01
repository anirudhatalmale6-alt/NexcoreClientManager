<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientUnion extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_unions';

    protected $fillable = [
        'client_id', 'union_name', 'council_name', 'membership_number',
        'registration_date', 'expiry_date', 'contribution_amount',
        'contribution_frequency', 'last_payment_date', 'next_payment_date',
        'status', 'document_path', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'expiry_date' => 'date',
        'last_payment_date' => 'date',
        'next_payment_date' => 'date',
        'contribution_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
