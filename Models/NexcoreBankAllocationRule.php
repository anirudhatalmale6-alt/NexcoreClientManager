<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreBankAllocationRule extends Model
{
    protected $table = 'cims_gl_bank_allocation_rules_master';

    protected $fillable = [
        'company_id', 'keyword', 'account_id', 'vat_type', 'priority', 'match_count', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(NexcoreGlChartOfAccount::class, 'account_id');
    }
}
