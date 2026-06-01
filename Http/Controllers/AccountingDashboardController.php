<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreGlChartOfAccount;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;

class AccountingDashboardController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;

        $totalAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)->count();
        $mainAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_level', 1)->count();
        $subAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_level', 2)->count();
        $detailAccounts = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_level', 3)->count();

        $totalJournals = NexcoreGlJournal::where('company_id', $companyId)->count();
        $totalPostedJournals = NexcoreGlJournal::where('company_id', $companyId)
            ->where('status', 'posted')
            ->count();

        $postedJournalIds = NexcoreGlJournal::where('company_id', $companyId)
            ->where('status', 'posted')
            ->pluck('id');

        $revenueAccountIds = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_type', 'revenue')
            ->pluck('id');

        $revenueTotal = NexcoreGlJournalLine::whereIn('journal_id', $postedJournalIds)
            ->whereIn('account_id', $revenueAccountIds)
            ->selectRaw('COALESCE(SUM(credit_amount), 0) - COALESCE(SUM(debit_amount), 0) as total')
            ->value('total') ?? 0;

        $expenseAccountIds = NexcoreGlChartOfAccount::where('company_id', $companyId)->whereIn('account_type', ['expense', 'cost_of_sales'])
            ->pluck('id');

        $expenseTotal = NexcoreGlJournalLine::whereIn('journal_id', $postedJournalIds)
            ->whereIn('account_id', $expenseAccountIds)
            ->selectRaw('COALESCE(SUM(debit_amount), 0) - COALESCE(SUM(credit_amount), 0) as total')
            ->value('total') ?? 0;

        $netProfit = $revenueTotal - $expenseTotal;

        $assetAccountIds = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_type', 'asset')->pluck('id');
        $totalAssets = NexcoreGlJournalLine::whereIn('journal_id', $postedJournalIds)
            ->whereIn('account_id', $assetAccountIds)
            ->selectRaw('COALESCE(SUM(debit_amount), 0) - COALESCE(SUM(credit_amount), 0) as total')
            ->value('total') ?? 0;

        $liabilityAccountIds = NexcoreGlChartOfAccount::where('company_id', $companyId)->where('account_type', 'liability')->pluck('id');
        $totalLiabilities = NexcoreGlJournalLine::whereIn('journal_id', $postedJournalIds)
            ->whereIn('account_id', $liabilityAccountIds)
            ->selectRaw('COALESCE(SUM(credit_amount), 0) - COALESCE(SUM(debit_amount), 0) as total')
            ->value('total') ?? 0;

        $recentJournals = NexcoreGlJournal::where('company_id', $companyId)
            ->with('lines')
            ->orderByDesc('journal_date')
            ->limit(10)
            ->get();

        return view('nexcore_client_manager::accounting.dashboard', compact(
            'client',
            'totalAccounts',
            'mainAccounts',
            'subAccounts',
            'detailAccounts',
            'totalJournals',
            'totalPostedJournals',
            'revenueTotal',
            'expenseTotal',
            'netProfit',
            'totalAssets',
            'totalLiabilities',
            'recentJournals'
        ));
    }
}
