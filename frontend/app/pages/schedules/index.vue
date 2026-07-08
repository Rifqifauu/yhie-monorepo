<template>
    <div class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen">
        <section class="py-14 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4 font-serif text-emerald-950 dark:text-emerald-50 tracking-tight">
                        {{ locale === "id" ? "Jadwal Kegiatan" : "Event Schedules" }}
                    </h1>
                    <div class="w-20 h-1 bg-gradient-to-r from-emerald-800 to-emerald-500 mx-auto rounded-full mb-4"></div>
                    <p class="text-slate-600 dark:text-emerald-100/70">
                        {{ locale === "id" ? "Semua jadwal kegiatan dan program kami, dari yang akan datang hingga yang sudah selesai." : "All our event and program schedules, from upcoming to past." }}
                    </p>
                </div>

                <div class="max-w-md mx-auto mb-10">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        size="lg"
                        :placeholder="locale === 'id' ? 'Cari jadwal...' : 'Search schedules...'"
                        class="w-full"
                        @keyup.enter="applySearch"
                    />
                </div>

                <!-- Loading State -->
                <div v-if="pending" class="flex flex-col items-center justify-center py-20 gap-4">
                    <UIcon name="i-heroicons-arrow-path" class="w-12 h-12 animate-spin text-emerald-600 dark:text-emerald-400" />
                    <span class="text-sm font-medium tracking-wide text-emerald-700 dark:text-emerald-300">
                        {{ locale === "id" ? "Memuat jadwal..." : "Loading schedules..." }}
                    </span>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="max-w-xl mx-auto my-6">
                    <UAlert
                        icon="i-heroicons-exclamation-triangle"
                        color="red"
                        variant="soft"
                        :title="locale === 'id' ? 'Gagal memuat jadwal' : 'Failed to load schedules'"
                        :description="locale === 'id' ? 'Terjadi kesalahan saat mengambil data jadwal dari server.' : 'An error occurred while fetching schedule data.'"
                    >
                        <template #actions>
                            <UButton size="sm" color="red" variant="solid" icon="i-heroicons-arrow-path-20-solid" @click="refresh">
                                {{ locale === "id" ? "Coba Lagi" : "Try Again" }}
                            </UButton>
                        </template>
                    </UAlert>
                </div>

                <!-- Empty State -->
                <div v-else-if="!schedules || schedules.length === 0" class="py-12">
                    <EmptyData
                        title="Jadwal"
                        :description="locale === 'id' ? 'Belum ada jadwal yang tersedia saat ini.' : 'No schedules available at the moment.'"
                        icon="i-lucide-calendar-x"
                    />
                </div>

                <!-- Data State -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="sched in schedules"
                        :key="sched.id"
                        class="bg-white dark:bg-emerald-900/20 rounded-3xl p-6 border border-emerald-100 dark:border-emerald-800/50 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full"
                                :class="isUpcoming(sched) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800/60 dark:text-slate-400'"
                            >
                                <UIcon :name="isUpcoming(sched) ? 'i-lucide-calendar-clock' : 'i-lucide-check-circle'" class="w-3 h-3" />
                                {{ isUpcoming(sched) ? (locale === "id" ? "Akan Datang" : "Upcoming") : (locale === "id" ? "Selesai" : "Past") }}
                            </span>
                        </div>

                        <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-2 flex items-center gap-1.5">
                            <UIcon name="i-lucide-calendar" class="w-3.5 h-3.5" />
                            {{ formatDate(sched.start_date) }}
                            <span v-if="formatDate(sched.start_date) !== formatDate(sched.end_date)"> - {{ formatDate(sched.end_date) }}</span>
                        </div>

                        <h3 class="font-bold text-slate-900 dark:text-emerald-50 text-lg mb-2 leading-snug line-clamp-2">
                            {{ titleOf(sched) }}
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-emerald-100/70 line-clamp-3 leading-relaxed">
                            {{ descOf(sched) }}
                        </p>
                    </div>
                </div>

                <div v-if="totalItems > 0" class="flex justify-center mt-12">
                    <UPagination v-model:page="page" :total="totalItems" :items-per-page="10" @update:page="changePage" />
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
const { locale } = useI18n();
const {
    page,
    searchInput,
    schedules,
    totalItems,
    pending,
    error,
    titleOf,
    descOf,
    applySearch,
    changePage,
    refresh,
} = useSchedules();

const formatDate = (dateStr: string) => {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;

    return new Intl.DateTimeFormat(locale.value === "id" ? "id-ID" : "en-US", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    }).format(date);
};

const isUpcoming = (sched: { end_date?: string; start_date?: string }) => {
    const ref = sched.end_date || sched.start_date;
    if (!ref) return false;
    return new Date(ref).getTime() > Date.now();
};

useSeoMeta({
    title: () => (locale.value === "id" ? "Jadwal Kegiatan - YHIE" : "Event Schedules - YHIE"),
    description: () =>
        locale.value === "id"
            ? "Semua jadwal kegiatan dan program Yayasan Hafiz Indonesia Emas."
            : "All event and program schedules from Yayasan Hafiz Indonesia Emas.",
});
</script>
