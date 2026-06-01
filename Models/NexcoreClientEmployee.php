<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientEmployee extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_employees';

    protected $fillable = [
        'client_id', 'employee_number', 'title', 'first_name', 'last_name',
        'id_number', 'tax_number', 'date_of_birth', 'gender', 'position',
        'department', 'start_date', 'termination_date', 'salary_type',
        'basic_salary', 'pay_frequency', 'bank_name', 'bank_branch_code',
        'bank_account_number', 'bank_account_type', 'email', 'phone',
        'address', 'employment_status', 'notes', 'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'date_of_birth'    => 'date',
        'start_date'       => 'date',
        'termination_date' => 'date',
        'basic_salary'     => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
