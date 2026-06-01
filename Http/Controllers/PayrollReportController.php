<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientEmployee;
use Modules\NexcoreClientManager\Models\NexcoreClientPayPeriod;
use Modules\NexcoreClientManager\Models\NexcoreClientPayslip;
use Modules\NexcoreClientManager\Models\NexcoreClientMibcoContribution;

class PayrollReportController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $periods = NexcoreClientPayPeriod::where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->get();

        $defaultPeriodId = $periods->first()->id ?? null;
        if (!request('period_id') && $defaultPeriodId) {
            $periodWithPayslips = NexcoreClientPayslip::where('client_id', $clientId)
                ->select('pay_period_id')
                ->groupBy('pay_period_id')
                ->orderByRaw('count(*) desc')
                ->first();
            if ($periodWithPayslips) {
                $defaultPeriodId = $periodWithPayslips->pay_period_id;
            }
        }
        $selectedPeriodId = request('period_id', $defaultPeriodId);

        $payslips = collect();
        $mibco    = collect();
        $employees = NexcoreClientEmployee::where('client_id', $clientId)->get();

        if ($selectedPeriodId) {
            $payslips = NexcoreClientPayslip::where('client_id', $clientId)
                ->where('pay_period_id', $selectedPeriodId)
                ->with('employee')
                ->get();

            $mibco = NexcoreClientMibcoContribution::where('client_id', $clientId)
                ->where('pay_period_id', $selectedPeriodId)
                ->with('employee')
                ->get();
        }

        $summaryTotals = [
            'basic_salary'     => $payslips->sum('basic_salary'),
            'gross_pay'        => $payslips->sum('gross_pay'),
            'paye'             => $payslips->sum('paye'),
            'uif_employee'     => $payslips->sum('uif_employee'),
            'uif_employer'     => $payslips->sum('uif_employer'),
            'sdl'              => $payslips->sum('sdl'),
            'total_deductions' => $payslips->sum('total_deductions'),
            'net_pay'          => $payslips->sum('net_pay'),
            'employer_cost'    => $payslips->sum('employer_cost'),
            'overtime_amount'  => $payslips->sum('overtime_amount'),
        ];

        $mibcoTotals = [
            'pension_employee'    => $mibco->sum('pension_employee'),
            'pension_employer'    => $mibco->sum('pension_employer'),
            'provident_employee'  => $mibco->sum('provident_employee'),
            'provident_employer'  => $mibco->sum('provident_employer'),
            'death_benefit'       => $mibco->sum('death_benefit'),
            'funeral_benefit'     => $mibco->sum('funeral_benefit'),
            'sick_pay_fund'       => $mibco->sum('sick_pay_fund'),
            'holiday_fund'        => $mibco->sum('holiday_fund'),
            'total_employee'      => $mibco->sum('total_employee'),
            'total_employer'      => $mibco->sum('total_employer'),
            'total_contribution'  => $mibco->sum('total_contribution'),
        ];

        $selectedPeriod = $periods->firstWhere('id', $selectedPeriodId);

        return view('nexcore_client_manager::payroll.reports.index', compact(
            'client', 'periods', 'selectedPeriodId', 'selectedPeriod',
            'payslips', 'mibco', 'employees',
            'summaryTotals', 'mibcoTotals'
        ));
    }
}
