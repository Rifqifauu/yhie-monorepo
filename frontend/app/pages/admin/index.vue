<template>
    <div>
        <AdminTopbar
            title="Dashboard"
            description="Pantau performa konten, aktivitas tim, dan status sistem dari satu tempat."
        />

        <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <AdminStatCard
                    title="Total Artikel"
                    value="24"
                    change="+12%"
                    icon="i-lucide-newspaper"
                    trend="up"
                />
                <AdminStatCard
                    title="Galeri Aktif"
                    value="138"
                    change="+8 file"
                    icon="i-lucide-images"
                    trend="up"
                />
                <AdminStatCard
                    title="Partner"
                    value="16"
                    change="2 update"
                    icon="i-lucide-handshake"
                    trend="neutral"
                />
                <AdminStatCard
                    title="Pesan Masuk"
                    value="9"
                    change="Perlu follow up"
                    icon="i-lucide-inbox"
                    trend="neutral"
                />
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <UCard class="xl:col-span-2">
                    <template #header>
                        <div
                            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <h2
                                    class="text-lg font-semibold text-slate-900 dark:text-white"
                                >
                                    Aktivitas terbaru
                                </h2>
                                <p
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Ringkasan update terakhir dari tim konten
                                    dan media.
                                </p>
                            </div>
                            <UButton
                                color="neutral"
                                variant="soft"
                                icon="i-lucide-list-filter"
                            >
                                Filter
                            </UButton>
                        </div>
                    </template>

                    <div class="space-y-4">
                        <div
                            v-for="item in recentActivities"
                            :key="item.title"
                            class="flex items-start gap-4 rounded-2xl border border-slate-200/70 bg-slate-50/70 p-4 dark:border-white/10 dark:bg-white/5"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                :class="item.color"
                            >
                                <UIcon :name="item.icon" class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <p
                                        class="font-medium text-slate-900 dark:text-white"
                                    >
                                        {{ item.title }}
                                    </p>
                                    <span
                                        class="text-xs text-slate-400 dark:text-slate-500"
                                    >
                                        {{ item.time }}
                                    </span>
                                </div>
                                <p
                                    class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400"
                                >
                                    {{ item.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </UCard>

                <div class="space-y-6">
                    <UCard>
                        <template #header>
                            <div>
                                <h2
                                    class="text-lg font-semibold text-slate-900 dark:text-white"
                                >
                                    Quick actions
                                </h2>
                                <p
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Shortcut untuk pekerjaan yang sering
                                    dipakai.
                                </p>
                            </div>
                        </template>

                        <div class="grid gap-3">
                            <UButton
                                v-for="action in quickActions"
                                :key="action.label"
                                :icon="action.icon"
                                color="neutral"
                                variant="soft"
                                class="justify-start"
                            >
                                {{ action.label }}
                            </UButton>
                        </div>
                    </UCard>

                    <UCard>
                        <template #header>
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <div>
                                    <h2
                                        class="text-lg font-semibold text-slate-900 dark:text-white"
                                    >
                                        Status sistem
                                    </h2>
                                    <p
                                        class="text-sm text-slate-500 dark:text-slate-400"
                                    >
                                        Monitoring singkat layanan admin.
                                    </p>
                                </div>
                                <UBadge color="success" variant="subtle">
                                    Online
                                </UBadge>
                            </div>
                        </template>

                        <div class="space-y-4">
                            <div
                                v-for="service in services"
                                :key="service.name"
                                class="flex items-center justify-between gap-3 rounded-xl border border-slate-200/70 px-4 py-3 dark:border-white/10"
                            >
                                <div>
                                    <p
                                        class="font-medium text-slate-900 dark:text-white"
                                    >
                                        {{ service.name }}
                                    </p>
                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        {{ service.description }}
                                    </p>
                                </div>
                                <UBadge :color="service.color" variant="subtle">
                                    {{ service.status }}
                                </UBadge>
                            </div>
                        </div>
                    </UCard>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    layout: "admin",
});

const recentActivities = [
    {
        title: "Artikel program tahfidz diperbarui",
        description:
            "Konten landing page program telah disesuaikan dengan copywriting terbaru untuk periode pendaftaran Juni.",
        time: "10 menit lalu",
        icon: "i-lucide-file-pen-line",
        color: "bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300",
    },
    {
        title: "Galeri kegiatan ditambah 12 foto baru",
        description:
            "Tim media telah mengunggah dokumentasi kegiatan wisuda tahfidz dan outbound akhir pekan.",
        time: "45 menit lalu",
        icon: "i-lucide-image-plus",
        color: "bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300",
    },
    {
        title: "Partner baru menunggu publikasi",
        description:
            "Data partner baru sudah masuk dan tinggal menunggu review sebelum tampil di halaman publik.",
        time: "2 jam lalu",
        icon: "i-lucide-handshake",
        color: "bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300",
    },
];

const quickActions = [
    {
        label: "Tambah artikel baru",
        icon: "i-lucide-square-pen",
    },
    {
        label: "Upload media galeri",
        icon: "i-lucide-upload",
    },
    {
        label: "Perbarui data partner",
        icon: "i-lucide-users-round",
    },
    {
        label: "Atur pengaturan situs",
        icon: "i-lucide-settings",
    },
];

const services = [
    {
        name: "Website frontend",
        description: "Halaman publik dapat diakses normal.",
        status: "Healthy",
        color: "success",
    },
    {
        name: "API settings",
        description: "Sinkronisasi konten berjalan lancar.",
        status: "Stable",
        color: "primary",
    },
    {
        name: "Media storage",
        description: "Kapasitas tersisa 68%.",
        status: "Monitor",
        color: "warning",
    },
];
</script>
