<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientPayPeriod extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_pay_periods';

    protected $fillable = [
        'client_id', 'name', 'pay_frequency', 'period_start', 'period_end',
        'payment_date', 'status', 'total_gross', 'total_deductions', 'total_net',
        'total_employer_cost', 'notes', 'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'period_start'        => 'date',
        'period_end'          => 'date',
        'payment_date'        => 'date',
        'total_gross'         => 'decimal:2',
        'total_deductions'    => 'decimal:2',
        'total_net'           => 'decimal:2',
        'total_employer_cost' => 'decimal:2',
        'is_active'           => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function payslips()
    {
        return $this->hasMany(NexcoreClientPayslip::class, 'pay_period_id');
    }
}
