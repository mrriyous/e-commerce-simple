<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden bg-linear-to-br from-slate-50 via-blue-50/30 to-purple-50/30 p-6 md:p-10 dark:from-slate-950 dark:via-blue-950/20 dark:to-purple-950/20"
    >
        <!-- Decorative background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-20 top-1/4 size-96 rounded-full bg-blue-200/20 blur-3xl dark:bg-blue-800/10"></div>
            <div class="absolute -left-20 bottom-1/4 size-96 rounded-full bg-purple-200/20 blur-3xl dark:bg-purple-800/10"></div>
        </div>

        <div class="relative flex w-full max-w-md flex-col gap-6">
            <Link
                :href="home()"
                class="flex items-center gap-2 self-center font-medium transition-transform duration-200 hover:scale-105"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-linear-to-br from-blue-500 to-blue-600 p-2 shadow-lg shadow-blue-500/20">
                    <AppLogoIcon
                        class="size-6 fill-current text-white"
                    />
                </div>
            </Link>

            <div class="flex flex-col gap-6">
                <Card class="relative overflow-hidden border-2 border-slate-200/50 bg-linear-to-br from-white via-white to-slate-50/50 shadow-2xl shadow-slate-200/50 dark:border-slate-800/50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800/50 dark:shadow-slate-900/50">
                    <!-- Decorative gradient overlay -->
                    <div class="absolute inset-0 bg-linear-to-br from-blue-500/0 via-purple-500/0 to-blue-500/0 opacity-0 transition-opacity duration-300 group-hover:opacity-5"></div>
                    
                    <CardHeader class="relative px-10 pt-8 pb-0 text-center">
                        <CardTitle class="mb-2 text-2xl font-bold tracking-tight sm:text-3xl">
                            {{ title }}
                        </CardTitle>
                        <CardDescription class="text-base">
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="relative px-10 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
