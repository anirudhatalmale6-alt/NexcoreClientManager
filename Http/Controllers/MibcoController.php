<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientEmployee;
use Modules\NexcoreClientManager\Models\NexcoreClientPayPeriod;
use Modules\NexcoreClientManager\Models\NexcoreClientMibcoContribution;

class MibcoController extends Controller
{
    protected array $statuses = [
        'draft'     => 'Draft',
        'submitted' => 'Submitted',
        'paid'      => 'Paid',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $periods = NexcoreClientPayPeriod::where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->get();

        $defaultPeriodId = $periods->first()->id ?? null;
        if (!request('period_id') && $defaultPeriodId) {
            $periodWithData = NexcoreClientMibcoContribution::where('client_id', $clientId)
                ->select('pay_period_id')
                ->groupBy('pay_period_id')
                ->orderByRaw('count(*) desc')
                ->first();
            if ($periodWithData) {
                $defaultPeriodId = $periodWithData->pay_period_id;
            }
        }
        $selectedPeriodId = request('period_id', $defaultPeriodId);

        $query = NexcoreClientMibcoContribution::where('client_id', $clientId)
            ->with(['employee', 'payPeriod']);

        if ($selectedPeriodId) {
            $query->where('pay_period_id', $selectedPeriodId);
        }

        $contributions = $query->orderBy('employee_id')->get();

        $statuses = $this->statuses;
        $selectedPeriod = $periods->firstWhere('id', $selectedPeriodId);

        return view('nexcore_client_manager::payroll.mibco.index', compact('client', 'contributions', 'statuses', 'periods', 'selectedPeriodId', 'selectedPeriod'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $employees = NexcoreClientEmployee::where('client_id', $clientId)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $periods = NexcoreClientPayPeriod::where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->get();

        $statuses = $this->statuses;

        return view('nexcore_client_manager::payroll.mibco.form', compact('client', 'employees', 'periods', 'statuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'employee_id'         => 'required|exists:nexcore_client_employees,id',
            'pay_period_id'       => 'required|exists:nexcore_client_pay_periods,id',
            'pension_employee'    => 'nullable|numeric|min:0',
            'pension_employer'    => 'nullable|numeric|min:0',
            'provident_employee'  => 'nullable|numeric|min:0',
            'provident_employer'  => 'nullable|numeric|min:0',
            'death_benefit'       => 'nullable|numeric|min:0',
            'funeral_benefit'     => 'nullable|numeric|min:0',
            'sick_pay_fund'       => 'nullable|numeric|min:0',
            'holiday_fund'        => 'nullable|numeric|min:0',
            'status'              => 'required|in:draft,submitted,paid',
            'notes'               => 'nullable|string',
        ]);

        $data = $request->only([
            'employee_id', 'pay_period_id',
            'pension_employee', 'pension_employer',
            'provident_employee', 'provident_employer',
            'death_benefit', 'funeral_benefit',
            'sick_pay_fund', 'holiday_fund',
            'status', 'notes',
        ]);

        $data['total_employee'] = ($data['pension_employee'] ?? 0) + ($data['provident_employee'] ?? 0);
        $data['total_employer'] = ($data['pension_employer'] ?? 0) + ($data['provident_employer'] ?? 0)
            + ($data['death_benefit'] ?? 0) + ($data['funeral_benefit'] ?? 0)
            + ($data['sick_pay_fund'] ?? 0) + ($data['holiday_fund'] ?? 0);
        $data['total_contribution'] = $data['total_employee'] + $data['total_employer'];

        $data['client_id']  = $clientId;
        $data['is_active']  = true;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        NexcoreClientMibcoContribution::create($data);

        return redirect()->route('nexcore.clients.show.payroll.mibco', $clientId)
            ->with('success', 'MIBCO contribution created successfully.');
    }

    public function edit($clientId, $contributionId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $contribution = NexcoreClientMibcoContribution::where('client_id', $clientId)->findOrFail($contributionId);

        $employees = NexcoreClientEmployee::where('client_id', $clientId)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $periods = NexcoreClientPayPeriod::where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->get();

        $statuses = $this->statuses;

        return view('nexcore_client_manager::payroll.mibco.form', compact('client', 'contribution', 'employees', 'periods', 'statuses'));
    }

    public function update(Request $request, $clientId, $contributionId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $contribution = NexcoreClientMibcoContribution::where('client_id', $clientId)->findOrFail($contributionId);

        $request->validate([
            'employee_id'         => 'required|exists:nexcore_client_employees,id',
            'pay_period_id'       => 'required|exists:nexcore_client_pay_periods,id',
            'pension_employee'    => 'nullable|numeric|min:0',
            'pension_employer'    => 'nullable|numeric|min:0',
            'provident_employee'  => 'nullable|numeric|min:0',
            'provident_employer'  => 'nullable|numeric|min:0',
            'death_benefit'       => 'nullable|numeric|min:0',
            'funeral_benefit'     => 'nullable|numeric|min:0',
            'sick_pay_fund'       => 'nullable|numeric|min:0',
            'holiday_fund'        => 'nullable|numeric|min:0',
            'status'              => 'required|in:draft,submitted,paid',
            'notes'               => 'nullable|string',
        ]);

        $data = $request->only([
            'employee_id', 'pay_period_id',
            'pension_employee', 'pension_employer',
            'provident_employee', 'provident_employer',
            'death_benefit', 'funeral_benefit',
            'sick_pay_fund', 'holiday_fund',
            'status', 'notes',
        ]);

        $data['total_employee'] = ($data['pension_employee'] ?? 0) + ($data['provident_employee'] ?? 0);
        $data['total_employer'] = ($data['pension_employer'] ?? 0) + ($data['provident_employer'] ?? 0)
            + ($data['death_benefit'] ?? 0) + ($data['funeral_benefit'] ?? 0)
            + ($data['sick_pay_fund'] ?? 0) + ($data['holiday_fund'] ?? 0);
        $data['total_contribution'] = $data['total_employee'] + $data['total_employer'];
        $data['updated_by'] = auth()->id();

        $contribution->update($data);

        return redirect()->route('nexcore.clients.show.payroll.mibco', $clientId)
            ->with('success', 'MIBCO contribution updated successfully.');
    }

    public function destroy($clientId, $contributionId)
    {
        $contribution = NexcoreClientMibcoContribution::where('client_id', $clientId)->findOrFail($contributionId);
        $contribution->delete();

        return redirect()->route('nexcore.clients.show.payroll.mibco', $clientId)
            ->with('success', 'MIBCO contribution deleted successfully.');
    }
}
