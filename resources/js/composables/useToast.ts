import { ref } from 'vue';

export type ToastVariant = 'success' | 'error' | 'info' | 'warning';

export interface Toast {
    id: string;
    title?: string;
    description: string;
    variant?: ToastVariant;
    duration?: number;
}

const toasts = ref<Toast[]>([]);

export const useToast = () => {
    const addToast = (toast: Omit<Toast, 'id'>) => {
        const id = Math.random().toString(36).substring(2, 9);
        const newToast: Toast = {
            ...toast,
            id,
            variant: toast.variant || 'success',
            duration: toast.duration || 3000,
        };

        toasts.value.push(newToast);

        // Auto remove after duration
        if (newToast.duration && newToast.duration > 0) {
            setTimeout(() => {
                removeToast(id);
            }, newToast.duration);
        }

        return id;
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex((toast) => toast.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (description: string, title?: string) => {
        return addToast({ description, title, variant: 'success' });
    };

    const error = (description: string, title?: string) => {
        return addToast({ description, title, variant: 'error' });
    };

    const info = (description: string, title?: string) => {
        return addToast({ description, title, variant: 'info' });
    };

    const warning = (description: string, title?: string) => {
        return addToast({ description, title, variant: 'warning' });
    };

    return {
        toasts,
        addToast,
        removeToast,
        success,
        error,
        info,
        warning,
    };
};
