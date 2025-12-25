<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ProductTile from '@/components/ProductTile.vue';
import { Badge } from '@/components/ui/badge';
import { Head, Link } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import type { PaginatedData } from '@/types';

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: string;
    stock_quantity: number;
    rating: string;
    reviews_count: number;
    image_url: string | null;
}

defineProps<{
    query: string;
    products: PaginatedData<Product>;
}>();
</script>

<template>
    <Head :title="query ? `Search: ${query}` : 'Search Products'" />
    <AppLayout variant="header">
        <!-- Search Header -->
        <section class="w-screen border-b bg-background" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container mx-auto w-full max-w-7xl px-4 py-4 lg:py-6">
                <div class="mb-2">
                    <Badge
                        variant="secondary"
                        class="mb-2 inline-flex items-center gap-2 bg-primary/10 text-primary"
                    >
                        <Search class="size-3 fill-primary text-primary" />
                        Search Results
                    </Badge>
                </div>
                <h1 class="mb-1 text-2xl font-bold tracking-tight sm:text-3xl lg:text-4xl">
                    <span v-if="query">Search: "{{ query }}"</span>
                    <span v-else>Search Products</span>
                </h1>
                <p class="text-sm text-muted-foreground sm:text-base">
                    <span v-if="query && products.total > 0">
                        Found {{ products.total }} {{ products.total === 1 ? 'result' : 'results' }} for "{{ query }}"
                    </span>
                    <span v-else-if="query && products.total === 0">
                        No results found for "{{ query }}"
                    </span>
                    <span v-else>
                        Enter a search term to find products
                    </span>
                </p>
            </div>
        </section>

        <!-- Products Grid -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -right-20 top-20 size-96 rounded-full bg-blue-200/10 blur-3xl dark:bg-blue-800/5"></div>
                <div class="absolute -left-20 bottom-20 size-96 rounded-full bg-yellow-200/10 blur-3xl dark:bg-yellow-800/5"></div>
            </div>

            <div class="container relative mx-auto w-full max-w-7xl px-4 py-12 lg:py-16">
                <!-- Products Count -->
                <div v-if="products.data.length > 0" class="mb-8 flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">
                        Showing {{ products.from }} to {{ products.to }} of {{ products.total }} products
                    </p>
                </div>

                <!-- Products Grid -->
                <div v-if="products.data.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductTile
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <!-- Empty State -->
                <div v-else class="py-16 text-center">
                    <Search class="mx-auto mb-4 size-16 text-muted-foreground" />
                    <h3 class="mb-2 text-xl font-semibold">No products found</h3>
                    <p class="text-muted-foreground">
                        <span v-if="query">
                            We couldn't find any products matching "{{ query }}". Try different keywords or browse our categories.
                        </span>
                        <span v-else>
                            Enter a search term to find products.
                        </span>
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="products.links && products.links.length > 3" class="mt-12 flex items-center justify-center gap-2">
                    <Link
                        v-for="link in products.links"
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

