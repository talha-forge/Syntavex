<script setup lang="ts">
import type { FleetPulse } from '@/types';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps<{ pulse: FleetPulse }>();

const now = ref(new Date());
let timer: ReturnType<typeof setTimeout> | undefined;

// Tick on each minute boundary so the clock never lags the system time.
const tick = () => {
    now.value = new Date();
    timer = setTimeout(tick, 60_000 - (Date.now() % 60_000));
};

onMounted(tick);
onBeforeUnmount(() => clearTimeout(timer));

const format = (at: Date) =>
    `${at.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })} · ${at.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', hour12: false })}`;

const clockLabel = computed(() => format(now.value));

const latestRunTitle = computed(() =>
    props.pulse.latest_run_at ? `Latest run ${format(new Date(props.pulse.latest_run_at))}` : props.pulse.latest_run_label,
);
</script>

<template>
    <span
        v-if="pulse.live"
        class="flex shrink-0 items-center gap-2 rounded-full border border-accent-cyan/35 bg-accent-cyan/10 px-3 py-1.5 font-mono text-[10.5px] font-semibold tracking-[0.08em] text-accent-cyan"
    >
        <span
            class="pulse-breathe h-1.5 w-1.5 rounded-full bg-accent-cyan shadow-[0_0_10px_2px_rgba(45,226,230,0.7)]"
            aria-hidden="true"
        />
        FLEET LIVE ·
        <time :datetime="now.toISOString()" :title="latestRunTitle">{{ clockLabel }}</time>
    </span>
    <span
        v-else
        class="flex shrink-0 items-center gap-2 rounded-full border border-[rgba(160,205,245,0.16)] bg-white/[0.03] px-3 py-1.5 font-mono text-[10.5px] font-semibold tracking-[0.08em] text-ink-600"
    >
        <span class="h-1.5 w-1.5 rounded-full bg-ink-800" aria-hidden="true" />
        FLEET IDLE
    </span>
</template>
