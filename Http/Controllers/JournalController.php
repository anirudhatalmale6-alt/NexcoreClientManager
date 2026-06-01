<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;

class JournalController extends Controller
{
    protected array $statuses = [
        'draft' => 'Draft',
        'posted' => 'Posted',
        'reversed' => 'Reversed',
    ];

    protected array $sources = [
        'manual' => 'Manual',
        'bank_import' => 'Bank Import',
        'system' => 'System',
        'opening' => 'Opening',
    ];

    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $journals = NexcoreGlJournal::where('company_id', $companyId)
            ->withCount('lines')
            ->orderByDesc('journal_date')
            ->get();

        $statuses = $this->statuses;

        return view('nexcore_client_manager::accounting.journals.index', compact('client', 'journals', 'statuses'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $accounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('account_level', 3)
            ->orderBy('account_code')
            ->get();

        $statuses = $this->statuses;
        $sources = $this->sources;

        $count = NexcoreGlJournal::where('company_id', $companyId)->count();
        $journalNumber = 'JNL-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

        return view('nexcore_client_manager::accounting.journals.form', compact('client', 'accounts', 'statuses', 'sources', 'journalNumber'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $request->validate([
            'journal_date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'description' => 'required|string|max:500',
            'source' => 'required|string|in:' . implode(',', array_keys($this->sources)),
            'status' => 'required|string|in:' . implode(',', array_keys($this->statuses)),
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:cims_gl_chart_of_accounts_master,id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.debit_amount' => 'nullable|numeric|min:0',
            'lines.*.credit_amount' => 'nullable|numeric|min:0',
        ]);

        $totalDebits = collect($request->lines)->sum(fn($line) => (float) ($line['debit_amount'] ?? 0));
        $totalCredits = collect($request->lines)->sum(fn($line) => (float) ($line['credit_amount'] ?? 0));

        if (round($totalDebits, 2) !== round($totalCredits, 2)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total debits (' . number_format($totalDebits, 2) . ') must equal total credits (' . number_format($totalCredits, 2) . ').');
        }

        DB::transaction(function () use ($request, $companyId, $totalDebits, $totalCredits) {
            $count = NexcoreGlJournal::where('company_id', $companyId)->count();
            $journalNumber = 'JNL-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

            $journal = NexcoreGlJournal::create([
                'company_id' => $companyId,
                'journal_number' => $journalNumber,
                'journal_date' => $request->journal_date,
                'reference' => $request->reference,
                'description' => $request->description,
                'source' => $request->source,
                'status' => 'posted',
                'total_debit' => $totalDebits,
                'total_credit' => $totalCredits,
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            $order = 1;
            foreach ($request->lines as $line) {
                NexcoreGlJournalLine::create([
                    'journal_id' => $journal->id,
                    'account_id' => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit_amount' => $line['debit_amount'] ?? 0,
                    'credit_amount' => $line['credit_amount'] ?? 0,
                    'vat_type' => 'none',
                    'line_order' => $order++,
                ]);
            }
        });

        return redirect()->route('nexcore.clients.show.accounting.journals', $clientId)
            ->with('success', 'Journal entry created successfully.');
    }

    public function edit($clientId, $journalId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $journal = NexcoreGlJournal::where('company_id', $companyId)
            ->with('lines')
            ->findOrFail($journalId);

        $accounts = NexcoreGlChartOfAccount::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('account_level', 3)
            ->orderBy('account_code')
            ->get();

        $statuses = $this->statuses;
        $sources = $this->sources;

        return view('nexcore_client_manager::accounting.journals.form', compact('client', 'journal', 'accounts', 'statuses', 'sources'));
    }

    public function update(Request $request, $clientId, $journalId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $journal = NexcoreGlJournal::where('company_id', $companyId)->findOrFail($journalId);

        $request->validate([
            'journal_date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'description' => 'required|string|max:500',
            'source' => 'required|string|in:' . implode(',', array_keys($this->sources)),
            'status' => 'required|string|in:' . implode(',', array_keys($this->statuses)),
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:cims_gl_chart_of_accounts_master,id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.debit_amount' => 'nullable|numeric|min:0',
            'lines.*.credit_amount' => 'nullable|numeric|min:0',
        ]);

        $totalDebits = collect($request->lines)->sum(fn($line) => (float) ($line['debit_amount'] ?? 0));
        $totalCredits = collect($request->lines)->sum(fn($line) => (float) ($line['credit_amount'] ?? 0));

        if (round($totalDebits, 2) !== round($totalCredits, 2)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total debits (' . number_format($totalDebits, 2) . ') must equal total credits (' . number_format($totalCredits, 2) . ').');
        }

        DB::transaction(function () use ($request, $journal, $totalDebits, $totalCredits) {
            $journal->update([
                'journal_date' => $request->journal_date,
                'reference' => $request->reference,
                'description' => $request->description,
                'source' => $request->source,
                'status' => $request->status,
                'total_debit' => $totalDebits,
                'total_credit' => $totalCredits,
            ]);

            if ($request->status === 'posted' && !$journal->posted_at) {
                $journal->update([
                    'posted_by' => auth()->id(),
                    'posted_at' => now(),
                ]);
            }

            NexcoreGlJournalLine::where('journal_id', $journal->id)->delete();

            $order = 1;
            foreach ($request->lines as $line) {
                NexcoreGlJournalLine::create([
                    'journal_id' => $journal->id,
                    'account_id' => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit_amount' => $line['debit_amount'] ?? 0,
                    'credit_amount' => $line['credit_amount'] ?? 0,
                    'vat_type' => 'none',
                    'line_order' => $order++,
                ]);
            }
        });

        return redirect()->route('nexcore.clients.show.accounting.journals', $clientId)
            ->with('success', 'Journal entry updated successfully.');
    }

    public function destroy($clientId, $journalId)
    {
        $companyId = $clientId;
        $journal = NexcoreGlJournal::where('company_id', $companyId)->findOrFail($journalId);

        DB::transaction(function () use ($journal) {
            NexcoreGlJournalLine::where('journal_id', $journal->id)->delete();
            $journal->delete();
        });

        return redirect()->route('nexcore.clients.show.accounting.journals', $clientId)
            ->with('success', 'Journal entry deleted successfully.');
    }
}
