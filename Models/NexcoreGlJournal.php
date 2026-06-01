<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreGlJournal extends Model
{
    protected $table = 'cims_gl_journal_master_header';

    protected $fillable = [
        'company_id', 'journal_number', 'journal_date', 'period_id',
        'reference', 'description', 'source', 'status',
        'total_debit', 'total_credit', 'reversal_of', 'notes',
        'attachment_path', 'attachment_name',
        'created_by', 'posted_by', 'posted_at',
    ];

    protected $casts = [
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'journal_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'company_id');
    }

    public function lines()
    {
        return $this->hasMany(NexcoreGlJournalLine::class, 'journal_id');
    }
}
