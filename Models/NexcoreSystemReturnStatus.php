<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSystemReturnStatus extends Model
{
    protected $table = 'nexcore_system_return_statuses';
    protected $fillable = ['name', 'color', 'sort_order', 'is_active', 'created_by', 'updated_by'];
    protected $casts = ['is_active' => 'boolean'];
}
