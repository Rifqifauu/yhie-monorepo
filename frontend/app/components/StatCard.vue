<template>
    <div
        class="relative overflow-hidden rounded-2xl border border-white/20 bg-white/10 p-6 shadow-xl backdrop-blur-lg"
    >
        <div
            class="absolute -inset-0.5 bg-gradient-to-br from-emerald-400/30 to-blue-500/30 opacity-50 blur-xl"
        ></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-emerald-800">{{
                    title
                }}</span>
            </div>
            <div class="mt-2 text-2xl font-bold text-emerald-800">
                {{ value }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    title: string;
    value: string | number;
}>();
</script>
