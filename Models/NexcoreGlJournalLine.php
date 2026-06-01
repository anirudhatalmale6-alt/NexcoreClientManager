<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreGlJournalLine extends Model
{
    protected $table = 'cims_gl_journal_header_linked_entries';

    protected $fillable = [
        'journal_id', 'account_id', 'description',
        'debit_amount', 'credit_amount',
        'vat_amount', 'vat_type', 'ma_hidden', 'note', 'line_order',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'ma_hidden' => 'boolean',
    ];

    public function journal()
    {
        return $this->belongsTo(NexcoreGlJournal::class, 'journal_id');
    }

    public function account()
    {
        return $this->belongsTo(NexcoreGlChartOfAccount::class, 'account_id');
    }
}
