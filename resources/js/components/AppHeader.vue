<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { toUrl, urlIsActive } from '@/lib/utils';
import { login, register } from '@/routes';
import { index as cartIndex } from '@/actions/App/Http/Controllers/CartController';
import type { BreadcrumbItem, NavItem } from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Folder,
    Menu,
    Search,
    ShoppingCart,
    MapPin,
    ChevronDown,
    Home,
    Grid3x3,
    ChevronRight,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { router } from '@inertiajs/vue3';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const canRegister = computed(() => page.props.canRegister ?? true);
const cart = computed(() => (page.props.cart as { items_count?: number; total?: number }) || { items_count: 0, total: 0 });

const isCurrentRoute = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        urlIsActive(url, page.url),
);

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(toUrl(url))
            ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
            : '',
);

const mainNavItems: NavItem[] = [];

const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

// Get categories from shared props
const categories = computed(() => {
    const cats = (page.props.categories as Array<{ id: number; name: string; slug: string }>) || [];
    if (!Array.isArray(cats) || cats.length === 0) {
        return [];
    }
    return cats.map((category) => ({
        name: category.name,
        href: `/categories/${category.slug}`,
        slug: category.slug,
    }));
});

const searchQuery = ref('');
const selectedLocation = ref('New York, NY');

// Sync search query from URL when on search page
const urlParams = new URLSearchParams(window.location.search);
const initialQuery = urlParams.get('q') || '';
if (initialQuery) {
    searchQuery.value = initialQuery;
}

// Watch for URL changes to sync search query
watch(() => page.url, (newUrl) => {
    // Extract query parameter from the URL
    const queryMatch = newUrl.match(/[?&]q=([^&]*)/);
    if (queryMatch) {
        searchQuery.value = decodeURIComponent(queryMatch[1]);
    } else if (newUrl.startsWith('/search')) {
        // Clear search query if on search page without query param
        searchQuery.value = '';
    }
}, { immediate: true });

// Handle search submission
const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get('/search', { q: searchQuery.value.trim() });
    }
};

// Handle Enter key press
const handleKeyPress = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        handleSearch();
    }
};
</script>

<template>
    <div class="border-b border-sidebar-border/80 bg-background/95 backdrop-blur-sm">
        <!-- Top Navigation Bar -->
        <div class="border-b border-sidebar-border/50 bg-linear-to-r from-background via-background to-blue-50/30 dark:to-blue-950/10 shadow-sm">
            <div class="mx-auto flex h-16 items-center gap-4 px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-9 w-9 transition-all duration-200 hover:bg-accent/80 hover:scale-105 cursor-pointer"
                            >
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[320px] sm:w-[380px] p-0">
                            <SheetTitle class="sr-only"
                                >Navigation Menu</SheetTitle
                            >
                            <!-- Header Section -->
                            <div class="border-b border-sidebar-border bg-linear-to-br from-background to-slate-50/50 dark:to-slate-900/30 p-6">
                                <SheetHeader class="flex flex-row items-center justify-between text-left">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-blue-600 p-2.5 shadow-lg shadow-blue-500/25">
                                            <AppLogoIcon
                                                class="size-7 fill-current text-white"
                                            />
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-bold text-foreground">Menu</h2>
                                            <p class="text-xs text-muted-foreground">Browse our store</p>
                                        </div>
                                    </div>
                                </SheetHeader>
                                
                                <!-- Home Button -->
                                <div class="mt-4">
                                    <Button
                                        variant="outline"
                                        :as-child="true"
                                        class="w-full justify-start gap-2 h-11 bg-linear-to-r from-blue-50 to-blue-100/50 dark:from-blue-950/20 dark:to-blue-950/10 border-blue-200 dark:border-blue-800 hover:from-blue-100 hover:to-blue-150/50 dark:hover:from-blue-950/30 dark:hover:to-blue-950/20 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200"
                                        :class="
                                            isCurrentRoute('/')
                                                ? 'bg-linear-to-r from-blue-500 to-blue-600 text-white border-blue-500 hover:from-blue-600 hover:to-blue-700'
                                                : ''
                                        "
                                    >
                                        <Link href="/" class="flex items-center gap-2">
                                            <Home class="h-4 w-4" />
                                            <span class="font-semibold">Go to Home</span>
                                        </Link>
                                    </Button>
                                </div>
                                
                                <!-- Mobile Search in Menu -->
                                <div class="mt-4 relative">
                                    <Search
                                        class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                                    />
                                    <Input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search products..."
                                        class="w-full pl-10 pr-4 h-10 text-sm border-2 bg-background"
                                        @keyup.enter="handleKeyPress"
                                    />
                                </div>
                            </div>

                            <!-- Navigation Content -->
                            <div class="flex-1 overflow-y-auto">
                                <nav class="space-y-2 px-3">
                                    <!-- Categories Section -->
                                    <div v-if="categories.length > 0" class="pt-4 border-sidebar-border">
                                        <h3 class="px-4 mb-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                            Categories
                                        </h3>
                                        <div class="space-y-0.5">
                                            <Link
                                                v-for="category in categories"
                                                :key="category.slug"
                                                :href="category.href"
                                                class="block rounded-lg px-4 py-2.5 text-sm font-medium transition-colors hover:bg-accent"
                                                :class="
                                                    isCurrentRoute(category.href)
                                                        ? 'text-primary bg-accent'
                                                        : 'text-foreground'
                                                "
                                            >
                                                {{ category.name }}
                                            </Link>
                                        </div>
                                    </div>
                                </nav>
                            </div>

                            <!-- Footer Section -->
                            <div class="border-t border-sidebar-border bg-linear-to-br from-slate-50/50 to-background dark:from-slate-900/30 dark:to-background p-4">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="text-muted-foreground">
                                        <p class="font-semibold text-foreground">Free delivery</p>
                                        <p class="text-xs">on orders over $299</p>
                                    </div>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <!-- Logo -->
                <Link :href="'/'" class="flex items-center gap-x-2 shrink-0 transition-transform duration-200 hover:scale-105">
                    <AppLogo />
                </Link>

                <!-- Search Bar (Center) -->
                <div class="relative flex-1 max-w-2xl mx-4 hidden md:block">
                    <div class="relative group">
                        <Search
                            class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground transition-colors duration-200 group-focus-within:text-primary"
                        />
                        <Input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search for products..."
                            class="w-full pl-10 pr-4 h-10 border-2 transition-all duration-200 focus:border-primary/50 focus:ring-2 focus:ring-primary/20 hover:border-primary/30"
                            @keyup.enter="handleKeyPress"
                        />
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="ml-auto flex items-center gap-2 shrink-0">
                    <!-- Shopping Cart -->
                    <Button
                        variant="ghost"
                        size="icon"
                        :as-child="true"
                        class="group relative h-9 w-9 transition-all duration-200 hover:bg-accent/80 hover:scale-105 cursor-pointer"
                        title="Shopping Cart"
                    >
                        <Link :href="cartIndex.url()">
                            <ShoppingCart class="size-5 transition-transform duration-200 group-hover:scale-110" />
                            <span
                                v-if="(cart.items_count ?? 0) > 0"
                                class="absolute right-1 top-1 flex min-w-4 items-center justify-center rounded-full bg-linear-to-br from-blue-500 to-blue-600 px-1 text-[10px] font-bold text-white shadow-lg shadow-blue-500/30"
                            >
                                {{ (cart.items_count ?? 0) > 99 ? '99+' : cart.items_count }}
                            </span>
                        </Link>
                    </Button>

                    <!-- User Menu / Auth -->
                    <template v-if="auth?.user">
                        <DropdownMenu>
                            <DropdownMenuTrigger :as-child="true">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="group relative size-9 w-auto rounded-full p-1 transition-all duration-200 hover:scale-105 hover:ring-2 hover:ring-primary/20 cursor-pointer"
                                >
                                    <Avatar class="size-7 overflow-hidden rounded-full ring-2 ring-primary/10 transition-all duration-200 group-hover:ring-primary/30">
                                        <AvatarImage
                                            v-if="auth.user.avatar"
                                            :src="auth.user.avatar"
                                            :alt="auth.user.name"
                                        />
                                        <AvatarFallback
                                            class="rounded-lg bg-linear-to-br from-primary/20 to-primary/10 font-semibold text-primary dark:from-primary/30 dark:to-primary/20"
                                        >
                                            {{ getInitials(auth.user?.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <UserMenuContent :user="auth.user" />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>
                    <template v-else>
                        <Button
                            :as-child="true"
                            size="sm"
                            class="bg-linear-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/30 cursor-pointer"
                        >
                            <Link :href="login()">Login or Register</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation Bar (Categories) -->
        <div class="bg-linear-to-r from-slate-50/50 via-background to-slate-50/50 dark:from-slate-900/50 dark:via-background dark:to-slate-900/50 border-b border-sidebar-border/30 hidden md:block">
            <div class="mx-auto flex h-12 items-center justify-between gap-4 px-4 md:max-w-7xl">
                <!-- Categories (Left) -->
                <nav class="hidden lg:flex items-center gap-6">
                    <Link
                        v-for="category in categories"
                        :key="category.slug"
                        :href="category.href"
                        class="relative inline-block text-sm font-medium transition-all duration-200 hover:scale-105 cursor-pointer"
                        :class="
                            isCurrentRoute(category.href)
                                ? 'text-primary'
                                : 'text-muted-foreground hover:text-primary'
                        "
                    >
                        {{ category.name }}
                        <span
                            v-if="isCurrentRoute(category.href)"
                            class="absolute bottom-0 left-0 right-0 h-0.5 bg-linear-to-r from-blue-500 to-blue-600 rounded-full pointer-events-none"
                        ></span>
                    </Link>
                </nav>


                <!-- Promotional Message (Center) - Desktop -->
                <div class="hidden lg:flex flex-1 items-center justify-center">
                    <p class="text-sm text-muted-foreground">
                        <span class="text-primary font-semibold bg-primary/10 px-2 py-1 rounded-md"
                            >Free delivery</span
                        >
                        <span class="ml-2">on orders over $299</span>
                    </p>
                </div>

                <!-- Location Selector (Right) -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="group hidden lg:flex items-center gap-1 text-sm transition-all duration-200 hover:bg-accent/80 hover:scale-105 cursor-pointer"
                        >
                            <MapPin class="size-4 transition-transform duration-200 group-hover:scale-110" />
                            <span class="text-muted-foreground"
                                >Deliver to</span
                            >
                            <span class="font-medium">{{ selectedLocation }}</span>
                            <ChevronDown class="size-4 transition-transform duration-200 group-hover:rotate-180" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem @click="selectedLocation = 'New York, NY'">
                            New York, NY
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="selectedLocation = 'Los Angeles, CA'">
                            Los Angeles, CA
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="selectedLocation = 'Chicago, IL'">
                            Chicago, IL
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="selectedLocation = 'Houston, TX'">
                            Houston, TX
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <!-- Breadcrumbs (if needed) -->
        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-b border-sidebar-border/70 bg-linear-to-r from-background via-background to-slate-50/30 dark:to-slate-900/30"
        >
            <div
                class="mx-auto flex h-10 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
