<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSystemCountry extends Model
{
    protected $table = 'nexcore_system_countries';

    protected $fillable = ['name', 'code', 'phone_code', 'is_active', 'created_by', 'updated_by'];

    protected $casts = ['is_active' => 'boolean'];
}
