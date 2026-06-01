<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientBank extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_banks';

    protected $fillable = [
        'client_id', 'bank_id', 'account_type_id', 'gl_account_id',
        'account_name', 'account_number', 'branch_code', 'swift_code',
        'account_label', 'is_primary', 'is_active', 'notes',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function bank()
    {
        return $this->belongsTo(\Modules\CIMS_PMPRO\Models\NexcorSystemBank::class, 'bank_id');
    }

    public function accountType()
    {
        return $this->belongsTo(NexcoreSystemAccountType::class, 'account_type_id');
    }

    public function glAccount()
    {
        return $this->belongsTo(NexcoreGlChartOfAccount::class, 'gl_account_id');
    }
}
