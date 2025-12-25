<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, ArrowRight } from 'lucide-vue-next';

interface Transaction {
    id: number;
    transaction_number: string;
}

const props = defineProps<{
    transaction: Transaction;
}>();
</script>

<template>
    <Head title="Thank You" />
    <AppLayout variant="header">
        <!-- Thank You Section -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container relative mx-auto w-full max-w-4xl px-4 py-16 lg:py-24">
                <!-- Success Icon -->
                <div class="mb-6 flex justify-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-linear-to-br from-green-500 via-green-600 to-green-700 shadow-lg shadow-green-500/20">
                        <CheckCircle class="size-12 text-white drop-shadow-xl" />
                    </div>
                </div>

                <!-- Thank You Message -->
                <div class="mb-8 text-center">
                    <h1 class="mb-3 text-3xl font-bold tracking-tight lg:text-4xl">Thank You for Your Purchase!</h1>
                    <p class="text-lg text-muted-foreground">
                        Your order has been placed successfully.
                    </p>
                </div>

                <!-- Action Button -->
                <div class="flex justify-center">
                    <Button
                        :as-child="true"
                        size="default"
                        class="bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30"
                    >
                        <Link :href="`/transactions/${transaction.transaction_number}`" class="flex items-center gap-2">
                            View Order Details
                            <ArrowRight class="size-4" />
                        </Link>
                    </Button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <AppFooter />
    </AppLayout>
</template>

