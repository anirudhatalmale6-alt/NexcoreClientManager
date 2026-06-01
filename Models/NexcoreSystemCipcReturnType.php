<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSystemCipcReturnType extends Model
{
    protected $table = 'nexcore_system_cipc_return_types';
    protected $fillable = ['name', 'code', 'description', 'is_active', 'created_by', 'updated_by'];
    protected $casts = ['is_active' => 'boolean'];
}
