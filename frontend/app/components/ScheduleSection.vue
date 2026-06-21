<template>
    <section class="relative w-full mx-auto px-4 py-24 overflow-hidden transition-colors duration-500 bg-slate-50 dark:bg-emerald-950/80">
        <!-- Background Decorations -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <!-- Grid Pattern -->
            <svg class="absolute inset-0 h-full w-full opacity-[0.03] dark:opacity-[0.05]" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="schedule-grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M0 40V0H40" fill="none" stroke="currentColor" stroke-width="1"></path>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#schedule-grid)"></rect>
            </svg>
            
            <!-- Glowing Blobs -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-96 h-96 bg-emerald-400/20 dark:bg-emerald-600/20 rounded-full blur-3xl opacity-50 mix-blend-multiply dark:mix-blend-screen"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-80 h-80 bg-teal-400/20 dark:bg-teal-600/20 rounded-full blur-3xl opacity-50 mix-blend-multiply dark:mix-blend-screen"></div>
        </div>

        <div class="max-w-6xl mx-auto relative z-10">
            <div class="text-center mb-16 max-w-5xl mx-auto p-4">
                <h2 class="text-4xl md:text-5xl font-bold mb-4 font-serif text-gray-900 dark:text-gray-50 tracking-tight">
                    {{ locale === 'id' ? 'Jadwal Kegiatan' : 'Event Schedules' }}
                </h2>
                <div class="w-20 h-1 bg-gradient-to-r from-emerald-800 to-emerald-500 mx-auto rounded-full"></div>
                <span class="text-lg font-medium text-emerald-850 dark:text-white mt-4 block">
                    {{ locale === 'id' ? 'Ikuti berbagai jadwal kegiatan dan program kami.' : 'Join our upcoming events and programs.' }}
                </span>
            </div>

            <!-- Loading State -->
            <div v-if="pending" class="flex flex-col items-center justify-center py-20 gap-4">
                <UIcon name="i-heroicons-arrow-path" class="w-12 h-12 animate-spin text-emerald-600 dark:text-emerald-400" />
                <span class="text-sm font-medium tracking-wide text-emerald-700 dark:text-emerald-300">
                    {{ locale === 'id' ? 'Memuat jadwal...' : 'Loading schedules...' }}
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
                            {{ locale === 'id' ? 'Coba Lagi' : 'Try Again' }}
                        </UButton>
                    </template>
                </UAlert>
            </div>

            <!-- Empty State -->
            <div v-else-if="!schedules || schedules.length === 0" class="text-center py-12">
                <EmptyData
                    title="Jadwal"
                    :description="locale === 'id' ? 'Belum ada jadwal yang tersedia saat ini.' : 'No schedules available at the moment.'"
                    icon="i-lucide-calendar-x"
                />
            </div>

            <!-- Grid Data -->
            <div v-else>
                <div class="relative max-w-5xl mx-auto py-10">
                    <!-- Central Vertical Line -->
                    <div class="absolute left-[27px] md:left-1/2 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-100 via-emerald-300 to-emerald-100 dark:from-emerald-900/50 dark:via-emerald-700 dark:to-emerald-900/50 transform md:-translate-x-1/2 rounded-full"></div>

                    <div class="space-y-16">
                        <div v-for="(schedule, index) in schedules" :key="schedule.id" 
                             class="relative flex flex-col md:flex-row items-start md:items-center group">
                             
                            <!-- Timeline Dot & Icon -->
                            <div class="absolute left-0 md:left-1/2 w-14 h-14 bg-white dark:bg-emerald-950 rounded-full border-4 border-emerald-100 dark:border-emerald-800 shadow-md transform md:-translate-x-1/2 flex items-center justify-center z-10 group-hover:border-emerald-400 dark:group-hover:border-emerald-500 group-hover:scale-110 transition-all duration-500">
                                <UIcon name="i-lucide-calendar-clock" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>

                            <!-- Content Wrapper -->
                            <div :class="[
                                'w-full md:w-1/2 pl-20 md:pl-0',
                                index % 2 === 0 ? 'md:pr-16 md:text-right flex flex-col md:items-end' : 'md:pl-16 md:ml-auto flex flex-col md:items-start'
                            ]">
                                <!-- Card -->
                                <div class="w-full flex flex-col bg-white/70 dark:bg-emerald-950/40 backdrop-blur-md rounded-3xl p-7 border border-white/60 dark:border-emerald-800/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] hover:shadow-[0_15px_40px_rgb(16,185,129,0.1)] hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                                    
                                    <!-- Card Decorative Gradient -->
                                    <div :class="[
                                        'absolute top-0 w-40 h-40 bg-gradient-to-br from-emerald-100/60 to-transparent dark:from-emerald-800/40 -z-10',
                                        index % 2 === 0 ? 'right-0 rounded-bl-full' : 'left-0 rounded-br-full'
                                    ]"></div>

                                    <div :class="['inline-flex flex-col text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/50 px-4 py-2 rounded-xl border border-emerald-100 dark:border-emerald-800/50 shadow-sm backdrop-blur-sm mb-4', 
                                         index % 2 === 0 ? 'md:items-end items-start' : 'items-start'
                                    ]">
                                        <div class="flex items-center gap-1.5">
                                            <UIcon name="i-lucide-clock" class="w-4 h-4" />
                                            <span class="tracking-wide">{{ formatDate(schedule.start_date) }}</span>
                                        </div>
                                        <div v-if="formatDate(schedule.start_date) !== formatDate(schedule.end_date)" class="flex items-center gap-1.5 mt-1 opacity-80">
                                            <span class="ml-1 text-emerald-600/50 dark:text-emerald-400/50">-</span>
                                            <span class="tracking-wide">{{ formatDate(schedule.end_date) }}</span>
                                        </div>
                                    </div>
                                    
                                    <h3 class="text-2xl font-bold text-slate-900 dark:text-emerald-50 mb-3 leading-snug">
                                        {{ titleOf(schedule) }}
                                    </h3>
                                    
                                    <p class="text-slate-600 dark:text-emerald-100/80 text-sm leading-relaxed whitespace-pre-line">
                                        {{ descOf(schedule) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="schedules.length > 0" class="mt-16 flex justify-center">
                    <UButton
                        color="white"
                        variant="solid"
                        size="lg"
                        class="rounded-full px-8 py-3 text-sm font-semibold shadow-sm hover:shadow-md text-emerald-900 bg-white dark:bg-emerald-900 dark:text-emerald-50 border border-emerald-200 dark:border-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-800 transition-all duration-300"
                        to="/schedules"
                    >
                        {{ locale === 'id' ? 'Lihat Semua Jadwal' : 'View All Schedules' }}
                    </UButton>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useSchedules } from "~/composables/useSchedules";
import { useI18n } from "vue-i18n";

const { locale } = useI18n();
const { schedules, pending, error, refresh, titleOf, descOf } = useSchedules();

const formatDate = (dateStr: string) => {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;

    return new Intl.DateTimeFormat(locale.value === 'id' ? "id-ID" : "en-US", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    }).format(date);
};
</script>
