<template>
    <div
        class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-white"
    >
        <div class="grid min-h-screen lg:grid-cols-[280px_minmax(0,1fr)]">
            <div class="hidden lg:block">
                <AdminSidebar class="sticky top-0 h-screen" />
            </div>

            <div class="flex min-h-screen flex-col">
                <div
                    class="border-b border-slate-200/70 bg-white/85 px-4 py-3 backdrop-blur dark:border-white/10 dark:bg-slate-950/75 lg:hidden"
                >
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300"
                            >
                                <UIcon
                                    name="i-lucide-shield-check"
                                    class="h-5 w-5"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-semibold text-slate-900 dark:text-white"
                                >
                                    YHIE Admin
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Dashboard overview
                                </p>
                            </div>
                        </div>

                        <UButton
                            color="neutral"
                            variant="soft"
                            icon="i-lucide-menu"
                            @click="isSidebarOpen = true"
                        >
                            Menu
                        </UButton>
                    </div>
                </div>

                <USlideover
                    v-model:open="isSidebarOpen"
                    side="left"
                    class="lg:hidden"
                >
                    <template #content>
                        <div
                            class="h-full w-full max-w-xs bg-white dark:bg-slate-950"
                        >
                            <AdminSidebar class="h-full" />
                        </div>
                    </template>
                </USlideover>

                <main class="flex-1 overflow-x-hidden">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const isSidebarOpen = ref(false);
</script>
