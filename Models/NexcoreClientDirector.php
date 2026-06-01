<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientDirector extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_directors';

    protected $fillable = [
        'client_id', 'director_type_id', 'title_id',
        'first_name', 'last_name', 'id_number', 'passport_number',
        'nationality', 'date_of_birth', 'email', 'mobile_number', 'office_number',
        'residential_address', 'appointment_date', 'resignation_date',
        'shareholding_percentage', 'director_photo', 'is_active', 'notes',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
        'appointment_date' => 'date',
        'resignation_date' => 'date',
        'shareholding_percentage' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function directorType()
    {
        return $this->belongsTo(NexcoreSystemDirectorType::class, 'director_type_id');
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
