<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreClientPayslipDeduction extends Model
{
    protected $table = 'nexcore_client_payslip_deductions';

    protected $fillable = [
        'payslip_id', 'deduction_type', 'amount', 'is_statutory',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'is_statutory' => 'boolean',
    ];

    public function payslip()
    {
        return $this->belongsTo(NexcoreClientPayslip::class, 'payslip_id');
    }
}
