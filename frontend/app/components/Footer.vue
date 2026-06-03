<template>
    <footer class="relative overflow-hidden bg-slate-50 dark:bg-emerald-950/80 border-t border-emerald-100/70 dark:border-emerald-900/60 pt-16 pb-8 transition-colors duration-300">
        <!-- Background Mosque Silhouette -->
        <img
            src="/shadow-mosque.webp"
            class="absolute bottom-0 right-0 w-[400px] pointer-events-none opacity-[0.06] dark:opacity-[0.03] dark:invert dark:brightness-110"
            alt=""
        />

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <!-- Column 1: Organization Bio -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <NuxtImg
                            src="/logo.png"
                            alt="Logo YHIE"
                            class="w-14 h-auto object-contain"
                        />
                        <div>
                            <h3 class="font-serif font-bold text-emerald-950 dark:text-amber-400 text-lg leading-tight">
                                {{ siteName }}
                            </h3>
                            <span class="text-xs uppercase tracking-widest text-emerald-600 dark:text-emerald-300/80 font-bold">
                                Foundation
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-emerald-100/70 leading-relaxed">
                        {{ siteDescription }}
                    </p>
                </div>

                <!-- Column 2: Quick Navigation Links -->
                <div>
                    <h4 class="font-serif font-bold text-emerald-950 dark:text-emerald-50 text-base mb-4 pb-2 border-b border-emerald-100 dark:border-emerald-900/40">
                        {{ locale === 'en' ? 'Quick Links' : 'Tautan Cepat' }}
                    </h4>
                    <ul class="space-y-2.5 text-sm">
                        <li v-for="(item, index) in tm('nav.menu')" :key="index">
                            <NuxtLink
                                :to="localePath(rt(item.path))"
                                class="text-slate-600 dark:text-emerald-100/70 hover:text-emerald-600 dark:hover:text-amber-400 transition-colors flex items-center gap-2 group"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400/50 group-hover:bg-amber-400 transition-colors"></span>
                                {{ rt(item.name) }}
                            </NuxtLink>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Contact & Legal -->
                <div>
                    <h4 class="font-serif font-bold text-emerald-950 dark:text-emerald-50 text-base mb-4 pb-2 border-b border-emerald-100 dark:border-emerald-900/40">
                        {{ locale === 'en' ? 'Get In Touch' : 'Hubungi Kami' }}
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3">
                            <UIcon name="i-lucide-mail" class="w-5 h-5 text-emerald-600 dark:text-amber-400 shrink-0 mt-0.5" />
                            <a :href="`mailto:${contactEmail}`" class="text-slate-600 dark:text-emerald-100/70 hover:underline">
                                {{ contactEmail }}
                            </a>
                        </li>
                        <li class="flex items-start gap-3">
                            <UIcon name="i-lucide-phone" class="w-5 h-5 text-emerald-600 dark:text-amber-400 shrink-0 mt-0.5" />
                            <a :href="`tel:${contactPhone}`" class="text-slate-600 dark:text-emerald-100/70 hover:underline">
                                {{ contactPhone }}
                            </a>
                        </li>
                        <li class="flex items-start gap-3">
                            <UIcon name="i-lucide-message-circle" class="w-5 h-5 text-emerald-600 dark:text-amber-400 shrink-0 mt-0.5" />
                            <a :href="waLink" target="_blank" rel="noopener noreferrer" class="text-slate-600 dark:text-emerald-100/70 hover:underline">
                                WhatsApp Chat
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Credentials/Address Info -->
                <div>
                    <h4 class="font-serif font-bold text-emerald-950 dark:text-emerald-50 text-base mb-4 pb-2 border-b border-emerald-100 dark:border-emerald-900/40">
                        {{ locale === 'en' ? 'Office Address' : 'Alamat Kantor' }}
                    </h4>
                    <p class="text-sm text-slate-600 dark:text-emerald-100/70 leading-relaxed mb-4">
                        {{ locale === 'en'
                            ? 'Jakarta, Indonesia. Official accreditation from Kemenkumham RI and International Accreditation Organization (IAO).'
                            : 'Jakarta, Indonesia. Berizin resmi Kemenkumham RI & Terakreditasi International Accreditation Organization (IAO).'
                        }}
                    </p>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                            <UIcon name="i-lucide-shield-check" class="w-5 h-5" />
                        </span>
                        <span class="text-xs text-slate-500 dark:text-emerald-100/50 font-medium">
                            Kemenkumham RI Registered
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="mt-12 pt-8 border-t border-emerald-100 dark:border-emerald-900/40 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500 dark:text-emerald-100/50">
                <p>&copy; {{ new Date().getFullYear() }} {{ siteName }}. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1.5">
                        <UIcon name="i-lucide-globe" class="w-3.5 h-3.5" />
                        {{ locale === 'en' ? 'Accredited IAO' : 'Terakreditasi IAO' }}
                    </span>
                    <span>Designed for Excellence</span>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { useSettings } from "~/composables/useSettings";

const { locale, tm, rt } = useI18n();
const localePath = useLocalePath();

const { siteName, siteDescription, contactEmail, contactPhone, waLink } = useSettings();
</script>
