<template>
    <aside
        class="flex h-full w-full flex-col border-r border-slate-200 bg-white transition-colors duration-300 dark:border-slate-800 dark:bg-slate-950"
    >
        <!-- Header & Toggle Button -->
        <div
            class="flex items-center border-b border-slate-100 py-5 transition-all duration-300 dark:border-slate-800"
            :class="isOpen ? 'justify-between px-6' : 'justify-center px-4'"
        >
            <div v-if="isOpen" class="flex items-center gap-3 overflow-hidden">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shadow-sm dark:bg-emerald-500/10 dark:text-emerald-400"
                >
                    <UIcon name="i-lucide-shield-check" class="h-5 w-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <p
                        class="truncate text-sm font-bold text-slate-800 dark:text-white"
                    >
                        YHIE Admin
                    </p>
                    <p
                        class="truncate text-xs font-medium text-slate-500 dark:text-slate-400"
                    >
                        Management Panel
                    </p>
                </div>
            </div>

            <!-- Toggle Button (Hanya tampil di Desktop) -->
            <button
                v-if="!isMobile"
                @click="toggleSidebar"
                class="shrink-0 rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                title="Toggle Sidebar"
            >
                <UIcon
                    :name="
                        isOpen
                            ? 'i-lucide-panel-left-close'
                            : 'i-lucide-panel-left-open'
                    "
                    class="h-5 w-5"
                />
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto px-4 py-6">
            <div class="space-y-1">
                <p
                    v-show="isOpen"
                    class="mb-3 px-3 text-xs font-bold uppercase tracking-wider text-slate-400 transition-opacity duration-300 dark:text-slate-500"
                >
                    Navigation
                </p>
                <nav class="space-y-1">
                    <NuxtLink
                        v-for="item in navigation"
                        :key="item.to"
                        :to="item.to"
                        class="group flex items-center rounded-lg py-2.5 text-sm font-medium transition-all duration-200"
                        :class="[
                            isOpen ? 'gap-3 px-3' : 'justify-center px-0',
                            isActive(item.to)
                                ? 'bg-emerald-50 text-emerald-700 shadow-sm dark:bg-emerald-500/10 dark:text-emerald-400'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white',
                        ]"
                        :title="!isOpen ? item.label : ''"
                    >
                        <UIcon
                            :name="item.icon"
                            class="h-5 w-5 shrink-0 transition-colors duration-200"
                            :class="
                                isActive(item.to)
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : 'text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300'
                            "
                        />
                        <span v-if="isOpen" class="truncate">{{
                            item.label
                        }}</span>

                        <UBadge
                            v-if="item.badge && isOpen"
                            size="sm"
                            :variant="isActive(item.to) ? 'solid' : 'soft'"
                            :color="isActive(item.to) ? 'emerald' : 'gray'"
                            class="ml-auto"
                        >
                            {{ item.badge }}
                        </UBadge>
                    </NuxtLink>
                </nav>
            </div>
        </div>

        <!-- Footer / Logout -->
        <div class="border-t border-slate-100 p-4 dark:border-slate-800">
            <button
                class="group flex w-full items-center rounded-lg py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-red-50 hover:text-red-600 dark:text-slate-400 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                :class="isOpen ? 'gap-3 px-3' : 'justify-center px-0'"
                :title="!isOpen ? 'Logout' : ''"
            >
                <UIcon
                    name="i-lucide-log-out"
                    class="h-5 w-5 shrink-0 text-slate-400 group-hover:text-red-500 dark:text-slate-500 dark:group-hover:text-red-400"
                />
                <span v-if="isOpen" class="truncate">Logout</span>
            </button>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";

const props = defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
});

const route = useRoute();
const isDesktopSidebarOpen = useState("desktopSidebar", () => true);

const isOpen = computed(() =>
    props.isMobile ? true : isDesktopSidebarOpen.value,
);

const toggleSidebar = () => {
    if (!props.isMobile) {
        isDesktopSidebarOpen.value = !isDesktopSidebarOpen.value;
    }
};

const navigation = [
    { label: "Dashboard", to: "/admin", icon: "i-lucide-layout-dashboard" },
    {
        label: "Users",
        to: "/admin/users",
        icon: "i-lucide-users",
        badge: "New",
    },
    {
        label: "Registrations",
        to: "/admin/registrations",
        icon: "i-lucide-folders",
    },
    { label: "Programs", to: "/admin/programs", icon: "i-lucide-book" },
    { label: "Articles", to: "/admin/articles", icon: "i-lucide-file-text" },
    { label: "Media", to: "/admin/media", icon: "i-lucide-image" },
    { label: "Schedule", to: "/admin/schedules", icon: "i-lucide-calendar" },
    { label: "Partners", to: "/admin/partners", icon: "i-lucide-stars" },
    { label: "Settings", to: "/admin/settings", icon: "i-lucide-settings-2" },
];

const isActive = (path: string) => {
    if (path === "/admin") {
        return route.path === path;
    }
    return route.path.startsWith(path);
};
</script>
