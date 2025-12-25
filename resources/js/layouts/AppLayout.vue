<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    variant?: 'header' | 'sidebar';
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    variant: 'sidebar',
});
</script>

<template>
    <AppHeaderLayout v-if="variant === 'header'" :breadcrumbs="breadcrumbs">
        <slot />
    </AppHeaderLayout>
    <AppSidebarLayout v-else :breadcrumbs="breadcrumbs">
        <slot />
    </AppSidebarLayout>
</template>
