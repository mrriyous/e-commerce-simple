<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Star, ArrowRight, Package, Loader2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';
import { add as addToCart } from '@/actions/App/Http/Controllers/CartController';

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

interface Props {
    product: Product;
    getProductIcon?: (productName: string) => any;
}

const props = defineProps<Props>();

const page = usePage();
const isAddingToCart = ref(false);
const { success: showSuccessToast, error: showErrorToast } = useToast();

const getIcon = () => {
    if (props.getProductIcon) {
        return props.getProductIcon(props.product.name);
    }
    return Package;
};

const handleAddToCart = async (e: Event) => {
    e.preventDefault();
    e.stopPropagation();

    if (!page.props.auth?.user) {
        router.visit('/login');
        return;
    }

    if (props.product.stock_quantity <= 0) {
        return;
    }

    isAddingToCart.value = true;

    try {
        await router.post(
            addToCart.url(),
            {
                product_id: props.product.id,
                quantity: 1,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    showSuccessToast('Item added to cart successfully!');
                },
                onError: (errors) => {
                    showErrorToast(errors.message || 'Unable to add item to cart. Please try again.');
                },
                onFinish: () => {
                    isAddingToCart.value = false;
                },
            }
        );
    } catch (error) {
        isAddingToCart.value = false;
    }
};
</script>

<template>
    <Card
        class="group relative overflow-hidden border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-0 transition-all duration-300 hover:scale-[1.02] hover:border-primary/30 hover:shadow-2xl hover:shadow-primary/10 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900 dark:hover:border-primary/30"
    >
        <!-- Animated background gradient on hover -->
        <div class="absolute inset-0 bg-linear-to-br from-primary/0 to-primary/0 transition-all duration-300 group-hover:from-primary/5 group-hover:to-primary/5"></div>

        <Link
            :href="`/products/${product.slug}`"
            class="block"
        >
            <div
                class="relative aspect-square w-full overflow-hidden bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 transition-transform duration-300 group-hover:scale-105"
            >
                <div class="flex h-full items-center justify-center">
                    <component
                        :is="getIcon()"
                        class="size-20 text-white/90 drop-shadow-xl transition-transform duration-300 group-hover:scale-110"
                    />
                </div>
                <!-- Decorative corner accent -->
                <div class="absolute -right-8 -top-8 size-32 rounded-full bg-white/10 blur-2xl"></div>
                <!-- Stock badge -->
                <div
                    v-if="product.stock_quantity <= 0"
                    class="absolute right-2 top-2 rounded-full bg-red-500 px-2 py-1 text-xs font-semibold text-white"
                >
                    Out of Stock
                </div>
                <div
                    v-else-if="product.stock_quantity < 10"
                    class="absolute right-2 top-2 rounded-full bg-yellow-500 px-2 py-1 text-xs font-semibold text-white"
                >
                    Low Stock
                </div>
            </div>
            <CardHeader class="relative px-6 pt-5">
                <div class="mb-3 flex items-center gap-1.5">
                    <div class="flex gap-0.5">
                        <Star
                            v-for="i in 5"
                            :key="i"
                            :class="[
                                'size-4 drop-shadow-sm',
                                i <= Math.round(parseFloat(product.rating))
                                    ? 'fill-yellow-400 text-yellow-400'
                                    : 'fill-gray-300 text-gray-300 dark:fill-gray-700 dark:text-gray-700',
                            ]"
                        />
                    </div>
                    <span class="ml-1.5 text-sm font-medium text-muted-foreground">
                        {{ product.rating }} ({{ product.reviews_count }})
                    </span>
                </div>
                <CardTitle class="mb-2 text-lg">{{ product.name }}</CardTitle>
                <CardDescription class="text-sm leading-relaxed">
                    {{ product.description || 'No description available.' }}
                </CardDescription>
            </CardHeader>
        </Link>
        <CardContent class="relative px-6 pb-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-2xl font-bold text-primary"
                        >${{ parseFloat(product.price).toFixed(2) }}</span
                    >
                </div>
                <Button
                    size="sm"
                    :disabled="product.stock_quantity <= 0 || isAddingToCart"
                    class="group/btn bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    @click.prevent.stop="handleAddToCart"
                >
                    <Loader2
                        v-if="isAddingToCart"
                        class="mr-1.5 size-3.5 animate-spin"
                    />
                    <ArrowRight
                        v-else
                        class="ml-1.5 size-3.5 transition-transform duration-200 group-hover/btn:translate-x-1"
                    />
                    {{ isAddingToCart ? 'Adding...' : 'Add to Cart' }}
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
