<template>
    <div
        class="min-h-screen bg-gray-50 transition-colors duration-300 dark:bg-gray-950"
    >
        <AdminHeader
            title="Beranda"
            description="Pantau performa konten, aktivitas tim, dan status sistem dari satu tempat."
        />

        <div class="space-y-8 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <StatCard
                    v-for="stat in stats"
                    :key="stat.title"
                    :title="stat.title"
                    :value="stat.value"
                    :icon="stat.icon"
                    class="transform transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-900 dark:border-gray-800"
                />
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Status Pendaftaran
                    </h3>
                    <div class="grid gap-4">
                        <div
                            v-for="item in registrationStats"
                            :key="item.label"
                            :class="[
                                'flex items-center justify-between rounded-2xl border p-5 transition-all duration-300 hover:shadow-md',
                                item.bgClass,
                                item.borderClass,
                            ]"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    :class="[
                                        'flex h-10 w-10 items-center justify-center rounded-full',
                                        item.iconBg,
                                    ]"
                                >
                                    <span
                                        :class="[
                                            item.icon,
                                            'h-6 w-6',
                                            item.iconColor,
                                        ]"
                                    />
                                </div>
                                <span
                                    class="font-medium"
                                    :class="item.textColor"
                                    >{{ item.label }}</span
                                >
                            </div>
                            <span
                                class="text-xl font-bold"
                                :class="item.textColor"
                                >{{ item.value }}</span
                            >
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <h3
                        class="mb-4 text-lg font-bold text-gray-900 dark:text-white"
                    >
                        Jadwal Mendatang
                    </h3>
                    <div
                        class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800"
                    >
                        <table class="w-full text-left text-sm">
                            <thead
                                class="bg-gray-50 text-gray-600 uppercase text-xs dark:bg-gray-800 dark:text-gray-400"
                            >
                                <tr>
                                    <th class="px-4 py-3">Nama Agenda</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-100 dark:divide-gray-800"
                            >
                                <tr
                                    v-for="item in dashboard?.upcomingSchedule"
                                    :key="item.id"
                                    class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                >
                                    <td
                                        class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ item.name }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-500 dark:text-gray-400"
                                    >
                                        {{ formatDate(item.start_date) }}
                                    </td>
                                </tr>
                                <tr v-if="!dashboard?.upcomingSchedule?.length">
                                    <td
                                        colspan="2"
                                        class="px-4 py-8 text-center text-gray-400"
                                    >
                                        Tidak ada jadwal dalam 7 hari kedepan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: "admin" });

const client = useSanctumClient();
const { data: dashboard } = await useAsyncData("dashboard", () =>
    client("/api/dashboard"),
);

// Helper Format
const formatCurrency = (val: number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(val);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};

// Data Komputasi untuk UI yang rapi
const stats = computed(() => [
    {
        title: "Artikel Terbit",
        value: dashboard.value?.articles ?? 0,
        icon: "i-heroicons-document-text",
    },
    {
        title: "Transaksi Pending",
        value: dashboard.value?.pendingTransaction ?? 0,
        icon: "i-heroicons-clock",
    },
    {
        title: "Total Pendapatan",
        value: formatCurrency(dashboard.value?.totalRevenue ?? 0),
        icon: "i-heroicons-banknotes",
    },
]);

const registrationStats = computed(() => [
    {
        label: "Pending",
        value: dashboard.value?.pendingRegistrations ?? 0,
        icon: "i-heroicons-clock",
        bgClass: "bg-yellow-50/50 dark:bg-yellow-950/20",
        borderClass: "border-yellow-100 dark:border-yellow-900",
        iconBg: "bg-yellow-100 dark:bg-yellow-900",
        iconColor: "text-yellow-600 dark:text-yellow-400",
        textColor: "text-yellow-900 dark:text-yellow-400",
    },
    {
        label: "Diterima",
        value: dashboard.value?.acceptedRegistrations ?? 0,
        icon: "i-heroicons-check-circle",
        bgClass: "bg-green-50/50 dark:bg-green-950/20",
        borderClass: "border-green-100 dark:border-green-900",
        iconBg: "bg-green-100 dark:bg-green-900",
        iconColor: "text-green-600 dark:text-green-400",
        textColor: "text-green-900 dark:text-green-400",
    },
    {
        label: "Ditolak",
        value: dashboard.value?.rejectedRegistrations ?? 0,
        icon: "i-heroicons-x-circle",
        bgClass: "bg-red-50/50 dark:bg-red-950/20",
        borderClass: "border-red-100 dark:border-red-900",
        iconBg: "bg-red-100 dark:bg-red-900",
        iconColor: "text-red-600 dark:text-red-400",
        textColor: "text-red-900 dark:text-red-400",
    },
]);
</script>
