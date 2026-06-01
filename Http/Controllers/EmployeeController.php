<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientEmployee;

class EmployeeController extends Controller
{
    protected array $salaryTypes = [
        'monthly'     => 'Monthly',
        'hourly'      => 'Hourly',
        'weekly'      => 'Weekly',
        'fortnightly' => 'Fortnightly',
    ];

    protected array $payFrequencies = [
        'monthly'     => 'Monthly',
        'weekly'      => 'Weekly',
        'fortnightly' => 'Fortnightly',
    ];

    protected array $genders = [
        'male'   => 'Male',
        'female' => 'Female',
        'other'  => 'Other',
    ];

    protected array $accountTypes = [
        'cheque'       => 'Cheque / Current',
        'savings'      => 'Savings',
        'transmission' => 'Transmission',
    ];

    protected array $employmentStatuses = [
        'active'     => 'Active',
        'probation'  => 'Probation',
        'suspended'  => 'Suspended',
        'terminated' => 'Terminated',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $employees = NexcoreClientEmployee::where('client_id', $clientId)
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get();

        $salaryTypes        = $this->salaryTypes;
        $payFrequencies     = $this->payFrequencies;
        $genders            = $this->genders;
        $accountTypes       = $this->accountTypes;
        $employmentStatuses = $this->employmentStatuses;

        return view('nexcore_client_manager::payroll.employees.index', compact(
            'client', 'employees', 'salaryTypes', 'payFrequencies',
            'genders', 'accountTypes', 'employmentStatuses'
        ));
    }

    public function create($clientId)
    {
        $client             = NexcoreClient::findOrFail($clientId);
        $salaryTypes        = $this->salaryTypes;
        $payFrequencies     = $this->payFrequencies;
        $genders            = $this->genders;
        $accountTypes       = $this->accountTypes;
        $employmentStatuses = $this->employmentStatuses;

        return view('nexcore_client_manager::payroll.employees.form', compact(
            'client', 'salaryTypes', 'payFrequencies',
            'genders', 'accountTypes', 'employmentStatuses'
        ));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'salary_type'       => 'required|in:monthly,hourly,weekly,fortnightly',
            'pay_frequency'     => 'required|in:monthly,weekly,fortnightly',
            'employment_status' => 'required|in:active,probation,suspended,terminated',
            'basic_salary'      => 'required|numeric|min:0',
            'email'             => 'nullable|email|max:255',
            'id_number'         => 'nullable|string|max:13',
            'employee_number'   => 'nullable|string|max:255',
            'title'             => 'nullable|string|max:50',
            'tax_number'        => 'nullable|string|max:255',
            'date_of_birth'     => 'nullable|date',
            'gender'            => 'nullable|in:male,female,other',
            'position'          => 'nullable|string|max:255',
            'department'        => 'nullable|string|max:255',
            'start_date'        => 'nullable|date',
            'termination_date'  => 'nullable|date',
            'bank_name'         => 'nullable|string|max:255',
            'bank_branch_code'  => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_type' => 'nullable|in:cheque,savings,transmission',
            'phone'             => 'nullable|string|max:50',
            'address'           => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        NexcoreClientEmployee::create(array_merge(
            $request->only([
                'employee_number', 'title', 'first_name', 'last_name',
                'id_number', 'tax_number', 'date_of_birth', 'gender',
                'position', 'department', 'start_date', 'termination_date',
                'salary_type', 'basic_salary', 'pay_frequency',
                'bank_name', 'bank_branch_code', 'bank_account_number', 'bank_account_type',
                'email', 'phone', 'address', 'employment_status', 'notes',
            ]),
            [
                'client_id'  => $clientId,
                'is_active'  => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]
        ));

        return redirect()->route('nexcore.clients.show.payroll.employees', $clientId)
            ->with('success', 'Employee added successfully.');
    }

    public function edit($clientId, $employeeId)
    {
        $client             = NexcoreClient::findOrFail($clientId);
        $employee           = NexcoreClientEmployee::where('client_id', $clientId)->findOrFail($employeeId);
        $salaryTypes        = $this->salaryTypes;
        $payFrequencies     = $this->payFrequencies;
        $genders            = $this->genders;
        $accountTypes       = $this->accountTypes;
        $employmentStatuses = $this->employmentStatuses;

        return view('nexcore_client_manager::payroll.employees.form', compact(
            'client', 'employee', 'salaryTypes', 'payFrequencies',
            'genders', 'accountTypes', 'employmentStatuses'
        ));
    }

    public function update(Request $request, $clientId, $employeeId)
    {
        $client   = NexcoreClient::findOrFail($clientId);
        $employee = NexcoreClientEmployee::where('client_id', $clientId)->findOrFail($employeeId);

        $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'salary_type'       => 'required|in:monthly,hourly,weekly,fortnightly',
            'pay_frequency'     => 'required|in:monthly,weekly,fortnightly',
            'employment_status' => 'required|in:active,probation,suspended,terminated',
            'basic_salary'      => 'required|numeric|min:0',
            'email'             => 'nullable|email|max:255',
            'id_number'         => 'nullable|string|max:13',
            'employee_number'   => 'nullable|string|max:255',
            'title'             => 'nullable|string|max:50',
            'tax_number'        => 'nullable|string|max:255',
            'date_of_birth'     => 'nullable|date',
            'gender'            => 'nullable|in:male,female,other',
            'position'          => 'nullable|string|max:255',
            'department'        => 'nullable|string|max:255',
            'start_date'        => 'nullable|date',
            'termination_date'  => 'nullable|date',
            'bank_name'         => 'nullable|string|max:255',
            'bank_branch_code'  => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_type' => 'nullable|in:cheque,savings,transmission',
            'phone'             => 'nullable|string|max:50',
            'address'           => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        $data = $request->only([
            'employee_number', 'title', 'first_name', 'last_name',
            'id_number', 'tax_number', 'date_of_birth', 'gender',
            'position', 'department', 'start_date', 'termination_date',
            'salary_type', 'basic_salary', 'pay_frequency',
            'bank_name', 'bank_branch_code', 'bank_account_number', 'bank_account_type',
            'email', 'phone', 'address', 'employment_status', 'notes',
        ]);

        $data['updated_by'] = auth()->id();

        $employee->update($data);

        return redirect()->route('nexcore.clients.show.payroll.employees', $clientId)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy($clientId, $employeeId)
    {
        $employee = NexcoreClientEmployee::where('client_id', $clientId)->findOrFail($employeeId);
        $employee->delete();

        return redirect()->route('nexcore.clients.show.payroll.employees', $clientId)
            ->with('success', 'Employee deleted successfully.');
    }
}
