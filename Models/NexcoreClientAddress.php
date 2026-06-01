<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientAddress extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_addresses';

    protected $fillable = [
        'client_id',
        'address_type_id',
        'address_label',
        'address_line_1',
        'address_line_2',
        'suburb',
        'city',
        'province_id',
        'postal_code',
        'country_id',
        'latitude',
        'longitude',
        'is_primary',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function addressType()
    {
        return $this->belongsTo(NexcoreSystemAddressType::class, 'address_type_id');
    }

    public function province()
    {
        return $this->belongsTo(NexcoreSystemProvince::class, 'province_id');
    }

    public function country()
    {
        return $this->belongsTo(NexcoreSystemCountry::class, 'country_id');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->suburb,
            $this->city,
            $this->province ? $this->province->name : null,
            $this->postal_code,
        ]);
        return implode(', ', $parts);
    }
}
