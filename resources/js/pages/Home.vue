<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AppFooter from '@/components/AppFooter.vue';
import ProductTile from '@/components/ProductTile.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Head, Link } from '@inertiajs/vue3';
import {
    ShoppingBag,
    Truck,
    Shield,
    Star,
    ArrowRight,
    Check,
    Sofa,
    Home,
    Wrench,
    Package,
} from 'lucide-vue-next';
import { home, login, register } from '@/routes';

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

withDefaults(
    defineProps<{
        canRegister: boolean;
        featuredProducts: Product[];
    }>(),
    {
        canRegister: true,
        featuredProducts: () => [],
    },
);

// Helper function to get icon based on product name or category
const getProductIcon = (productName: string) => {
    const name = productName.toLowerCase();
    if (name.includes('sofa') || name.includes('couch') || name.includes('settee')) {
        return Sofa;
    }
    if (name.includes('bed') || name.includes('mattress')) {
        return Home;
    }
    if (name.includes('table') || name.includes('desk')) {
        return Home;
    }
    if (name.includes('chair') || name.includes('stool')) {
        return Home;
    }
    return Package;
};

const testimonials = [
    {
        name: 'Sarah Johnson',
        role: 'Verified Customer',
        rating: 5,
        text: 'Furnished my entire living room from here! The quality is excellent and assembly was straightforward. Highly recommend!',
        avatar: 'SJ',
    },
    {
        name: 'Michael Chen',
        role: 'Verified Customer',
        rating: 5,
        text: 'Love my new dining table! Great value for money and the delivery team was professional. Will shop here again.',
        avatar: 'MC',
    },
    {
        name: 'Emily Rodriguez',
        role: 'Verified Customer',
        rating: 5,
        text: 'The bed frame is perfect and the storage drawers are a game changer. Assembly service was worth every penny!',
        avatar: 'ER',
    },
];
</script>

<template>
    <Head title="Home - Modern Furniture & Home Decor" />
    <AppLayout variant="header">
        <!-- Hero Section -->
        <section
            class="relative w-screen overflow-hidden bg-linear-to-br from-blue-600 via-blue-500 to-yellow-500 py-20 lg:py-32"
        >
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute -left-20 -top-20 size-96 rounded-full bg-white/10 blur-3xl"
                ></div>
                <div
                    class="absolute -right-20 -bottom-20 size-96 rounded-full bg-yellow-400/20 blur-3xl"
                ></div>
            </div>

            <div class="container relative mx-auto w-full max-w-7xl px-4">
                <div class="mx-auto max-w-3xl text-center">
                    <Badge
                        variant="secondary"
                        class="mb-6 inline-flex items-center gap-2 bg-white/20 text-white backdrop-blur-sm"
                    >
                        <Star class="size-3 fill-yellow-300 text-yellow-300" />
                        Over 10,000+ Happy Homes
                    </Badge>
                    <h1
                        class="mb-6 text-5xl font-bold tracking-tight text-white sm:text-6xl lg:text-7xl"
                    >
                        Create Your Dream Home
                        <span
                            class="block bg-gradient-to-r from-yellow-300 to-white bg-clip-text text-transparent"
                        >
                            With Modern Furniture
                        </span>
                    </h1>
                    <p class="mb-8 text-xl text-white/90 sm:text-2xl">
                        Discover stylish furniture and home decor at affordable prices.
                        Free delivery, easy assembly, and 30-day returns.
                    </p>
                    <div
                        class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                    >
                        <Button
                            v-if="$page.props.auth.user"
                            :as-child="true"
                            size="lg"
                            class="group/btn w-full bg-white text-blue-600 shadow-xl shadow-white/20 transition-all duration-300 hover:scale-105 hover:bg-white/95 hover:shadow-2xl hover:shadow-white/30 sm:w-auto cursor-pointer"
                        >
                            <Link :href="home()" class="flex items-center">
                                Browse Products
                                <ArrowRight class="ml-2 size-4 transition-transform duration-300 group-hover/btn:translate-x-1" />
                            </Link>
                        </Button>
                        <template v-else>
                            <Button
                                :as-child="true"
                                size="lg"
                                class="group/btn w-full bg-white text-blue-600 shadow-xl shadow-white/20 transition-all duration-300 hover:scale-105 hover:bg-white/95 hover:shadow-2xl hover:shadow-white/30 sm:w-auto cursor-pointer"
                            >
                                <Link :href="register()" class="flex items-center">
                                    Start Shopping
                                    <ArrowRight class="ml-2 size-4 transition-transform duration-300 group-hover/btn:translate-x-1" />
                                </Link>
                            </Button>
                            <Button
                                :as-child="true"
                                variant="outline"
                                size="lg"
                                class="w-full border-2 border-white/80 bg-white/10 text-white backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:border-white hover:bg-white/20 hover:shadow-lg hover:shadow-white/10 sm:w-auto cursor-pointer"
                            >
                                <Link :href="login()">Sign In</Link>
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="w-screen border-b bg-linear-to-r from-blue-50 to-yellow-50 dark:from-blue-950/30 dark:to-yellow-950/30">
            <div class="container mx-auto w-full max-w-7xl px-4 py-12">
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="text-center">
                        <div class="mb-2 text-4xl font-bold text-primary">
                            10,000+
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Happy Customers
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-4xl font-bold text-primary">
                            5,000+
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Furniture Pieces
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-4xl font-bold text-primary">
                            100+
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Showrooms
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-4xl font-bold text-primary">
                            24/7
                        </div>
                        <div class="text-sm text-muted-foreground">
                            Customer Support
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
            <!-- Decorative background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -right-20 top-20 size-96 rounded-full bg-blue-200/20 blur-3xl dark:bg-blue-800/10"></div>
                <div class="absolute -left-20 bottom-20 size-96 rounded-full bg-yellow-200/20 blur-3xl dark:bg-yellow-800/10"></div>
            </div>

            <div class="container relative mx-auto w-full max-w-7xl px-4 py-20 lg:py-28">
                <div class="mb-16 text-center">
                    <Badge
                        variant="secondary"
                        class="mb-4 inline-flex items-center gap-2 bg-primary/10 text-primary"
                    >
                        <Star class="size-3 fill-primary text-primary" />
                        Best Sellers
                    </Badge>
                    <h2 class="mb-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                        Featured Furniture
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground sm:text-xl">
                        Handpicked pieces to transform your home
                    </p>
                </div>
                <div v-if="featuredProducts.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductTile
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :product="product"
                        :get-product-icon="getProductIcon"
                    />
                </div>
                <!-- Empty State -->
                <div v-else class="py-16 text-center">
                    <Package class="mx-auto mb-4 size-16 text-muted-foreground" />
                    <h3 class="mb-2 text-xl font-semibold">No featured products available</h3>
                    <p class="text-muted-foreground">
                        There are no featured products at the moment.
                    </p>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20">
            <!-- Decorative background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -right-20 top-20 size-96 rounded-full bg-blue-200/20 blur-3xl dark:bg-blue-800/10"></div>
                <div class="absolute -left-20 bottom-20 size-96 rounded-full bg-purple-200/20 blur-3xl dark:bg-purple-800/10"></div>
            </div>
            
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-20 lg:py-28">
                <div class="mb-16 text-center">
                    <Badge
                        variant="secondary"
                        class="mb-4 inline-flex items-center gap-2 bg-primary/10 text-primary"
                    >
                        <Star class="size-3 fill-primary text-primary" />
                        Trusted by Thousands
                    </Badge>
                    <h2 class="mb-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                        Why Choose Us
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground sm:text-xl">
                        Everything you need for a complete furniture shopping experience
                    </p>
                </div>
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        class="group relative overflow-hidden border-2 border-blue-200/50 bg-linear-to-br from-white via-blue-50/50 to-white transition-all duration-300 hover:scale-[1.02] hover:border-blue-300 hover:shadow-2xl hover:shadow-blue-200/50 dark:border-blue-800/50 dark:from-slate-900 dark:via-blue-950/30 dark:to-slate-900 dark:hover:border-blue-700 dark:hover:shadow-blue-900/20"
                    >
                        <!-- Animated background gradient on hover -->
                        <div class="absolute inset-0 bg-linear-to-br from-blue-500/0 to-blue-600/0 transition-all duration-300 group-hover:from-blue-500/5 group-hover:to-blue-600/5"></div>
                        
                        <CardHeader class="relative">
                            <div
                                class="mb-6 inline-flex size-16 items-center justify-center rounded-2xl bg-linear-to-br from-blue-500 via-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/30 transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-blue-500/50"
                            >
                                <Truck class="size-7 transition-transform duration-300 group-hover:scale-110" />
                            </div>
                            <CardTitle class="mb-3 text-2xl text-blue-900 dark:text-blue-100">
                                Free Delivery
                            </CardTitle>
                            <CardDescription class="text-base leading-relaxed text-blue-700/80 dark:text-blue-300/80">
                                Free delivery on orders over $299. Professional delivery
                                team brings furniture right to your room.
                            </CardDescription>
                        </CardHeader>
                    </Card>
                    
                    <Card
                        class="group relative overflow-hidden border-2 border-green-200/50 bg-linear-to-br from-white via-green-50/50 to-white transition-all duration-300 hover:scale-[1.02] hover:border-green-300 hover:shadow-2xl hover:shadow-green-200/50 dark:border-green-800/50 dark:from-slate-900 dark:via-green-950/30 dark:to-slate-900 dark:hover:border-green-700 dark:hover:shadow-green-900/20"
                    >
                        <!-- Animated background gradient on hover -->
                        <div class="absolute inset-0 bg-linear-to-br from-green-500/0 to-green-600/0 transition-all duration-300 group-hover:from-green-500/5 group-hover:to-green-600/5"></div>
                        
                        <CardHeader class="relative">
                            <div
                                class="mb-6 inline-flex size-16 items-center justify-center rounded-2xl bg-linear-to-br from-green-500 via-green-600 to-green-700 text-white shadow-lg shadow-green-500/30 transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-green-500/50"
                            >
                                <Wrench class="size-7 transition-transform duration-300 group-hover:scale-110" />
                            </div>
                            <CardTitle class="mb-3 text-2xl text-green-900 dark:text-green-100">
                                Assembly Service
                            </CardTitle>
                            <CardDescription class="text-base leading-relaxed text-green-700/80 dark:text-green-300/80">
                                Professional assembly service available. Our experts
                                will set up your furniture perfectly.
                            </CardDescription>
                        </CardHeader>
                    </Card>
                    
                    <Card
                        class="group relative overflow-hidden border-2 border-purple-200/50 bg-linear-to-br from-white via-purple-50/50 to-white transition-all duration-300 hover:scale-[1.02] hover:border-purple-300 hover:shadow-2xl hover:shadow-purple-200/50 dark:border-purple-800/50 dark:from-slate-900 dark:via-purple-950/30 dark:to-slate-900 dark:hover:border-purple-700 dark:hover:shadow-purple-900/20"
                    >
                        <!-- Animated background gradient on hover -->
                        <div class="absolute inset-0 bg-linear-to-br from-purple-500/0 to-purple-600/0 transition-all duration-300 group-hover:from-purple-500/5 group-hover:to-purple-600/5"></div>
                        
                        <CardHeader class="relative">
                            <div
                                class="mb-6 inline-flex size-16 items-center justify-center rounded-2xl bg-linear-to-br from-purple-500 via-purple-600 to-purple-700 text-white shadow-lg shadow-purple-500/30 transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl group-hover:shadow-purple-500/50"
                            >
                                <Shield class="size-7 transition-transform duration-300 group-hover:scale-110" />
                            </div>
                            <CardTitle class="mb-3 text-2xl text-purple-900 dark:text-purple-100">
                                365-Day Returns
                            </CardTitle>
                            <CardDescription class="text-base leading-relaxed text-purple-700/80 dark:text-purple-300/80">
                                Not satisfied? Return any item within 365 days for
                                a full refund. We stand behind our quality.
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-blue-50 via-yellow-50/50 to-purple-50 dark:from-slate-950 dark:via-blue-950/30 dark:to-purple-950/20">
            <!-- Decorative background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute right-0 top-0 size-96 rounded-full bg-yellow-200/30 blur-3xl dark:bg-yellow-900/10"></div>
                <div class="absolute left-0 bottom-0 size-96 rounded-full bg-blue-200/30 blur-3xl dark:bg-blue-900/10"></div>
            </div>
            
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-20 lg:py-28">
                <div class="mb-16 text-center">
                    <Badge
                        variant="secondary"
                        class="mb-4 inline-flex items-center gap-2 bg-primary/10 text-primary"
                    >
                        <Star class="size-3 fill-primary text-primary" />
                        Customer Reviews
                    </Badge>
                    <h2 class="mb-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                        What Our Customers Say
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground sm:text-xl">
                        Join thousands of satisfied customers who trust us with their home
                    </p>
                </div>
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="(testimonial, index) in testimonials"
                        :key="testimonial.name"
                        class="group relative overflow-hidden border-2 border-slate-200/50 bg-linear-to-br from-white via-slate-50/50 to-white transition-all duration-300 hover:scale-[1.02] hover:border-primary/30 hover:shadow-2xl hover:shadow-primary/10 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-800/30 dark:to-slate-900 dark:hover:border-primary/30"
                    >
                        <!-- Decorative quote icon -->
                        <div class="absolute -right-4 -top-4 text-8xl font-serif leading-none text-primary/5 transition-all duration-300 group-hover:text-primary/10 dark:text-primary/10 dark:group-hover:text-primary/20">
                            "
                        </div>
                        
                        <!-- Animated background gradient on hover -->
                        <div class="absolute inset-0 bg-linear-to-br from-primary/0 to-primary/0 transition-all duration-300 group-hover:from-primary/5 group-hover:to-primary/5"></div>
                        
                        <CardHeader class="relative">
                            <!-- Rating stars -->
                            <div class="mb-6 flex items-center gap-2">
                                <div class="flex gap-1">
                                    <Star
                                        v-for="i in testimonial.rating"
                                        :key="i"
                                        class="size-5 fill-yellow-400 text-yellow-400 drop-shadow-sm"
                                    />
                                </div>
                                <span class="text-sm font-medium text-muted-foreground">
                                    {{ testimonial.rating }}.0
                                </span>
                            </div>
                            
                            <!-- Testimonial text -->
                            <CardDescription class="mb-6 text-base leading-relaxed text-slate-700 dark:text-slate-300">
                                "{{ testimonial.text }}"
                            </CardDescription>
                            
                            <!-- Customer info -->
                            <div class="flex items-center gap-4 border-t border-slate-200/50 pt-6 dark:border-slate-800/50">
                                <div
                                    class="relative flex size-14 items-center justify-center rounded-full bg-linear-to-br from-primary/20 via-primary/15 to-primary/10 font-semibold text-primary shadow-lg ring-2 ring-primary/20 transition-all duration-300 group-hover:scale-110 group-hover:ring-primary/30 dark:from-primary/30 dark:via-primary/20 dark:to-primary/10"
                                >
                                    {{ testimonial.avatar }}
                                </div>
                                <div class="flex-1">
                                    <CardTitle class="mb-1 text-lg font-semibold">
                                        {{ testimonial.name }}
                                    </CardTitle>
                                    <CardDescription class="text-sm font-medium text-primary/80">
                                        {{ testimonial.role }}
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20">
            <!-- Decorative background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -left-20 top-1/2 size-96 -translate-y-1/2 rounded-full bg-blue-300/20 blur-3xl dark:bg-blue-800/10"></div>
                <div class="absolute -right-20 top-1/2 size-96 -translate-y-1/2 rounded-full bg-yellow-300/20 blur-3xl dark:bg-yellow-800/10"></div>
            </div>
            
            <div class="container relative mx-auto w-full max-w-7xl px-4 py-20 lg:py-28">
                <Card
                    class="group relative overflow-hidden border-2 border-primary/20 bg-linear-to-br from-blue-600 via-blue-500 to-yellow-500 shadow-2xl shadow-primary/20 transition-all duration-300 hover:shadow-primary/30 dark:border-primary/30"
                >
                    <!-- Animated background overlay -->
                    <div class="absolute inset-0 bg-linear-to-br from-white/0 via-white/0 to-white/0 transition-all duration-300 group-hover:from-white/5 group-hover:via-white/5 group-hover:to-white/5"></div>
                    
                    <!-- Decorative elements -->
                    <div class="absolute -right-20 -top-20 size-64 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="absolute -left-20 -bottom-20 size-64 rounded-full bg-yellow-300/20 blur-2xl"></div>
                    
                    <CardHeader class="relative text-center">
                        <Badge
                            variant="secondary"
                            class="mb-6 inline-flex items-center gap-2 bg-white/20 text-white backdrop-blur-sm"
                        >
                            <Star class="size-3 fill-yellow-300 text-yellow-300" />
                            Start Your Journey Today
                        </Badge>
                        <CardTitle class="mb-4 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Ready to Transform Your Home?
                        </CardTitle>
                        <CardDescription class="mx-auto max-w-2xl text-xl text-white/95 sm:text-2xl">
                            Join over 10,000 happy homeowners who have created their dream spaces with us
                        </CardDescription>
                    </CardHeader>
                    <CardContent
                        class="relative flex flex-col items-center justify-center gap-4 sm:flex-row"
                    >
                        <Button
                            v-if="$page.props.auth.user"
                            :as-child="true"
                            size="lg"
                            class="group/btn w-full bg-white text-blue-600 shadow-lg transition-all duration-300 hover:scale-105 hover:bg-white/95 hover:shadow-xl hover:shadow-white/20 sm:w-auto cursor-pointer"
                        >
                            <Link :href="home()" class="flex items-center">
                                Browse All Products
                                <ArrowRight class="ml-2 size-4 transition-transform duration-300 group-hover/btn:translate-x-1" />
                            </Link>
                        </Button>
                        <template v-else>
                            <Button
                                :as-child="true"
                                size="lg"
                                class="group/btn w-full bg-white text-blue-600 shadow-lg transition-all duration-300 hover:scale-105 hover:bg-white/95 hover:shadow-xl hover:shadow-white/20 sm:w-auto cursor-pointer"
                            >
                                <Link :href="register()" class="flex items-center">
                                    Create Free Account
                                    <ArrowRight class="ml-2 size-4 transition-transform duration-300 group-hover/btn:translate-x-1" />
                                </Link>
                            </Button>
                            <Button
                                :as-child="true"
                                variant="outline"
                                size="lg"
                                class="w-full border-2 border-white/80 bg-white/10 text-white backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:border-white hover:bg-white/20 hover:shadow-lg hover:shadow-white/10 sm:w-auto cursor-pointer"
                            >
                                <Link :href="login()">Sign In</Link>
                            </Button>
                        </template>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- Footer -->
        <AppFooter />
    </AppLayout>
</template>
