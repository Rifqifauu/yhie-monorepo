<template>
    <div
        class="mb-6 flex flex-col gap-4 bg-white dark:bg-gray-900 p-4 rounded-xl ring-1 ring-gray-200 dark:ring-gray-800 shadow-sm"
    >
        <nav
            class="flex items-center gap-1.5 text-sm font-medium text-slate-500 dark:text-slate-400"
        >
            <NuxtLink
                to="/admin"
                class="flex items-center gap-1.5 transition-colors hover:text-emerald-600 dark:hover:text-emerald-400 cursor-pointer focus:outline-none"
            >
                <UIcon name="i-lucide-layout-dashboard" class="h-4 w-4" />
                <span>Dashboard</span>
            </NuxtLink>

            <UIcon name="i-lucide-chevron-right" class="h-4 w-4 opacity-50" />

            <NuxtLink
                :to="
                    parentRoute ||
                    `/admin/${title.toLowerCase().replace(/\s+/g, '-')}`
                "
                class="transition-colors hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none"
                :class="
                    !childTitle
                        ? 'font-semibold text-emerald-700 dark:text-emerald-400 pointer-events-none'
                        : ''
                "
            >
                {{ title }}
            </NuxtLink>

            <template v-if="childTitle">
                <UIcon
                    name="i-lucide-chevron-right"
                    class="h-4 w-4 opacity-50"
                />
                <span
                    class="font-semibold text-emerald-700 dark:text-emerald-400"
                >
                    {{ childTitle }}
                </span>
            </template>
        </nav>

        <div
            class="flex flex-col items-center justify-between gap-4 pt-4 sm:flex-row"
        >
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div
                    v-if="icon"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-600 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400"
                >
                    <UIcon :name="icon" class="h-6 w-6" />
                </div>

                <div class="flex flex-col">
                    <h1
                        class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
                    >
                        {{ childTitle || title }}
                    </h1>
                    <p
                        v-if="description"
                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                    >
                        {{ description }}
                    </p>
                </div>
            </div>

            <div
                class="flex w-full shrink-0 items-center gap-2 sm:w-auto sm:justify-end"
            >
                <slot name="actions" />

                <UButton
                    v-if="showCreate"
                    :to="
                        createRoute ||
                        `/admin/${title.toLowerCase().replace(/\s+/g, '-')}/create`
                    "
                    icon="i-lucide-plus"
                    color="primary"
                >
                    Buat {{ title }}
                </UButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
interface Props {
    title: string;
    childTitle?: string;
    description?: string;
    icon?: string;
    showCreate?: boolean;
    createRoute?: string;
    parentRoute?: string; // Tambahan properti kustom rute halaman induk jika tidak standar
}

withDefaults(defineProps<Props>(), {
    childTitle: "",
    description: "",
    icon: "",
    showCreate: false,
    createRoute: "",
    parentRoute: "",
});
</script>
