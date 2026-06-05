<template>
    <aside
        class="flex h-full flex-col border-r border-slate-200/70 bg-emerald-800/25 backdrop-blur dark:border-white/10 dark:bg-slate-950/80"
    >
        <div
            class="flex items-center gap-3 border-b border-slate-200/70 px-5 py-5 dark:border-white/10"
        >
            <div
                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300"
            >
                <UIcon name="i-lucide-shield-check" class="h-5 w-5" />
            </div>
            <div class="min-w-0">
                <p
                    class="truncate text-sm font-semibold text-slate-900 dark:text-white"
                >
                    YHIE Admin
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Management panel
                </p>
            </div>
        </div>

        <div class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
            <div>
                <p
                    class="mb-3 px-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 dark:text-slate-500"
                >
                    Navigation
                </p>
                <nav class="space-y-1.5">
                    <NuxtLink
                        v-for="item in navigation"
                        :key="item.to"
                        :to="item.to"
                        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors"
                        :class="
                            isActive(item.to)
                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white'
                        "
                    >
                        <UIcon :name="item.icon" class="h-5 w-5 shrink-0" />
                        <span class="truncate">{{ item.label }}</span>
                        <UBadge
                            v-if="item.badge"
                            size="sm"
                            variant="subtle"
                            color="neutral"
                            class="ml-auto"
                        >
                            {{ item.badge }}
                        </UBadge>
                    </NuxtLink>
                </nav>
            </div>
        </div>
    </aside>
</template>

<script setup lang="ts">
const route = useRoute();

const navigation = [
    {
        label: "Dashboard",
        to: "/admin",
        icon: "i-lucide-layout-dashboard",
    },
    {
        label: "Users",
        to: "/admin/users",
        icon: "i-lucide-users",
    },
    {
        label: "Articles",
        to: "/admin/articles",
        icon: "i-lucide-file-text",
    },
    {
        label: "Media",
        to: "/admin/media",
        icon: "i-lucide-image",
    },

    {
        label: "Settings",
        to: "/admin/settings",
        icon: "i-lucide-settings-2",
    },
];

const isActive = (path: string) => {
    if (path === "/admin") {
        return route.path === path;
    }

    return route.path.startsWith(path);
};
</script>
