<template>
    <div
        class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white min-h-screen py-16"
    >
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div v-if="pending" class="space-y-4">
                <USkeleton class="h-8 w-1/2 rounded-lg mx-auto" />
                <USkeleton class="h-64 w-full rounded-3xl" />
            </div>

            <UAlert
                v-else-if="!transaction"
                icon="i-lucide-file-x"
                color="red"
                variant="soft"
                :title="t('invoice.notFoundTitle')"
                :description="t('invoice.notFoundDesc')"
            >
                <template #actions>
                    <UButton
                        size="sm"
                        color="red"
                        variant="solid"
                        :to="localePath('/')"
                    >
                        {{ t("invoice.backHome") }}
                    </UButton>
                    <UButton
                        size="sm"
                        color="red"
                        variant="soft"
                        :to="localePath('/invoice/search')"
                    >
                        {{ t("invoice.searchCta") }}
                    </UButton>
                </template>
            </UAlert>

            <div v-else class="space-y-6">
                <div class="text-center">
                    <h1
                        class="text-2xl md:text-3xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50"
                    >
                        {{ t("invoice.title") }}
                    </h1>
                    <p
                        class="mt-1 font-mono text-sm text-emerald-700 dark:text-emerald-300"
                    >
                        {{ transaction.reference_id }}
                    </p>
                </div>

                <div
                    class="bg-white/80 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl space-y-4"
                >
                    <div class="flex justify-between text-sm">
                        <span
                            class="text-slate-500 dark:text-emerald-200/60"
                            >{{ t("invoice.registrant") }}</span
                        >
                        <span class="font-semibold">{{
                            transaction.program_registration?.full_name || "-"
                        }}</span>
                    </div>
                    <div class="flex justify-between text-sm gap-4">
                        <span
                            class="text-slate-500 dark:text-emerald-200/60 shrink-0"
                            >{{ t("invoice.program") }}</span
                        >
                        <span class="font-semibold text-right">{{
                            programTitle
                        }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span
                            class="text-slate-500 dark:text-emerald-200/60"
                            >{{ t("invoice.amount") }}</span
                        >
                        <span
                            class="font-bold text-emerald-800 dark:text-amber-400"
                            >{{ formatIDR(transaction.amount) }}</span
                        >
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span
                            class="text-slate-500 dark:text-emerald-200/60"
                            >{{ t("invoice.status") }}</span
                        >
                        <UBadge :color="statusColor" variant="subtle">{{
                            t(`invoice.status${statusLabel}`)
                        }}</UBadge>
                    </div>
                </div>

                <div
                    v-if="transaction.payment_status === 'pending'"
                    class="bg-emerald-50/60 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-900/60 rounded-3xl p-6 space-y-3 text-sm"
                >
                    <h3
                        class="font-bold text-emerald-900 dark:text-emerald-300"
                    >
                        {{ t("invoice.transferInfo") }}
                    </h3>
                    <div
                        class="bg-white/60 dark:bg-emerald-900/40 p-3 rounded-xl border border-emerald-200/30 space-y-1"
                    >
                        <p class="font-semibold">
                            {{
                                getSettingValue(
                                    "bank_name",
                                    "Bank Syariah Indonesia (BSI)",
                                )
                            }}
                        </p>
                        <p class="font-mono text-base font-bold">
                            {{
                                getSettingValue(
                                    "bank_account_number",
                                    "7123456789",
                                )
                            }}
                        </p>
                        <p
                            class="text-xs text-slate-500 dark:text-emerald-200/50"
                        >
                            a.n.
                            {{
                                getSettingValue(
                                    "bank_account_name",
                                    "Yayasan Hafiz Indonesia Emas",
                                )
                            }}
                        </p>
                    </div>
                </div>

                <!-- Payment Gateway (DOKU) - tampil kalau transaksi punya payment_url -->
                <div
                    v-if="
                        transaction.payment_status === 'pending' &&
                        transaction.payment_url
                    "
                    class="bg-white/80 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl space-y-3"
                >
                    <div class="flex items-center gap-3">
                        <UIcon
                            name="i-lucide-credit-card"
                            class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400"
                        />
                        <div class="text-sm">
                            <p class="font-semibold text-slate-900 dark:text-white">
                                {{ t("invoice.pgTitle") }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-emerald-200/60">
                                {{ t("invoice.pgDesc") }}
                            </p>
                        </div>
                    </div>

                    <p v-if="dokuLoadError" class="text-xs text-red-500">
                        {{ t("invoice.pgLoadError") }}
                    </p>

                    <button
                        @click="handlePayWithDoku"
                        :disabled="dokuLoading"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-600 hover:to-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold shadow-lg transition-all duration-300"
                    >
                        {{ dokuLoading ? t("invoice.uploading") : t("invoice.pgButton") }}
                    </button>
                </div>

                <!-- Payment Gateway - belum tersedia utk transaksi ini (transfer manual, dsb) -->
                <div
                    v-else-if="transaction.payment_status === 'pending'"
                    class="flex items-center gap-3 p-4 rounded-2xl border border-dashed border-slate-300 dark:border-emerald-800/60 text-slate-400 dark:text-emerald-200/40"
                >
                    <UIcon name="i-lucide-credit-card" class="w-5 h-5 shrink-0" />
                    <div class="text-sm">
                        <p class="font-semibold">
                            {{ t("invoice.pgTitle") }}
                        </p>
                        <p class="text-xs">{{ t("invoice.pgComingSoon") }}</p>
                    </div>
                </div>

                <div
                    v-if="transaction.payment_status === 'pending'"
                    class="bg-white/80 dark:bg-emerald-900/40 border border-emerald-200/70 dark:border-emerald-800/60 rounded-3xl p-6 shadow-xl space-y-4"
                >
                    <h3 class="font-bold text-slate-900 dark:text-white">
                        {{ t("invoice.uploadTitle") }}
                    </h3>
                    <p
                        class="text-sm text-slate-600 dark:text-emerald-100/70"
                    >
                        {{
                            transaction.transaction_receipt
                                ? t("invoice.alreadyUploaded")
                                : t("invoice.uploadDesc")
                        }}
                    </p>

                    <input
                        type="file"
                        accept="image/png,image/jpeg,image/webp"
                        @change="onFileChange"
                        class="block w-full text-sm text-slate-600 dark:text-emerald-100/70 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-emerald-100 dark:file:bg-emerald-900/50 file:text-emerald-700 dark:file:text-emerald-300 file:font-semibold cursor-pointer"
                    />

                    <p v-if="uploadError" class="text-xs text-red-500">
                        {{ uploadError }}
                    </p>

                    <button
                        @click="handleUpload"
                        :disabled="!selectedFile || isUploading"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 disabled:from-emerald-300 disabled:to-emerald-400 disabled:cursor-not-allowed text-white font-bold shadow-lg transition-all duration-300"
                    >
                        {{
                            isUploading
                                ? t("invoice.uploading")
                                : t("invoice.uploadButton")
                        }}
                    </button>
                </div>

                <a
                    v-else-if="transaction.transaction_receipt"
                    :href="receiptUrl"
                    target="_blank"
                    class="block text-center text-sm font-semibold text-emerald-700 dark:text-emerald-300 hover:underline"
                >
                    {{ t("invoice.viewReceipt") }}
                </a>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const route = useRoute();
const { t, locale } = useI18n();
const localePath = useLocalePath();
const toast = useToast();
const { getSettingValue } = useSettings();

const referenceId = route.params.reference_id as string;
const { transaction, pending, isUploading, uploadError, uploadReceipt } =
    useInvoice(referenceId);

const {
    isLoading: dokuLoading,
    loadError: dokuLoadError,
    openCheckout,
} = useDokuCheckout();

const handlePayWithDoku = () => {
    if (!transaction.value?.payment_url) return;
    openCheckout(transaction.value.payment_url);
};

const selectedFile = ref<File | null>(null);

const onFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    selectedFile.value = target.files?.[0] || null;
};

const handleUpload = async () => {
    if (!selectedFile.value) return;

    const success = await uploadReceipt(selectedFile.value);
    if (success) {
        selectedFile.value = null;
        toast.add({
            title: t("invoice.uploadSuccess"),
            color: "success",
            icon: "i-lucide-circle-check",
        });
    }
};

const programTitle = computed(() => {
    const program = transaction.value?.program_registration?.program;
    if (!program) return "-";
    return locale.value === "en" ? program.title_en : program.title_id;
});

const statusLabel = computed(() => {
    const status = transaction.value?.payment_status || "pending";
    return status.charAt(0).toUpperCase() + status.slice(1);
});

const statusColor = computed(() => {
    const colorMap: Record<string, string> = {
        pending: "warning",
        completed: "success",
        failed: "error",
        expired: "neutral",
    };
    return colorMap[transaction.value?.payment_status || "pending"];
});

const fileUrl = useFileUrl();
const receiptUrl = computed(() =>
    fileUrl(transaction.value?.transaction_receipt),
);

const formatIDR = (price?: number | string) => {
    const numericPrice =
        typeof price === "string" ? parseFloat(price) : (price ?? 0);
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(numericPrice);
};

useSeoMeta({
    title: () => `${t("invoice.title")} - YHIE`,
});
</script>
