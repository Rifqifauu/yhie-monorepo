<template>
    <aside
        class="flex h-screen flex-col border-r border-emerald-700 bg-emerald-800 transition-colors duration-300 dark:border-slate-800 dark:bg-slate-950"
    >
        <div
            class="flex items-center gap-3 border-b border-emerald-700 px-6 py-5 dark:border-slate-800"
        >
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-900 text-emerald-100 shadow-inner dark:bg-emerald-500/20 dark:text-emerald-400"
            >
                <UIcon name="i-lucide-shield-check" class="h-5 w-5" />
            </div>
            <div class="min-w-0 flex-1">
                <p
                    class="truncate text-sm font-bold text-white dark:text-white"
                >
                    YHIE Admin
                </p>
                <p
                    class="truncate text-xs font-medium text-emerald-200 dark:text-slate-400"
                >
                    Management Panel
                </p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6">
            <div class="space-y-1">
                <p
                    class="mb-3 px-3 text-xs font-bold uppercase tracking-wider text-emerald-300 dark:text-slate-500"
                >
                    Navigation
                </p>
                <nav class="space-y-1">
                    <NuxtLink
                        v-for="item in navigation"
                        :key="item.to"
                        :to="item.to"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200"
                        :class="
                            isActive(item.to)
                                ? 'bg-emerald-900 text-white shadow-inner dark:bg-emerald-500/10 dark:text-emerald-400'
                                : 'text-emerald-100 hover:bg-emerald-700 hover:text-white dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white'
                        "
                    >
                        <UIcon
                            :name="item.icon"
                            class="h-5 w-5 shrink-0 transition-colors duration-200"
                            :class="
                                isActive(item.to)
                                    ? 'text-white dark:text-emerald-400'
                                    : 'text-emerald-300 group-hover:text-white dark:group-hover:text-slate-300'
                            "
                        />
                        <span class="truncate">{{ item.label }}</span>

                        <UBadge
                            v-if="item.badge"
                            size="sm"
                            :variant="isActive(item.to) ? 'solid' : 'soft'"
                            :color="isActive(item.to) ? 'white' : 'gray'"
                            class="ml-auto"
                        >
                            {{ item.badge }}
                        </UBadge>
                    </NuxtLink>
                </nav>
            </div>
        </div>

        <div class="border-t border-emerald-700 p-4 dark:border-slate-800">
            <button
                class="group flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-emerald-100 transition-all duration-200 hover:bg-red-500/20 hover:text-red-100 dark:text-slate-400 dark:hover:bg-red-500/10 dark:hover:text-red-400"
            >
                <UIcon
                    name="i-lucide-log-out"
                    class="h-5 w-5 shrink-0 text-emerald-300 group-hover:text-red-300 dark:group-hover:text-red-400"
                />
                <span class="truncate">Logout</span>
            </button>
        </div>
    </aside>
</template>

<script setup lang="ts">
const route = useRoute();

const navigation = [
    { label: "Dashboard", to: "/admin", icon: "i-lucide-layout-dashboard" },
    {
        label: "Users",
        to: "/admin/users",
        icon: "i-lucide-users",
        badge: "New",
    },
    { label: "Articles", to: "/admin/articles", icon: "i-lucide-file-text" },
    { label: "Media", to: "/admin/media", icon: "i-lucide-image" },
    { label: "Settings", to: "/admin/settings", icon: "i-lucide-settings-2" },
];

const isActive = (path: string) => {
    if (path === "/admin") {
        return route.path === path;
    }
    return route.path.startsWith(path);
};
</script>
