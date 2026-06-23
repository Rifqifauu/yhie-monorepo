<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <!-- Page Header -->
        <AdminHeader
            title="Pengaturan Website"
            icon="i-lucide-settings"
            description="Kelola informasi kontak, rekening pembayaran, dan tautan media sosial untuk seluruh website dari satu tempat."
            :showCreate="false"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UButton
                        color="neutral"
                        variant="soft"
                        icon="i-lucide-rotate-cw"
                        :loading="pending"
                        @click="refresh"
                        >Muat Ulang</UButton
                    >
                </div>
            </template>
        </AdminHeader>

        <!-- Main Form Container -->
        <div v-if="pending && !settings.length" class="space-y-6 p-6 bg-white dark:bg-gray-900 rounded-lg ring-1 ring-gray-200 dark:ring-gray-800 shadow-sm animate-pulse">
            <div class="h-8 bg-gray-200 dark:bg-gray-800 rounded w-1/4"></div>
            <div class="space-y-4 pt-4">
                <div class="h-12 bg-gray-200 dark:bg-gray-800 rounded"></div>
                <div class="h-12 bg-gray-200 dark:bg-gray-800 rounded"></div>
                <div class="h-12 bg-gray-200 dark:bg-gray-800 rounded"></div>
            </div>
        </div>

        <div v-else-if="error" class="max-w-xl mx-auto py-12">
            <UAlert
                icon="i-lucide-alert-triangle"
                color="red"
                variant="soft"
                title="Gagal Memuat Pengaturan"
                description="Terjadi kesalahan saat mengambil data pengaturan dari server. Silakan coba beberapa saat lagi."
            >
                <template #actions>
                    <UButton size="sm" color="red" @click="refresh">Coba Lagi</UButton>
                </template>
            </UAlert>
        </div>

        <UForm v-else :state="form" class="space-y-6 flex-1 flex flex-col" @submit="handleSubmit">
            <!-- Tabs Navigation -->
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800 pb-px">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    @click="activeTab = tab.key"
                    class="px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-all flex items-center gap-2 cursor-pointer focus:outline-none"
                    :class="
                        activeTab === tab.key
                            ? 'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400 font-bold'
                            : 'border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-white'
                    "
                >
                    <UIcon :name="tab.icon" class="w-4 h-4" />
                    <span>{{ tab.label }}</span>
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="flex-1 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6">
                <!-- Tab 1: Umum & Kontak -->
                <div v-show="activeTab === 'general'" class="space-y-6 max-w-3xl">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-850 pb-2">
                        Kontak & Alamat Yayasan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Nomor WhatsApp"
                            name="wa_number"
                            help="Format internasional tanpa tanda plus (+). Contoh: 6281234567890"
                            required
                        >
                            <UInput
                                v-model="form.wa_number"
                                placeholder="Contoh: 628123456789"
                                icon="i-lucide-message-square"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Alamat Email Kontak"
                            name="contact_email"
                            help="Email resmi yayasan untuk dihubungi pengunjung."
                            required
                        >
                            <UInput
                                v-model="form.contact_email"
                                placeholder="Contoh: info@yhie.or.id"
                                icon="i-lucide-mail"
                                size="lg"
                                type="email"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <UFormField
                        label="Alamat Kantor"
                        name="office_address"
                        help="Alamat lengkap lokasi sekretariat yayasan."
                        required
                    >
                        <UTextarea
                            v-model="form.office_address"
                            placeholder="Tulis alamat kantor lengkap..."
                            icon="i-lucide-map-pin"
                            :rows="4"
                            size="lg"
                            class="w-full"
                        />
                    </UFormField>
                </div>

                <!-- Tab 2: Rekening Bank -->
                <div v-show="activeTab === 'bank'" class="space-y-6 max-w-3xl">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-850 pb-2">
                        Rekening Pembayaran Program
                    </h3>
                    
                    <UFormField
                        label="Nama Bank"
                        name="bank_name"
                        help="Nama bank resmi untuk tujuan transfer. Contoh: Bank Syariah Indonesia (BSI)"
                        required
                    >
                        <UInput
                            v-model="form.bank_name"
                            placeholder="Contoh: Bank Syariah Indonesia (BSI)"
                            icon="i-lucide-landmark"
                            size="lg"
                            class="w-full"
                        />
                    </UFormField>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Nomor Rekening"
                            name="bank_account_number"
                            help="Nomor rekening bank tanpa tanda strip atau titik."
                            required
                        >
                            <UInput
                                v-model="form.bank_account_number"
                                placeholder="Contoh: 7123456789"
                                icon="i-lucide-credit-card"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Nama Pemilik Rekening (Atas Nama)"
                            name="bank_account_name"
                            help="Nama resmi pemilik rekening sesuai buku tabungan."
                            required
                        >
                            <UInput
                                v-model="form.bank_account_name"
                                placeholder="Contoh: Yayasan Hafiz Indonesia Emas"
                                icon="i-lucide-user"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>
                </div>

                <!-- Tab 3: Media Sosial -->
                <div v-show="activeTab === 'social'" class="space-y-6 max-w-3xl">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-850 pb-2">
                        Tautan Media Sosial Yayasan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField label="Link Facebook" name="social_facebook">
                            <UInput
                                v-model="form.social_facebook"
                                placeholder="Contoh: https://facebook.com/nama-page"
                                icon="i-lucide-facebook"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField label="Link Instagram" name="social_instagram">
                            <UInput
                                v-model="form.social_instagram"
                                placeholder="Contoh: https://instagram.com/username"
                                icon="i-lucide-instagram"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField label="Link YouTube" name="social_youtube">
                            <UInput
                                v-model="form.social_youtube"
                                placeholder="Contoh: https://youtube.com/c/nama-channel"
                                icon="i-lucide-youtube"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField label="Link Twitter/X" name="social_twitter">
                            <UInput
                                v-model="form.social_twitter"
                                placeholder="Contoh: https://x.com/username"
                                icon="i-lucide-twitter"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <UFormField label="Link TikTok" name="social_tiktok">
                        <UInput
                            v-model="form.social_tiktok"
                            placeholder="Contoh: https://tiktok.com/@username"
                            icon="i-lucide-music-2"
                            size="lg"
                            class="w-full"
                        />
                    </UFormField>
                </div>
            </div>

            <!-- Form Action Footer -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <UButton
                    type="submit"
                    color="primary"
                    size="lg"
                    icon="i-lucide-save"
                    :loading="isSubmitting"
                >
                    Simpan Perubahan
                </UButton>
            </div>
        </UForm>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from "vue";
import { useSettings } from "~/composables/useSettings";

definePageMeta({
    layout: "admin",
});

const toast = useToast();
const { settings, pending, error, refresh, isSubmitting, getSettingValue, saveAllSettings } = useSettings();

// Tabs Config
const activeTab = ref("general");
const tabs = [
    { key: "general", label: "Umum & Kontak", icon: "i-lucide-contact" },
    { key: "bank", label: "Rekening Bank", icon: "i-lucide-landmark" },
    { key: "social", label: "Media Sosial", icon: "i-lucide-share-2" },
];

// Form Reactive State
const form = reactive({
    wa_number: "",
    contact_email: "",
    office_address: "",
    bank_name: "",
    bank_account_number: "",
    bank_account_name: "",
    social_facebook: "",
    social_instagram: "",
    social_youtube: "",
    social_twitter: "",
    social_tiktok: "",
});

// Watch settings and fill form when data arrives
watch(
    settings,
    (newVal) => {
        if (newVal && newVal.length > 0) {
            form.wa_number = getSettingValue("wa_number");
            form.contact_email = getSettingValue("contact_email");
            form.office_address = getSettingValue("office_address");
            form.bank_name = getSettingValue("bank_name");
            form.bank_account_number = getSettingValue("bank_account_number");
            form.bank_account_name = getSettingValue("bank_account_name");
            form.social_facebook = getSettingValue("social_facebook");
            form.social_instagram = getSettingValue("social_instagram");
            form.social_youtube = getSettingValue("social_youtube");
            form.social_twitter = getSettingValue("social_twitter");
            form.social_tiktok = getSettingValue("social_tiktok");
        }
    },
    { immediate: true },
);

const handleSubmit = async () => {
    const res = await saveAllSettings(form);
    if (res.success) {
        toast.add({
            title: "Pengaturan Disimpan!",
            description: "Semua data pengaturan situs berhasil diperbarui.",
            color: "success",
            icon: "i-lucide-circle-check",
        });
    } else {
        toast.add({
            title: "Gagal Menyimpan",
            description: res.error,
            color: "error",
            icon: "i-lucide-alert-triangle",
        });
    }
};
</script>
