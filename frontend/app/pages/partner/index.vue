<template>
    <div
        class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen"
    >
        <PartnerHero />

        <section class="py-14 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Loading -->
                <div
                    v-if="pending"
                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="n in 8"
                        :key="n"
                        class="flex flex-col items-center p-5 rounded-2xl border border-emerald-100 dark:border-emerald-900/30 bg-white/40 dark:bg-emerald-900/5"
                    >
                        <USkeleton
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl"
                        />
                        <USkeleton class="h-4 w-3/4 mt-4" />
                        <USkeleton class="h-3 w-5/6 mt-2" />
                        <USkeleton class="h-3 w-2/3 mt-1" />
                    </div>
                </div>

                <!-- Error -->
                <div v-else-if="error" class="max-w-xl mx-auto">
                    <UAlert
                        icon="i-heroicons-exclamation-triangle"
                        color="red"
                        variant="soft"
                        :title="
                            locale === 'en'
                                ? 'Failed to load partners'
                                : 'Gagal memuat data partner'
                        "
                        :description="
                            locale === 'en'
                                ? 'Please try again later.'
                                : 'Silakan coba lagi beberapa saat.'
                        "
                    >
                        <template #actions>
                            <UButton size="sm" color="red" @click="refresh">
                                {{ locale === "en" ? "Retry" : "Coba lagi" }}
                            </UButton>
                        </template>
                    </UAlert>
                </div>

                <!-- Empty -->
                <div v-else-if="!partners.length" class="text-center py-16">
                    <EmptyData
                        title="Partner"
                        description="Belum ada data mitra."
                    />
                </div>

                <!-- Multi-Column Grid of Partners -->
                <div
                    v-else
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="partner in partners"
                        :key="partner.id"
                        @click="openModal(partner)"
                        class="cursor-pointer flex flex-col items-center text-center p-5 rounded-2xl bg-white/50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/30 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-700"
                    >
                        <!-- Logo -->
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white dark:bg-emerald-900/20 p-2 flex items-center justify-center border border-emerald-50 dark:border-emerald-900/20 shrink-0"
                        >
                            <NuxtImg
                                v-if="partner.logo"
                                :src="imageUrl(partner.logo)"
                                :alt="nameOf(partner)"
                                class="max-w-full max-h-full object-contain"
                            />
                            <UIcon
                                v-else
                                name="i-lucide-building-2"
                                class="w-8 h-8 text-emerald-600 dark:text-emerald-400"
                            />
                        </div>

                        <!-- Name & Desc -->
                        <h3
                            class="mt-4 font-serif font-bold text-sm sm:text-base text-emerald-950 dark:text-emerald-50 line-clamp-1"
                        >
                            {{ nameOf(partner) }}
                        </h3>
                        <p
                            class="mt-2 text-xs text-slate-500 dark:text-emerald-100/60 line-clamp-3 leading-relaxed"
                        >
                            {{ descOf(partner) }}
                        </p>
                    </div>
                </div>

                <!-- Partner Detail Modal -->
                <UModal v-model:open="isModalOpen">
                    <template #content>
                        <UCard v-if="selectedPartner" class="!bg-white dark:!bg-emerald-950 ring-1 !ring-emerald-100 dark:!ring-emerald-900 divide-y !divide-emerald-100 dark:!divide-emerald-800/50">
                            <template #header>
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-serif font-bold text-emerald-950 dark:text-emerald-50">
                                        {{ nameOf(selectedPartner) }}
                                    </h3>
                                    <UButton color="gray" variant="ghost" icon="i-heroicons-x-mark-20-solid" class="-my-1" @click="isModalOpen = false" />
                                </div>
                            </template>

                            <div class="flex flex-col items-center p-4">
                                <div class="w-32 h-32 rounded-2xl bg-white dark:bg-emerald-900/20 p-2 flex items-center justify-center border border-emerald-50 dark:border-emerald-900/20 mb-6 shrink-0">
                                    <NuxtImg
                                        v-if="selectedPartner.logo"
                                        :src="imageUrl(selectedPartner.logo)"
                                        :alt="nameOf(selectedPartner)"
                                        class="max-w-full max-h-full object-contain"
                                    />
                                    <UIcon
                                        v-else
                                        name="i-lucide-building-2"
                                        class="w-12 h-12 text-emerald-600 dark:text-emerald-400"
                                    />
                                </div>
                                <p class="text-sm text-slate-600 dark:text-emerald-100/80 leading-relaxed text-center whitespace-pre-line">
                                    {{ descOf(selectedPartner) }}
                                </p>
                            </div>
                        </UCard>
                    </template>
                </UModal>

                <!-- Pagination -->
                <ProgramPagination
                    v-if="totalPages > 1"
                    :page="page"
                    :total-pages="totalPages"
                    :page-items="pageItems"
                    class="mt-12"
                    @change-page="changePage"
                />
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { usePartners } from "~/composables/usePartners";

const { locale } = useI18n();

const {
    page,
    partners,
    totalPages,
    pending,
    error,
    pageItems,
    nameOf,
    descOf,
    imageUrl,
    changePage,
    refresh,
} = usePartners();

const isModalOpen = ref(false);
const selectedPartner = ref<any>(null);

const openModal = (partner: any) => {
    selectedPartner.value = partner;
    isModalOpen.value = true;
};

useSeoMeta({
    title: () => (locale.value === "en" ? "Partners - YHIE" : "Partner - YHIE"),
    description: () =>
        locale.value === "en"
            ? "Trusted partners collaborating with Yayasan Hafiz Indonesia Emas."
            : "Daftar mitra terpercaya yang berkolaborasi dengan Yayasan Hafiz Indonesia Emas.",
    ogTitle: () =>
        locale.value === "en" ? "Partners - YHIE" : "Partner - YHIE",
    ogDescription: () =>
        locale.value === "en"
            ? "Discover our trusted partners in advancing Qur'anic education."
            : "Temukan mitra terpercaya kami dalam memajukan pendidikan Al-Qur'an.",
});
</script>
