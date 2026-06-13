<template>
    <aside
        class="flex h-full w-full flex-col border-r border-slate-200 bg-white transition-colors duration-300 dark:border-slate-800 dark:bg-slate-950"
    >
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

        <!-- Dropdown Menu Nuxt UI -->
        <div class="border-t border-slate-200 p-4 dark:border-slate-800">
            <!-- Dropdown Menu Nuxt UI -->
            <div class="border-t border-slate-200 p-4 dark:border-slate-800">
                <UDropdownMenu
                    :items="userMenuItems"
                    :content="{ placement: 'top-start', class: 'w-56' }"
                    class="w-full"
                >
                    <!-- Tambahkan class w-full di sini agar tombolnya memenuhi container -->
                    <button
                        class="group flex w-full items-center justify-between rounded-lg px-2 py-2 text-sm font-medium transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        :title="!isOpen ? user?.name || 'Profil Pengguna' : ''"
                    >
                        <div
                            class="flex items-center gap-3 w-full overflow-hidden"
                        >
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                            >
                                <UIcon name="i-lucide-user" class="h-4 w-4" />
                            </div>

                            <span
                                v-if="isOpen"
                                class="truncate text-slate-700 dark:text-slate-200 flex-1 text-left"
                            >
                                {{ user?.name || "Administrator" }}
                            </span>
                        </div>

                        <UIcon
                            v-if="isOpen"
                            name="i-lucide-chevron-up"
                            class="h-4 w-4 shrink-0 text-slate-400 transition-transform group-hover:text-slate-600 dark:group-hover:text-slate-300"
                        />
                    </button>
                </UDropdownMenu>
            </div>
        </div>
    </aside>

    <!-- Modal Logout -->
    <UModal v-model:open="openModal">
        <template #content>
            <div class="p-6 space-y-6">
                <div class="space-y-1">
                    <h3
                        class="text-lg font-semibold text-gray-950 dark:text-white"
                    >
                        Konfirmasi Logout
                    </h3>
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin logout?
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <UButton
                        label="Batal"
                        color="neutral"
                        variant="ghost"
                        @click="openModal = false"
                    />
                    <UButton
                        label="Logout"
                        color="error"
                        :loading="loading"
                        @click="handleLogout"
                    />
                </div>
            </div>
        </template>
    </UModal>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { useRoute } from "vue-router";
import type { DropdownMenuItem } from "@nuxt/ui";

const { user, logout } = useSanctumAuth();

const openModal = ref(false);
const loading = ref(false);
const toast = useToast();
const colorMode = useColorMode();
const route = useRoute();

const props = defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
});

const isDesktopSidebarOpen = useState("desktopSidebar", () => true);

const isOpen = computed(() =>
    props.isMobile ? true : isDesktopSidebarOpen.value,
);

const toggleSidebar = () => {
    if (!props.isMobile) {
        isDesktopSidebarOpen.value = !isDesktopSidebarOpen.value;
    }
};

const toggleTheme = () => {
    colorMode.preference = colorMode.value === "dark" ? "light" : "dark";
};

const openLogoutModal = () => {
    openModal.value = true;
};

// Computed Items untuk UDropdownMenu
const userMenuItems = computed<DropdownMenuItem[][]>(() => [
    [
        {
            label: colorMode.value === "dark" ? "Mode Terang" : "Mode Gelap",
            icon: colorMode.value === "dark" ? "i-lucide-sun" : "i-lucide-moon",
            onSelect: toggleTheme,
        },
    ],
    [
        {
            label: "Logout",
            icon: "i-lucide-log-out",
            color: "error",
            onSelect: openLogoutModal,
        },
    ],
]);

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

const handleLogout = async () => {
    try {
        loading.value = true;
        await logout();
    } catch (error) {
        toast.add({
            title: "Error",
            description: "Failed to logout. " + error,
            color: "error",
        });
    } finally {
        loading.value = false;
        openModal.value = false;
    }
};
</script>
