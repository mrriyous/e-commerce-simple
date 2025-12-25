<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    ShoppingCart,
    Trash2,
    Minus,
    Plus,
    Package,
    ArrowLeft,
    CheckCircle,
    ArrowRight,
    X,
} from 'lucide-vue-next';
import { ref, computed, reactive, watch } from 'vue';
import { useToast } from '@/composables/useToast';
import { update, remove } from '@/actions/App/Http/Controllers/CartController';

interface Product {
    id: number;
    name: string;
    slug: string;
    price: string;
    image_url: string | null;
    stock_quantity: number;
}

interface CartItem {
    id: number;
    quantity: number;
    price_at_time: string;
    price_at_time_formatted: string | null;
    total_price: string;
    total_price_formatted: string | null;
    subtotal_formatted: string | null;
    product: Product;
}

interface Cart {
    id: number;
    items: CartItem[];
    total: number;
    items_count: number;
}

interface Transaction {
    id: number;
    transaction_number: string;
}

const props = defineProps<{
    cartData: Cart;
    transaction?: Transaction | null;
}>();

const cart = computed(() => props.cartData);
const page = usePage();
const showThankYou = ref(!!props.transaction);

// Watch for transaction in page props (from flash)
watch(() => (page.props as any).transaction, (transaction) => {
    if (transaction) {
        showThankYou.value = true;
    }
}, { immediate: true });

const dismissThankYou = () => {
    showThankYou.value = false;
};

const { success: showSuccessToast, error: showErrorToast } = useToast();
// Initialize selectedItems with all cart item IDs by default
const selectedItems = ref<number[]>(cart.value.items.map((item) => item.id));
const updatingQuantities = ref<Set<number>>(new Set());
const removingItems = ref<Set<number>>(new Set());
const isProcessingCheckout = ref(false);

// Create a reactive object to track item selections for v-model
const itemSelections = reactive<Record<number, boolean>>({});

// Initialize and sync itemSelections with cart items
const syncItemSelections = () => {
    cart.value.items.forEach((item) => {
        if (!(item.id in itemSelections)) {
            // Default to true (selected) for new items
            itemSelections[item.id] = selectedItems.value.includes(item.id);
        }
    });
    // Remove selections for items that no longer exist in cart
    Object.keys(itemSelections).forEach((id) => {
        const itemId = Number(id);
        if (!cart.value.items.find((item) => item.id === itemId)) {
            delete itemSelections[itemId];
        }
    });
};

// Initialize on mount - set all items as selected by default
cart.value.items.forEach((item) => {
    itemSelections[item.id] = true;
});
syncItemSelections();

// Watch cart items to update itemSelections when items are added/removed
watch(() => cart.value.items, () => {
    syncItemSelections();
}, { deep: true });

// Sync itemSelections with selectedItems (when selectedItems changes from Select All, etc.)
watch(selectedItems, (newSelected) => {
    cart.value.items.forEach((item) => {
        itemSelections[item.id] = newSelected.includes(item.id);
    });
}, { deep: true });

// Watch itemSelections and update selectedItems (when individual checkboxes change)
let isUpdatingFromSelections = false;
watch(itemSelections, (newSelections) => {
    if (isUpdatingFromSelections) return;
    isUpdatingFromSelections = true;
    selectedItems.value = Object.entries(newSelections)
        .filter(([_, selected]) => selected)
        .map(([id, _]) => Number(id));
    isUpdatingFromSelections = false;
}, { deep: true });

const isAllSelected = computed({
    get: () => {
        return cart.value.items.length > 0 && selectedItems.value.length === cart.value.items.length;
    },
    set: (checked: boolean) => {
        if (checked) {
            selectedItems.value = cart.value.items.map((item) => item.id);
        } else {
            selectedItems.value = [];
        }
    }
});

const isIndeterminate = computed(() => {
    return selectedItems.value.length > 0 && selectedItems.value.length < cart.value.items.length;
});

const handleUpdateQuantity = async (item: CartItem, newQuantity: number) => {
    if (newQuantity < 1 || newQuantity > item.product.stock_quantity) {
        return;
    }

    updatingQuantities.value.add(item.id);

    try {
        await router.put(
            update.url({ cartItem: item.id }),
            { quantity: newQuantity },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // showSuccessToast('Cart updated');
                },
                onError: (errors) => {
                    showErrorToast(errors.message || 'Failed to update quantity');
                },
                onFinish: () => {
                    updatingQuantities.value.delete(item.id);
                },
            }
        );
    } catch (error) {
        updatingQuantities.value.delete(item.id);
        showErrorToast('Failed to update quantity');
    }
};

const handleRemoveItem = async (itemId: number) => {
    removingItems.value.add(itemId);

    try {
        await router.delete(
            remove.url({ cartItem: itemId }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    showSuccessToast('Item removed from cart');
                    // Remove from selected items if it was selected
                    const index = selectedItems.value.indexOf(itemId);
                    if (index > -1) {
                        selectedItems.value.splice(index, 1);
                    }
                },
                onError: (errors) => {
                    showErrorToast(errors.message || 'Failed to remove item');
                },
                onFinish: () => {
                    removingItems.value.delete(itemId);
                },
            }
        );
    } catch (error) {
        removingItems.value.delete(itemId);
        showErrorToast('Failed to remove item');
    }
};

const DELIVERY_FEE = 25;
const FREE_DELIVERY_THRESHOLD = 299;

const selectedTotal = computed(() => {
    if (selectedItems.value.length === 0) {
        return 0;
    }
    return cart.value.items
        .filter((item) => selectedItems.value.includes(item.id))
        .reduce((sum, item) => sum + parseFloat(item.total_price), 0);
});

const selectedItemsCount = computed(() => selectedItems.value.length);

const displayTotal = computed(() => {
    // If items are selected, show selected total; otherwise show cart total
    if (selectedItemsCount.value > 0) {
        return selectedTotal.value;
    }
    return cart.value.total;
});

const isDeliveryFree = computed(() => {
    return displayTotal.value >= FREE_DELIVERY_THRESHOLD;
});

const deliveryFee = computed(() => {
    return isDeliveryFree.value ? 0 : DELIVERY_FEE;
});

const finalTotal = computed(() => {
    return displayTotal.value + deliveryFee.value;
});

const handleCheckout = async () => {
    if (selectedItems.value.length === 0) {
        showErrorToast('Please select at least one item');
        return;
    }

    if (!deliveryAddress.address || !deliveryAddress.city || !deliveryAddress.zipCode) {
        showErrorToast('Please fill in all delivery address fields');
        return;
    }

    isProcessingCheckout.value = true;

    try {
        await router.post('/transactions', {
            selected_items: selectedItems.value,
            delivery_address: deliveryAddress.address,
            delivery_city: deliveryAddress.city,
            delivery_zip_code: deliveryAddress.zipCode,
        }, {
            preserveScroll: false,
            onSuccess: () => {
                // Clear selected items
                selectedItems.value = [];
            },
            onError: (errors) => {
                if (errors.selected_items) {
                    showErrorToast(errors.selected_items);
                } else if (errors.delivery_address || errors.delivery_city || errors.delivery_zip_code) {
                    showErrorToast('Please check your delivery address information');
                } else {
                    showErrorToast(errors.message || 'Failed to process order. Please try again.');
                }
            },
            onFinish: () => {
                isProcessingCheckout.value = false;
            },
        });
    } catch (error) {
        isProcessingCheckout.value = false;
        showErrorToast('Failed to process order. Please try again.');
    }
};

// Form data for delivery address
const deliveryAddress = reactive({
    address: '',
    city: '',
    zipCode: '',
});
</script>

<template>
    <Head title="Shopping Cart" />
    <AppLayout variant="header">
        <!-- Breadcrumb -->
        <section class="w-screen border-b bg-background" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container mx-auto w-full max-w-7xl px-4 py-4">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Link href="/" class="hover:text-primary transition-colors">Home</Link>
                    <span>/</span>
                    <span class="text-foreground">Shopping Cart</span>
                </div>
            </div>
        </section>

        <!-- Cart Section -->
        <section class="relative w-screen bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-8 lg:py-12">
                <!-- Thank You Message -->
                <div v-if="showThankYou && (props.transaction || (page.props as any).transaction)" class="mb-8 rounded-lg border-2 border-green-200/50 bg-linear-to-br from-green-50 via-white to-green-50/30 p-6 shadow-lg transition-all duration-200 dark:border-green-800/50 dark:from-green-950/30 dark:via-slate-900 dark:to-green-950/20">
                    <div class="relative">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="absolute right-0 top-0 h-6 w-6 text-muted-foreground hover:text-foreground"
                            @click="dismissThankYou"
                        >
                            <X class="size-4" />
                        </Button>
                        <div class="mb-4 flex justify-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-linear-to-br from-green-500 via-green-600 to-green-700 shadow-lg shadow-green-500/20">
                                <CheckCircle class="size-8 text-white drop-shadow-xl" />
                            </div>
                        </div>
                        <div class="mb-4 text-center">
                            <h2 class="mb-2 text-2xl font-bold tracking-tight">Thank You for Your Purchase!</h2>
                            <p class="text-base text-muted-foreground">
                                Your order has been placed successfully.
                            </p>
                        </div>
                        <div class="flex justify-center">
                            <Button
                                :as-child="true"
                                size="default"
                                class="bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30"
                            >
                                <Link :href="`/transactions/${(props.transaction || (page.props as any).transaction)?.transaction_number}`" class="flex items-center gap-2">
                                    View Order Details
                                    <ArrowRight class="size-4" />
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Cart Content (hidden when thank you is showing) -->
                <div v-if="!showThankYou">
                    <!-- Header -->
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <Link
                                href="/"
                                class="group flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-muted-foreground transition-all duration-200 hover:border-primary/30 hover:bg-primary/5 hover:text-primary hover:shadow-sm dark:border-slate-800 dark:bg-slate-900/50 dark:hover:border-primary/30"
                            >
                                <ArrowLeft class="size-3.5 transition-transform duration-200 group-hover:-translate-x-1" />
                                Continue Shopping
                            </Link>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight lg:text-3xl">Shopping Cart</h1>
                        <div class="w-32"></div>
                    </div>

                    <!-- Empty Cart -->
                    <div v-if="!cart.items || cart.items.length === 0" class="flex flex-col items-center justify-center rounded-xl border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white py-12 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 shadow-lg shadow-blue-500/20">
                            <ShoppingCart class="size-10 text-white/90 drop-shadow-xl" />
                        </div>
                        <h2 class="mb-2 text-2xl font-bold tracking-tight">Your cart is empty</h2>
                        <p class="mb-6 text-base text-muted-foreground">Start adding items to your cart!</p>
                        <Button :as-child="true" size="default" class="bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30">
                            <Link href="/">Start Shopping</Link>
                        </Button>
                    </div>

                    <!-- Cart Items -->
                    <div v-else class="grid gap-6 lg:grid-cols-3 lg:items-start">
                    <!-- Left Side: Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Select All -->
                        <div class="flex items-center gap-3 rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-3 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900 dark:hover:border-primary/30">
                            <Checkbox
                                v-model="isAllSelected"
                                :data-state="isIndeterminate ? 'indeterminate' : undefined"
                            />
                            <span class="text-sm font-semibold">Select All ({{ selectedItemsCount }} items)</span>
                        </div>

                        <!-- Cart Items List -->
                        <div class="space-y-3">
                            <div
                                v-for="item in cart.items"
                                :key="item.id"
                                class="group relative flex items-center gap-4 rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-4 transition-all duration-300 hover:scale-[1.01] hover:border-primary/30 hover:shadow-lg hover:shadow-primary/10 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900 dark:hover:border-primary/30"
                            >
                                <!-- Animated background gradient on hover -->
                                <div class="absolute inset-0 rounded-lg bg-linear-to-br from-primary/0 to-primary/0 transition-all duration-300 group-hover:from-primary/5 group-hover:to-primary/5"></div>

                                <!-- Checkbox -->
                                <div class="relative z-10">
                                <Checkbox
                                    v-model="itemSelections[item.id]"
                                />
                                </div>

                                <!-- Product Image -->
                                <div class="relative z-10 flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 shadow-md shadow-blue-500/20 transition-transform duration-300 group-hover:scale-105 dark:border-slate-800/50">
                                    <Package class="size-10 text-white/90 drop-shadow-xl transition-transform duration-300 group-hover:scale-110" />
                                    <!-- Decorative corner accent -->
                                    <div class="absolute -right-3 -top-3 size-16 rounded-full bg-white/10 blur-xl"></div>
                                </div>

                                <!-- Product Info -->
                                <div class="relative z-10 flex-1 min-w-0">
                                    <Link
                                        :href="`/products/${item.product.slug}`"
                                        class="block"
                                    >
                                        <h3 class="mb-1 text-base font-bold tracking-tight transition-colors hover:text-primary">
                                            {{ item.product.name }}
                                        </h3>
                                    </Link>
                                    <p class="text-xs font-medium text-muted-foreground">
                                        Price: <span class="text-primary font-semibold">${{ item.price_at_time_formatted ?? '0.00' }}</span>
                                    </p>
                                    <p v-if="item.product.stock_quantity < item.quantity" class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        Only {{ item.product.stock_quantity }} available
                                    </p>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="relative z-10 flex items-center gap-1.5 rounded-lg border-2 border-slate-200 bg-white p-0.5 dark:border-slate-800 dark:bg-slate-900/50">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-7 w-7 transition-all duration-200 hover:bg-accent hover:scale-110"
                                        :disabled="item.quantity <= 1 || updatingQuantities.has(item.id)"
                                        @click="handleUpdateQuantity(item, item.quantity - 1)"
                                    >
                                        <Minus class="size-3.5" />
                                    </Button>
                                    <span class="w-10 text-center text-sm font-bold">{{ item.quantity }}</span>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-7 w-7 transition-all duration-200 hover:bg-accent hover:scale-110"
                                        :disabled="item.quantity >= item.product.stock_quantity || updatingQuantities.has(item.id)"
                                        @click="handleUpdateQuantity(item, item.quantity + 1)"
                                    >
                                        <Plus class="size-3.5" />
                                    </Button>
                                </div>

                                <!-- Subtotal -->
                                <div class="relative z-10 w-28 text-right">
                                    <p class="text-lg font-bold text-primary">${{ item.total_price_formatted ?? '0.00' }}</p>
                                    <p class="text-xs text-muted-foreground">${{ item.price_at_time_formatted ?? '0.00' }} each</p>
                                </div>

                                <!-- Remove Button -->
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="relative z-10 h-8 w-8 rounded-lg text-destructive transition-all duration-200 hover:bg-destructive/10 hover:scale-110"
                                    :disabled="removingItems.has(item.id)"
                                    @click="handleRemoveItem(item.id)"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Order Summary & Forms -->
                    <div class="lg:col-span-1 lg:sticky lg:top-4 lg:self-start">
                        <div class="space-y-4">
                            <!-- Order Summary -->
                            <div class="rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-4 transition-all duration-200 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                                <h2 class="mb-4 text-xl font-bold tracking-tight">Order Summary</h2>
                                <div class="space-y-3">
                                    <div v-if="selectedItemsCount === 0" class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Items ({{ cart.items_count }})</span>
                                        <span class="font-semibold">${{ cart.total.toFixed(2) }}</span>
                                    </div>
                                    <div v-if="selectedItemsCount > 0" class="flex justify-between rounded-lg bg-primary/5 p-2.5 text-sm dark:bg-primary/10">
                                        <span class="font-medium text-muted-foreground">Selected ({{ selectedItemsCount }})</span>
                                        <span class="font-bold text-primary">${{ selectedTotal.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Delivery</span>
                                        <span v-if="isDeliveryFree" class="font-semibold text-green-600 dark:text-green-400">Free</span>
                                        <span v-else class="font-semibold">${{ deliveryFee.toFixed(2) }}</span>
                                    </div>
                                    <div class="border-t border-slate-200 pt-3 dark:border-slate-800">
                                        <div class="flex justify-between text-lg font-bold">
                                            <span>Total</span>
                                            <span class="text-primary">${{ finalTotal.toFixed(2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p v-if="!isDeliveryFree" class="mt-3 text-center text-xs text-muted-foreground">
                                    Add ${{ (FREE_DELIVERY_THRESHOLD - displayTotal).toFixed(2) }} more for free delivery
                                </p>
                                <p v-else class="mt-3 text-center text-xs font-semibold text-green-600 dark:text-green-400">
                                    ✓ Free delivery applied!
                                </p>
                            </div>

                            <!-- Delivery Address Form -->
                            <div class="rounded-lg border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white p-4 transition-all duration-200 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900">
                                <h2 class="mb-4 text-xl font-bold tracking-tight">Delivery Address</h2>
                                <div class="grid gap-5">
                                    <div class="grid gap-2">
                                        <Label for="address" class="text-sm font-semibold">Address</Label>
                                        <Input 
                                            id="address" 
                                            v-model="deliveryAddress.address" 
                                            placeholder="123 Main Street"
                                            class="h-11 border-2 transition-all duration-200 focus:border-primary/50 focus:ring-2 focus:ring-primary/20 hover:border-primary/30"
                                        />
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="grid gap-2">
                                            <Label for="city" class="text-sm font-semibold">City</Label>
                                            <Input 
                                                id="city" 
                                                v-model="deliveryAddress.city" 
                                                placeholder="New York"
                                                class="h-11 border-2 transition-all duration-200 focus:border-primary/50 focus:ring-2 focus:ring-primary/20 hover:border-primary/30"
                                            />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label for="zipCode" class="text-sm font-semibold">ZIP Code</Label>
                                            <Input 
                                                id="zipCode" 
                                                v-model="deliveryAddress.zipCode" 
                                                placeholder="10001"
                                                class="h-11 border-2 transition-all duration-200 focus:border-primary/50 focus:ring-2 focus:ring-primary/20 hover:border-primary/30"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- Checkout Button -->
                        <Button
                            size="default"
                            class="cursor-pointer w-full bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            :disabled="selectedItemsCount === 0 || isProcessingCheckout"
                            @click="handleCheckout"
                        >
                            {{ isProcessingCheckout ? 'Processing...' : `Checkout (${selectedItemsCount || cart.items_count} items)` }}
                        </Button>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <AppFooter />
    </AppLayout>
</template>
