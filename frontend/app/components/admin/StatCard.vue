<template>
    <UCard class="h-full">
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-2">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    {{ title }}
                </p>
                <p class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                    {{ value }}
                </p>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center rounded-full px-2 py-1 font-medium"
                        :class="trend === 'up'
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
                            : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'"
                    >
                        <UIcon :name="trend === 'up' ? 'i-lucide-trending-up' : 'i-lucide-activity'" class="mr-1 h-3.5 w-3.5" />
                        {{ change }}
                    </span>
                    <span class="text-slate-400 dark:text-slate-500">dibanding minggu lalu</span>
                </div>
            </div>

            <div class="flex h-11 w-11 items-center justify-center rounded-xl"
                :class="trend === 'up'
                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
                    : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'"
            >
                <UIcon :name="icon" class="h-5 w-5" />
            </div>
        </div>
    </UCard>
</template>

<script setup lang="ts">
defineProps<{
    title: string;
    value: string;
    change: string;
    icon: string;
    trend?: 'up' | 'neutral';
}>();
</script>
