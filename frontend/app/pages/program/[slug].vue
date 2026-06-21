<template>
    <div
        class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen pb-20"
    >
        <section
            class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50 dark:from-emerald-950 dark:via-emerald-950 dark:to-amber-950/40 border-b border-emerald-100/70 dark:border-emerald-800/60 py-12 md:py-20"
        >
            <img
                src="/gunungslamet.png"
                class="absolute inset-0 w-full h-full object-cover pointer-events-none opacity-20 dark:opacity-15 dark:invert dark:brightness-120 transition-all duration-300"
                alt=""
            />
            <div
                class="absolute inset-0 bg-gradient-to-br from-emerald-50/75 via-white/65 to-amber-50/55 dark:from-emerald-950/85 dark:via-emerald-950/80 dark:to-amber-950/50 transition-colors duration-300"
                aria-hidden="true"
            ></div>

            <div
                class="absolute -top-24 -right-24 w-80 h-80 bg-amber-400/20 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-28 -left-28 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl"
            ></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <NuxtLink
                    :to="localePath('/program')"
                    class="inline-flex items-center gap-2 group text-sm font-semibold text-emerald-700 dark:text-emerald-300 hover:text-emerald-800 dark:hover:text-emerald-200 transition-colors mb-6"
                >
                    <UIcon
                        name="i-lucide-arrow-left"
                        class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1.5"
                    />
                    <span>{{
                        locale === "en"
                            ? "Back to Programs"
                            : "Kembali ke Program"
                    }}</span>
                </NuxtLink>

                <div v-if="pending" class="max-w-3xl">
                    <USkeleton class="h-4 w-28 rounded-full mb-3" />
                    <USkeleton class="h-10 md:h-14 w-3/4 rounded-2xl mb-4" />
                    <USkeleton class="h-6 w-1/2 rounded-full" />
                </div>

                <div v-else-if="program" class="max-w-4xl" data-aos="fade-up">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-[0.2em] bg-emerald-100/80 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 border border-emerald-200/50 dark:border-emerald-800/50"
                    >
                        Program Sertifikasi
                    </span>
                    <h1
                        class="mt-4 text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50 leading-tight"
                    >
                        {{ titleOf(program) }}
                    </h1>
                    <div
                        class="mt-6 flex flex-wrap gap-4 text-sm text-slate-600 dark:text-emerald-100/70"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <UIcon
                                name="i-lucide-award"
                                class="w-4.5 h-4.5 text-amber-500"
                            />
                            Sertifikasi IAO
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <UIcon
                                name="i-lucide-trees"
                                class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400"
                            />
                            Tadabbur Alam
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <UIcon
                                name="i-lucide-shield-check"
                                class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400"
                            />
                            Legalitas Resmi
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-12 lg:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="error" class="max-w-xl mx-auto py-12">
                    <UAlert
                        icon="i-heroicons-exclamation-triangle"
                        color="red"
                        variant="soft"
                        title="Program Tidak Ditemukan"
                        description="Halaman program yang Anda cari tidak ditemukan atau telah dipindahkan."
                    >
                        <template #actions>
                            <UButton
                                size="sm"
                                color="red"
                                variant="solid"
                                :to="localePath('/program')"
                            >
                                Kembali ke Program
                            </UButton>
                        </template>
                    </UAlert>
                </div>

                <div
                    v-else-if="pending"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8"
                >
                    <div class="lg:col-span-8 space-y-6">
                        <USkeleton class="aspect-[16/9] w-full rounded-3xl" />
                        <div class="space-y-3">
                            <USkeleton class="h-6 w-1/4 rounded-full" />
                            <USkeleton class="h-4 w-full rounded" />
                            <USkeleton class="h-4 w-full rounded" />
                            <USkeleton class="h-4 w-5/6 rounded" />
                        </div>
                    </div>
                    <div class="lg:col-span-4">
                        <USkeleton class="h-96 w-full rounded-3xl" />
                    </div>
                </div>

                <div
                    v-else-if="program"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start"
                >
                    <div
                        class="lg:col-span-7 xl:col-span-8 space-y-8"
                        data-aos="fade-right"
                    >
                        <div
                            class="relative overflow-hidden rounded-3xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/70 dark:bg-emerald-950/40 p-2 shadow-lg"
                        >
                            <div
                                class="relative aspect-[16/10] overflow-hidden rounded-2xl"
                            >
                                <NuxtImg
                                    v-if="program.image_path"
                                    :src="imageUrl(program.image_path)"
                                    :alt="titleOf(program)"
                                    width="960"
                                    height="600"
                                    format="webp"
                                    class="w-full h-full object-cover transition-transform duration-700 hover:scale-[1.03]"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/60"
                                >
                                    <UIcon
                                        name="i-lucide-image"
                                        class="w-16 h-16 text-emerald-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white/60 dark:bg-emerald-900/20 backdrop-blur-md rounded-3xl border border-emerald-200/50 dark:border-emerald-800/50 p-6 md:p-8 shadow-sm"
                        >
                            <h2
                                class="text-xl md:text-2xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50 mb-4"
                            >
                                {{
                                    locale === "en"
                                        ? "About this Program"
                                        : "Tentang Program Ini"
                                }}
                            </h2>
                            <div
                                class="prose prose-emerald dark:prose-invert max-w-none text-slate-700 dark:text-emerald-100/90 leading-relaxed space-y-4 whitespace-pre-line"
                            >
                                {{ descOf(program) }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="lg:col-span-5 xl:col-span-4 lg:sticky lg:top-24"
                        data-aos="fade-left"
                    >
                        <div
                            class="bg-white/80 dark:bg-emerald-900/40 backdrop-blur-xl border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl space-y-6"
                        >
                            <div>
                                <span
                                    class="text-xs font-semibold text-slate-500 dark:text-emerald-200/60 uppercase tracking-widest block mb-1"
                                >
                                    {{
                                        locale === "en"
                                            ? "Investment Price"
                                            : "Biaya Investasi"
                                    }}
                                </span>
                                <div class="flex items-baseline gap-2">
                                    <span
                                        class="text-3xl md:text-4xl font-serif font-extrabold text-emerald-800 dark:text-amber-400"
                                    >
                                        {{ formatPrice(priceOf(program)) }}
                                    </span>
                                </div>
                            </div>

                            <hr
                                class="border-emerald-200/40 dark:border-emerald-800/40"
                            />

                            <div class="space-y-3.5">
                                <h4
                                    class="text-sm font-bold text-slate-900 dark:text-white"
                                >
                                    {{
                                        locale === "en"
                                            ? "Program Features"
                                            : "Fitur Program"
                                    }}
                                </h4>
                                <ul
                                    class="space-y-2.5 text-sm text-slate-600 dark:text-emerald-100/80"
                                >
                                    <li class="flex items-start gap-2.5">
                                        <UIcon
                                            name="i-lucide-check-circle-2"
                                            class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"
                                        />
                                        <span>{{
                                            locale === "en"
                                                ? "Accredited by IAO (International)"
                                                : "Akreditasi IAO Internasional"
                                        }}</span>
                                    </li>
                                    <li class="flex items-start gap-2.5">
                                        <UIcon
                                            name="i-lucide-check-circle-2"
                                            class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"
                                        />
                                        <span>{{
                                            locale === "en"
                                                ? "Stress-free Exam Concept"
                                                : "Metode Ujian Bebas Stres"
                                        }}</span>
                                    </li>
                                    <li class="flex items-start gap-2.5">
                                        <UIcon
                                            name="i-lucide-check-circle-2"
                                            class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"
                                        />
                                        <span>{{
                                            locale === "en"
                                                ? "Outdoor Nature Activities"
                                                : "Tadabbur Alam Terintegrasi"
                                        }}</span>
                                    </li>
                                    <li class="flex items-start gap-2.5">
                                        <UIcon
                                            name="i-lucide-check-circle-2"
                                            class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"
                                        />
                                        <span>{{
                                            locale === "en"
                                                ? "Official Certification"
                                                : "Sertifikat Kelulusan Resmi"
                                        }}</span>
                                    </li>
                                </ul>
                            </div>

                            <hr
                                class="border-emerald-200/40 dark:border-emerald-800/40"
                            />

                            <div
                                v-if="
                                    priceOf(program) && priceOf(program) !== 0
                                "
                                class="space-y-3 p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-900/60 text-sm"
                            >
                                <h4
                                    class="font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-2"
                                >
                                    <UIcon
                                        name="i-lucide-credit-card"
                                        class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400"
                                    />
                                    <span>{{
                                        locale === "en"
                                            ? "Payment Transfer Info"
                                            : "Informasi Rekening Resmi"
                                    }}</span>
                                </h4>
                                <div
                                    class="text-slate-700 dark:text-emerald-100/90 space-y-1 bg-white/60 dark:bg-emerald-900/40 p-3 rounded-xl border border-emerald-200/30"
                                >
                                    <div
                                        class="flex justify-between font-semibold"
                                    >
                                        <span
                                            >Bank Syariah Indonesia (BSI)</span
                                        >
                                    </div>
                                    <div
                                        class="flex justify-between items-center gap-2 mt-1"
                                    >
                                        <span
                                            class="font-mono text-base tracking-wider text-emerald-950 dark:text-white font-bold"
                                            >7123456789</span
                                        >
                                        <button
                                            @click="
                                                copyToClipboard(
                                                    '7123456789',
                                                    'account',
                                                )
                                            "
                                            class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 font-sans"
                                        >
                                            <UIcon
                                                :name="
                                                    accountCopied
                                                        ? 'i-lucide-check'
                                                        : 'i-lucide-copy'
                                                "
                                                class="w-3.5 h-3.5"
                                            />
                                            <span>{{
                                                accountCopied
                                                    ? locale === "en"
                                                        ? "Copied"
                                                        : "Tersalin"
                                                    : locale === "en"
                                                      ? "Copy"
                                                      : "Salin"
                                            }}</span>
                                        </button>
                                    </div>
                                    <p
                                        class="text-xs text-slate-500 dark:text-emerald-200/50 mt-1"
                                    >
                                        a.n. Yayasan Hafiz Indonesia Emas
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <button
                                    @click="isRegistrationModalOpen = true"
                                    class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-bold shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5 flex justify-center items-center gap-2"
                                >
                                    <UIcon
                                        name="i-lucide-pen-line"
                                        class="w-5 h-5"
                                    />
                                    <span>{{
                                        locale === "en"
                                            ? "Register Now"
                                            : "Daftar Sekarang"
                                    }}</span>
                                </button>

                                <button
                                    @click="copyShareLink"
                                    class="w-full py-3.5 px-6 rounded-xl border border-emerald-300 dark:border-emerald-700 bg-white/40 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 font-semibold transition-all duration-300 flex justify-center items-center gap-2"
                                >
                                    <UIcon
                                        :name="
                                            copied
                                                ? 'i-lucide-check'
                                                : 'i-lucide-share-2'
                                        "
                                        class="w-4.5 h-4.5"
                                    />
                                    <span>{{
                                        copied
                                            ? locale === "en"
                                                ? "Copied!"
                                                : "Berhasil Disalin!"
                                            : locale === "en"
                                              ? "Share Program"
                                              : "Bagikan Program"
                                    }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <ProgramRegistrationModal
            v-if="program"
            v-model="isRegistrationModalOpen"
            :program-id="program.id"
            :program-title="titleOf(program)"
            :program-price="formatPrice(priceOf(program))"
        />
    </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig();
const route = useRoute();
const { locale, t } = useI18n();
const localePath = useLocalePath();
const client = useSanctumClient();

const slug = route.params.slug as string;
const copied = ref(false);
const accountCopied = ref(false);
const isRegistrationModalOpen = ref(false);

const {
    data: apiResponse,
    pending,
    error,
} = await useAsyncData(`program-${slug}`, () =>
    client(`/api/programs/${slug}`, {
        params: {
            lang: locale.value,
        },
    }),
);

const program = computed(() => (apiResponse.value as any)?.data);

const titleOf = (p: any) => (locale.value === "en" ? p.title_en : p.title_id);
const descOf = (p: any) =>
    locale.value === "en" ? p.description_en : p.description_id;
const priceOf = (p: any) => (locale.value === "en" ? p.price_en : p.price_id);

const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

const imageUrl = (path?: string) => {
    if (!path) return "";
    if (path.startsWith("http")) return path;
    return `${backendUrl}${path.startsWith("/") ? path : `/${path}`}`;
};

const formatPrice = (price?: number | string) => {
    if (price === null || price === undefined) return "Gratis";
    const numericPrice = typeof price === "string" ? parseFloat(price) : price;
    if (isNaN(numericPrice)) return String(price);
    if (numericPrice === 0) return locale.value === "en" ? "Free" : "Gratis";
    const currency = locale.value === "en" ? "USD" : "IDR";
    const localeTag = locale.value === "en" ? "en-US" : "id-ID";
    return new Intl.NumberFormat(localeTag, {
        style: "currency",
        currency,
        maximumFractionDigits: 0,
    }).format(numericPrice);
};

const copyShareLink = () => {
    if (import.meta.client) {
        navigator.clipboard.writeText(window.location.href);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    }
};

const copyToClipboard = (text: string, type: "share" | "account") => {
    if (import.meta.client) {
        navigator.clipboard.writeText(text);
        if (type === "account") {
            accountCopied.value = true;
            setTimeout(() => {
                accountCopied.value = false;
            }, 2000);
        }
    }
};

useSeoMeta({
    title: () =>
        program.value
            ? `${titleOf(program.value)} - YHIE`
            : "Program Detail - YHIE",
    description: () =>
        program.value
            ? descOf(program.value).substring(0, 160)
            : "Detail program Yayasan Hafiz Indonesia Emas.",
    ogTitle: () =>
        program.value
            ? `${titleOf(program.value)} - YHIE`
            : "Program Detail - YHIE",
    ogDescription: () =>
        program.value
            ? descOf(program.value).substring(0, 160)
            : "Detail program Yayasan Hafiz Indonesia Emas.",
});
</script>
