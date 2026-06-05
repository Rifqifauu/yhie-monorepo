<template>
    <header
        class="sticky top-0 z-20 border-b border-slate-200/70 bg-white/85 backdrop-blur dark:border-white/10 dark:bg-slate-950/75"
    >
        <div
            class="flex flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8"
        >
            <div>
                <div
                    class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400"
                >
                    <UIcon name="i-lucide-panel-left" class="h-4 w-4" />
                    <span>Admin panel</span>
                </div>
                <h1
                    class="mt-1 text-xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-2xl"
                >
                    {{ title }}
                </h1>
                <p
                    v-if="description"
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    {{ description }}
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <UInput
                    icon="i-lucide-search"
                    placeholder="Cari data admin..."
                    size="lg"
                    class="min-w-0 sm:w-72"
                />
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
defineProps<{
    title: string;
    description?: string;
}>();
</script>
