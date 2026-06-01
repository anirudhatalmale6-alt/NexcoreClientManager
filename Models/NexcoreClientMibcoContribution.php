<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientMibcoContribution extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_mibco_contributions';

    protected $fillable = [
        'client_id',
        'employee_id',
        'pay_period_id',
        'pension_employee',
        'pension_employer',
        'provident_employee',
        'provident_employer',
        'death_benefit',
        'funeral_benefit',
        'sick_pay_fund',
        'holiday_fund',
        'total_employee',
        'total_employer',
        'total_contribution',
        'status',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'pension_employee' => 'decimal:2',
        'pension_employer' => 'decimal:2',
        'provident_employee' => 'decimal:2',
        'provident_employer' => 'decimal:2',
        'death_benefit' => 'decimal:2',
        'funeral_benefit' => 'decimal:2',
        'sick_pay_fund' => 'decimal:2',
        'holiday_fund' => 'decimal:2',
        'total_employee' => 'decimal:2',
        'total_employer' => 'decimal:2',
        'total_contribution' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function employee()
    {
        return $this->belongsTo(NexcoreClientEmployee::class, 'employee_id');
    }

    public function payPeriod()
    {
        return $this->belongsTo(NexcoreClientPayPeriod::class, 'pay_period_id');
    }
}
