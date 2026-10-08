import type { FlashToast, ToastTone } from '@/types';
import { readonly, ref } from 'vue';

const DURATION_MS = 3000;

// Module-level so a toast outlives the layout remount on each Inertia visit.
const current = ref<FlashToast | null>(null);
let timer: ReturnType<typeof setTimeout> | undefined;

const dismiss = () => {
    clearTimeout(timer);
    current.value = null;
};

const pause = () => clearTimeout(timer);

const resume = () => {
    clearTimeout(timer);
    timer = setTimeout(dismiss, DURATION_MS);
};

const show = (toast: FlashToast) => {
    if (toast.id === current.value?.id) {
        return;
    }

    current.value = toast;
    resume();
};

const notify = (message: string, tone: ToastTone = 'success') =>
    show({ id: `local-${Date.now()}`, tone, message });

export function useToast() {
    return { toast: readonly(current), show, notify, dismiss, pause, resume };
}
