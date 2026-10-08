<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);

const initials = computed(() => {
    const parts = (user.value?.name ?? '').trim().split(/\s+/).filter(Boolean);

    if (parts.length === 0) {
        return '';
    }

    return (parts.length > 1 ? `${parts[0][0]}${parts[parts.length - 1][0]}` : parts[0].slice(0, 2)).toUpperCase();
});
</script>

<template>
    <span
        v-if="user"
        class="group relative flex shrink-0 items-center gap-2 rounded-full border border-[rgba(160,205,245,0.16)] bg-white/[0.04] py-1 pl-1 pr-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-cyan"
        tabindex="0"
        :aria-label="`Signed in as ${user.name} (${user.email})`"
    >
        <span
            class="relative grid h-[26px] w-[26px] place-items-center rounded-full bg-[linear-gradient(145deg,#8B7CFF,#2DE2E6)] font-display text-[10px] font-semibold text-[#061020]"
            aria-hidden="true"
        >
            {{ initials }}
            <span
                class="absolute -bottom-px -right-px h-2 w-2 rounded-full border-[1.5px] border-[#0E1A2C] bg-status-completed shadow-[0_0_7px_rgba(85,217,139,0.75)]"
            />
        </span>
        <span class="text-xs font-medium text-ink-200" aria-hidden="true">{{ user.name }}</span>

        <span
            class="pointer-events-none absolute right-0 top-full z-30 mt-2 whitespace-nowrap rounded-lg border border-[#A0CDF5]/[0.16] bg-panel-base/95 px-3 py-2 font-mono text-[10.5px] text-ink-300 opacity-0 shadow-[0_12px_30px_rgba(2,8,18,0.55)] backdrop-blur-md transition duration-150 group-hover:opacity-100 group-focus-visible:opacity-100"
            aria-hidden="true"
        >
            Signed in as <span class="text-glow-cyan">{{ user.email }}</span>
        </span>
    </span>

    <span
        v-else
        class="flex shrink-0 items-center gap-2 rounded-full border border-[rgba(160,205,245,0.12)] bg-white/[0.02] px-3 py-1.5 text-xs text-ink-700"
    >
        <span class="h-1.5 w-1.5 rounded-full bg-ink-800" aria-hidden="true" />
        Signed out
    </span>
</template>
