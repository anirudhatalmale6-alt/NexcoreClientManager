<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSystemTitle extends Model
{
    protected $table = 'nexcore_system_titles';
    protected $fillable = ['name', 'is_active', 'created_by', 'updated_by'];
    protected $casts = ['is_active' => 'boolean'];
}
