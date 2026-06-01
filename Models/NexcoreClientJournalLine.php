<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreClientJournalLine extends Model
{
    public $timestamps = true;

    protected $table = 'nexcore_client_journal_lines';

    protected $fillable = [
        'journal_id', 'account_id', 'description', 'debit', 'credit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journal()
    {
        return $this->belongsTo(NexcoreClientJournal::class, 'journal_id');
    }

    public function account()
    {
        return $this->belongsTo(NexcoreClientAccount::class, 'account_id');
    }
}
