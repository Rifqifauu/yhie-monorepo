<template>
    <section
        class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50 dark:from-emerald-950 dark:via-emerald-950 dark:to-amber-950/40 border-b border-emerald-100/70 dark:border-emerald-800/60"
    >
        <img
            src="/shadow-mosque.webp"
            class="absolute bottom-0 left-1/2 -translate-x-1/2 w-auto h-full max-h-[85%] object-contain object-bottom pointer-events-none opacity-20 dark:opacity-35 dark:invert dark:brightness-150 transition-all duration-300"
            alt=""
        />
        <div
            class="absolute inset-0 bg-gradient-to-br from-emerald-50/75 via-white/65 to-amber-50/55 dark:from-emerald-950/70 dark:via-emerald-950/65 dark:to-amber-950/40 transition-colors duration-300"
            aria-hidden="true"
        ></div>

        <div
            class="absolute -top-24 -right-24 w-80 h-80 bg-amber-400/20 rounded-full blur-3xl"
        ></div>
        <div
            class="absolute -bottom-28 -left-28 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl"
        ></div>

        <div
            class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-28 lg:pt-28 lg:pb-36"
        >
            <div class="text-center max-w-3xl mx-auto" data-aos="fade-up">
                <span
                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400"
                >
                    <UIcon name="i-lucide-handshake" class="w-4 h-4" />
                    {{ locale === 'en' ? 'Our Partners' : 'Mitra Kami' }}
                </span>
                <h1
                    class="mt-4 text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50"
                >
                    {{ t("partners.title") }}
                </h1>
                <p
                    class="mt-4 text-lg text-slate-600 dark:text-emerald-100/80 leading-relaxed"
                >
                    {{ locale === 'en'
                        ? 'We proudly collaborate with leading institutions and organizations committed to advancing Qur\'anic education with international standards.'
                        : 'Kami bangga berkolaborasi dengan berbagai institusi dan organisasi terkemuka yang berkomitmen memajukan pendidikan Al-Qur\'an berstandar internasional.'
                    }}
                </p>

                <!-- Trust Indicators -->
                <div class="mt-8 flex flex-wrap justify-center gap-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-emerald-100/70">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center">
                            <UIcon name="i-lucide-shield-check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="font-medium">{{ locale === 'en' ? 'Verified' : 'Terverifikasi' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-emerald-100/70">
                        <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                            <UIcon name="i-lucide-globe" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="font-medium">{{ locale === 'en' ? 'International' : 'Internasional' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-emerald-100/70">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center">
                            <UIcon name="i-lucide-award" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="font-medium">{{ locale === 'en' ? 'Accredited' : 'Terakreditasi' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
const { t, locale } = useI18n();
</script>
