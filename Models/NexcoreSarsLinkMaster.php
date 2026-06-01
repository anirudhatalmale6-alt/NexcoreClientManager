<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSarsLinkMaster extends Model
{
    protected $table = 'nexcore_sars_link_master';

    protected $fillable = [
        'sars_code', 'sars_description', 'sars_section',
        'return_type', 'line_number', 'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function accounts()
    {
        return $this->hasMany(NexcoreGlChartOfAccount::class, 'sars_link_id');
    }

    public function templateItems()
    {
        return $this->hasMany(NexcoreAccountTemplateItem::class, 'sars_link_id');
    }
}
