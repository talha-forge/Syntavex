<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import type { ToastTone } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

withDefaults(defineProps<{ sidebar?: boolean }>(), { sidebar: false });

const TONES: Record<ToastTone, { card: string; icon: string }> = {
    success: {
        card: 'border-accent-cyan/45 shadow-[0_0_0_1px_rgba(45,226,230,0.08),0_0_32px_rgba(45,226,230,0.22),0_18px_44px_rgba(2,8,18,0.55)]',
        icon: 'border-accent-cyan/45 bg-accent-cyan/[0.14] text-accent-cyan shadow-[0_0_14px_rgba(45,226,230,0.35)]',
    },
    error: {
        card: 'border-status-critical/45 shadow-[0_0_0_1px_rgba(242,108,120,0.08),0_0_32px_rgba(242,108,120,0.22),0_18px_44px_rgba(2,8,18,0.55)]',
        icon: 'border-status-critical/45 bg-status-critical/[0.14] text-status-critical shadow-[0_0_14px_rgba(242,108,120,0.35)]',
    },
};

const page = usePage();
const { toast, show, dismiss, pause, resume } = useToast();

watch(
    () => page.props.flash?.toast,
    (next) => {
        if (next) {
            show(next);
        }
    },
    { immediate: true },
);
</script>

<template>
    <div
        class="pointer-events-none fixed inset-x-4 top-4 z-50 flex justify-center sm:inset-x-8 sm:top-[90px]"
        :class="sidebar ? 'sm:left-[108px]' : ''"
        role="status"
        aria-live="polite"
    >
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="-translate-y-4 opacity-0"
            leave-active-class="transition duration-300 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-if="toast"
                :key="toast.id"
                class="glass-panel pointer-events-auto flex w-full max-w-[460px] items-center gap-3.5 py-3.5 pl-4 pr-2.5"
                :class="TONES[toast.tone].card"
                @mouseenter="pause"
                @mouseleave="resume"
            >
                <span
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full border"
                    :class="TONES[toast.tone].icon"
                    aria-hidden="true"
                >
                    <svg
                        v-if="toast.tone === 'success'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m5 12.5 4.5 4.5L19 7.5" />
                    </svg>
                    <svg
                        v-else
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                    >
                        <path d="M12 7v6M12 17h.01" />
                    </svg>
                </span>

                <p class="min-w-0 flex-1 text-sm font-medium leading-snug text-ink-100">
                    {{ toast.message }}
                </p>

                <button
                    type="button"
                    class="grid h-7 w-7 shrink-0 place-items-center rounded-md text-ink-700 transition duration-150 hover:bg-white/[0.06] hover:text-ink-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-cyan"
                    aria-label="Dismiss notification"
                    @click="dismiss"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        aria-hidden="true"
                    >
                        <path d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
        </Transition>
    </div>
</template>
