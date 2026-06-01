<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreSystemDirectorType extends Model
{
    protected $table = 'nexcore_system_director_types';
    protected $fillable = ['name', 'description', 'is_active', 'created_by', 'updated_by'];
    protected $casts = ['is_active' => 'boolean'];
}
