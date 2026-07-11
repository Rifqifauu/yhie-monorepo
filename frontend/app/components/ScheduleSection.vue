<template>
    <section class="relative w-full mx-auto px-4 py-24 overflow-hidden transition-colors duration-500 bg-slate-50 dark:bg-emerald-950/80">
        <!-- Background Decorations -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <!-- Dynamic Grid Pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#10b981_1px,transparent_1px),linear-gradient(to_bottom,#10b981_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_80%,transparent_100%)] opacity-15 dark:opacity-[0.06]"></div>
            
            <!-- Glowing Blobs -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-96 h-96 bg-emerald-400/30 dark:bg-emerald-600/20 rounded-full blur-[100px] opacity-70 mix-blend-multiply dark:mix-blend-screen animate-pulse"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-80 h-80 bg-teal-400/30 dark:bg-teal-600/20 rounded-full blur-[100px] opacity-70 mix-blend-multiply dark:mix-blend-screen animate-pulse" style="animation-delay: 1.5s;"></div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16 max-w-5xl mx-auto p-4">
                <h2 data-aos="fade-up" class="text-4xl md:text-5xl font-bold mb-4 font-serif text-gray-900 dark:text-gray-50 tracking-tight">
                    {{ locale === 'id' ? 'Jadwal Kegiatan' : 'Event Schedules' }}
                </h2>
                <div data-aos="fade-up" data-aos-delay="50" class="w-20 h-1 bg-gradient-to-r from-emerald-800 to-emerald-500 mx-auto rounded-full"></div>
                <span data-aos="fade-up" data-aos-delay="100" class="text-lg font-medium text-emerald-850 dark:text-white mt-4 block">
                    {{ locale === 'id' ? 'Ikuti berbagai jadwal kegiatan dan program kami.' : 'Join our upcoming events and programs.' }}
                </span>
            </div>

            <!-- Loading State -->
            <div v-if="pending" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                <div class="lg:col-span-2 rounded-[2rem] p-8 md:p-12 border border-emerald-200 dark:border-white/10 min-h-[500px] flex flex-col justify-between">
                    <div class="space-y-4">
                        <USkeleton class="h-4 w-40 rounded" />
                        <USkeleton class="h-10 w-2/3 rounded" />
                    </div>
                    <div class="flex flex-wrap gap-4 md:gap-6">
                        <USkeleton v-for="n in 4" :key="n" class="w-20 h-24 md:w-24 md:h-28 rounded-2xl" />
                    </div>
                    <USkeleton class="h-24 w-full max-w-2xl rounded-2xl" />
                </div>
                <div class="lg:col-span-1 rounded-[2rem] p-6 border border-emerald-100 dark:border-emerald-800/60 space-y-4">
                    <USkeleton class="h-6 w-1/2 rounded" />
                    <USkeleton v-for="n in 3" :key="n" class="h-20 w-full rounded-2xl" />
                </div>
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

            <!-- Data State -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                <!-- Left Column: Highlight & Countdown -->
                <div data-aos="fade-right" class="lg:col-span-2 relative bg-emerald-50 dark:bg-emerald-950 rounded-[2rem] p-8 md:p-12 overflow-hidden shadow-[0_20px_50px_rgba(16,185,129,0.15)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-emerald-200 dark:border-white/10 group flex flex-col justify-between min-h-[500px]">
                    <!-- Dark background decorative elements -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/10 rounded-full blur-[80px] pointer-events-none"></div>
                    <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none"></div>

                    <div v-if="closestSchedule" class="relative z-10 flex flex-col h-full">
                        <div class="mb-10">
                            <span class="text-emerald-600 dark:text-slate-400 font-semibold tracking-wide uppercase mb-3 block text-sm">
                                {{ locale === 'id' ? "Menghitung mundur!" : "We're almost ready!" }}
                            </span>
                            <h3 class="text-4xl md:text-5xl font-bold text-emerald-950 dark:text-white leading-tight">
                                {{ locale === 'id' ? 'Menuju' : 'Launching in..' }}
                                <span class="relative whitespace-nowrap mt-2 block w-max max-w-full">
                                    <span class="relative z-10 break-words whitespace-normal block">{{ titleOf(closestSchedule) }}</span>
                                    <span class="absolute bottom-1 left-0 w-full h-3 bg-emerald-400/30 dark:bg-emerald-500/50 -z-10 rounded-full"></span>
                                </span>
                            </h3>
                        </div>

                        <!-- Countdown Timer Blocks -->
                        <div class="flex flex-wrap gap-4 md:gap-6 mb-auto">
                            <!-- Days -->
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-24 md:w-24 md:h-28 bg-emerald-900/5 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-emerald-900/10 dark:border-white/10 flex items-center justify-center shadow-inner relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-100/50 dark:from-white/10 to-transparent"></div>
                                    <span class="text-4xl md:text-6xl font-bold text-emerald-950 dark:text-white tracking-tighter relative z-10">{{ days }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 opacity-70 dark:opacity-80">
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                    <span class="text-emerald-900 dark:text-white text-xs font-bold uppercase tracking-widest">{{ locale === 'id' ? 'Hari' : 'Days' }}</span>
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                </div>
                            </div>
                            <!-- Hours -->
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-24 md:w-24 md:h-28 bg-emerald-900/5 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-emerald-900/10 dark:border-white/10 flex items-center justify-center shadow-inner relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-100/50 dark:from-white/10 to-transparent"></div>
                                    <span class="text-4xl md:text-6xl font-bold text-emerald-950 dark:text-white tracking-tighter relative z-10">{{ hours }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 opacity-70 dark:opacity-80">
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                    <span class="text-emerald-900 dark:text-white text-xs font-bold uppercase tracking-widest">{{ locale === 'id' ? 'Jam' : 'Hours' }}</span>
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                </div>
                            </div>
                            <!-- Minutes -->
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-24 md:w-24 md:h-28 bg-emerald-900/5 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-emerald-900/10 dark:border-white/10 flex items-center justify-center shadow-inner relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-100/50 dark:from-white/10 to-transparent"></div>
                                    <span class="text-4xl md:text-6xl font-bold text-emerald-950 dark:text-white tracking-tighter relative z-10">{{ minutes }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 opacity-70 dark:opacity-80">
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                    <span class="text-emerald-900 dark:text-white text-xs font-bold uppercase tracking-widest">{{ locale === 'id' ? 'Menit' : 'Minutes' }}</span>
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                </div>
                            </div>
                            <!-- Seconds -->
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-24 md:w-24 md:h-28 bg-emerald-900/5 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-emerald-900/10 dark:border-white/10 flex items-center justify-center shadow-inner relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-100/50 dark:from-white/10 to-transparent"></div>
                                    <span class="text-4xl md:text-6xl font-bold text-emerald-950 dark:text-white tracking-tighter relative z-10">{{ seconds }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-4 opacity-70 dark:opacity-80">
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                    <span class="text-emerald-900 dark:text-white text-xs font-bold uppercase tracking-widest">{{ locale === 'id' ? 'Detik' : 'Seconds' }}</span>
                                    <div class="h-px w-3 bg-emerald-900 dark:bg-white"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Detail Box -->
                        <div class="mt-12 bg-white/40 dark:bg-white/5 backdrop-blur-md border border-emerald-900/10 dark:border-white/10 rounded-2xl p-5 md:p-6 w-full max-w-2xl relative overflow-hidden group-hover:border-emerald-900/20 dark:group-hover:border-white/20 transition-colors">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 dark:bg-emerald-400 rounded-l-2xl"></div>
                            <div class="flex flex-col gap-3">
                                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider">
                                    <UIcon name="i-lucide-calendar-clock" class="w-4 h-4" />
                                    <span>{{ formatDate(closestSchedule.start_date) }}</span>
                                    <span v-if="formatDate(closestSchedule.start_date) !== formatDate(closestSchedule.end_date)"> - {{ formatDate(closestSchedule.end_date) }}</span>
                                </div>
                                <p class="text-emerald-800 dark:text-white/80 text-sm leading-relaxed line-clamp-2 md:line-clamp-3">
                                    {{ descOf(closestSchedule) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State for Countdown if no upcoming schedule -->
                    <div v-else class="flex flex-col items-center justify-center h-full text-center relative z-10 my-auto">
                        <div class="w-24 h-24 bg-emerald-900/5 dark:bg-white/5 rounded-full flex items-center justify-center mb-6 border border-emerald-900/10 dark:border-white/10 shadow-[0_0_50px_rgba(16,185,129,0.05)] dark:shadow-[0_0_50px_rgba(255,255,255,0.05)]">
                            <UIcon name="i-lucide-calendar-check" class="w-10 h-10 text-emerald-600 dark:text-white/40" />
                        </div>
                        <h3 class="text-3xl font-bold text-emerald-950 dark:text-white mb-3">
                            {{ locale === 'id' ? 'Nantikan Jadwal Selanjutnya' : 'To Be Announced' }}
                        </h3>
                        <p class="text-emerald-800/80 dark:text-slate-400 max-w-md mx-auto">
                            {{ locale === 'id' ? 'Belum ada jadwal terdekat saat ini. Pantau terus laman kami untuk program dan agenda selanjutnya.' : 'No upcoming schedules at the moment. Stay tuned for our next events.' }}
                        </p>
                    </div>
                </div>

                <!-- Right Column: Incoming Schedules -->
                <div data-aos="fade-left" data-aos-delay="100" class="lg:col-span-1 flex flex-col h-full">
                    <div class="bg-white dark:bg-emerald-950/40 rounded-[2rem] p-6 border border-emerald-100 dark:border-emerald-800/60 shadow-[0_10px_40px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.1)] flex flex-col h-full">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-emerald-100 dark:border-emerald-800/50">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-emerald-50">
                                {{ locale === 'id' ? 'Jadwal Lainnya' : 'Incoming Schedules' }}
                            </h3>
                            <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <UIcon name="i-lucide-list-collapse" class="w-4 h-4" />
                            </div>
                        </div>

                        <!-- Has other upcoming schedules -->
                        <div v-if="otherSchedules.length > 0" class="flex flex-col gap-4 flex-grow">
                            <div v-for="sched in otherSchedules" :key="sched.id" 
                                 class="group bg-slate-50 dark:bg-emerald-900/20 rounded-2xl p-4 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-700/60 transition-all duration-300">
                                <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mb-1.5 flex items-center gap-1.5 uppercase tracking-wide">
                                    <UIcon name="i-lucide-calendar" class="w-3 h-3" />
                                    {{ formatDate(sched.start_date) }}
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-emerald-50 text-base mb-1.5 leading-snug line-clamp-2 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors">
                                    {{ titleOf(sched) }}
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-emerald-100/60 line-clamp-2 leading-relaxed">
                                    {{ descOf(sched) }}
                                </p>
                            </div>
                        </div>

                        <!-- No other upcoming, but has past schedules -->
                        <div v-else-if="pastSchedules.length > 0" class="flex flex-col gap-4 flex-grow">
                            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                {{ locale === 'id' ? 'Jadwal Selesai' : 'Past Schedules' }}
                            </div>
                            <div v-for="sched in pastSchedules.slice(0, 3)" :key="'past-'+sched.id" 
                                 class="bg-slate-50/50 dark:bg-emerald-900/10 rounded-2xl p-4 border border-slate-100 dark:border-emerald-800/30 opacity-70 grayscale-[30%]">
                                <div class="text-[11px] font-bold text-slate-500 dark:text-emerald-500/70 mb-1.5 flex items-center gap-1.5 uppercase tracking-wide">
                                    <UIcon name="i-lucide-check-circle" class="w-3 h-3" />
                                    {{ formatDate(sched.start_date) }}
                                </div>
                                <h4 class="font-bold text-slate-700 dark:text-emerald-100/80 text-base mb-1.5 leading-snug line-clamp-2">
                                    {{ titleOf(sched) }}
                                </h4>
                            </div>
                        </div>

                        <!-- Absolutely no schedules -->
                        <div v-else class="flex flex-col items-center justify-center flex-grow py-10 opacity-60">
                            <UIcon name="i-lucide-ghost" class="w-10 h-10 text-emerald-300 mb-3" />
                            <p class="text-sm text-slate-500 text-center px-4">
                                {{ locale === 'id' ? 'Belum ada catatan jadwal.' : 'No schedules recorded.' }}
                            </p>
                        </div>

                        <div class="mt-8 pt-4 border-t border-emerald-100 dark:border-emerald-800/50">
                            <UButton
                                color="emerald"
                                variant="soft"
                                class="w-full justify-center rounded-xl py-2.5 font-medium"
                                trailing-icon="i-lucide-arrow-right"
                                :to="localePath('/schedules')"
                            >
                                {{ locale === 'id' ? 'Lihat Semua Jadwal' : 'View All Schedules' }}
                            </UButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useSchedules } from "~/composables/useSchedules";
import { useI18n } from "vue-i18n";
import { ref, computed, onMounted, onUnmounted } from "vue";

const { locale } = useI18n();
const localePath = useLocalePath();
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

// --- DATA LOGIC ---
const upcomingSchedules = computed(() => {
    const now = new Date().getTime();
    return schedules.value
        .filter(s => {
            if (!s.start_date) return false;
            return new Date(s.start_date).getTime() > now;
        })
        .sort((a, b) => new Date(a.start_date).getTime() - new Date(b.start_date).getTime());
});

const pastSchedules = computed(() => {
    const now = new Date().getTime();
    return schedules.value
        .filter(s => {
            if (!s.start_date) return false;
            return new Date(s.start_date).getTime() <= now;
        })
        .sort((a, b) => new Date(b.start_date).getTime() - new Date(a.start_date).getTime());
});

const closestSchedule = computed(() => upcomingSchedules.value.length > 0 ? upcomingSchedules.value[0] : null);
const otherSchedules = computed(() => upcomingSchedules.value.length > 1 ? upcomingSchedules.value.slice(1, 4) : []);

// --- COUNTDOWN LOGIC ---
const days = ref('00');
const hours = ref('00');
const minutes = ref('00');
const seconds = ref('00');
let timer: any = null;

const updateTimer = () => {
    if (!closestSchedule.value?.start_date) return;
    
    const target = new Date(closestSchedule.value.start_date).getTime();
    const now = new Date().getTime();
    const diff = target - now;
    
    if (diff <= 0) {
        days.value = '00';
        hours.value = '00';
        minutes.value = '00';
        seconds.value = '00';
        if (timer) clearInterval(timer);
        return;
    }
    
    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
    const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const m = Math.floor((diff / 1000 / 60) % 60);
    const s = Math.floor((diff / 1000) % 60);
    
    days.value = d.toString().padStart(2, '0');
    hours.value = h.toString().padStart(2, '0');
    minutes.value = m.toString().padStart(2, '0');
    seconds.value = s.toString().padStart(2, '0');
};

onMounted(() => {
    updateTimer();
    timer = setInterval(updateTimer, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>
