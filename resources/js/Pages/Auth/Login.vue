<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    status?: string;
    queue: { pending: number; frozen: number };
}>();

const queueHint = computed(() => {
    const { pending, frozen } = props.queue;

    if (pending === 0) {
        return null;
    }

    const awaiting = `${pending} action${pending === 1 ? '' : 's'} awaiting human review`;

    if (frozen === 0) {
        return `${awaiting}.`;
    }

    return `${awaiting}, including ${frozen === 1 ? 'one' : frozen} frozen mid-deletion.`;
});

const DEMO = {
    email: 'demo@syntavex.app',
    password: 'syntavex-demo',
} as const;

const credentials = [
    { key: 'email', label: 'Email', value: DEMO.email },
    { key: 'password', label: 'Password', value: DEMO.password },
] as const;

const form = useForm({
    email: '',
    password: '',
});

const copied = ref<string | null>(null);
let copiedTimer: ReturnType<typeof setTimeout> | undefined;

const copy = async (key: string, value: string) => {
    try {
        await navigator.clipboard.writeText(value);
    } catch {
        return;
    }

    copied.value = key;
    clearTimeout(copiedTimer);
    copiedTimer = setTimeout(() => (copied.value = null), 1600);
};

const enterDemo = () => {
    form.email = DEMO.email;
    form.password = DEMO.password;
    form.post(route('login'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <header>
            <p class="panel-eyebrow">Agent governance platform</p>
            <h1 class="mt-2 font-display text-[21px] font-semibold tracking-tight text-ink-100">
                Step into the control room
            </h1>
            <p class="mt-1.5 text-xs text-ink-700">
                Explore a live demo of AI agent governance.
            </p>
        </header>

        <p
            v-if="status"
            class="mt-5 rounded-lg border border-status-completed/35 bg-status-completed/[0.10] px-3.5 py-2.5 font-mono text-[11px] text-glow-green"
        >
            {{ status }}
        </p>

        <section
            aria-labelledby="demo-access-heading"
            class="relative mt-6 overflow-hidden rounded-xl border border-accent-cyan/25 bg-[linear-gradient(150deg,rgba(45,226,230,0.08),rgba(26,44,72,0.45)_55%,rgba(139,124,255,0.08))] p-4 shadow-[inset_0_1px_0_0_rgb(215_245_255/0.08)]"
        >
            <div class="flex items-center justify-between gap-3">
                <h2 id="demo-access-heading" class="panel-heading">Demo access</h2>
                <span
                    class="flex items-center gap-1.5 rounded-full border border-accent-cyan/45 bg-accent-cyan/[0.12] px-2.5 py-[3px] font-mono text-[9.5px] font-semibold tracking-[0.12em] text-glow-cyan"
                >
                    <span
                        class="pulse-breathe h-1.5 w-1.5 rounded-full bg-accent-cyan shadow-[0_0_8px_#2DE2E6]"
                        aria-hidden="true"
                    />
                    LIVE DEMO
                </span>
            </div>

            <dl class="mt-3.5 space-y-2">
                <div
                    v-for="item in credentials"
                    :key="item.key"
                    class="flex items-center gap-3 rounded-lg border border-white/[0.07] bg-navy-base/50 py-1.5 pl-3 pr-1.5"
                >
                    <dt class="w-[4.5rem] shrink-0 text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-ink-700">
                        {{ item.label }}
                    </dt>
                    <dd class="min-w-0 flex-1 truncate font-mono text-[12.5px] text-ink-100">
                        {{ item.value }}
                    </dd>
                    <button
                        type="button"
                        class="grid h-7 w-7 shrink-0 place-items-center rounded-md text-ink-600 transition duration-150 hover:bg-white/[0.06] hover:text-glow-cyan focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-cyan"
                        :aria-label="copied === item.key ? `${item.label} copied` : `Copy ${item.label.toLowerCase()}`"
                        @click="copy(item.key, item.value)"
                    >
                        <svg
                            v-if="copied === item.key"
                            class="h-3.5 w-3.5 text-status-completed"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="m5 12.5 4.5 4.5L19 7.5" />
                        </svg>
                        <svg
                            v-else
                            class="h-3.5 w-3.5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <rect x="9" y="9" width="11" height="11" rx="2" />
                            <path d="M5 15V6a2 2 0 0 1 2-2h9" />
                        </svg>
                    </button>
                </div>
            </dl>

            <button
                type="button"
                class="btn-glass btn-glass-primary mt-4 w-full justify-center"
                :disabled="form.processing"
                @click="enterDemo"
            >
                Enter demo
                <span aria-hidden="true">→</span>
            </button>

            <p v-if="queueHint" class="mt-3 text-center text-[11px] text-ink-700">
                {{ queueHint }}
            </p>
        </section>

        <p v-if="form.errors.email" class="field-error mt-3">{{ form.errors.email }}</p>

        <template #footer>
            <p class="text-center text-[11px] text-ink-800">
                Portfolio concept by Talha Ali · Seeded data, no live AI integrations.
            </p>
        </template>
    </GuestLayout>
</template>
