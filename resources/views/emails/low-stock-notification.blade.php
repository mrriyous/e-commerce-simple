<x-mail::message>
# Low Stock Alert

Hello,

This is to notify you that the following product is running low on stock.

A product with **{{ $product->stock_quantity }}** units remaining needs restocking attention.

**Product Details:**
- **Product Name:** {{ $product->name }}
- **SKU:** {{ $product->sku ?? 'N/A' }}
- **Current Stock:** {{ $product->stock_quantity }} units
- **Price:** ${{ number_format($product->price, 2) }}
- **Date:** {{ $product->updated_at->format('M d, Y H:i') }}

Please consider restocking this product soon to avoid running out of inventory.

Thank you for your attention!

Best regards,<br>
{{ config('app.name') }} Team
</x-mail::message>