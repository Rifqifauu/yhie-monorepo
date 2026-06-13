<template>
    <div>
        <AdminHeader
            title="Beranda"
            description="Pantau performa konten, aktivitas tim, dan status sistem dari satu tempat."
        />

        <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatCard
                    title="Pendaftaran Pending"
                    :value="dashboard?.registrations ?? 0"
                    icon="i-heroicons-user-group"
                />
                <StatCard
                    title="Artikel Terbit"
                    :value="dashboard?.articles ?? 0"
                    icon="i-heroicons-document-text"
                />
                <StatCard
                    title="Transaksi Pending"
                    :value="dashboard?.pendingTransaction ?? 0"
                    icon="i-heroicons-clock"
                />
                <StatCard
                    title="Total Pendapatan"
                    :value="formatCurrency(dashboard?.successTransaction ?? 0)"
                    icon="i-heroicons-banknotes"
                />
            </div>

            <!-- Schedule Table -->
            <div
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
            >
                <h3 class="mb-4 text-lg font-semibold text-gray-900">
                    Jadwal Mendatang
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-3">Nama Agenda</th>
                                <th class="px-4 py-3">Tanggal Mulai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="item in dashboard?.upcomingSchedule"
                                :key="item.id"
                            >
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ item.name }}
                                </td>
                                <td class="px-4 py-3">
                                    {{
                                        new Date(
                                            item.start_date,
                                        ).toLocaleDateString("id-ID")
                                    }}
                                </td>
                            </tr>
                            <tr
                                v-if="dashboard?.upcomingSchedule?.length === 0"
                            >
                                <td colspan="2" class="px-4 py-3 text-center">
                                    Tidak ada jadwal dalam 7 hari kedepan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
console.log(dashboard);

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(val);
};
</script>
