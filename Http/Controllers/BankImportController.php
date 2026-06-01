<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreBankAccount;
use Modules\NexcoreClientManager\Models\NexcoreBankTransaction;
use Modules\NexcoreClientManager\Models\NexcoreBankStatement;
use Modules\NexcoreClientManager\Models\NexcoreGlJournal;
use Modules\NexcoreClientManager\Models\NexcoreGlJournalLine;

class BankImportController extends Controller
{
    /**
     * Show the statement register (list of imported statements).
     */
    public function statements($clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        $statements = NexcoreBankStatement::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->orderBy('period_from', 'desc')
            ->get();

        return view('nexcore_client_manager::accounting.bank.statements', compact('client', 'bankAccount', 'statements', 'companyId'));
    }

    public function statementView($clientId, $bankId, $statementId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);
        $statement = NexcoreBankStatement::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->findOrFail($statementId);

        $transactions = NexcoreBankTransaction::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->where('batch_ref', $statement->batch_ref)
            ->with('allocatedAccount')
            ->orderBy('transaction_date')
            ->get();

        return view('nexcore_client_manager::accounting.bank.statement-view', compact('client', 'bankAccount', 'statement', 'transactions', 'companyId'));
    }

    /**
     * Delete a statement and all its linked transactions.
     */
    public function destroyStatement($clientId, $bankId, $statementId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $statement = NexcoreBankStatement::where('company_id', $companyId)
            ->where('bank_account_id', $bankId)
            ->findOrFail($statementId);

        $transactions = NexcoreBankTransaction::where('batch_ref', $statement->batch_ref)->get();

        $journalIds = $transactions->pluck('journal_id')->filter()->unique()->values()->toArray();

        if (!empty($journalIds)) {
            NexcoreGlJournalLine::whereIn('journal_id', $journalIds)->delete();
            NexcoreGlJournal::whereIn('id', $journalIds)->delete();
        }

        $deletedCount = NexcoreBankTransaction::where('batch_ref', $statement->batch_ref)->delete();

        $statement->delete();

        return redirect()->route('nexcore.clients.show.accounting.bank.statements', [$clientId, $bankId])
            ->with('success', "Statement and {$deletedCount} transactions with all linked journals removed.");
    }

    /**
     * Show the bank statement import page.
     */
    public function import($clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        // Auto-detect bank type from account name
        $name = strtolower($bankAccount->bank_name ?? '');
        if (str_contains($name, 'fnb') || str_contains($name, 'first national')) {
            $bankType = 'fnb';
        } elseif (str_contains($name, 'nedbank')) {
            $bankType = 'nedbank';
        } elseif (str_contains($name, 'absa')) {
            $bankType = 'absa';
        } elseif (str_contains($name, 'capitec')) {
            $bankType = 'capitec';
        } elseif (str_contains($name, 'standard')) {
            $bankType = 'standard';
        } else {
            $bankType = 'fnb';
        }

        return view('nexcore_client_manager::accounting.bank.import', compact('client', 'bankAccount', 'bankType', 'companyId'));
    }

    /**
     * AJAX endpoint: parse PDF text into structured transactions.
     */
    public function parsePdf(Request $request, $clientId, $bankId)
    {
        $pages = $request->input('pages', []);
        $ocrPages = $request->input('ocr_pages', []);
        $bankType = $request->input('bank_type', 'fnb');

        try {
            switch ($bankType) {
                case 'fnb':
                    $result = $this->parseFnbText($pages, $ocrPages);
                    break;
                case 'nedbank':
                    $result = $this->parseNedbankText($pages);
                    break;
                case 'absa':
                    $result = $this->parseAbsaText($pages);
                    break;
                case 'capitec':
                    $result = $this->parseCapitecText($pages);
                    break;
                case 'standard':
                    $result = $this->parseStandardText($pages);
                    break;
                default:
                    $result = $this->parseFnbText($pages, $ocrPages);
            }

            $result['_parser_version'] = 'CIMS-ORIGINAL-v4';
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Save parsed transactions to the database.
     */
    public function importSave(Request $request, $clientId, $bankId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $companyId = $clientId;
        $bankAccount = NexcoreBankAccount::where('company_id', $companyId)->findOrFail($bankId);

        $transactions = $request->input('transactions', []);
        $header = $request->input('header', []);
        $summary = $request->input('summary', []);
        $filename = $request->input('filename', 'bank_statement.pdf');

        if (empty($transactions)) {
            return response()->json(['error' => true, 'message' => 'No transactions to import.'], 422);
        }

        $batchRef = 'BSI-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . now()->format('YmdHis');
        $count = 0;

        \DB::beginTransaction();
        try {
            foreach ($transactions as $txn) {
                $amount = abs((float) ($txn['amount'] ?? 0));
                $direction = ((float) ($txn['amount'] ?? 0)) >= 0 ? 'credit' : 'debit';

                NexcoreBankTransaction::create([
                    'company_id'      => $companyId,
                    'bank_account_id' => $bankId,
                    'transaction_date' => $txn['date'] ?? null,
                    'description'     => $txn['description'] ?? '',
                    'amount'          => $amount,
                    'direction'       => $direction,
                    'balance'         => $txn['balance'] ?? null,
                    'status'          => 'unallocated',
                    'batch_ref'       => $batchRef,
                    'imported_at'     => now(),
                ]);
                $count++;
            }

            // Build statement reference: BS[last4 GL code]/[stmt number]/[MMMYYYY]
            $stmtNum = $header['statement_number'] ?? null;
            $periodTo = $header['period_to'] ?? ($summary['last_date'] ?? null);
            $glCode = '';
            if ($bankAccount->glAccount) {
                $glCode = $bankAccount->glAccount->account_code;
            } else {
                $bankAccount->load('glAccount');
                $glCode = $bankAccount->glAccount ? $bankAccount->glAccount->account_code : '';
            }
            $glLast4 = strlen($glCode) >= 4 ? substr($glCode, -4) : str_pad($glCode, 4, '0', STR_PAD_LEFT);
            $stmtNumPart = $stmtNum ?: '###';
            $periodPart = '';
            if ($periodTo) {
                $periodDate = \Carbon\Carbon::parse($periodTo);
                $periodPart = strtoupper($periodDate->format('MY'));
            } else {
                $periodPart = strtoupper(now()->format('MY'));
            }
            $statementRef = 'BS' . $glLast4 . '/' . $stmtNumPart . '/' . $periodPart;

            // Create statement record
            NexcoreBankStatement::create([
                'company_id'        => $companyId,
                'bank_account_id'   => $bankId,
                'statement_name'    => $header['account_holder'] ?? ($bankAccount->bank_name . ' Statement'),
                'statement_number'  => $stmtNum,
                'statement_ref'     => $statementRef,
                'period_from'       => $header['period_from'] ?? ($summary['first_date'] ?? null),
                'period_to'         => $periodTo,
                'upload_date'       => now(),
                'original_filename' => $filename,
                'transaction_count' => $count,
                'opening_balance'   => $header['opening_balance'] ?? ($summary['opening_balance'] ?? 0),
                'closing_balance'   => $header['closing_balance'] ?? ($summary['closing_balance'] ?? 0),
                'total_credits'     => $summary['total_credits'] ?? 0,
                'total_debits'      => $summary['total_debits'] ?? 0,
                'credit_count'      => $summary['credit_count'] ?? 0,
                'debit_count'       => $summary['debit_count'] ?? 0,
                'batch_ref'         => $batchRef,
                'status'            => 'imported',
            ]);

            \DB::commit();

            return response()->json([
                'success'   => true,
                'count'     => $count,
                'batch_ref' => $batchRef,
                'redirect'  => route('nexcore.clients.show.accounting.bank.accounts', $clientId),
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    //  FNB PARSER
    // =========================================================================

    /**
     * Parse FNB bank statement text.
     */
    private function parseFnbText(array $pages, array $ocrPages = []): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $processedLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (preg_match('/^(Cr|Dr)$/i', $trimmed) && !empty($processedLines)) {
                $processedLines[count($processedLines) - 1] .= $trimmed;
            } else {
                $processedLines[] = $line;
            }
        }
        $lines = $processedLines;

        $header = $this->parseFnbHeader($lines);
        $transactions = $this->parseFnbTransactions($lines, $header);

        if (!empty($ocrPages)) {
            $ocrTxns = $this->parseFnbOcrTransactions($ocrPages);

            $ocrUsed = [];
            foreach ($transactions as $txn) {
                if (!empty($txn['description'])) {
                    foreach ($ocrTxns as $oi => $ocrTxn) {
                        if (isset($ocrUsed[$oi])) continue;
                        if ($txn['date'] === $ocrTxn['date'] && abs(abs($txn['amount']) - abs($ocrTxn['amount'])) < 0.01) {
                            $ocrUsed[$oi] = true;
                            break;
                        }
                    }
                }
            }

            foreach ($transactions as &$txn) {
                if (empty($txn['description'])) {
                    foreach ($ocrTxns as $oi => $ocrTxn) {
                        if (isset($ocrUsed[$oi])) continue;
                        if ($txn['date'] === $ocrTxn['date'] && abs(abs($txn['amount']) - abs($ocrTxn['amount'])) < 0.01 && !empty($ocrTxn['description'])) {
                            $txn['description'] = $ocrTxn['description'];
                            $ocrUsed[$oi] = true;
                            break;
                        }
                    }
                }
            }
            unset($txn);

            foreach ($transactions as &$txn) {
                if (empty($txn['description'])) {
                    foreach ($ocrTxns as $oi => $ocrTxn) {
                        if (isset($ocrUsed[$oi])) continue;
                        if ($txn['date'] === $ocrTxn['date'] && !empty($ocrTxn['description'])) {
                            $txn['description'] = $ocrTxn['description'];
                            $ocrUsed[$oi] = true;
                            break;
                        }
                    }
                }
            }
            unset($txn);
        }

        $lastDesc = '';
        $bankCharges = $header['bank_charges'] ?? [];
        foreach ($transactions as &$txn) {
            if (!empty($txn['description'])) { $lastDesc = $txn['description']; continue; }
            $absAmt = abs($txn['amount']);
            $found = false;
            foreach ($bankCharges as $cAmt => $cName) {
                if (abs($absAmt - $cAmt) < 0.01) { $txn['description'] = $cName; $found = true; break; }
            }
            if (!$found) {
                $txn['description'] = $lastDesc ? $lastDesc . ' (cont.)' : 'Bank charge';
            }
        }
        unset($txn);

        $result = $this->buildParseResult($header, $transactions);
        $result['raw_text'] = $allText;
        return $result;
    }

    private function parseFnbOcrTransactions(array $ocrPages): array
    {
        $ocrTxns = [];
        $months = ['jan'=>'01','feb'=>'02','mar'=>'03','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12'];

        $allOcrText = implode("\n", $ocrPages);
        $endYear = '';
        $startYear = '';
        $startMonth = 0;
        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})\s+to\s+(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/i', $allOcrText, $pm)) {
            $startYear = $pm[3]; $endYear = $pm[6];
            $startMonth = intval($months[strtolower(substr($pm[2], 0, 3))] ?? 0);
        } elseif (preg_match('/(\d{4})/', $allOcrText, $pm)) {
            $startYear = $pm[1]; $endYear = $pm[1];
        }
        if (!$endYear) { $endYear = date('Y'); $startYear = $endYear; }

        $lines = explode("\n", $allOcrText);
        foreach ($lines as $line) {
            $t = trim($line);
            if (preg_match('/^(\d{1,2})\s+([A-Za-z]{3})\s+[|(\[]?\s*(.+?)\s+([\d,]+(?:\.\d{2})?)\s*(?:Cr|Dr)?\s*[|]?\s*([\d,]+\.\d{2})\s*(?:Cr|Dr)?/', $t, $m)) {
                $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $mon = $months[strtolower($m[2])] ?? '01';
                $monInt = intval($mon);
                if ($startYear !== $endYear) {
                    $txnYear = ($monInt >= $startMonth) ? $startYear : $endYear;
                } else {
                    $txnYear = $endYear;
                }
                $desc = trim($m[3]);
                $desc = preg_replace('/^[^A-Za-z0-9]+\s*/', '', $desc);
                $desc = preg_replace('/\s*[|}\]]+\s*$/', '', $desc);
                $rawAmt = str_replace(',', '', $m[4]);
                $amount = (float) $rawAmt;
                if (strpos($rawAmt, '.') === false && $amount > 100) {
                    $amount = $amount / 100;
                }
                $ocrTxns[] = [
                    'date' => $txnYear . '-' . $mon . '-' . $day,
                    'description' => $desc,
                    'amount' => $amount,
                ];
            }
        }
        return $ocrTxns;
    }

    private function parseFnbHeader(array $lines): array
    {
        $header = ['account_number' => '', 'account_holder' => '', 'branch_code' => '', 'statement_period' => '', 'statement_date' => '', 'statement_number' => null, 'period_from' => null, 'period_to' => null, 'opening_balance' => 0, 'closing_balance' => 0, 'bank_charges' => []];
        $fullText = implode(' ', $lines);
        if (preg_match('/(?:Platinum\s+Business\s+Account|Gold\s+Business\s+Account|Business\s+Account|Cheque\s+Account)\s*:?\s*(\d{8,15})/i', $fullText, $m)) $header['account_number'] = $m[1];
        elseif (preg_match('/Account\s*(?:No|Number|#)?\s*:?\s*(\d{8,15})/i', $fullText, $m)) $header['account_number'] = $m[1];
        if (preg_match('/Universal\s+Branch\s+Code\s*:?\s*(\d+)/i', $fullText, $m)) $header['branch_code'] = $m[1];
        if (preg_match('/Statement\s+Period\s*:?\s*(.+?)(?:Statement\s+Date|$)/i', $fullText, $m)) $header['statement_period'] = trim($m[1]);
        if (preg_match('/Statement\s+Date\s*:?\s*(\d{1,2}\s+\w+\s+\d{4})/i', $fullText, $m)) $header['statement_date'] = trim($m[1]);
        if (preg_match('/Statement\s+(?:No|Number)\s*:?\s*(\d+)/i', $fullText, $m)) $header['statement_number'] = $m[1];
        if (!empty($header['statement_period']) && preg_match('/(\d{1,2}\s+[A-Za-z]+\s+\d{4})\s+to\s+(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i', $header['statement_period'], $pm)) { $header['period_from'] = date('Y-m-d', strtotime($pm[1])); $header['period_to'] = date('Y-m-d', strtotime($pm[2])); }
        if (preg_match('/Opening\s+Balance\s+([\d,]+\.\d{2})\s*(Cr|Dr)?/i', $fullText, $m)) { $val = (float) str_replace(',', '', $m[1]); if (isset($m[2]) && strtolower($m[2]) === 'dr') $val = -$val; $header['opening_balance'] = $val; }
        if (preg_match('/Closing\s+Balance\s+([\d,]+\.\d{2})\s*(Cr|Dr)?/i', $fullText, $m)) { $val = (float) str_replace(',', '', $m[1]); if (isset($m[2]) && strtolower($m[2]) === 'dr') $val = -$val; $header['closing_balance'] = $val; }
        foreach ($lines as $line) { if (preg_match('/^\*(.+?)$/m', trim($line), $m)) { $header['account_holder'] = trim($m[1]); break; } }

        $chargeTypes = ['Service Fees', 'Cash Deposit Fees', 'Cash Handling Fees', 'Other Fees', 'Monthly Account Fee', 'Account Fee'];
        foreach ($chargeTypes as $ct) {
            if (preg_match('/' . preg_quote($ct, '/') . '\s+([\d,]+\.\d{2})\s*(Dr|Cr)?/i', $fullText, $cm)) {
                $chargeAmt = (float) str_replace(',', '', $cm[1]);
                if ($chargeAmt > 0) $header['bank_charges'][$chargeAmt] = $ct;
            }
        }

        return $header;
    }

    private function parseFnbTransactions(array $lines, array $header): array
    {
        $transactions = [];
        $months = ['jan'=>'01','feb'=>'02','mar'=>'03','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12'];

        $startYear = ''; $endYear = ''; $startMonth = 0; $endMonth = 0;
        $periodStr = $header['statement_period'] . ' ' . $header['statement_date'];
        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})\s+to\s+(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/i', $periodStr, $pm)) {
            $startYear = $pm[3]; $endYear = $pm[6];
            $startMonth = intval($months[strtolower(substr($pm[2], 0, 3))] ?? 0);
            $endMonth = intval($months[strtolower(substr($pm[5], 0, 3))] ?? 0);
        } elseif (preg_match('/(\d{4})/', $periodStr, $pm)) {
            $startYear = $pm[1]; $endYear = $pm[1];
        }
        if (!$endYear) { $endYear = date('Y'); $startYear = $endYear; }

        $inTxn = false;
        $lastDescription = '';

        foreach ($lines as $line) {
            $t = trim($line);
            if ($t === '') continue;
            if (preg_match('/Transactions\s+in\s+RAND/i', $t)) { $inTxn = true; continue; }
            if ($inTxn && preg_match('/Closing\s+Balance/i', $t)) { $inTxn = false; continue; }
            if (preg_match('/^\s*Date\s+Description\s+Amount\s+Balance/i', $t)) continue;
            if (preg_match('/^Page\s+\d+\s+of\s+\d+/i', $t)) continue;
            if (preg_match('/^Delivery\s+Method/i', $t)) continue;
            if (preg_match('/^EN:EM/i', $t)) continue;
            if (preg_match('/^\d{5,6}$/', $t)) continue;
            if (preg_match('/^(Branch\s+Number|Account\s+Number|GOLD\s+BUSINESS|DDA\s+AA|DDA\s+BH|XSTZFN|FN$)/i', $t)) continue;
            if (preg_match('/^(FNB\s+Verified|Reference\s+Number|Statements\s+\d|\$[A-Z0-9]+|PLATINUM\s+BUSINESS)/i', $t)) continue;
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}/', $t)) continue;
            if (preg_match('/No\.\s+(Credit|Debit)\s+Transactions|Turnover\s+for\s+Statement/i', $t)) continue;
            if (preg_match('/Accrued\s+Bank\s+Charges/i', $t)) continue;
            if (!$inTxn) {
                if (preg_match('/Opening\s+Balance|Statement\s+Balances|Bank\s+Charges|Interest\s+Rate|Service\s+Fees|Credit\s+Rate|Debit\s+Rate|Inclusive\s+of\s+VAT|Total\s+VAT/i', $t)) continue;
                if (preg_match('/^(P\s*O\s*Box|Street\s*Address|Universal\s*Branch|Lost\s*Cards|Account\s*Enquiries|Fraud|Relationship|Customer\s*VAT|Bank\s*VAT|Statement\s*(Period|Date)|Platinum|Gold|Tax\s*Invoice)/i', $t)) continue;
                if (preg_match('/Cash\s+Deposit\s+Fees|Cash\s+Handling\s+Fees|Other\s+Fees|Overdraft\s+Limit/i', $t)) continue;
                continue;
            }


            if (preg_match('/^(\d{2}\s+[A-Za-z]{3})\s*(.+)$/', $t, $lm)) {
                $rest = trim($lm[2]);

                $rest = preg_replace('/^[^A-Za-z0-9]+\s*/', '', $rest);

                if (preg_match('/^(.+?)\s+([\d,]+\.\d{2})\s*(Cr)?\s+([\d,]+\.\d{2})\s*(Cr|Dr)?\s*(?:[\d,]+\.\d{2})?\s*$/', $rest, $tm)) {
                    $amount = (float) str_replace(',', '', $tm[2]);
                    if (empty($tm[3])) $amount = -$amount;
                    $balance = (float) str_replace(',', '', $tm[4]);
                    $balanceType = isset($tm[5]) && $tm[5] !== '' ? $tm[5] : 'Dr';
                    if ($balanceType === 'Dr') $balance = -$balance;
                    $desc = trim($tm[1]);
                    $desc = preg_replace('/\s+\d{6}\*\d{4}\s+\d{2}\s+[A-Za-z]{3}\s*$/', '', $desc);
                    $desc = preg_replace('/\s+\d{6}\*\d{4}\s*$/', '', $desc);
                    if (preg_match('/(\d{2})\s+([A-Za-z]{3})/', $lm[1], $dm)) {
                        $day = $dm[1]; $mon = $months[strtolower($dm[2])] ?? '01';
                        $monInt = intval($mon);
                        if ($startYear !== $endYear) {
                            $txnYear = ($monInt >= $startMonth) ? $startYear : $endYear;
                        } else {
                            $txnYear = $endYear;
                        }
                        $fullDate = $txnYear . '-' . $mon . '-' . $day;
                    } else { $fullDate = $endYear . '-01-01'; }
                    $lastDescription = $desc;
                    $transactions[] = ['date' => $fullDate, 'description' => $desc, 'amount' => $amount, 'balance' => $balance];

                }
                elseif (preg_match('/^\s*([\d,]+\.\d{2})\s*(Cr)?\s+([\d,]+\.\d{2})\s*(Cr|Dr)?\s*(?:[\d,]+\.\d{2})?\s*$/', $rest, $tm)) {
                    $amount = (float) str_replace(',', '', $tm[1]);
                    if (empty($tm[2])) $amount = -$amount;
                    $balance = (float) str_replace(',', '', $tm[3]);
                    $balanceType = isset($tm[4]) && $tm[4] !== '' ? $tm[4] : 'Dr';
                    if ($balanceType === 'Dr') $balance = -$balance;
                    if (preg_match('/(\d{2})\s+([A-Za-z]{3})/', $lm[1], $dm)) {
                        $day = $dm[1]; $mon = $months[strtolower($dm[2])] ?? '01';
                        $monInt = intval($mon);
                        if ($startYear !== $endYear) {
                            $txnYear = ($monInt >= $startMonth) ? $startYear : $endYear;
                        } else {
                            $txnYear = $endYear;
                        }
                        $fullDate = $txnYear . '-' . $mon . '-' . $day;
                    } else { $fullDate = $endYear . '-01-01'; }
                    $transactions[] = ['date' => $fullDate, 'description' => '', 'amount' => $amount, 'balance' => $balance];
                }
            }
        }
        return $transactions;
    }

    // =========================================================================
    //  NEDBANK PARSER
    // =========================================================================

    /**
     * Parse Nedbank bank statement text (balance-difference method).
     */
    private function parseNedbankText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = [
            'bank'             => 'Nedbank',
            'account_number'   => null,
            'statement_period' => null,
            'opening_balance'  => null,
            'closing_balance'  => null,
            'account_holder'   => null,
            'statement_number' => null,
            'period_from'      => null,
            'period_to'        => null,
            'bank_charges'     => [],
        ];

        // Account number
        if (preg_match('/Account\s+(?:Number|No)\s*:?\s*(\d{6,15})/i', $allText, $m)) {
            $header['account_number'] = $m[1];
        }

        // Statement period (DD/MM/YYYY to DD/MM/YYYY)
        if (preg_match('/Statement\s+Period\s*:?\s*(\d{2}\/\d{2}\/\d{4})\s*(?:to|-)\s*(\d{2}\/\d{2}\/\d{4})/i', $allText, $m)) {
            $header['statement_period'] = $m[1] . ' to ' . $m[2];
            $from = \DateTime::createFromFormat('d/m/Y', $m[1]);
            $to = \DateTime::createFromFormat('d/m/Y', $m[2]);
            if ($from) $header['period_from'] = $from->format('Y-m-d');
            if ($to) $header['period_to'] = $to->format('Y-m-d');
        }

        // Opening balance
        if (preg_match('/Opening\s+Balance\s*:?\s*([\d,]+\.\d{2})/i', $allText, $m)) {
            $header['opening_balance'] = (float) str_replace(',', '', $m[1]);
        }

        // Closing balance
        if (preg_match('/Closing\s+Balance\s*:?\s*([\d,]+\.\d{2})/i', $allText, $m)) {
            $header['closing_balance'] = (float) str_replace(',', '', $m[1]);
        }

        // Account holder
        if (preg_match('/Account\s+Holder\s*:?\s*(.+)/i', $allText, $m)) {
            $header['account_holder'] = trim($m[1]);
        }

        // Statement number
        if (preg_match('/Statement\s+(?:No|Number)\s*:?\s*(\d+)/i', $allText, $m)) {
            $header['statement_number'] = $m[1];
        }

        // Parse transactions using balance-difference
        $transactions = [];
        $prevBalance = $header['opening_balance'];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Skip lines without DD/MM/YYYY date
            if (!preg_match('/(\d{2}\/\d{2}\/\d{4})/', $trimmed, $dateMatch)) {
                continue;
            }

            $dateStr = $dateMatch[1];
            $dateObj = \DateTime::createFromFormat('d/m/Y', $dateStr);
            if (!$dateObj) continue;
            $date = $dateObj->format('Y-m-d');

            // Extract all decimal numbers from the line
            preg_match_all('/([\d,]+\.\d{2})/', $trimmed, $numMatches);
            $numbers = array_map(function ($n) { return (float) str_replace(',', '', $n); }, $numMatches[1] ?? []);

            if (empty($numbers)) continue;

            // Last number = balance
            $balance = end($numbers);

            // Description: text between date and first number
            $description = $trimmed;
            // Remove the date
            $description = preg_replace('/\d{2}\/\d{2}\/\d{4}/', '', $description, 1);
            // Remove all numbers at end
            $description = preg_replace('/([\d,]+\.\d{2}\s*)+$/', '', $description);
            $description = trim($description);

            // Amount = current balance - previous balance
            if ($prevBalance !== null) {
                $amount = round($balance - $prevBalance, 2);
            } else {
                // If no previous balance, try second-to-last number as amount
                $amount = count($numbers) >= 2 ? $numbers[count($numbers) - 2] : 0;
            }

            $transactions[] = [
                'date'        => $date,
                'description' => $description,
                'amount'      => $amount,
                'balance'     => $balance,
            ];

            $prevBalance = $balance;
        }

        return $this->buildParseResult($header, $transactions);
    }

    // =========================================================================
    //  ABSA PARSER
    // =========================================================================

    /**
     * Parse ABSA bank statement text (balance-difference via finalizeBalanceDiffTxn).
     */
    private function parseAbsaText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = [
            'bank'             => 'ABSA',
            'account_number'   => null,
            'statement_period' => null,
            'opening_balance'  => null,
            'closing_balance'  => null,
            'account_holder'   => null,
            'statement_number' => null,
            'period_from'      => null,
            'period_to'        => null,
            'bank_charges'     => [],
        ];

        // Account number
        if (preg_match('/Account\s+(?:Number|No)\s*:?\s*(\d{6,15})/i', $allText, $m)) {
            $header['account_number'] = $m[1];
        }

        // Account holder
        if (preg_match('/Account\s+Holder\s*:?\s*(.+)/i', $allText, $m)) {
            $header['account_holder'] = trim($m[1]);
        }

        // Statement number
        if (preg_match('/Statement\s+(?:No|Number)\s*:?\s*(\d+)/i', $allText, $m)) {
            $header['statement_number'] = $m[1];
        }

        $inTransactions = false;
        $transactions = [];
        $prevBalance = null;
        $currentDate = null;
        $textLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // "Balance Brought Forward" starts transaction section
            if (preg_match('/Balance\s+Brought\s+Forward/i', $trimmed)) {
                $inTransactions = true;

                // Extract opening balance from this line
                if (preg_match('/([\d,]+\.\d{2})\s*$/', $trimmed, $bm)) {
                    $header['opening_balance'] = (float) str_replace(',', '', $bm[1]);
                    $prevBalance = $header['opening_balance'];
                }
                continue;
            }

            // "Balance Carried Forward" ends transaction section
            if (preg_match('/Balance\s+Carried\s+Forward/i', $trimmed)) {
                // Finalize any pending transaction
                if ($currentDate !== null && !empty($textLines)) {
                    $txn = $this->finalizeBalanceDiffTxn($currentDate, $textLines, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                        $prevBalance = $txn['balance'];
                    }
                }

                // Extract closing balance
                if (preg_match('/([\d,]+\.\d{2})\s*$/', $trimmed, $bm)) {
                    $header['closing_balance'] = (float) str_replace(',', '', $bm[1]);
                }
                $inTransactions = false;
                continue;
            }

            if (!$inTransactions) continue;

            // Check for date line (YYYY-MM-DD)
            if (preg_match('/^(\d{4}-\d{2}-\d{2})\s+(.*)$/', $trimmed, $dm)) {
                // Finalize previous transaction
                if ($currentDate !== null && !empty($textLines)) {
                    $txn = $this->finalizeBalanceDiffTxn($currentDate, $textLines, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                        $prevBalance = $txn['balance'];
                    }
                }

                $currentDate = $dm[1];
                $textLines = [trim($dm[2])];
            } else {
                // Continuation line (multi-line transaction)
                if ($currentDate !== null) {
                    $textLines[] = $trimmed;
                }
            }
        }

        // Finalize last pending transaction
        if ($currentDate !== null && !empty($textLines)) {
            $txn = $this->finalizeBalanceDiffTxn($currentDate, $textLines, $prevBalance);
            if ($txn) {
                $transactions[] = $txn;
            }
        }

        // Statement period from transactions
        if (!empty($transactions)) {
            $header['period_from'] = $header['period_from'] ?? $transactions[0]['date'];
            $header['period_to'] = $header['period_to'] ?? end($transactions)['date'];
        }

        return $this->buildParseResult($header, $transactions);
    }

    // =========================================================================
    //  CAPITEC PARSER
    // =========================================================================

    /**
     * Parse Capitec bank statement text.
     */
    private function parseCapitecText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);
        $header = ['account_number' => '', 'account_holder' => '', 'branch_code' => '', 'statement_period' => '', 'statement_date' => '', 'statement_number' => null, 'period_from' => null, 'period_to' => null, 'opening_balance' => 0, 'closing_balance' => 0];
        $fullText = implode(' ', $lines);
        if (preg_match('/Account\s+No\.?\s*:?\s*(\d{8,15})/i', $fullText, $m)) $header['account_number'] = $m[1];
        if (preg_match('/Branch:\s*(\d+)/i', $fullText, $m)) $header['branch_code'] = $m[1];
        if (preg_match('/Statement\s+(?:No|Number)\.?\s*:?\s*(\d+)/i', $fullText, $m)) $header['statement_number'] = $m[1];
        if (empty($header['statement_number'])) {
            for ($i = 0; $i < count($lines) - 1; $i++) {
                if (preg_match('/Statement\s+No\.?\s*$/i', trim($lines[$i]))) {
                    $nextLine = trim($lines[$i + 1]);
                    if (preg_match('/^(\d+)/', $nextLine, $snm)) {
                        $header['statement_number'] = $snm[1];
                        break;
                    }
                }
            }
        }
        if (preg_match('/Balance\s+brought\s+forward\s+.*?([+-]?\d[\d ]*\.\d{2})/i', $fullText, $m)) $header['opening_balance'] = (float) str_replace(['+', ' '], '', $m[1]);

        $transactions = [];
        $currentTxn = null;
        $inTxn = false;

        foreach ($lines as $line) {
            $t = trim($line);
            if ($t === '') continue;
            if (preg_match('/Balance\s+brought\s+forward/i', $t)) { $inTxn = true; continue; }
            if (!$inTxn) continue;
            if (preg_match('/^(Capitec\s+Bank|Branch:|Device:|Tel:|Date\s+\d{2}\/\d{2}\/\d{4}|Account\s+(type|No)|Statement\s+No|Business\s+Account\s+Statement|Telephone|Business\s+Reg|Client\s+VAT|Relationship)/i', $t)) continue;
            if (preg_match('/Page:?\s+\d+|^Post\s+Trans|^Date\s+Date\s*$/i', $t)) continue;
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s*$/i', $t)) continue;
            if (preg_match('/^Balance\s+[+-]?\d/i', $t) && !preg_match('/Balance\s+(brought|carried)/i', $t)) continue;
            if (preg_match('/^(Fee\s+Total|VAT\s+@|VAT\s+Total|All\s+fees|Statements\s+are|capitecbank|financial\s+services|Reg\.?\s+No|VAT\s+Reg|24hr|Neutron|No\s+Limit|Overdraft|Prime\s+Lending)/i', $t)) continue;
            if (preg_match('/^\d{1,3}\.\d{4}%\s*$/', $t)) continue;
            if (preg_match('/^(Description\s+Reference\s+Fees|Fees\s+Amount\s+Balance)/i', $t)) continue;

            if (preg_match('/^(\d{2}\/\d{2}\/\d{2})\s+(\d{2}\/\d{2}\/\d{2})\s+(.+)$/', $t, $dm)) {
                if ($currentTxn !== null) { $txn = $this->finalizeCapitecTxn($currentTxn); if ($txn) $transactions[] = $txn; }
                $parts = explode('/', $dm[1]);
                $postDate = '20' . $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                $currentTxn = ['date' => $postDate, 'textLines' => [$dm[3]]];
            } elseif ($currentTxn !== null) { $currentTxn['textLines'][] = $t; }
        }
        if ($currentTxn !== null) { $txn = $this->finalizeCapitecTxn($currentTxn); if ($txn) $transactions[] = $txn; }

        if (!empty($transactions)) {
            $header['closing_balance'] = end($transactions)['balance'];

            $monthCounts = [];
            foreach ($transactions as $txn) {
                $ym = substr($txn['date'], 0, 7);
                if (!isset($monthCounts[$ym])) $monthCounts[$ym] = 0;
                $monthCounts[$ym]++;
            }
            arsort($monthCounts);
            $dominantMonth = array_key_first($monthCounts);
            $header['period_from'] = $dominantMonth . '-01';
            $header['period_to'] = date('Y-m-t', strtotime($dominantMonth . '-01'));
            $header['statement_period'] = date('d F Y', strtotime($header['period_from'])) . ' to ' . date('d F Y', strtotime($header['period_to']));
        }
        return $this->buildParseResult($header, $transactions);
    }

    // =========================================================================
    //  STANDARD BANK PARSER
    // =========================================================================

    /**
     * Parse Standard Bank statement text (balance-difference via finalizeBalanceDiffTxn).
     */
    private function parseStandardText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = [
            'bank'             => 'Standard Bank',
            'account_number'   => null,
            'statement_period' => null,
            'opening_balance'  => null,
            'closing_balance'  => null,
            'account_holder'   => null,
            'statement_number' => null,
            'period_from'      => null,
            'period_to'        => null,
            'bank_charges'     => [],
        ];

        // Account number
        if (preg_match('/Account\s+(?:Number|No)\s*:?\s*(\d{6,15})/i', $allText, $m)) {
            $header['account_number'] = $m[1];
        }

        // Account holder
        if (preg_match('/Account\s+Holder\s*:?\s*(.+)/i', $allText, $m)) {
            $header['account_holder'] = trim($m[1]);
        }

        // Statement number
        if (preg_match('/Statement\s+(?:No|Number)\s*:?\s*(\d+)/i', $allText, $m)) {
            $header['statement_number'] = $m[1];
        }

        $months = [
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'may' => '05', 'jun' => '06', 'jul' => '07', 'aug' => '08',
            'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12',
        ];

        $inTransactions = false;
        $transactions = [];
        $prevBalance = null;
        $currentDate = null;
        $textLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // "STATEMENT OPENING BALANCE" starts
            if (preg_match('/STATEMENT\s+OPENING\s+BALANCE/i', $trimmed)) {
                $inTransactions = true;

                // Extract opening balance
                if (preg_match('/([\d,]+\.\d{2})\s*$/', $trimmed, $bm)) {
                    $header['opening_balance'] = (float) str_replace(',', '', $bm[1]);
                    $prevBalance = $header['opening_balance'];
                }
                continue;
            }

            // "Statement Summary" ends
            if (preg_match('/Statement\s+Summary/i', $trimmed)) {
                // Finalize pending transaction
                if ($currentDate !== null && !empty($textLines)) {
                    $txn = $this->finalizeBalanceDiffTxn($currentDate, $textLines, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                        $prevBalance = $txn['balance'];
                    }
                }
                $inTransactions = false;
                continue;
            }

            if (!$inTransactions) continue;

            // Date: DD Mon YY -> 20YY-MM-DD
            if (preg_match('/^(\d{1,2})\s+([A-Za-z]{3})\s+(\d{2})\s+(.*)$/', $trimmed, $dm)) {
                // Finalize previous transaction
                if ($currentDate !== null && !empty($textLines)) {
                    $txn = $this->finalizeBalanceDiffTxn($currentDate, $textLines, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                        $prevBalance = $txn['balance'];
                    }
                }

                $day = str_pad($dm[1], 2, '0', STR_PAD_LEFT);
                $monthStr = strtolower($dm[2]);
                $monthNum = $months[$monthStr] ?? '01';
                $yearShort = $dm[3];
                $yearFull = '20' . $yearShort;

                $currentDate = sprintf('%s-%s-%s', $yearFull, $monthNum, $day);
                $textLines = [trim($dm[4])];
            } else {
                // Continuation line
                if ($currentDate !== null && !empty($trimmed)) {
                    $textLines[] = $trimmed;
                }
            }
        }

        // Finalize last pending transaction
        if ($currentDate !== null && !empty($textLines)) {
            $txn = $this->finalizeBalanceDiffTxn($currentDate, $textLines, $prevBalance);
            if ($txn) {
                $transactions[] = $txn;
            }
        }

        // Closing balance / period
        if (!empty($transactions)) {
            $header['closing_balance'] = $header['closing_balance'] ?? end($transactions)['balance'];
            $header['period_from'] = $header['period_from'] ?? $transactions[0]['date'];
            $header['period_to'] = $header['period_to'] ?? end($transactions)['date'];
        }

        return $this->buildParseResult($header, $transactions);
    }

    // =========================================================================
    //  SHARED HELPERS
    // =========================================================================

    /**
     * Finalize a balance-difference transaction.
     * Joins textLines, extracts all decimals, last = balance, diff from prevBalance.
     * Strips last 2 numbers from text for description.
     */
    private function finalizeBalanceDiffTxn(string $date, array $textLines, ?float $prevBalance): ?array
    {
        $fullText = implode(' ', $textLines);

        // Extract all decimal numbers
        preg_match_all('/([\d,]+\.\d{2})/', $fullText, $numMatches);
        $numbers = array_map(function ($n) { return (float) str_replace(',', '', $n); }, $numMatches[1] ?? []);

        if (empty($numbers)) return null;

        // Last number = balance
        $balance = end($numbers);

        // Amount = balance - prevBalance
        if ($prevBalance !== null) {
            $amount = round($balance - $prevBalance, 2);
        } else {
            // Fallback: second-to-last as amount
            $amount = count($numbers) >= 2 ? $numbers[count($numbers) - 2] : 0;
        }

        // Description: strip last 2 numbers from text
        $description = $fullText;
        // Remove numbers from end (balance and amount)
        $numCount = min(2, count($numbers));
        for ($i = 0; $i < $numCount; $i++) {
            $lastPos = strrpos($description, $numMatches[1][count($numMatches[1]) - 1 - $i]);
            if ($lastPos !== false) {
                $description = substr($description, 0, $lastPos);
            }
        }
        $description = trim(preg_replace('/\s+/', ' ', $description));

        return [
            'date'        => $date,
            'description' => $description,
            'amount'      => $amount,
            'balance'     => $balance,
        ];
    }

    private function finalizeCapitecTxn(array $txnData): ?array
    {
        $fullText = implode(' ', $txnData['textLines']);
        preg_match_all('/[+-]?\d[\d ]*\.\d{2}(?!\d)/', $fullText, $allNums, PREG_OFFSET_CAPTURE);
        if (empty($allNums[0]) || count($allNums[0]) < 2) return null;
        $numEntries = $allNums[0];
        $lastEntry = end($numEntries);
        $secondLastEntry = $numEntries[count($numEntries) - 2];
        $balance = (float) str_replace(['+', ' '], '', $lastEntry[0]);
        $amount = (float) str_replace(['+', ' '], '', $secondLastEntry[0]);
        if (abs($amount) < 0.01) return null;

        $desc = $fullText;
        $toRemove = array_slice($numEntries, -2);
        usort($toRemove, function ($a, $b) { return $b[1] - $a[1]; });
        foreach ($toRemove as $entry) { $desc = substr_replace($desc, '', $entry[1], strlen($entry[0])); }
        $description = trim(preg_replace('/\s+/', ' ', $desc));
        if (empty($description)) return null;
        return ['date' => $txnData['date'], 'description' => $description, 'amount' => $amount, 'balance' => $balance];
    }

    /**
     * Build the final parse result with summary computations.
     */
    private function buildParseResult(array $header, array $transactions): array
    {
        $totalCredits = 0;
        $totalDebits = 0;
        $creditCount = 0;
        $debitCount = 0;

        foreach ($transactions as $txn) {
            $amt = (float) ($txn['amount'] ?? 0);
            if ($amt >= 0) {
                $totalCredits += $amt;
                $creditCount++;
            } else {
                $totalDebits += abs($amt);
                $debitCount++;
            }
        }

        $openingBalance = $header['opening_balance'] ?? 0;
        $closingBalance = $header['closing_balance'] ?? 0;

        // calculated_closing = opening + credits - debits
        $calculatedClosing = round($openingBalance + $totalCredits - $totalDebits, 2);

        // balance_match = abs(calculated - actual) < 0.02
        $balanceMatch = abs($calculatedClosing - $closingBalance) < 0.02;

        $firstDate = !empty($transactions) ? $transactions[0]['date'] : null;
        $lastDate = !empty($transactions) ? end($transactions)['date'] : null;

        return [
            'header'       => $header,
            'transactions' => $transactions,
            'summary'      => [
                'total_credits'      => round($totalCredits, 2),
                'total_debits'       => round($totalDebits, 2),
                'credit_count'       => $creditCount,
                'debit_count'        => $debitCount,
                'opening_balance'    => $openingBalance,
                'closing_balance'    => $closingBalance,
                'calculated_closing' => $calculatedClosing,
                'balance_match'      => $balanceMatch,
                'transaction_count'  => count($transactions),
                'first_date'         => $firstDate,
                'last_date'          => $lastDate,
            ],
        ];
    }

    // =========================================================================
    //  FNB FIXER — Exact copy of the original CIMS AccountsController parser
    //  DO NOT MODIFY — this is a reference baseline
    // =========================================================================

    public function parsePdfFixer(Request $request, $clientId, $bankId)
    {
        $pages = $request->input('pages', []);
        $ocrPages = $request->input('ocr_pages', []);

        if (empty($pages)) {
            return response()->json(['error' => 'No text data received.'], 400);
        }

        $result = $this->parseFnbTextFixer($pages, $ocrPages);
        $result['_parser_version'] = 'CIMS-ORIGINAL-FIXER';
        return response()->json($result);
    }

    private function parseFnbTextFixer(array $pages, array $ocrPages = []): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $processedLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (preg_match('/^(Cr|Dr)$/i', $trimmed) && !empty($processedLines)) {
                $processedLines[count($processedLines) - 1] .= $trimmed;
            } else {
                $processedLines[] = $line;
            }
        }
        $lines = $processedLines;

        $header = $this->parseFnbHeaderFixer($lines);
        $transactions = $this->parseFnbTransactionsFixer($lines, $header);

        if (!empty($ocrPages)) {
            $ocrTxns = $this->parseFnbOcrTransactionsFixer($ocrPages);

            $ocrUsed = [];
            foreach ($transactions as $txn) {
                if (!empty($txn['description'])) {
                    foreach ($ocrTxns as $oi => $ocrTxn) {
                        if (isset($ocrUsed[$oi])) continue;
                        if ($txn['date'] === $ocrTxn['date'] && abs(abs($txn['amount']) - abs($ocrTxn['amount'])) < 0.01) {
                            $ocrUsed[$oi] = true;
                            break;
                        }
                    }
                }
            }

            foreach ($transactions as &$txn) {
                if (empty($txn['description'])) {
                    foreach ($ocrTxns as $oi => $ocrTxn) {
                        if (isset($ocrUsed[$oi])) continue;
                        if ($txn['date'] === $ocrTxn['date'] && abs(abs($txn['amount']) - abs($ocrTxn['amount'])) < 0.01 && !empty($ocrTxn['description'])) {
                            $txn['description'] = $ocrTxn['description'];
                            $ocrUsed[$oi] = true;
                            break;
                        }
                    }
                }
            }
            unset($txn);

            foreach ($transactions as &$txn) {
                if (empty($txn['description'])) {
                    foreach ($ocrTxns as $oi => $ocrTxn) {
                        if (isset($ocrUsed[$oi])) continue;
                        if ($txn['date'] === $ocrTxn['date'] && !empty($ocrTxn['description'])) {
                            $txn['description'] = $ocrTxn['description'];
                            $ocrUsed[$oi] = true;
                            break;
                        }
                    }
                }
            }
            unset($txn);
        }

        $lastDesc = '';
        $bankCharges = $header['bank_charges'] ?? [];
        foreach ($transactions as &$txn) {
            if (!empty($txn['description'])) { $lastDesc = $txn['description']; continue; }
            $absAmt = abs($txn['amount']);
            $found = false;
            foreach ($bankCharges as $cAmt => $cName) {
                if (abs($absAmt - $cAmt) < 0.01) { $txn['description'] = $cName; $found = true; break; }
            }
            if (!$found) {
                $txn['description'] = $lastDesc ? $lastDesc . ' (cont.)' : 'Bank charge';
            }
        }
        unset($txn);

        return $this->buildParseResultFixer($header, $transactions);
    }

    private function parseFnbOcrTransactionsFixer(array $ocrPages): array
    {
        $ocrTxns = [];
        $months = ['jan'=>'01','feb'=>'02','mar'=>'03','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12'];

        $allOcrText = implode("\n", $ocrPages);
        $endYear = '';
        $startYear = '';
        $startMonth = 0;
        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})\s+to\s+(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/i', $allOcrText, $pm)) {
            $startYear = $pm[3]; $endYear = $pm[6];
            $startMonth = intval($months[strtolower(substr($pm[2], 0, 3))] ?? 0);
        } elseif (preg_match('/(\d{4})/', $allOcrText, $pm)) {
            $startYear = $pm[1]; $endYear = $pm[1];
        }
        if (!$endYear) { $endYear = date('Y'); $startYear = $endYear; }

        $lines = explode("\n", $allOcrText);
        foreach ($lines as $line) {
            $t = trim($line);
            if (preg_match('/^(\d{1,2})\s+([A-Za-z]{3})\s+[|(\[]?\s*(.+?)\s+([\d,]+(?:\.\d{2})?)\s*(?:Cr|Dr)?\s*[|]?\s*([\d,]+\.\d{2})\s*(?:Cr|Dr)?/', $t, $m)) {
                $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $mon = $months[strtolower($m[2])] ?? '01';
                $monInt = intval($mon);
                if ($startYear !== $endYear) {
                    $txnYear = ($monInt >= $startMonth) ? $startYear : $endYear;
                } else {
                    $txnYear = $endYear;
                }
                $desc = trim($m[3]);
                $desc = preg_replace('/^[^A-Za-z0-9]+\s*/', '', $desc);
                $desc = preg_replace('/\s*[|}\]]+\s*$/', '', $desc);
                $rawAmt = str_replace(',', '', $m[4]);
                $amount = (float) $rawAmt;
                if (strpos($rawAmt, '.') === false && $amount > 100) {
                    $amount = $amount / 100;
                }
                $ocrTxns[] = [
                    'date' => $txnYear . '-' . $mon . '-' . $day,
                    'description' => $desc,
                    'amount' => $amount,
                ];
            }
        }
        return $ocrTxns;
    }

    private function parseFnbHeaderFixer(array $lines): array
    {
        $header = ['account_number' => '', 'account_holder' => '', 'branch_code' => '', 'statement_period' => '', 'statement_date' => '', 'statement_number' => null, 'period_from' => null, 'period_to' => null, 'opening_balance' => 0, 'closing_balance' => 0, 'bank_charges' => []];
        $fullText = implode(' ', $lines);
        if (preg_match('/(?:Platinum\s+Business\s+Account|Business\s+Account|Cheque\s+Account)\s*:?\s*(\d{8,15})/i', $fullText, $m)) $header['account_number'] = $m[1];
        elseif (preg_match('/Account\s*(?:No|Number|#)?\s*:?\s*(\d{8,15})/i', $fullText, $m)) $header['account_number'] = $m[1];
        if (preg_match('/Universal\s+Branch\s+Code\s*:?\s*(\d+)/i', $fullText, $m)) $header['branch_code'] = $m[1];
        if (preg_match('/Statement\s+Period\s*:?\s*(.+?)(?:Statement\s+Date|$)/i', $fullText, $m)) $header['statement_period'] = trim($m[1]);
        if (preg_match('/Statement\s+Date\s*:?\s*(\d{1,2}\s+\w+\s+\d{4})/i', $fullText, $m)) $header['statement_date'] = trim($m[1]);
        if (preg_match('/Statement\s+(?:No|Number)\s*:?\s*(\d+)/i', $fullText, $m)) $header['statement_number'] = $m[1];
        if (!empty($header['statement_period']) && preg_match('/(\d{1,2}\s+[A-Za-z]+\s+\d{4})\s+to\s+(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i', $header['statement_period'], $pm)) { $header['period_from'] = date('Y-m-d', strtotime($pm[1])); $header['period_to'] = date('Y-m-d', strtotime($pm[2])); }
        if (preg_match('/Opening\s+Balance\s+([\d,]+\.\d{2})\s*(Cr|Dr)?/i', $fullText, $m)) { $val = (float) str_replace(',', '', $m[1]); if (isset($m[2]) && strtolower($m[2]) === 'dr') $val = -$val; $header['opening_balance'] = $val; }
        if (preg_match('/Closing\s+Balance\s+([\d,]+\.\d{2})\s*(Cr|Dr)?/i', $fullText, $m)) { $val = (float) str_replace(',', '', $m[1]); if (isset($m[2]) && strtolower($m[2]) === 'dr') $val = -$val; $header['closing_balance'] = $val; }
        foreach ($lines as $line) { if (preg_match('/^\*(.+?)$/m', trim($line), $m)) { $header['account_holder'] = trim($m[1]); break; } }

        $chargeTypes = ['Service Fees', 'Cash Deposit Fees', 'Cash Handling Fees', 'Other Fees', 'Monthly Account Fee', 'Account Fee'];
        foreach ($chargeTypes as $ct) {
            if (preg_match('/' . preg_quote($ct, '/') . '\s+([\d,]+\.\d{2})\s*(Dr|Cr)?/i', $fullText, $cm)) {
                $chargeAmt = (float) str_replace(',', '', $cm[1]);
                if ($chargeAmt > 0) $header['bank_charges'][$chargeAmt] = $ct;
            }
        }

        return $header;
    }

    private function parseFnbTransactionsFixer(array $lines, array $header): array
    {
        $transactions = [];
        $months = ['jan'=>'01','feb'=>'02','mar'=>'03','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12'];

        $startYear = ''; $endYear = ''; $startMonth = 0; $endMonth = 0;
        $periodStr = $header['statement_period'] . ' ' . $header['statement_date'];
        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})\s+to\s+(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/i', $periodStr, $pm)) {
            $startYear = $pm[3]; $endYear = $pm[6];
            $startMonth = intval($months[strtolower(substr($pm[2], 0, 3))] ?? 0);
            $endMonth = intval($months[strtolower(substr($pm[5], 0, 3))] ?? 0);
        } elseif (preg_match('/(\d{4})/', $periodStr, $pm)) {
            $startYear = $pm[1]; $endYear = $pm[1];
        }
        if (!$endYear) { $endYear = date('Y'); $startYear = $endYear; }

        $inTxn = false;
        $lastDescription = '';

        foreach ($lines as $line) {
            $t = trim($line);
            if ($t === '') continue;
            if (preg_match('/Transactions\s+in\s+RAND/i', $t)) { $inTxn = true; continue; }
            if ($inTxn && preg_match('/Closing\s+Balance/i', $t)) { $inTxn = false; continue; }
            if (preg_match('/^\s*Date\s+Description\s+Amount\s+Balance/i', $t)) continue;
            if (preg_match('/^Page\s+\d+\s+of\s+\d+/i', $t)) continue;
            if (preg_match('/^Delivery\s+Method/i', $t)) continue;
            if (preg_match('/^EN:EM/i', $t)) continue;
            if (preg_match('/^\d{5,6}$/', $t)) continue;
            if (preg_match('/^(Branch\s+Number|Account\s+Number|GOLD\s+BUSINESS|DDA\s+AA|DDA\s+BH|XSTZFN|FN$)/i', $t)) continue;
            if (preg_match('/^(FNB\s+Verified|Reference\s+Number|Statements\s+\d|\$[A-Z0-9]+|PLATINUM\s+BUSINESS)/i', $t)) continue;
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}/', $t)) continue;
            if (preg_match('/No\.\s+(Credit|Debit)\s+Transactions|Turnover\s+for\s+Statement/i', $t)) continue;
            if (preg_match('/Accrued\s+Bank\s+Charges/i', $t)) continue;
            if (!$inTxn) {
                if (preg_match('/Opening\s+Balance|Statement\s+Balances|Bank\s+Charges|Interest\s+Rate|Service\s+Fees|Credit\s+Rate|Debit\s+Rate|Inclusive\s+of\s+VAT|Total\s+VAT/i', $t)) continue;
                if (preg_match('/^(P\s*O\s*Box|Street\s*Address|Universal\s*Branch|Lost\s*Cards|Account\s*Enquiries|Fraud|Relationship|Customer\s*VAT|Bank\s*VAT|Statement\s*(Period|Date)|Platinum|Gold|Tax\s*Invoice)/i', $t)) continue;
                if (preg_match('/Cash\s+Deposit\s+Fees|Cash\s+Handling\s+Fees|Other\s+Fees|Overdraft\s+Limit/i', $t)) continue;
                continue;
            }


            if (preg_match('/^(\d{2}\s+[A-Za-z]{3})\s*(.+)$/', $t, $lm)) {
                $rest = trim($lm[2]);

                $rest = preg_replace('/^[^A-Za-z0-9]+\s*/', '', $rest);

                if (preg_match('/^(.+?)\s+([\d,]+\.\d{2})\s*(Cr)?\s+([\d,]+\.\d{2})\s*(Cr|Dr)?\s*(?:[\d,]+\.\d{2})?\s*$/', $rest, $tm)) {
                    $amount = (float) str_replace(',', '', $tm[2]);
                    if (empty($tm[3])) $amount = -$amount;
                    $balance = (float) str_replace(',', '', $tm[4]);
                    $balanceType = isset($tm[5]) && $tm[5] !== '' ? $tm[5] : 'Dr';
                    if ($balanceType === 'Dr') $balance = -$balance;
                    $desc = trim($tm[1]);
                    $desc = preg_replace('/\s+\d{6}\*\d{4}\s+\d{2}\s+[A-Za-z]{3}\s*$/', '', $desc);
                    $desc = preg_replace('/\s+\d{6}\*\d{4}\s*$/', '', $desc);
                    if (preg_match('/(\d{2})\s+([A-Za-z]{3})/', $lm[1], $dm)) {
                        $day = $dm[1]; $mon = $months[strtolower($dm[2])] ?? '01';
                        $monInt = intval($mon);
                        if ($startYear !== $endYear) {
                            $txnYear = ($monInt >= $startMonth) ? $startYear : $endYear;
                        } else {
                            $txnYear = $endYear;
                        }
                        $fullDate = $txnYear . '-' . $mon . '-' . $day;
                    } else { $fullDate = $endYear . '-01-01'; }
                    $lastDescription = $desc;
                    $transactions[] = ['date' => $fullDate, 'description' => $desc, 'amount' => $amount, 'balance' => $balance];

                }
                elseif (preg_match('/^\s*([\d,]+\.\d{2})\s*(Cr)?\s+([\d,]+\.\d{2})\s*(Cr|Dr)?\s*(?:[\d,]+\.\d{2})?\s*$/', $rest, $tm)) {
                    $amount = (float) str_replace(',', '', $tm[1]);
                    if (empty($tm[2])) $amount = -$amount;
                    $balance = (float) str_replace(',', '', $tm[3]);
                    $balanceType = isset($tm[4]) && $tm[4] !== '' ? $tm[4] : 'Dr';
                    if ($balanceType === 'Dr') $balance = -$balance;
                    if (preg_match('/(\d{2})\s+([A-Za-z]{3})/', $lm[1], $dm)) {
                        $day = $dm[1]; $mon = $months[strtolower($dm[2])] ?? '01';
                        $monInt = intval($mon);
                        if ($startYear !== $endYear) {
                            $txnYear = ($monInt >= $startMonth) ? $startYear : $endYear;
                        } else {
                            $txnYear = $endYear;
                        }
                        $fullDate = $txnYear . '-' . $mon . '-' . $day;
                    } else { $fullDate = $endYear . '-01-01'; }
                    $transactions[] = ['date' => $fullDate, 'description' => '', 'amount' => $amount, 'balance' => $balance];
                }
            }
        }
        return $transactions;
    }

    private function buildParseResultFixer(array $header, array $transactions): array
    {
        $totalCredits = 0; $totalDebits = 0; $creditCount = 0; $debitCount = 0;
        foreach ($transactions as $txn) {
            if ($txn['amount'] > 0) { $totalCredits += $txn['amount']; $creditCount++; }
            else { $totalDebits += abs($txn['amount']); $debitCount++; }
        }
        $calculatedClosing = $header['opening_balance'] + $totalCredits - $totalDebits;
        $closingBalance = $header['closing_balance'] ?: (empty($transactions) ? 0 : end($transactions)['balance']);
        $balanceMatch = abs($calculatedClosing - $closingBalance) < 0.02;

        return [
            'header' => $header,
            'transactions' => $transactions,
            'summary' => [
                'transaction_count' => count($transactions), 'credit_count' => $creditCount, 'debit_count' => $debitCount,
                'total_credits' => round($totalCredits, 2), 'total_debits' => round($totalDebits, 2),
                'calculated_closing' => round($calculatedClosing, 2), 'balance_match' => $balanceMatch,
            ],
        ];
    }
}
