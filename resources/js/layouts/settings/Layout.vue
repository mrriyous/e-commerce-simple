<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { User, Lock, Shield, Palette } from 'lucide-vue-next';
import { computed } from 'vue';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: editProfile(),
        icon: User,
    },
    {
        title: 'Password',
        href: editPassword(),
        icon: Lock,
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
        icon: Shield,
    },
    {
        title: 'Appearance',
        href: editAppearance(),
        icon: Palette,
    },
];

const page = usePage();
const currentPath = computed(() => page.url);
</script>

<template>
    <!-- Settings Header -->
    <section class="w-screen border-b bg-background" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
        <div class="container mx-auto w-full max-w-7xl px-4 py-4 lg:py-6">
            <div class="mb-2">
                <Badge
                    variant="secondary"
                    class="mb-2 inline-flex items-center gap-2 bg-primary/10 text-primary"
                >
                    <User class="size-3 fill-primary text-primary" />
                    Account Settings
                </Badge>
            </div>
            <h1 class="mb-1 text-2xl font-bold tracking-tight sm:text-3xl lg:text-4xl">
                Settings
            </h1>
            <p class="text-sm text-muted-foreground sm:text-base">
                Manage your profile and account settings
            </p>
        </div>
    </section>

    <!-- Settings Content -->
    <section class="relative w-screen overflow-hidden bg-linear-to-br from-slate-50 via-white to-blue-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950/20" style="margin-left: calc(-50vw + 50%); margin-right: calc(-50vw + 50%);">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-20 top-20 size-96 rounded-full bg-blue-200/10 blur-3xl dark:bg-blue-800/5"></div>
            <div class="absolute -left-20 bottom-20 size-96 rounded-full bg-purple-200/10 blur-3xl dark:bg-purple-800/5"></div>
        </div>

        <div class="container relative mx-auto w-full max-w-7xl px-4 py-8 lg:py-12">
            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Sidebar Navigation -->
                <aside class="w-full lg:w-64 shrink-0">
                    <Card class="border-2 border-slate-200/50 bg-white/80 backdrop-blur-sm dark:border-slate-800/50 dark:bg-slate-900/80 p-4">
                        <nav class="flex flex-col space-y-1">
                            <Button
                                v-for="item in sidebarNavItems"
                                :key="toUrl(item.href)"
                                variant="ghost"
                                :class="[
                                    'w-full justify-start transition-all duration-200',
                                    urlIsActive(item.href, currentPath)
                                        ? 'bg-primary/10 text-primary font-medium hover:bg-primary/20'
                                        : 'hover:bg-slate-100 dark:hover:bg-slate-800',
                                ]"
                                as-child
                            >
                                <Link :href="item.href" class="flex items-center gap-3">
                                    <component :is="item.icon" class="h-4 w-4" />
                                    {{ item.title }}
                                </Link>
                            </Button>
                        </nav>
                    </Card>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 min-w-0">
                    <Card class="border-2 border-slate-200/50 bg-white/80 backdrop-blur-sm dark:border-slate-800/50 dark:bg-slate-900/80 p-6 lg:p-8">
                        <slot />
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>
