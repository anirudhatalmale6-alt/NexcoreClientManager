<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientContact extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_contacts';

    protected $fillable = [
        'client_id', 'contact_type_id', 'title_id',
        'first_name', 'last_name', 'id_number', 'designation',
        'email', 'mobile_number', 'office_number', 'fax_number',
        'contact_photo', 'is_primary', 'is_active', 'notes',
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

    public function contactType()
    {
        return $this->belongsTo(NexcoreSystemContactType::class, 'contact_type_id');
    }

    public function title()
    {
        return $this->belongsTo(NexcoreSystemTitle::class, 'title_id');
    }

    public function getFullNameAttribute()
    {
        $t = $this->title ? $this->title->name . ' ' : '';
        return $t . $this->first_name . ' ' . $this->last_name;
    }
}
