<template>
    <div
        class="flex h-screen w-full overflow-hidden bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100"
    >
        <aside
            class="relative z-20 hidden h-full shrink-0 overflow-hidden transition-[width] duration-300 ease-in-out lg:block"
            :class="isDesktopSidebarOpen ? 'w-[260px]' : 'w-0'"
        >
            <div
                class="flex h-full w-[260px] flex-col border-r border-slate-200 bg-white transition-transform duration-300 ease-in-out dark:border-slate-800 dark:bg-slate-900"
                :class="
                    isDesktopSidebarOpen ? 'translate-x-0' : '-translate-x-full'
                "
            >
                <AdminSidebar
                    class="scrollbar-hide h-full w-full flex-1 overflow-y-auto overflow-x-hidden"
                />
            </div>
        </aside>

        <div class="flex h-full min-w-0 flex-1 flex-col overflow-hidden">
            <header
                class="z-10 flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 dark:border-slate-800 dark:bg-slate-900 lg:hidden"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400"
                    >
                        <UIcon name="i-lucide-shield-check" class="h-5 w-5" />
                    </div>
                    <div>
                        <p
                            class="text-sm font-semibold tracking-tight text-slate-900 dark:text-white"
                        >
                            YHIE Admin
                        </p>
                        <p
                            class="text-[11px] font-medium text-slate-500 dark:text-slate-400"
                        >
                            Dashboard Overview
                        </p>
                    </div>
                </div>

                <UButton
                    color="gray"
                    variant="ghost"
                    icon="i-lucide-menu"
                    class="hover:bg-slate-100 dark:hover:bg-slate-800"
                    @click="isMobileSidebarOpen = true"
                />
            </header>

            <USlideover
                v-model:open="isMobileSidebarOpen"
                side="left"
                class="lg:hidden"
            >
                <template #content>
                    <div
                        class="relative flex h-full w-full max-w-[260px] flex-col bg-white shadow-xl dark:bg-slate-900"
                    >
                        <UButton
                            color="gray"
                            variant="ghost"
                            icon="i-lucide-x"
                            class="absolute right-3 top-3 z-50 lg:hidden"
                            @click="isMobileSidebarOpen = false"
                        />
                        <AdminSidebar
                            class="scrollbar-hide flex-1 overflow-y-auto"
                        />
                    </div>
                </template>
            </USlideover>

            <main
                class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 lg:p-8"
            >
                <div class="mx-auto max-w-7xl animate-in fade-in duration-500">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

const isMobileSidebarOpen = ref(false);
const isDesktopSidebarOpen = ref(true);
</script>

<style scoped>
/* Opsional: Menyembunyikan garis scrollbar pada sidebar secara visual,
   tapi tetap bisa di-scroll menggunakan mouse wheel/trackpad jika menu terlalu banyak */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
