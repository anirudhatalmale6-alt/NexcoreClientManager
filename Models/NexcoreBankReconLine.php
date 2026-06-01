<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreBankReconLine extends Model
{
    protected $table = 'cims_gl_bank_recon_header_linked_lines';

    protected $fillable = [
        'reconciliation_id', 'transaction_id', 'journal_line_id',
        'source', 'transaction_date', 'description', 'amount', 'is_matched',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_matched' => 'boolean',
    ];

    public function reconciliation()
    {
        return $this->belongsTo(NexcoreBankReconciliation::class, 'reconciliation_id');
    }

    public function transaction()
    {
        return $this->belongsTo(NexcoreBankTransaction::class, 'transaction_id');
    }
}
