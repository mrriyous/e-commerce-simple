<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Head, Link } from '@inertiajs/vue3';
import { Receipt, Calendar, Package, ArrowRight, ArrowLeft } from 'lucide-vue-next';
import type { PaginatedData } from '@/types';

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
    items_count?: number;
}

const props = defineProps<{
    transactions: PaginatedData<Transaction>;
}>();

const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'completed':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'cancelled':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300';
    }
};
</script>

<template>
    <Head title="My Transactions" />
    <AppLayout variant="header">
        <!-- Header -->
        <section class="w-screen border-b bg-background" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container mx-auto w-full max-w-7xl px-4 py-4">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Link href="/" class="hover:text-primary transition-colors">Home</Link>
                    <span>/</span>
                    <span class="text-foreground">My Transactions</span>
                </div>
            </div>
        </section>

        <!-- Transactions Section -->
        <section class="relative w-screen bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-8 lg:py-12">
                <!-- Page Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link
                            href="/"
                            class="group flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-muted-foreground transition-all duration-200 hover:border-primary/30 hover:bg-primary/5 hover:text-primary hover:shadow-sm dark:border-slate-800 dark:bg-slate-900/50 dark:hover:border-primary/30"
                        >
                            <ArrowLeft class="size-3.5 transition-transform duration-200 group-hover:-translate-x-1" />
                            Back to Home
                        </Link>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight lg:text-3xl">My Transactions</h1>
                    <div class="w-32"></div>
                </div>

                <!-- Transactions List -->
                <div v-if="transactions.data && transactions.data.length > 0" class="space-y-4">
                    <div
                        v-for="transaction in transactions.data"
                        :key="transaction.id"
                        class="group relative rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-6 transition-all duration-300 hover:scale-[1.01] hover:border-primary/30 hover:shadow-lg hover:shadow-primary/10 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900 dark:hover:border-primary/30"
                    >
                        <!-- Animated background gradient on hover -->
                        <div class="absolute inset-0 rounded-lg bg-linear-to-br from-primary/0 to-primary/0 transition-all duration-300 group-hover:from-primary/5 group-hover:to-primary/5"></div>

                        <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <!-- Left Side: Transaction Info -->
                            <div class="flex-1 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 shadow-md shadow-blue-500/20 dark:border-slate-800/50">
                                        <Receipt class="size-6 text-white/90 drop-shadow-xl" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold tracking-tight text-foreground">
                                            {{ transaction.transaction_number }}
                                        </h3>
                                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                            <Calendar class="size-3.5" />
                                            <span>{{ formatDate(transaction.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    <Badge :class="getStatusColor(transaction.status)" class="font-semibold">
                                        {{ transaction.status }}
                                    </Badge>
                                    <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                                        <Package class="size-4" />
                                        <span>{{ transaction.items_count || 0 }} item(s)</span>
                                    </div>
                                </div>

                                <div class="text-sm text-muted-foreground">
                                    <div class="flex items-center gap-1.5">
                                        <span>Delivery to:</span>
                                        <span class="font-medium text-foreground">
                                            {{ transaction.delivery_address }}, {{ transaction.delivery_city }} {{ transaction.delivery_zip_code }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side: Total & Actions -->
                            <div class="flex flex-col items-end gap-3 lg:items-end">
                                <div class="text-right">
                                    <p class="text-sm text-muted-foreground">Total</p>
                                    <p class="text-2xl font-bold text-primary">${{ Number(transaction.total).toFixed(2) }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">
                                        Subtotal: ${{ Number(transaction.subtotal).toFixed(2) }}
                                        <span v-if="Number(transaction.delivery_fee) > 0">
                                            + Delivery: ${{ Number(transaction.delivery_fee).toFixed(2) }}
                                        </span>
                                        <span v-else class="text-green-600 dark:text-green-400">
                                            (Free Delivery)
                                        </span>
                                    </p>
                                </div>
                                <Button
                                    :as-child="true"
                                    variant="outline"
                                    size="default"
                                    class="border-slate-200 bg-white text-muted-foreground shadow-lg transition-all duration-200 hover:scale-105 hover:bg-primary/5 hover:text-primary hover:shadow-xl dark:border-slate-800 dark:bg-slate-900/50 dark:hover:bg-primary/10"
                                >
                                    <Link :href="`/transactions/${transaction.transaction_number}`" class="flex items-center gap-2">
                                        View Details
                                        <ArrowRight class="size-4" />
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex flex-col items-center justify-center rounded-xl border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white py-16 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 shadow-lg shadow-blue-500/20">
                        <Receipt class="size-10 text-white/90 drop-shadow-xl" />
                    </div>
                    <h2 class="mb-2 text-2xl font-bold tracking-tight">No transactions yet</h2>
                    <p class="mb-6 text-base text-muted-foreground">Start shopping to see your transactions here!</p>
                    <Button :as-child="true" size="default" class="bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30">
                        <Link href="/">Start Shopping</Link>
                    </Button>
                </div>

                <!-- Pagination -->
                <div v-if="transactions.links && transactions.links.length > 3" class="mt-12 flex items-center justify-center gap-2">
                    <Link
                        v-for="link in transactions.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                            link.active
                                ? 'bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20'
                                : 'bg-white text-muted-foreground hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800',
                            !link.url && 'opacity-50 cursor-not-allowed',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </section>

        <!-- Footer -->
        <AppFooter />
    </AppLayout>
</template>

