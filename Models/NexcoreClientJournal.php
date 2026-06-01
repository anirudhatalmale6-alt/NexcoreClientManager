<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientJournal extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_journals';

    protected $fillable = [
        'client_id', 'journal_number', 'journal_date', 'reference',
        'narration', 'period_year', 'period_month', 'status',
        'total_amount', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_amount' => 'decimal:2',
        'journal_date' => 'date',
        'period_year' => 'integer',
        'period_month' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function lines()
    {
        return $this->hasMany(NexcoreClientJournalLine::class, 'journal_id');
    }
}
