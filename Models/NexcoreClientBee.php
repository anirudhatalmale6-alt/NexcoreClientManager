<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientBee extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_bee';

    protected $fillable = [
        'client_id', 'level', 'certificate_number', 'assessor', 'verification_agency',
        'issued_date', 'expiry_date', 'status', 'document_path', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
