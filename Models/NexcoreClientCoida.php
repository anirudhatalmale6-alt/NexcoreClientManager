<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientCoida extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_coida';

    protected $fillable = [
        'client_id', 'registration_number', 'employer_number', 'assessment_year',
        'assessment_amount', 'payment_date', 'letter_good_standing_date',
        'letter_expiry_date', 'status', 'document_path', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'letter_good_standing_date' => 'date',
        'letter_expiry_date' => 'date',
        'assessment_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }
}
