<x-mail::message>
# Daily Sales Report

Hello Admin,

Here is your daily sales report for **{{ $reportDate }}**.

## Summary

- **Total Sales:** ${{ number_format($totalSales, 2) }}
- **Total Items Sold:** {{ number_format($totalItems) }}
- **Total Transactions:** {{ number_format($totalTransactions) }}

## Products Sold Today

<x-mail::table>
| Product Name | Quantity Sold | Average Price | Total Revenue |
|:------------|--------------:|--------------:|--------------:|
@foreach($compiledProducts as $product)
| {{ $product['product_name'] }} | {{ number_format($product['total_quantity']) }} | ${{ number_format($product['average_price'], 2) }} | ${{ number_format($product['total_revenue'], 2) }} |
@endforeach
</x-mail::table>

Thank you for your attention!

Best regards,<br>
{{ config('app.name') }} Team
</x-mail::message>

