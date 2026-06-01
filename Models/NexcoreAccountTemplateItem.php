<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreAccountTemplateItem extends Model
{
    protected $table = 'nexcore_account_template_items';

    protected $fillable = [
        'template_id', 'account_code', 'segment1', 'segment2', 'segment3',
        'account_level', 'account_name', 'account_type', 'normal_balance',
        'vat_type', 'is_system', 'is_header', 'description', 'sars_link_id',
    ];

    protected $casts = [
        'account_level' => 'integer',
        'is_system' => 'boolean',
        'is_header' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(NexcoreAccountTemplate::class, 'template_id');
    }

    public function sarsLink()
    {
        return $this->belongsTo(NexcoreSarsLinkMaster::class, 'sars_link_id');
    }
}
