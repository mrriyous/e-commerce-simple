<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReport;
use App\Models\Transaction;
use App\Models\TransactionReportNotifProcess;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class GenerateDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:generate-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send daily sales report email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating daily sales report...');

        $reportDate = Carbon::today()->format('Y-m-d');
        $reportDateFormatted = Carbon::today()->format('F j, Y');

        // Get all transactions for the report date
        $transactions = Transaction::whereDate('created_at', $reportDate)
            ->with('items')
            ->get();

        if ($transactions->isEmpty()) {
            $this->info("No transactions found for {$reportDateFormatted}. No report will be generated.");
            return Command::SUCCESS;
        }

        // Collect all items from all transactions
        $allItems = collect();
        foreach ($transactions as $transaction) {
            $allItems = $allItems->merge($transaction->items);
        }

        // Compile data by product name
        $compiledProducts = $allItems->groupBy('product_name')->map(function ($items, $productName) {
            $totalQuantity = $items->sum('quantity');
            $totalRevenue = $items->sum('total_price');
            $averagePrice = $items->avg('price_at_time');

            return [
                'product_name' => $productName,
                'total_quantity' => $totalQuantity,
                'total_revenue' => $totalRevenue,
                'average_price' => $averagePrice,
            ];
        })->values()->toArray();

        // Calculate totals
        $totalSales = $transactions->sum('total');
        $totalItems = $allItems->sum('quantity');
        $totalTransactions = $transactions->count();

        // Create tracking record with report data
        $process = TransactionReportNotifProcess::create([
            'report_date' => $reportDate,
            'report_data' => $compiledProducts,
            'total_sales' => $totalSales,
            'total_items' => $totalItems,
            'total_transactions' => $totalTransactions,
            'sent_at' => now(),
            'status' => 'pending',
        ]);

        try {
            // Send email to admin
            Mail::to('mrriyous@gmail.com')->send(
                new DailySalesReport(
                    $compiledProducts,
                    $totalSales,
                    $totalItems,
                    $totalTransactions,
                    $reportDateFormatted
                )
            );

            // Update tracking record as sent (data already saved above)
            $process->update([
                'status' => 'sent',
            ]);

            $this->info("Daily sales report for {$reportDateFormatted} has been sent successfully.");
            $this->info("Total Sales: $" . number_format($totalSales, 2));
            $this->info("Total Items Sold: {$totalItems}");
            $this->info("Total Transactions: {$totalTransactions}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            // Update tracking record as failed
            $process->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->error("Failed to send daily sales report: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

