<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientPayslip extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_payslips';

    protected $fillable = [
        'client_id', 'employee_id', 'pay_period_id', 'basic_salary', 'gross_pay',
        'total_deductions', 'net_pay', 'employer_cost', 'paye', 'uif_employee',
        'uif_employer', 'sdl', 'overtime_hours', 'overtime_amount', 'status',
        'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'basic_salary'     => 'decimal:2',
        'gross_pay'        => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay'          => 'decimal:2',
        'employer_cost'    => 'decimal:2',
        'paye'             => 'decimal:2',
        'uif_employee'     => 'decimal:2',
        'uif_employer'     => 'decimal:2',
        'sdl'              => 'decimal:2',
        'overtime_hours'   => 'decimal:2',
        'overtime_amount'  => 'decimal:2',
        'is_active'        => 'boolean',
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

    public function earnings()
    {
        return $this->hasMany(NexcoreClientPayslipEarning::class, 'payslip_id');
    }

    public function deductions()
    {
        return $this->hasMany(NexcoreClientPayslipDeduction::class, 'payslip_id');
    }
}
