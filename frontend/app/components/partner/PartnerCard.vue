<template>
    <div
        class="group relative overflow-hidden rounded-2xl border border-emerald-200/60 dark:border-emerald-800/50 bg-white/80 dark:bg-emerald-900/30 backdrop-blur-sm shadow-sm hover:shadow-xl hover:border-emerald-300 dark:hover:border-emerald-600 transition-all duration-300"
    >
        <div class="flex flex-col sm:flex-row items-stretch">
            <!-- Logo area -->
            <div
                class="sm:w-40 md:w-48 shrink-0 flex items-center justify-center p-6 bg-gradient-to-br from-emerald-50 via-white to-amber-50/30 dark:from-emerald-950/60 dark:via-emerald-900/40 dark:to-emerald-950/60 border-b sm:border-b-0 sm:border-r border-emerald-100/50 dark:border-emerald-800/40"
            >
                <NuxtImg
                    v-if="imageSrc"
                    :src="imageSrc"
                    :alt="name"
                    width="160"
                    height="120"
                    format="webp"
                    class="max-w-full max-h-24 object-contain transition-transform duration-500 group-hover:scale-110"
                />
                <div v-else class="flex items-center justify-center">
                    <UIcon
                        name="i-lucide-building-2"
                        class="w-12 h-12 text-emerald-300 dark:text-emerald-700"
                    />
                </div>
            </div>

            <!-- Text content -->
            <div class="flex-1 p-5 flex flex-col justify-center">
                <div class="flex items-center gap-2 mb-2">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100/80 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50"
                    >
                        <UIcon name="i-lucide-handshake" class="w-3 h-3" />
                        {{ locale === 'en' ? 'Partner' : 'Mitra' }}
                    </span>
                </div>
                <h3
                    class="text-lg font-serif font-bold text-slate-900 dark:text-emerald-50 line-clamp-1 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors duration-300"
                >
                    {{ name }}
                </h3>
                <p
                    class="mt-1.5 text-sm text-slate-600 dark:text-emerald-100/70 line-clamp-2 leading-relaxed"
                >
                    {{ description }}
                </p>
            </div>

            <!-- Arrow indicator -->
            <div class="hidden sm:flex items-center px-4">
                <UIcon
                    name="i-lucide-chevron-right"
                    class="w-5 h-5 text-emerald-300 dark:text-emerald-700 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-all duration-300 group-hover:translate-x-1"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const { locale } = useI18n();

defineProps<{
    imageSrc: string;
    name: string;
    description: string;
}>();
</script>
