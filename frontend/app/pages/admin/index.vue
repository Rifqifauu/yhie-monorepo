<template>
    <div
        class="min-h-screen bg-emerald-50/20 transition-colors duration-300 dark:bg-gray-950"
    >
        <div
            class="sticky top-0 z-10 border-b border-gray-200 border-t-4 border-t-emerald-500 bg-white/80 backdrop-blur-xl dark:border-gray-800 dark:bg-gray-900/80 dark:border-t-emerald-400"
        >
            <UContainer>
                <div
                    class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <div class="flex items-center gap-3">
                            <h1
                                class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                            >
                                Beranda
                            </h1>
                            <UBadge color="emerald" variant="soft" size="sm"
                                >Sistem Aktif</UBadge
                            >
                        </div>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Pantau performa konten, aktivitas pendaftaran, dan
                            jadwal hari ini.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <UButton
                            icon="i-heroicons-arrow-down-tray"
                            color="gray"
                            variant="ghost"
                            @click="downloadReport"
                        >
                            Unduh Laporan
                        </UButton>
                        <UButton
                            icon="i-heroicons-plus"
                            color="emerald"
                            to="/admin/schedules/create"
                        >
                            Agenda Baru
                        </UButton>
                    </div>
                </div>
            </UContainer>
        </div>

        <UContainer class="py-8">
            <div class="space-y-8">
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <NuxtLink
                        v-for="stat in stats"
                        :key="stat.title"
                        :to="stat.to"
                        class="block"
                    >
                    <UCard
                        class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/5 dark:hover:shadow-emerald-400/5 cursor-pointer"
                        :ui="{ body: { padding: 'p-6' } }"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <UAvatar
                                    :icon="stat.icon"
                                    size="lg"
                                    :ui="{
                                        background:
                                            'bg-emerald-100 dark:bg-emerald-950/50',
                                        text: 'text-emerald-600 dark:text-emerald-400',
                                    }"
                                />
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        {{ stat.title }}
                                    </p>
                                    <p
                                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                                    >
                                        {{ stat.value }}
                                    </p>
                                </div>
                            </div>
                            <div
                                v-if="stat.trend"
                                :class="[
                                    'flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold',
                                    stat.trend > 0
                                        ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                                        : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400',
                                ]"
                            >
                                <UIcon
                                    :name="
                                        stat.trend > 0
                                            ? 'i-heroicons-arrow-trending-up'
                                            : 'i-heroicons-arrow-trending-down'
                                    "
                                    class="h-4 w-4"
                                />
                                <span>{{ Math.abs(stat.trend) }}%</span>
                            </div>
                        </div>

                        <div
                            class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800/60"
                        >
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                {{ stat.detail }}
                            </p>
                        </div>
                    </UCard>
                    </NuxtLink>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                    <div class="space-y-6 lg:col-span-5">
                        <div class="flex items-center justify-between">
                            <h3
                                class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                            >
                                Rekap Pendaftaran
                            </h3>
                            <UButton
                                variant="link"
                                color="emerald"
                                class="px-0"
                                to="/admin/registrations"
                                >Kelola</UButton
                            >
                        </div>

                        <div class="grid gap-4">
                            <UCard
                                v-for="item in registrationStats"
                                :key="item.label"
                                :class="[
                                    'group transition-all duration-300 hover:-translate-y-1',
                                    item.bgClass,
                                    item.ringClass,
                                ]"
                                :ui="{ body: { padding: 'p-5' }, ring: '' }"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div
                                            :class="[
                                                'flex h-12 w-12 items-center justify-center rounded-xl shadow-sm transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3',
                                                item.iconBg,
                                            ]"
                                        >
                                            <UIcon
                                                :name="item.icon"
                                                class="h-6 w-6"
                                                :class="item.iconColor"
                                            />
                                        </div>
                                        <div>
                                            <span
                                                class="block font-semibold text-gray-700 dark:text-gray-300"
                                                >{{ item.label }}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500"
                                                >{{ item.description }}</span
                                            >
                                        </div>
                                    </div>
                                    <span
                                        class="text-2xl font-bold tracking-tight"
                                        :class="item.textColor"
                                        >{{ item.value }}</span
                                    >
                                </div>
                            </UCard>
                        </div>
                    </div>

                    <div class="flex flex-col lg:col-span-7">
                        <UCard
                            class="flex flex-grow flex-col overflow-hidden shadow-sm transition-all hover:border-emerald-200 dark:hover:border-emerald-800"
                            :ui="{
                                body: { padding: '' },
                                header: { padding: 'px-6 py-5' },
                                footer: {
                                    padding:
                                        'px-6 py-4 bg-gray-50/50 dark:bg-gray-800/20',
                                },
                            }"
                        >
                            <template #header>
                                <div class="flex items-center justify-between">
                                    <h3
                                        class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                                    >
                                        Jadwal Terdekat
                                    </h3>
                                    <UBadge color="gray" variant="soft"
                                        >7 Hari Kedepan</UBadge
                                    >
                                </div>
                            </template>

                            <UTable
                                :columns="scheduleColumns"
                                :data="dashboard?.upcomingSchedule || []"
                                class="w-full"
                            >
                                <template #name-cell="{ row }">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-xs text-gray-500 line-clamp-1"
                                        >
                                            {{
                                                row.original?.title_id ||
                                                "Tidak ada deskripsi"
                                            }}
                                        </span>
                                    </div>
                                </template>

                                <template #start_date-cell="{ row }">
                                    <div
                                        class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        <UIcon
                                            name="i-heroicons-calendar"
                                            class="h-4 w-4 text-emerald-500"
                                        />
                                        {{
                                            formatDate(
                                                row.original?.start_date ||
                                                    row.start_date,
                                            )
                                        }}
                                    </div>
                                </template>
                                <template #end_date-cell="{ row }">
                                    <div
                                        class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        <UIcon
                                            name="i-heroicons-calendar"
                                            class="h-4 w-4 text-emerald-500"
                                        />
                                        {{
                                            formatDate(
                                                row.original?.end_date ||
                                                    row.end_date,
                                            )
                                        }}
                                    </div>
                                </template>

                                <template #empty>
                                    <div
                                        class="flex flex-col items-center justify-center py-16 text-center"
                                    >
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-950/30"
                                        >
                                            <UIcon
                                                name="i-heroicons-calendar-days"
                                                class="h-8 w-8 text-emerald-500 dark:text-emerald-400"
                                            />
                                        </div>
                                        <h4
                                            class="mt-4 font-semibold text-gray-900 dark:text-white"
                                        >
                                            Jadwal Kosong
                                        </h4>
                                        <p
                                            class="mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            Belum ada agenda yang dijadwalkan
                                            untuk 7 hari kedepan. Silakan buat
                                            agenda baru.
                                        </p>
                                    </div>
                                </template>
                            </UTable>

                            <template #footer>
                                <div class="flex w-full justify-center">
                                    <UButton
                                        variant="ghost"
                                        color="gray"
                                        icon="i-heroicons-arrow-right"
                                        trailing
                                        to="/admin/schedules"
                                    >
                                        Lihat Kalender Penuh
                                    </UButton>
                                </div>
                            </template>
                        </UCard>
                    </div>
                </div>
            </div>
        </UContainer>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: "admin" });

// 1. Definisikan interface yang ketat untuk Nuxt 4 (menghindari TS error object is unknown)
interface Schedule {
    title_id: string;
    type?: string;
    start_date: string;
    end_date: string;
}

interface DashboardData {
    articles: number;
    pendingTransaction: number;
    totalRevenue: number;
    pendingRegistrations: number;
    approvedRegistration: number;
    rejectedRegistration: number;
    upcomingSchedule: Schedule[];
}

const client = useSanctumClient();

// 2. Berikan strict Generic Types <DashboardData> pada useAsyncData
const { data: dashboard } = await useAsyncData<DashboardData>("dashboard", () =>
    client("/api/dashboard"),
);

// 3. Tambahkan kolom "type" agar `<template #type-cell>` kamu bisa ter-render
const scheduleColumns = [
    { id: "name", accessorKey: "title_id", header: "Nama Agenda" },
    { id: "start_date", accessorKey: "start_date", header: "Mulai" },
    { id: "end_date", accessorKey: "end_date", header: "Selesai" },
];

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(val);
};

const downloadReport = () => {
    if (!import.meta.client || !dashboard.value) return;

    const rows = [
        ["Metrik", "Nilai"],
        ["Artikel Terbit", dashboard.value.articles],
        ["Transaksi Pending", dashboard.value.pendingTransaction],
        ["Total Pendapatan", dashboard.value.totalRevenue],
        ["Pendaftaran Menunggu", dashboard.value.pendingRegistrations],
        ["Pendaftaran Diterima", dashboard.value.approvedRegistration],
        ["Pendaftaran Ditolak", dashboard.value.rejectedRegistration],
    ];
    const csv = rows.map((row) => row.join(",")).join("\n");

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `laporan-dashboard-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);
};

const formatDate = (date: string) => {
    // Note: Pastikan data API selalu dalam UTC format agar meminimalkan hydration warning di Nuxt 4
    if (!date) return "-";
    return new Date(date).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

const stats = computed(() => [
    {
        title: "Artikel Terbit",
        value: dashboard.value?.articles ?? 0,
        icon: "i-heroicons-document-text",
        trend: 12,
        detail: "Dibandingkan dengan 30 hari terakhir",
        to: "/admin/articles",
    },
    {
        title: "Transaksi Pending",
        value: dashboard.value?.pendingTransaction ?? 0,
        icon: "i-heroicons-clock",
        trend: -5,
        detail: "Sistem merespon lebih cepat dari biasanya",
        to: "/admin/transactions",
    },
    {
        title: "Total Pendapatan",
        value: formatCurrency(dashboard.value?.totalRevenue ?? 0),
        icon: "i-heroicons-banknotes",
        trend: 8,
        detail: "Total akumulasi bulan ini",
        to: "/admin/transactions",
    },
]);

const registrationStats = computed(() => [
    {
        label: "Menunggu Peninjauan",
        description: "Perlu diverifikasi admin",
        value: dashboard.value?.pendingRegistrations ?? 0,
        icon: "i-heroicons-clock",
        bgClass: "bg-yellow-50/50 dark:bg-yellow-950/20",
        ringClass: "ring-1 ring-yellow-200 dark:ring-yellow-900/50",
        iconBg: "bg-yellow-100 dark:bg-yellow-900",
        iconColor: "text-yellow-600 dark:text-yellow-400",
        textColor: "text-yellow-900 dark:text-yellow-400",
    },
    {
        label: "Pendaftaran Diterima",
        description: "Selesai diproses",
        value: dashboard.value?.approvedRegistration ?? 0,
        icon: "i-heroicons-check-circle",
        bgClass: "bg-emerald-50/50 dark:bg-emerald-950/20",
        ringClass: "ring-1 ring-emerald-200 dark:ring-emerald-900/50",
        iconBg: "bg-emerald-100 dark:bg-emerald-900",
        iconColor: "text-emerald-600 dark:text-emerald-400",
        textColor: "text-emerald-900 dark:text-emerald-400",
    },
    {
        label: "Pendaftaran Ditolak",
        description: "Tidak memenuhi syarat",
        value: dashboard.value?.rejectedRegistration ?? 0,
        icon: "i-heroicons-x-circle",
        bgClass: "bg-red-50/50 dark:bg-red-950/20",
        ringClass: "ring-1 ring-red-200 dark:ring-red-900/50",
        iconBg: "bg-red-100 dark:bg-red-900",
        iconColor: "text-red-600 dark:text-red-400",
        textColor: "text-red-900 dark:text-red-400",
    },
]);
</script>
