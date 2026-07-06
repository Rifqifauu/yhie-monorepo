<template>
    <div
        class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen py-16"
    >
        <div class="max-w-2xl mx-auto px-4 sm:px-6 space-y-6">
            <div class="text-center">
                <h1
                    class="text-2xl md:text-3xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50"
                >
                    {{ t("invoice.searchTitle") }}
                </h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-emerald-200/60">
                    {{ t("invoice.searchDesc") }}
                </p>
            </div>

            <form
                @submit.prevent="handleSearch"
                class="bg-white/80 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl space-y-4"
            >
                <div>
                    <label
                        class="block text-sm font-medium text-slate-600 dark:text-emerald-100/70 mb-1.5"
                    >
                        {{ t("invoice.searchEmailLabel") }}
                    </label>
                    <input
                        v-model="email"
                        type="email"
                        required
                        class="w-full rounded-xl border border-emerald-200 dark:border-emerald-800/60 bg-white dark:bg-emerald-950/40 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    />
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-slate-600 dark:text-emerald-100/70 mb-1.5"
                    >
                        {{ t("invoice.searchPhoneLabel") }}
                    </label>
                    <input
                        v-model="phone"
                        type="tel"
                        required
                        class="w-full rounded-xl border border-emerald-200 dark:border-emerald-800/60 bg-white dark:bg-emerald-950/40 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="isSearching"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold shadow-lg transition-all duration-300"
                >
                    {{ isSearching ? t("invoice.searching") : t("invoice.searchButton") }}
                </button>
            </form>

            <div v-if="searchError" class="text-sm text-red-500 text-center">
                {{ searchError }}
            </div>

            <div v-if="hasSearched && !isSearching">
                <div
                    v-if="results.length === 0"
                    class="text-center text-sm text-slate-500 dark:text-emerald-200/60 py-6"
                >
                    {{ t("invoice.searchNoResults") }}
                </div>

                <div v-else class="space-y-3">
                    <h2
                        class="text-sm font-semibold text-slate-600 dark:text-emerald-100/70"
                    >
                        {{ t("invoice.searchResultsTitle") }}
                    </h2>
                    <NuxtLink
                        v-for="item in results"
                        :key="item.reference_id"
                        :to="localePath(`/invoice/${item.reference_id}`)"
                        class="block bg-white/80 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60 rounded-2xl p-4 hover:border-emerald-400 dark:hover:border-emerald-600 transition-colors"
                    >
                        <div class="flex justify-between items-center gap-3">
                            <div class="min-w-0">
                                <p
                                    class="font-mono text-xs text-emerald-700 dark:text-emerald-300"
                                >
                                    {{ item.reference_id }}
                                </p>
                                <p class="font-semibold text-sm line-clamp-1">
                                    {{ programTitle(item) }}
                                </p>
                            </div>
                            <UBadge
                                :color="statusColor(item.payment_status)"
                                variant="subtle"
                                class="shrink-0"
                            >
                                {{ t(`invoice.status${statusLabel(item.payment_status)}`) }}
                            </UBadge>
                        </div>
                    </NuxtLink>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const { t, locale } = useI18n();
const localePath = useLocalePath();
const { results, isSearching, searchError, hasSearched, search } =
    useInvoiceSearch();

const email = ref("");
const phone = ref("");

const handleSearch = () => {
    if (!email.value || !phone.value) return;
    search(email.value, phone.value);
};

const programTitle = (item: any) => {
    const program = item.program_registration?.program;
    if (!program) return "-";
    return locale.value === "en" ? program.title_en : program.title_id;
};

const statusLabel = (status: string) =>
    status.charAt(0).toUpperCase() + status.slice(1);

type BadgeColor = "warning" | "success" | "error" | "neutral";

const statusColor = (status: string): BadgeColor => {
    const colorMap: Record<string, BadgeColor> = {
        pending: "warning",
        completed: "success",
        failed: "error",
        expired: "neutral",
    };
    return colorMap[status] || "neutral";
};

useSeoMeta({
    title: () => `${t("invoice.searchTitle")} - YHIE`,
});
</script>
