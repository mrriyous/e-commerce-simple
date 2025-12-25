<?php

namespace App\Jobs;

use App\Mail\LowStockNotification;
use App\Models\Product;
use App\Models\ProductLowStockNotifProcess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $productId
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Retrieve the product fresh from the database
            $product = Product::findOrFail($this->productId);

            // Create tracking record
            $process = ProductLowStockNotifProcess::create([
                'product_id' => $product->id,
                'sent_at' => now(),
                'status' => 'pending',
            ]);

            // Send email
            Mail::to('mrriyous@gmail.com')->send(new LowStockNotification($product));

            // Update tracking record as sent
            $process->update([
                'status' => 'sent',
            ]);

            // Update product's low_notification_sent_at
            $product->update([
                'low_notification_sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Update tracking record as failed
            if (isset($process)) {
                $process->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }
}
