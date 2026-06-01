<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSystemSarsReturnType extends Model
{
    protected $table = 'nexcore_system_sars_return_types';
    protected $fillable = ['name', 'code', 'description', 'is_active', 'created_by', 'updated_by'];
    protected $casts = ['is_active' => 'boolean'];
}
