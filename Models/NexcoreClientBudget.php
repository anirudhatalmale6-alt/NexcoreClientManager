<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientBudget extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_budgets';

    protected $fillable = [
        'client_id', 'account_id', 'period_year', 'period_month',
        'budget_amount', 'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'period_year' => 'integer',
        'period_month' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function account()
    {
        return $this->belongsTo(NexcoreClientAccount::class, 'account_id');
    }
}
