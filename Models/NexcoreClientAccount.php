<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientAccount extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_accounts';

    protected $fillable = [
        'client_id', 'account_code', 'account_name', 'account_type',
        'sub_type', 'parent_id', 'description', 'opening_balance',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function parent()
    {
        return $this->belongsTo(NexcoreClientAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NexcoreClientAccount::class, 'parent_id');
    }

    public function journalLines()
    {
        return $this->hasMany(NexcoreClientJournalLine::class, 'account_id');
    }

    public function budgets()
    {
        return $this->hasMany(NexcoreClientBudget::class, 'account_id');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }
}
