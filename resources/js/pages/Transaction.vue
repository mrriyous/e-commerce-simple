<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { Package, ArrowLeft, MapPin, Calendar } from 'lucide-vue-next';

interface TransactionItem {
    id: number;
    product_name: string;
    product_image_url: string | null;
    quantity: number;
    price_at_time: string;
    total_price: string;
    price_at_time_formatted?: string;
    total_price_formatted?: string;
}

interface Transaction {
    id: number;
    transaction_number: string;
    delivery_address: string;
    delivery_city: string;
    delivery_zip_code: string;
    subtotal: string | number;
    delivery_fee: string | number;
    total: string | number;
    status: string;
    created_at: string;
    items: TransactionItem[];
}

const props = defineProps<{
    transaction: Transaction | null;
}>();

const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="transaction ? `Order ${transaction.transaction_number}` : 'Order Not Found'" />
    <AppLayout variant="header">
        <!-- Show error if transaction not found -->
        <div v-if="!transaction" class="container mx-auto w-full max-w-7xl px-4 py-12">
            <div class="flex flex-col items-center justify-center rounded-xl border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white py-12 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                <h2 class="mb-2 text-2xl font-bold tracking-tight">Order Not Found</h2>
                <p class="mb-6 text-base text-muted-foreground">The order you're looking for doesn't exist.</p>
                <Button :as-child="true" size="default" class="bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30">
                    <Link href="/cart">Back to Cart</Link>
                </Button>
            </div>
        </div>

        <!-- Transaction Details -->
        <div v-else>
        <!-- Breadcrumb -->
        <section class="w-screen border-b bg-background" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container mx-auto w-full max-w-7xl px-4 py-4">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Link href="/" class="hover:text-primary transition-colors">Home</Link>
                    <span>/</span>
                    <Link href="/cart" class="hover:text-primary transition-colors">Cart</Link>
                    <span>/</span>
                    <span class="text-foreground">Order {{ transaction.transaction_number }}</span>
                </div>
            </div>
        </section>

        <!-- Transaction Section -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-8 lg:py-12">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link
                            href="/cart"
                            class="group flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-muted-foreground transition-all duration-200 hover:border-primary/30 hover:bg-primary/5 hover:text-primary hover:shadow-sm dark:border-slate-800 dark:bg-slate-900/50 dark:hover:border-primary/30"
                        >
                            <ArrowLeft class="size-3.5 transition-transform duration-200 group-hover:-translate-x-1" />
                            Back to Cart
                        </Link>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight lg:text-3xl">Order {{ transaction.transaction_number }}</h1>
                    <div class="w-32"></div>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Left Side: Order Items -->
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Order Items List -->
                        <div class="rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-4 transition-all duration-200 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                            <h2 class="mb-4 text-xl font-bold tracking-tight">Order Items</h2>
                            <div class="space-y-3">
                                <div
                                    v-for="item in transaction.items"
                                    :key="item.id"
                                    class="flex items-center gap-4 rounded-lg border-2 border-slate-200/50 bg-white p-4 dark:border-slate-800/50 dark:bg-slate-900/50"
                                >
                                    <!-- Product Image -->
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 shadow-md shadow-blue-500/20 dark:border-slate-800/50">
                                        <Package class="size-10 text-white/90 drop-shadow-xl" />
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="mb-1 text-base font-bold tracking-tight">
                                            {{ item.product_name }}
                                        </h3>
                                        <p class="text-xs font-medium text-muted-foreground">
                                            Quantity: <span class="text-primary font-semibold">{{ item.quantity }}</span>
                                        </p>
                                        <p class="text-xs font-medium text-muted-foreground">
                                            Price: <span class="text-primary font-semibold">${{ item.price_at_time_formatted ?? parseFloat(item.price_at_time).toFixed(2) }}</span>
                                        </p>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="w-28 text-right">
                                        <p class="text-lg font-bold text-primary">${{ item.total_price_formatted ?? parseFloat(item.total_price).toFixed(2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-4 space-y-4">
                            <!-- Order Summary -->
                            <div class="rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-4 transition-all duration-200 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                                <h2 class="mb-4 text-xl font-bold tracking-tight">Order Summary</h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Items ({{ transaction.items.length }})</span>
                                        <span class="font-semibold">${{ Number(transaction.subtotal).toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Delivery</span>
                                        <span v-if="Number(transaction.delivery_fee) === 0" class="font-semibold text-green-600 dark:text-green-400">Free</span>
                                        <span v-else class="font-semibold">${{ Number(transaction.delivery_fee).toFixed(2) }}</span>
                                    </div>
                                    <div class="border-t border-slate-200 pt-3 dark:border-slate-800">
                                        <div class="flex justify-between text-lg font-bold">
                                            <span>Total</span>
                                            <span class="text-primary">${{ Number(transaction.total).toFixed(2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Info -->
                            <div class="rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-4 transition-all duration-200 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                                <h2 class="mb-4 text-xl font-bold tracking-tight">Order Information</h2>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <Calendar class="mt-0.5 size-4 text-muted-foreground" />
                                        <div>
                                            <p class="text-sm font-semibold">Order Date</p>
                                            <p class="text-xs text-muted-foreground">{{ formatDate(transaction.created_at) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <Package class="mt-0.5 size-4 text-muted-foreground" />
                                        <div>
                                            <p class="text-sm font-semibold">Status</p>
                                            <p class="text-xs text-muted-foreground capitalize">{{ transaction.status }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Address -->
                            <div class="rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-4 transition-all duration-200 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                                <h2 class="mb-4 text-xl font-bold tracking-tight">Delivery Address</h2>
                                <div class="flex items-start gap-3">
                                    <MapPin class="mt-0.5 size-4 text-muted-foreground" />
                                    <div>
                                        <p class="text-sm text-muted-foreground">
                                            {{ transaction.delivery_address }}<br>
                                            {{ transaction.delivery_city }}, {{ transaction.delivery_zip_code }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <Button
                                :as-child="true"
                                size="default"
                                class="cursor-pointer w-full bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30"
                            >
                                <Link href="/">Continue Shopping</Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </div>

        <!-- Footer -->
        <AppFooter />
    </AppLayout>
</template>

