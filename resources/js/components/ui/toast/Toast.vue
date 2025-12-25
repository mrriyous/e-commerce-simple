<script setup lang="ts">
import { X, CheckCircle2, AlertCircle, Info, AlertTriangle } from 'lucide-vue-next';
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import type { Toast as ToastType } from '@/composables/useToast';

interface Props {
    toast: ToastType;
    onClose: (id: string) => void;
}

const props = defineProps<Props>();

const variantConfig = computed(() => {
    const variants = {
        success: {
            icon: CheckCircle2,
            iconClass: 'text-green-600 dark:text-green-400',
            borderClass: 'border-green-200/50 dark:border-green-800/50',
            bgClass: 'bg-linear-to-br from-green-50 to-green-100/50 dark:from-green-950/30 dark:to-green-900/20 border shadow-lg',
        },
        error: {
            icon: AlertCircle,
            iconClass: 'text-destructive',
            borderClass: 'border-destructive/20 dark:border-destructive/30',
            bgClass: 'bg-linear-to-br from-red-50 to-red-100/50 dark:from-red-950/30 dark:to-red-900/20 border shadow-lg',
        },
        info: {
            icon: Info,
            iconClass: 'text-primary',
            borderClass: 'border-primary/20 dark:border-primary/30',
            bgClass: 'bg-linear-to-br from-blue-50 to-blue-100/50 dark:from-blue-950/30 dark:to-blue-900/20 border shadow-lg',
        },
        warning: {
            icon: AlertTriangle,
            iconClass: 'text-yellow-600 dark:text-yellow-400',
            borderClass: 'border-yellow-200/50 dark:border-yellow-800/50',
            bgClass: 'bg-linear-to-br from-yellow-50 to-yellow-100/50 dark:from-yellow-950/30 dark:to-yellow-900/20 border shadow-lg',
        },
    };
    return variants[props.toast.variant || 'success'];
});

const IconComponent = computed(() => variantConfig.value.icon);
</script>

<template>
    <div
        :class="
            cn(
                'group pointer-events-auto relative flex w-full items-center gap-3 overflow-hidden rounded-lg border p-4 shadow-lg transition-all data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-80 data-[state=closed]:slide-out-to-right-full data-[state=open]:slide-in-from-top-full data-[state=open]:sm:slide-in-from-bottom-full',
                variantConfig.bgClass,
                variantConfig.borderClass
            )
        "
    >
        <component
            :is="IconComponent"
            :class="cn('size-5 shrink-0', variantConfig.iconClass)"
        />
        <div class="flex-1 space-y-1">
            <div v-if="toast.title" class="text-sm font-medium text-foreground">
                {{ toast.title }}
            </div>
            <div class="text-sm font-medium text-foreground">
                {{ toast.description }}
            </div>
        </div>
        <button
            class="shrink-0 rounded-md p-1 text-muted-foreground transition-opacity hover:text-foreground hover:bg-accent focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring"
            @click="onClose(toast.id)"
        >
            <X class="size-4" />
            <span class="sr-only">Close</span>
        </button>
    </div>
</template>
