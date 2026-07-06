<template>
    <!-- Background solid pekat pada Light (emerald-800) dan Dark (emerald-950) -->
    <footer
        class="relative overflow-hidden bg-emerald-800 dark:bg-emerald-950 pt-10 pb-8 transition-colors duration-500"
    >
        <!-- LAYER ORNAMEN ISLAMI (Menggunakan SVG Pattern Geometris Islami yang Valid) -->
        <div
            class="absolute inset-0 opacity-[0.2] dark:opacity-[0.1] pointer-events-none mix-blend-overlay"
            style="
                background-image: url(&quot;data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cpath d='M30 0 L60 30 L30 60 L0 30 Z M30 10 L50 30 L30 50 L10 30 Z M30 20 L40 30 L30 40 L20 30 Z' fill='none' stroke='white' stroke-width='1'/%3E%3Cpath d='M0 0 L60 60 M60 0 L0 60' fill='none' stroke='white' stroke-width='0.5' stroke-dasharray='2,2'/%3E%3C/svg%3E&quot;);
                background-size: 60px 60px;
            "
        ></div>

        <!-- Radial glow untuk memberikan dimensi (tidak flat) -->
        <div
            class="absolute inset-0 pointer-events-none bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-emerald-600/40 via-transparent to-transparent dark:from-emerald-800/30"
        ></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8"
            >
                <!-- Column 1: Organization Bio -->
                <div class="space-y-5 lg:col-span-1">
                    <div>
                        <div class="flex items-center gap-4">
                            <!-- Glassmorphism Wrapper untuk Logo -->
                            <div
                                class="bg-white/10 dark:bg-emerald-900/40 backdrop-blur-md p-2.5 rounded-t-2xl rounded-b-lg shadow-sm border border-white/20 dark:border-emerald-800/30"
                            >
                                <img
                                    :src="logoUrl"
                                    alt="Logo YHIE"
                                    class="w-11 h-auto object-contain"
                                    @error="onLogoError"
                                />
                            </div>
                            <h3
                                class="font-serif font-bold text-amber-500 text-xl leading-tight"
                            >
                                {{ siteName }}
                            </h3>
                        </div>
                        <span
                            v-if="tagline"
                            class="block mt-3 text-[11px] uppercase tracking-[0.2em] text-emerald-300/80 font-bold"
                        >
                            {{ tagline }}
                        </span>
                    </div>
                    <p class="text-sm text-emerald-100/70 leading-relaxed">
                        {{ siteDescription }}
                    </p>
                    <p
                        v-if="aboutHistory"
                        class="text-xs text-emerald-100/50 leading-relaxed"
                    >
                        {{ aboutHistory }}
                    </p>
                </div>

                <!-- Column 2: Quick Navigation Links -->
                <div>
                    <h4
                        class="font-serif font-bold text-emerald-50 text-base mb-5 pb-3 border-b border-emerald-700/50 dark:border-emerald-800/60 flex items-center gap-2"
                    >
                        <!-- Variasi bentuk pembatas berbentuk jajaran genjang/segitiga khas motif anyaman islami -->
                        <span
                            class="w-2 h-4 bg-amber-500 skew-x-12 rounded-sm"
                        ></span>
                        {{ locale === "en" ? "Quick Links" : "Tautan Cepat" }}
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li
                            v-for="(item, index) in tm('nav.menu')"
                            :key="index"
                        >
                            <NuxtLink
                                :to="localePath(rt(item.path))"
                                class="text-emerald-100/70 hover:text-amber-400 transition-all duration-300 flex items-center gap-3 group"
                            >
                                <!-- Bullet list berbentuk bintang segi delapan (Rub El Hizb) mini menggunakan rotasi ganda -->
                                <span
                                    class="w-1.5 h-1.5 bg-emerald-900/60 group-hover:bg-amber-400 rotate-45 transition-all duration-300 shrink-0 border border-white/10 relative flex items-center justify-center"
                                >
                                    <span
                                        class="absolute w-1.5 h-1.5 bg-inherit border border-white/10 rotate-45 group-hover:bg-amber-400"
                                    ></span>
                                </span>
                                <span
                                    class="group-hover:translate-x-1 transition-transform duration-300 font-medium pl-1"
                                    >{{ rt(item.name) }}</span
                                >
                            </NuxtLink>
                        </li>
                        <li>
                            <NuxtLink
                                :to="localePath('/invoice/search')"
                                class="text-emerald-100/70 hover:text-amber-400 transition-all duration-300 flex items-center gap-3 group"
                            >
                                <span
                                    class="w-1.5 h-1.5 bg-emerald-900/60 group-hover:bg-amber-400 rotate-45 transition-all duration-300 shrink-0 border border-white/10 relative flex items-center justify-center"
                                >
                                    <span
                                        class="absolute w-1.5 h-1.5 bg-inherit border border-white/10 rotate-45 group-hover:bg-amber-400"
                                    ></span>
                                </span>
                                <span
                                    class="group-hover:translate-x-1 transition-transform duration-300 font-medium pl-1"
                                    >{{ t("invoice.searchCta") }}</span
                                >
                            </NuxtLink>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Contact & Legal -->
                <div>
                    <h4
                        class="font-serif font-bold text-emerald-50 text-base mb-5 pb-3 border-b border-emerald-700/50 dark:border-emerald-800/60 flex items-center gap-2"
                    >
                        <span
                            class="w-2 h-4 bg-amber-500 skew-x-12 rounded-sm"
                        ></span>
                        {{ locale === "en" ? "Get In Touch" : "Hubungi Kami" }}
                    </h4>
                    <ul class="space-y-3 text-sm">
                        <li>
                            <a
                                :href="`mailto:${contactEmail}`"
                                class="flex items-center gap-3 group text-emerald-100/70 hover:text-amber-400 transition-colors"
                            >
                                <!-- Glassmorphism Icon Mail -->
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 dark:bg-emerald-900/40 backdrop-blur-md border border-white/10 group-hover:bg-amber-400 group-hover:text-emerald-900 transition-all shrink-0 shadow-sm"
                                >
                                    <UIcon
                                        name="i-lucide-mail"
                                        class="w-4 h-4 text-amber-400 group-hover:text-emerald-900"
                                    />
                                </div>
                                <span class="break-all font-medium">{{
                                    contactEmail
                                }}</span>
                            </a>
                        </li>
                        <li>
                            <a
                                :href="`tel:${contactPhone}`"
                                class="flex items-center gap-3 group text-emerald-100/70 hover:text-amber-400 transition-colors"
                            >
                                <!-- Glassmorphism Icon Phone -->
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 dark:bg-emerald-900/40 backdrop-blur-md border border-white/10 group-hover:bg-amber-400 group-hover:text-emerald-900 transition-all shrink-0 shadow-sm"
                                >
                                    <UIcon
                                        name="i-lucide-phone"
                                        class="w-4 h-4 text-amber-400 group-hover:text-emerald-900"
                                    />
                                </div>
                                <span class="font-medium">{{
                                    contactPhone
                                }}</span>
                            </a>
                        </li>
                        <li>
                            <a
                                :href="waLink"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-3 group text-emerald-100/70 hover:text-amber-400 transition-colors"
                            >
                                <!-- Glassmorphism Icon WhatsApp -->
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 dark:bg-emerald-900/40 backdrop-blur-md border border-white/10 group-hover:bg-amber-400 group-hover:text-emerald-900 transition-all shrink-0 shadow-sm"
                                >
                                    <UIcon
                                        name="i-lucide-message-circle"
                                        class="w-4 h-4 text-amber-400 group-hover:text-emerald-900"
                                    />
                                </div>
                                <span class="font-medium">WhatsApp Kami</span>
                            </a>
                        </li>
                        <li v-if="instagramUrl">
                            <a
                                :href="instagramUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-3 group text-emerald-100/70 hover:text-amber-400 transition-colors"
                            >
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 dark:bg-emerald-900/40 backdrop-blur-md border border-white/10 group-hover:bg-amber-400 group-hover:text-emerald-900 transition-all shrink-0 shadow-sm"
                                >
                                    <UIcon
                                        name="i-lucide-instagram"
                                        class="w-4 h-4 text-amber-400 group-hover:text-emerald-900"
                                    />
                                </div>
                                <span class="font-medium">Instagram</span>
                            </a>
                        </li>
                        <li v-if="facebookUrl">
                            <a
                                :href="facebookUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-3 group text-emerald-100/70 hover:text-amber-400 transition-colors"
                            >
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 dark:bg-emerald-900/40 backdrop-blur-md border border-white/10 group-hover:bg-amber-400 group-hover:text-emerald-900 transition-all shrink-0 shadow-sm"
                                >
                                    <UIcon
                                        name="i-lucide-facebook"
                                        class="w-4 h-4 text-amber-400 group-hover:text-emerald-900"
                                    />
                                </div>
                                <span class="font-medium">Facebook</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Credentials/Address Info -->
                <div>
                    <h4
                        class="font-serif font-bold text-emerald-50 text-base mb-5 pb-3 border-b border-emerald-700/50 dark:border-emerald-800/60 flex items-center gap-2"
                    >
                        <span
                            class="w-2 h-4 bg-amber-500 skew-x-12 rounded-sm"
                        ></span>
                        {{
                            locale === "en" ? "Office Address" : "Alamat Kantor"
                        }}
                    </h4>
                    <div class="flex items-start gap-3">
                        <!-- Glassmorphism Icon Map Pin -->
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 dark:bg-emerald-900/40 backdrop-blur-md border border-white/10 shrink-0 shadow-sm"
                        >
                            <UIcon
                                name="i-lucide-map-pin"
                                class="w-4 h-4 text-amber-400"
                            />
                        </div>
                        <div>
                            <p
                                class="text-sm text-emerald-100/70 leading-relaxed pt-1 font-medium"
                            >
                                {{
                                    address ||
                                    (locale === "en"
                                        ? "Jakarta, Indonesia. Official accreditation from Kemenkumham RI and International Accreditation Organization (IAO)."
                                        : "Jakarta, Indonesia. Berizin resmi Kemenkumham RI & Terakreditasi International Accreditation Organization (IAO).")
                                }}
                            </p>
                            <p
                                v-if="operatingHours"
                                class="text-xs text-emerald-100/50 mt-1.5"
                            >
                                {{ operatingHours }}
                            </p>
                        </div>
                    </div>

                    <!-- Embed Google Maps -->
                    <div
                        v-if="mapEmbed"
                        class="mt-4 rounded-xl overflow-hidden border border-white/10 dark:border-emerald-800/30 [&_iframe]:w-full [&_iframe]:h-36 [&_iframe]:block"
                        v-html="mapEmbed"
                    ></div>

                    <!-- Badge Legalitas (dipindah dari kolom kiri) -->
                    <div
                        class="inline-flex items-center gap-3 mt-5 px-3 py-2 rounded-xl bg-white/10 dark:bg-emerald-950/40 backdrop-blur-md border border-white/10 dark:border-emerald-800/30 shadow-sm"
                    >
                        <span
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-900/60 text-amber-400 shrink-0 shadow-inner border border-white/10"
                        >
                            <UIcon name="i-lucide-shield-check" class="w-4 h-4" />
                        </span>
                        <span
                            class="text-xs text-emerald-50 font-semibold tracking-wide"
                        >
                            Kemenkumham RI Registered
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div
                class="mt-14 pt-6 border-t border-emerald-700/50 dark:border-emerald-800/60 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-emerald-300/70"
            >
                <p class="font-semibold">
                    &copy; {{ new Date().getFullYear() }} {{ siteName }}.
                    {{
                        locale === "en"
                            ? "All rights reserved."
                            : "Hak cipta dilindungi."
                    }}
                </p>
                <div class="flex items-center gap-6 font-semibold">
                    <span
                        class="flex items-center gap-1.5 hover:text-amber-400 transition-colors cursor-default"
                    >
                        <UIcon name="i-lucide-globe" class="w-4 h-4" />
                        {{
                            locale === "en"
                                ? "Accredited IAO"
                                : "Terakreditasi IAO"
                        }}
                    </span>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { useSettings } from "~/composables/useSettings";

const { t, locale, tm, rt } = useI18n();
const localePath = useLocalePath();

const {
    siteName,
    siteDescription,
    contactEmail,
    contactPhone,
    waLink,
    address,
    tagline,
    logoUrl,
    instagramUrl,
    facebookUrl,
    mapEmbed,
    operatingHours,
    getSettingValue,
} = useSettings();

// Sejarah singkat (ikut bahasa aktif) untuk ditampilkan ringkas di footer.
const aboutHistory = computed(() =>
    getSettingValue(`about_history_${locale.value}`),
);

// Fallback jika logo_url (URL eksternal) gagal dimuat.
const onLogoError = (e: Event) => {
    const img = e.target as HTMLImageElement;
    if (!img.dataset.fallback) {
        img.dataset.fallback = "1";
        img.src = "/logo.png";
    }
};
</script>
