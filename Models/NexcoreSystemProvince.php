<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSystemProvince extends Model
{
    protected $table = 'nexcore_system_provinces';

    protected $fillable = ['name', 'code', 'is_active', 'created_by', 'updated_by'];

    protected $casts = ['is_active' => 'boolean'];
}
