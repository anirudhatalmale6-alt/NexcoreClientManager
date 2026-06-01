<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreClientPayslipEarning extends Model
{
    protected $table = 'nexcore_client_payslip_earnings';

    protected $fillable = [
        'payslip_id', 'earning_type', 'amount', 'is_taxable',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'is_taxable' => 'boolean',
    ];

    public function payslip()
    {
        return $this->belongsTo(NexcoreClientPayslip::class, 'payslip_id');
    }
}
