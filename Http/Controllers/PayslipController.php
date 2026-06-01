<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientEmployee;
use Modules\NexcoreClientManager\Models\NexcoreClientPayPeriod;
use Modules\NexcoreClientManager\Models\NexcoreClientPayslip;

class PayslipController extends Controller
{
    protected array $statuses = [
        'draft'     => 'Draft',
        'processed' => 'Processed',
        'finalised' => 'Finalised',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $payslips = NexcoreClientPayslip::where('client_id', $clientId)
            ->with(['employee', 'payPeriod'])
            ->orderByDesc('pay_period_id')
            ->orderBy('employee_id')
            ->get()
            ->sortBy(fn($p) => optional($p->employee)->last_name ?? '');

        $statuses = $this->statuses;

        return view('nexcore_client_manager::payroll.payslips.index', compact('client', 'payslips', 'statuses'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $employees = NexcoreClientEmployee::where('client_id', $clientId)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $periods  = NexcoreClientPayPeriod::where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->get();

        $statuses = $this->statuses;

        return view('nexcore_client_manager::payroll.payslips.form', compact('client', 'employees', 'periods', 'statuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'employee_id'      => 'required|exists:nexcore_client_employees,id',
            'pay_period_id'    => 'required|exists:nexcore_client_pay_periods,id',
            'basic_salary'     => 'required|numeric|min:0',
            'gross_pay'        => 'required|numeric|min:0',
            'total_deductions' => 'required|numeric|min:0',
            'net_pay'          => 'required|numeric|min:0',
            'employer_cost'    => 'required|numeric|min:0',
            'paye'             => 'nullable|numeric|min:0',
            'uif_employee'     => 'nullable|numeric|min:0',
            'uif_employer'     => 'nullable|numeric|min:0',
            'sdl'              => 'nullable|numeric|min:0',
            'overtime_hours'   => 'nullable|numeric|min:0',
            'overtime_amount'  => 'nullable|numeric|min:0',
            'status'           => 'required|in:draft,processed,finalised',
            'notes'            => 'nullable|string',
        ]);

        NexcoreClientPayslip::create(array_merge(
            $request->only([
                'employee_id', 'pay_period_id', 'basic_salary', 'gross_pay',
                'total_deductions', 'net_pay', 'employer_cost', 'paye',
                'uif_employee', 'uif_employer', 'sdl', 'overtime_hours',
                'overtime_amount', 'status', 'notes',
            ]),
            [
                'client_id'  => $clientId,
                'is_active'  => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        ));

        return redirect()->route('nexcore.clients.show.payroll.payslips', $clientId)
            ->with('success', 'Payslip created successfully.');
    }

    public function edit($clientId, $payslipId)
    {
        $client   = NexcoreClient::findOrFail($clientId);
        $payslip  = NexcoreClientPayslip::where('client_id', $clientId)
            ->with(['earnings', 'deductions'])
            ->findOrFail($payslipId);

        $employees = NexcoreClientEmployee::where('client_id', $clientId)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $periods  = NexcoreClientPayPeriod::where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->get();

        $statuses = $this->statuses;

        return view('nexcore_client_manager::payroll.payslips.form', compact('client', 'payslip', 'employees', 'periods', 'statuses'));
    }

    public function update(Request $request, $clientId, $payslipId)
    {
        $client  = NexcoreClient::findOrFail($clientId);
        $payslip = NexcoreClientPayslip::where('client_id', $clientId)->findOrFail($payslipId);

        $request->validate([
            'employee_id'      => 'required|exists:nexcore_client_employees,id',
            'pay_period_id'    => 'required|exists:nexcore_client_pay_periods,id',
            'basic_salary'     => 'required|numeric|min:0',
            'gross_pay'        => 'required|numeric|min:0',
            'total_deductions' => 'required|numeric|min:0',
            'net_pay'          => 'required|numeric|min:0',
            'employer_cost'    => 'required|numeric|min:0',
            'paye'             => 'nullable|numeric|min:0',
            'uif_employee'     => 'nullable|numeric|min:0',
            'uif_employer'     => 'nullable|numeric|min:0',
            'sdl'              => 'nullable|numeric|min:0',
            'overtime_hours'   => 'nullable|numeric|min:0',
            'overtime_amount'  => 'nullable|numeric|min:0',
            'status'           => 'required|in:draft,processed,finalised',
            'notes'            => 'nullable|string',
        ]);

        $data = $request->only([
            'employee_id', 'pay_period_id', 'basic_salary', 'gross_pay',
            'total_deductions', 'net_pay', 'employer_cost', 'paye',
            'uif_employee', 'uif_employer', 'sdl', 'overtime_hours',
            'overtime_amount', 'status', 'notes',
        ]);
        $data['updated_by'] = auth()->id();

        $payslip->update($data);

        return redirect()->route('nexcore.clients.show.payroll.payslips', $clientId)
            ->with('success', 'Payslip updated successfully.');
    }

    public function destroy($clientId, $payslipId)
    {
        $payslip = NexcoreClientPayslip::where('client_id', $clientId)->findOrFail($payslipId);

        $payslip->earnings()->delete();
        $payslip->deductions()->delete();
        $payslip->delete();

        return redirect()->route('nexcore.clients.show.payroll.payslips', $clientId)
            ->with('success', 'Payslip deleted successfully.');
    }
}
