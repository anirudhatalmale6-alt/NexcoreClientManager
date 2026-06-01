<?php
// Migration: Add opening_balance_date and opening_balance_amount to bank accounts table
// Run once via URL then delete

require_once __DIR__ . '/../../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$table = 'cims_gl_bank_accounts_linked_to_coa';

if (!Schema::hasColumn($table, 'opening_balance_date')) {
    Schema::table($table, function (Blueprint $t) {
        $t->date('opening_balance_date')->nullable()->after('is_active');
    });
    echo "Added opening_balance_date column.\n";
} else {
    echo "opening_balance_date already exists.\n";
}

if (!Schema::hasColumn($table, 'opening_balance_amount')) {
    Schema::table($table, function (Blueprint $t) {
        $t->decimal('opening_balance_amount', 15, 2)->nullable()->default(0)->after('opening_balance_date');
    });
    echo "Added opening_balance_amount column.\n";
} else {
    echo "opening_balance_amount already exists.\n";
}

echo "\nDone. Delete this file now.\n";
