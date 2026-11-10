<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportingController;

Auth::loginUsingId(1);

$ctrl = new ReportingController();

// 1. Test CashBook
$cbReq = new Request([
    'date' => date('Y-m-d'),
    'branch_id' => 'all',
]);
$cbRes = $ctrl->cashbook($cbReq);
echo "CashBook view loaded successfully: " . $cbRes->name() . "\n";
$cbData = $cbRes->getData();
echo "CashBook Opening Balance: " . $cbData['openingBalance'] . "\n";
echo "CashBook Total Receipts: " . $cbData['totalReceipts'] . "\n";
echo "CashBook Total Payments: " . $cbData['totalPayments'] . "\n";
echo "CashBook Closing Balance: " . $cbData['closingBalance'] . "\n";

// 2. Test Profit & Loss Report
$plReq = new Request([
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-d'),
    'branch_id' => 'all',
    'report_type' => 'product',
]);
$plRes = $ctrl->fetchProfitLossReport($plReq);
$plData = json_decode($plRes->getContent(), true);
echo "P&L Summary Total Sales: " . ($plData['summary']['total_sales'] ?? 'N/A') . "\n";
echo "P&L Summary Total Cost (COGS): " . ($plData['summary']['total_cost'] ?? 'N/A') . "\n";
echo "P&L Summary Gross Profit: " . ($plData['summary']['gross_profit'] ?? 'N/A') . "\n";
echo "P&L Summary Net Profit: " . ($plData['summary']['net_profit'] ?? 'N/A') . "\n";
