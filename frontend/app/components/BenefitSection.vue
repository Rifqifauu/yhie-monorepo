<template>
    <section
        class="py-20 lg:py-28 border-t border-emerald-900 dark:border-emerald-200 bg-gradient-to-b from-white to-emerald-800/30 dark:from-emerald-900 dark:to-gray-900 transition-colors duration-500 overflow-hidden relative"
    >
        <UIcon
            v-for="astroid in astroids"
            :key="astroid.id"
            name="i-lucide-astroid"
            class="absolute text-amber-500 dark:text-amber-400 text-2xl pointer-events-none drop-shadow-[0_2px_8px_rgba(245,158,11,0.3)]"
            :style="{
                top: astroid.top,
                left: astroid.left,
            }"
        />

        <div
            class="absolute top-0 left-1/4 w-96 h-96 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-3xl pointer-events-none"
        ></div>
        <div
            class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"
        ></div>

        <div
            class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl relative z-10"
        >
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2
                    data-aos="fade-up"
                    class="text-3xl md:text-5xl font-serif font-bold text-gray-900 dark:text-white mb-6 tracking-tight leading-tight"
                >
                    {{ t("benefits.title") }}
                </h2>
                <div class="flex items-center justify-center gap-1.5 mb-6">
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                    <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></div>
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                </div>
                <p
                    data-aos="fade-up"
                    data-aos-delay="50"
                    class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed max-w-2xl mx-auto"
                >
                    {{ t("benefits.description") }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3 relative z-20">
                <UCard
                    v-for="(item, index) in tm('benefits.items')"
                    :key="index"
                    data-aos="fade-up"
                    :data-aos-delay="index * 100"
                    class="group transition-all rounded-4xl duration-500 hover:-translate-y-3 bg-white/40 dark:bg-emerald-950/20 shadow-xl border border-white/40 dark:border-emerald-700/30 backdrop-blur-md"
                >
                    <div
                        class="absolute top-0 right-0 -mt-6 -mr-6 w-36 h-36 bg-gradient-to-br from-emerald-400/20 to-amber-400/20 dark:from-emerald-500/10 dark:to-amber-500/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                    ></div>

                    <div
                        class="relative w-16 h-16 mb-8 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/60 dark:from-emerald-950/40 dark:to-emerald-900/20 flex items-center justify-center border border-emerald-100/80 dark:border-emerald-800/30 group-hover:shadow-xl group-hover:shadow-emerald-500/10 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500"
                    >
                        <UIcon
                            :name="getIcon(index)"
                            class="w-8 h-8 text-emerald-600 dark:text-emerald-400 group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors duration-500"
                        />
                    </div>

                    <div class="relative z-10">
                        <h3
                            class="text-2xl font-bold text-gray-900 dark:text-white mb-3 font-serif group-hover:text-emerald-700 dark:group-hover:text-amber-400 transition-colors duration-300 tracking-tight"
                        >
                            {{ rt(item.title) }}
                        </h3>

                        <div
                            class="w-8 h-[2px] bg-amber-500/50 group-hover:w-16 group-hover:bg-emerald-500 transition-all duration-500 mb-4"
                        ></div>

                        <p
                            class="text-base text-gray-600 dark:text-gray-200 leading-relaxed"
                        >
                            {{ rt(item.description) }}
                        </p>
                    </div>
                </UCard>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
const { t, tm, rt } = useI18n();

// Koordinat yang sudah dipetakan di sela-sela kosong area section
// 3 di area atas (dekat judul), 4 di area tengah sela kartu, 3 di area bawah
const astroids = [
    { id: 1, top: "8%", left: "12%" },
    { id: 2, top: "5%", left: "82%" },
    { id: 3, top: "22%", left: "72%" },

    { id: 4, top: "45%", left: "4%" },
    { id: 5, top: "50%", left: "34%" },
    { id: 6, top: "48%", left: "65%" },
    { id: 7, top: "55%", left: "92%" },

    { id: 8, top: "85%", left: "15%" },
    { id: 9, top: "90%", left: "48%" },
    { id: 10, top: "82%", left: "80%" },
];

const getIcon = (index: number) => {
    const icons = [
        "i-heroicons-sparkles-solid",
        "i-heroicons-globe-alt-solid",
        "i-heroicons-user-group-solid",
    ];
    return icons[index] || "i-heroicons-check-badge-solid";
};
</script>
