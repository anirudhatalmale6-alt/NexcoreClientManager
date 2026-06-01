<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreGlChartOfAccount extends Model
{
    protected $table = 'cims_gl_chart_of_accounts_master';

    protected $fillable = [
        'company_id', 'account_code', 'segment1', 'segment2', 'segment3',
        'account_level', 'account_name', 'account_type', 'normal_balance',
        'vat_type', 'is_active', 'is_system', 'is_header',
        'description', 'parent_id', 'sars_link_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'is_header' => 'boolean',
        'account_level' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function sarsLink()
    {
        return $this->belongsTo(NexcoreSarsLinkMaster::class, 'sars_link_id');
    }

    public function scopeMainAccounts($query)
    {
        return $query->where('account_level', 1);
    }

    public function scopeSubAccounts($query)
    {
        return $query->where('account_level', 2);
    }

    public function scopeDetailAccounts($query)
    {
        return $query->where('account_level', 3);
    }
}
