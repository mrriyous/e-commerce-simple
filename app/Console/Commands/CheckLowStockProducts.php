<?php

namespace App\Console\Commands;

use App\Jobs\SendLowStockNotification;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckLowStockProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for low stock products and send notification emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for low stock products...');

        $threshold = 10; // Low stock threshold
        $threeDaysAgo = Carbon::now()->subDays(3);

        // Find products with low stock where notification hasn't been sent in the last 3 days or never sent
        $lowStockProducts = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', $threshold)
            ->where(function ($query) use ($threeDaysAgo) {
                $query->whereNull('low_notification_sent_at')
                    ->orWhere('low_notification_sent_at', '<=', $threeDaysAgo);
            })
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low stock products found that need notification.');
            return Command::SUCCESS;
        }

        $this->info("Found {$lowStockProducts->count()} product(s) with low stock.");

        foreach ($lowStockProducts as $product) {
            $this->line("Dispatching notification for: {$product->name} (Stock: {$product->stock_quantity})");
            SendLowStockNotification::dispatch($product->id);
        }

        $this->info('Low stock notification jobs have been dispatched successfully.');

        return Command::SUCCESS;
    }
}
