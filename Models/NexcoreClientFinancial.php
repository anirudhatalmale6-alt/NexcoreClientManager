<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientFinancial extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_financials';

    protected $fillable = [
        'client_id', 'financial_type_id', 'status_id',
        'financial_year', 'period_start', 'period_end',
        'prepared_by', 'reviewed_by', 'approved_date',
        'document_path', 'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'period_start' => 'date',
        'period_end' => 'date',
        'approved_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function financialType()
    {
        return $this->belongsTo(NexcoreSystemFinancialType::class, 'financial_type_id');
    }

    public function status()
    {
        return $this->belongsTo(NexcoreSystemReturnStatus::class, 'status_id');
    }
}
