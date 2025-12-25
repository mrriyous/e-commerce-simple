<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Star,
    ShoppingCart,
    Package,
    Truck,
    Shield,
    Check,
    ArrowLeft,
    Minus,
    Plus,
    CheckCircle2,
    Loader2,
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import { getInitials } from '@/composables/useInitials';
import { useToast } from '@/composables/useToast';
import { add as addToCart } from '@/actions/App/Http/Controllers/CartController';

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: string;
    sku: string | null;
    stock_quantity: number;
    rating: string;
    reviews_count: number;
    image_url: string | null;
    category: Category | null;
}

interface Review {
    id: number;
    user_name: string;
    user_avatar: string;
    rating: number;
    comment: string;
    date: string;
    verified_purchase: boolean;
}

const props = defineProps<{
    product: Product;
    reviews: Review[];
}>();

const page = usePage();
const quantity = ref(1);
const isAddingToCart = ref(false);
const addToCartError = ref<string | null>(null);
const { success: showSuccessToast, error: showErrorToast } = useToast();

// Watch for success flash message from backend
watch(() => (page.props as any).flash?.success, (message: string | undefined) => {
    if (message) {
        showSuccessToast(message);
    }
});

// Watch for error flash messages
watch(() => (page.props as any).flash?.error, (message: string | undefined) => {
    if (message) {
        showErrorToast(message);
    }
});

const increaseQuantity = () => {
    quantity.value += 1;
};

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value -= 1;
    }
};

const isInStock = computed(() => props.product.stock_quantity > 0);
const hasLowStock = computed(() => props.product.stock_quantity > 0 && props.product.stock_quantity < 10);

const handleAddToCart = async () => {
    if (!page.props.auth?.user) {
        router.visit('/login');
        return;
    }

    if (!isInStock.value) {
        return;
    }

    isAddingToCart.value = true;
    addToCartError.value = null;

    try {
        await router.post(
            addToCart.url(),
            {
                product_id: props.product.id,
                quantity: quantity.value,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Cart count will be updated via Inertia shared props
                    addToCartError.value = null;
                    showSuccessToast('Item added to cart successfully!');
                },
                onError: (errors) => {
                    const errorMessage = errors.message || 'Unable to add item to cart. Please try again.';
                    addToCartError.value = errorMessage;
                    showErrorToast(errorMessage);
                },
                onFinish: () => {
                    isAddingToCart.value = false;
                },
            }
        );
    } catch (error) {
        addToCartError.value = 'An error occurred while adding to cart';
        isAddingToCart.value = false;
    }
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
};

const averageRating = computed(() => {
    if (props.reviews.length === 0) return '0.0';
    const sum = props.reviews.reduce((acc, review) => acc + review.rating, 0);
    return (sum / props.reviews.length).toFixed(1);
});

const ratingDistribution = computed(() => {
    const distribution: { [key: number]: number } = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
    props.reviews.forEach((review) => {
        if (review.rating >= 1 && review.rating <= 5) {
            distribution[review.rating]++;
        }
    });
    return distribution;
});
</script>

<template>
    <Head :title="`${product.name} - Product Details`" />
    <AppLayout variant="header">
        <!-- Breadcrumb -->
        <section class="w-screen border-b bg-background" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container mx-auto w-full max-w-7xl px-4 py-4">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Link href="/" class="hover:text-primary transition-colors">Home</Link>
                    <span>/</span>
                    <Link
                        v-if="product.category"
                        :href="`/categories/${product.category.slug}`"
                        class="hover:text-primary transition-colors"
                    >
                        {{ product.category.name }}
                    </Link>
                    <span v-if="product.category">/</span>
                    <span class="text-foreground">{{ product.name }}</span>
                </div>
            </div>
        </section>

        <!-- Product Detail Section -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-8 lg:py-12">
                <div class="grid gap-8 lg:grid-cols-2">
                    <!-- Product Image (Left) -->
                    <div class="space-y-4">
                        <div class="relative aspect-square w-full overflow-hidden rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 dark:border-slate-800/50">
                            <div class="flex h-full items-center justify-center">
                                <Package class="size-32 text-white/90 drop-shadow-xl" />
                            </div>
                            <!-- Stock badge -->
                            <div
                                v-if="!isInStock"
                                class="absolute right-4 top-4 rounded-full bg-red-500 px-3 py-1.5 text-sm font-semibold text-white"
                            >
                                Out of Stock
                            </div>
                            <div
                                v-else-if="hasLowStock"
                                class="absolute right-4 top-4 rounded-full bg-yellow-500 px-3 py-1.5 text-sm font-semibold text-white"
                            >
                                Low Stock
                            </div>
                        </div>
                    </div>

                    <!-- Product Info (Right) -->
                    <div class="space-y-6">
                        <!-- Category Badge -->
                        <div v-if="product.category">
                            <Link
                                :href="`/categories/${product.category.slug}`"
                                class="inline-block"
                            >
                                <Badge
                                    variant="secondary"
                                    class="bg-primary/10 text-primary hover:bg-primary/20"
                                >
                                    {{ product.category.name }}
                                </Badge>
                            </Link>
                        </div>

                        <!-- Product Name -->
                        <h1 class="text-3xl font-bold tracking-tight lg:text-4xl">
                            {{ product.name }}
                        </h1>

                        <!-- Rating -->
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="flex gap-0.5">
                                    <Star
                                        v-for="i in 5"
                                        :key="i"
                                        :class="[
                                            'size-5',
                                            i <= Math.round(parseFloat(product.rating))
                                                ? 'fill-yellow-400 text-yellow-400'
                                                : 'fill-gray-300 text-gray-300 dark:fill-gray-700 dark:text-gray-700',
                                        ]"
                                    />
                                </div>
                                <span class="text-lg font-semibold">{{ product.rating }}</span>
                            </div>
                            <Separator orientation="vertical" class="h-6" />
                            <span class="text-muted-foreground">
                                {{ product.reviews_count }} {{ product.reviews_count === 1 ? 'review' : 'reviews' }}
                            </span>
                            <Separator orientation="vertical" class="h-6" />
                            <span v-if="product.sku" class="text-sm text-muted-foreground">
                                SKU: {{ product.sku }}
                            </span>
                        </div>

                        <!-- Price -->
                        <div class="flex items-baseline gap-3">
                            <span class="text-4xl font-bold text-primary">
                                ${{ parseFloat(product.price).toFixed(2) }}
                            </span>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <h3 class="text-lg font-semibold">Description</h3>
                            <p class="text-muted-foreground leading-relaxed">
                                {{ product.description || 'No description available.' }}
                            </p>
                        </div>

                        <!-- Stock Info -->
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                            <div class="flex items-center gap-2">
                                <CheckCircle2
                                    v-if="isInStock"
                                    class="size-5 text-green-600 dark:text-green-400"
                                />
                                <Package
                                    v-else
                                    class="size-5 text-red-600 dark:text-red-400"
                                />
                                <span class="font-medium">
                                    <span v-if="isInStock">
                                        {{ product.stock_quantity }} {{ product.stock_quantity === 1 ? 'item' : 'items' }} in stock
                                    </span>
                                    <span v-else>Out of stock</span>
                                </span>
                            </div>
                        </div>

                        <!-- Quantity and Add to Cart -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium">Quantity:</span>
                                <div class="flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-800">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-9 w-9"
                                        :disabled="quantity <= 1"
                                        @click="decreaseQuantity"
                                    >
                                        <Minus class="size-4" />
                                    </Button>
                                    <span class="w-12 text-center font-semibold">{{ quantity }}</span>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-9 w-9"
                                        :disabled="quantity >= product.stock_quantity || !isInStock"
                                        @click="increaseQuantity"
                                    >
                                        <Plus class="size-4" />
                                    </Button>
                                </div>
                            </div>

                            <Button
                                size="lg"
                                :disabled="!isInStock || isAddingToCart"
                                class="w-full cursor-pointer bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-[1.02] hover:shadow-xl hover:shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
                                @click="handleAddToCart"
                            >
                                <Loader2
                                    v-if="isAddingToCart"
                                    class="mr-2 size-5 animate-spin"
                                />
                                <ShoppingCart
                                    v-else
                                    class="mr-2 size-5"
                                />
                                {{ isAddingToCart ? 'Adding...' : 'Add to Cart' }}
                            </Button>
                            <p
                                v-if="addToCartError"
                                class="text-sm text-red-600 dark:text-red-400"
                            >
                                {{ addToCartError }}
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/50">
                                <Truck class="size-5 text-primary" />
                                <div>
                                    <div class="font-semibold">Free Shipping</div>
                                    <div class="text-sm text-muted-foreground">On orders over $299</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/50">
                                <Shield class="size-5 text-primary" />
                                <div>
                                    <div class="font-semibold">Secure Payment</div>
                                    <div class="text-sm text-muted-foreground">100% secure checkout</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/50">
                                <Check class="size-5 text-primary" />
                                <div>
                                    <div class="font-semibold">Easy Returns</div>
                                    <div class="text-sm text-muted-foreground">30-day return policy</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Ratings and Reviews Section -->
        <section class="relative w-screen overflow-hidden bg-background" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-12 lg:py-16">
                <div class="mb-8">
                    <h2 class="mb-2 text-2xl font-bold">Customer Reviews</h2>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-0.5">
                                <Star
                                    v-for="i in 5"
                                    :key="i"
                                    :class="[
                                        'size-5',
                                        i <= Math.round(parseFloat(averageRating))
                                            ? 'fill-yellow-400 text-yellow-400'
                                            : 'fill-gray-300 text-gray-300 dark:fill-gray-700 dark:text-gray-700',
                                    ]"
                                />
                            </div>
                            <span class="text-xl font-bold">{{ averageRating }}</span>
                            <span class="text-muted-foreground">out of 5</span>
                        </div>
                        <Separator orientation="vertical" class="h-6" />
                        <span class="text-muted-foreground">
                            {{ reviews.length }} {{ reviews.length === 1 ? 'review' : 'reviews' }}
                        </span>
                    </div>
                </div>

                <!-- Rating Distribution -->
                <div class="mb-8 rounded-lg border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="mb-4 font-semibold">Rating Distribution</h3>
                    <div class="space-y-2">
                        <div
                            v-for="rating in [5, 4, 3, 2, 1]"
                            :key="rating"
                            class="flex items-center gap-3"
                        >
                            <div class="flex items-center gap-1 w-20">
                                <span class="text-sm font-medium">{{ rating }}</span>
                                <Star class="size-4 fill-yellow-400 text-yellow-400" />
                            </div>
                            <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden dark:bg-slate-800">
                                <div
                                    class="h-full bg-yellow-400 transition-all duration-300"
                                    :style="{
                                        width: `${reviews.length > 0 ? (ratingDistribution[rating as keyof typeof ratingDistribution] / reviews.length) * 100 : 0}%`,
                                    }"
                                ></div>
                            </div>
                            <span class="text-sm text-muted-foreground w-12 text-right">
                                {{ ratingDistribution[rating as keyof typeof ratingDistribution] }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="space-y-6">
                    <div
                        v-for="review in reviews"
                        :key="review.id"
                        class="rounded-lg border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/50"
                    >
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <Avatar class="size-10">
                                    <AvatarFallback class="bg-primary/10 text-primary font-semibold">
                                        {{ review.user_avatar }}
                                    </AvatarFallback>
                                </Avatar>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold">{{ review.user_name }}</span>
                                        <Badge
                                            v-if="review.verified_purchase"
                                            variant="secondary"
                                            class="bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs"
                                        >
                                            <CheckCircle2 class="mr-1 size-3" />
                                            Verified Purchase
                                        </Badge>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex gap-0.5">
                                            <Star
                                                v-for="i in 5"
                                                :key="i"
                                                :class="[
                                                    'size-4',
                                                    i <= review.rating
                                                        ? 'fill-yellow-400 text-yellow-400'
                                                        : 'fill-gray-300 text-gray-300 dark:fill-gray-700 dark:text-gray-700',
                                                ]"
                                            />
                                        </div>
                                        <span class="text-sm text-muted-foreground">
                                            {{ formatDate(review.date) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted-foreground leading-relaxed">
                            {{ review.comment }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <AppFooter />
    </AppLayout>
</template>

