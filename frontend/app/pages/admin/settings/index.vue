<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader title="Pengaturan Website" icon="i-lucide-settings"
            description="Kelola informasi umum, kontak, lokasi, dan pembayaran website dari satu tempat." :showCreate="false">
            <template #actions>
                <UButton color="neutral" variant="soft" icon="i-lucide-rotate-cw" :loading="pending" @click="refresh">
                    Muat Ulang</UButton>
            </template>
        </AdminHeader>

        <!-- Loading Skeleton -->
        <div v-if="pending && !settings.length" class="space-y-4 p-6 bg-white dark:bg-gray-900 rounded-lg ring-1 ring-gray-200 dark:ring-gray-800 animate-pulse">
            <div class="h-8 bg-gray-200 dark:bg-gray-800 rounded w-1/4" />
            <div v-for="i in 3" :key="i" class="h-12 bg-gray-200 dark:bg-gray-800 rounded" />
        </div>

        <!-- Error State -->
        <UAlert v-else-if="error" icon="i-lucide-alert-triangle" color="red" variant="soft" title="Gagal Memuat Pengaturan"
            description="Terjadi kesalahan saat mengambil data. Silakan coba lagi.">
            <template #actions><UButton size="sm" color="red" @click="refresh">Coba Lagi</UButton></template>
        </UAlert>

        <!-- Main Form -->
        <UForm v-else :state="form" class="space-y-6 flex-1 flex flex-col" @submit="handleSubmit">
            <!-- Tab Navigation -->
            <div class="flex gap-1 border-b border-gray-200 dark:border-gray-800 pb-px overflow-x-auto">
                <button v-for="tab in tabs" :key="tab.key" type="button" @click="activeTab = tab.key"
                    class="px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-all flex items-center gap-2 cursor-pointer focus:outline-none whitespace-nowrap"
                    :class="activeTab === tab.key
                        ? 'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400'
                        : 'border-transparent text-gray-500 hover:text-gray-900 dark:hover:text-white'">
                    <UIcon :name="tab.icon" class="w-4 h-4" /><span>{{ tab.label }}</span>
                </button>
            </div>

            <!-- Tab Content -->
            <div class="flex-1 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6">
                <template v-for="tab in tabs" :key="tab.key">
                    <div v-show="activeTab === tab.key" class="space-y-6 max-w-3xl">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                            {{ tab.title }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <UFormField v-for="f in tab.fields" :key="f.key" :label="f.label" :name="f.key"
                                :help="f.help" :required="f.required" :class="{ 'md:col-span-2': f.full }">
                                <!-- Select -->
                                <USelect v-if="f.type === 'select'" v-model="form[f.key]" :items="f.options!"
                                    placeholder="Pilih..." size="lg" class="w-full" />
                                <!-- Textarea -->
                                <UTextarea v-else-if="f.type === 'textarea'" v-model="form[f.key]"
                                    :placeholder="f.placeholder" :rows="f.rows || 3" size="lg" class="w-full" />
                                <!-- Default Input -->
                                <UInput v-else v-model="form[f.key]" :placeholder="f.placeholder"
                                    :icon="f.icon" :type="f.type || 'text'" size="lg" class="w-full" />
                            </UFormField>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-800">
                <UButton type="submit" color="primary" size="lg" icon="i-lucide-save" :loading="isSubmitting">
                    Simpan Perubahan
                </UButton>
            </div>
        </UForm>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from "vue";
import { useSettings } from "~/composables/useSettings";

definePageMeta({ layout: "admin" });

const toast = useToast();
const { settings, pending, error, refresh, isSubmitting, getSettingValue, saveAllSettings } = useSettings();

// ── Field Definitions ──────────────────────────────────────────────────────
interface Field {
    key: string; label: string; placeholder?: string; icon?: string;
    type?: string; help?: string; required?: boolean; full?: boolean;
    rows?: number; options?: string[];
}
interface Tab { key: string; label: string; icon: string; title: string; fields: Field[]; }

const bankOptions = ["Bank Syariah Indonesia (BSI)", "BCA", "BCA Syariah", "BNI", "BNI Syariah", "BRI", "BRI Syariah", "Mandiri", "Mandiri Syariah", "CIMB Niaga", "BTPN", "Bank Muamalat", "Bank Mega", "Lainnya"];

const tabs: Tab[] = [
    {
        key: "general", label: "Informasi Umum", icon: "i-lucide-globe", title: "Informasi Dasar Website",
        fields: [
            { key: "site_name", label: "Nama Website", placeholder: "Contoh: Yayasan Hafiz Indonesia Emas", icon: "i-lucide-type", required: true },
            { key: "tagline", label: "Slogan / Tagline", placeholder: "Contoh: Mencetak Generasi Qurani", icon: "i-lucide-quote" },
            { key: "meta_description", label: "Deskripsi Website (SEO)", placeholder: "Deskripsi singkat untuk mesin pencari Google...", type: "textarea", rows: 2, full: true, help: "Tampil di hasil pencarian Google. Maks. 160 karakter." },
            { key: "logo_url", label: "URL Logo Website", placeholder: "https://... atau /images/logo.png", icon: "i-lucide-image", full: true, help: "Link gambar logo utama website." },
            { key: "favicon_url", label: "URL Favicon", placeholder: "https://... atau /favicon.ico", icon: "i-lucide-app-window", help: "Ikon kecil di tab browser." },
        ],
    },
    {
        key: "contact", label: "Kontak & Sosial", icon: "i-lucide-contact", title: "Kontak & Media Sosial",
        fields: [
            { key: "wa_number", label: "No. WhatsApp", placeholder: "628123456789", icon: "i-lucide-message-square", required: true, help: "Format internasional tanpa +." },
            { key: "contact_email", label: "Alamat Email", placeholder: "info@yhie.or.id", icon: "i-lucide-mail", type: "email", required: true },
            { key: "social_instagram", label: "Instagram", placeholder: "https://instagram.com/username", icon: "i-lucide-instagram" },
            { key: "social_facebook", label: "Facebook", placeholder: "https://facebook.com/page", icon: "i-lucide-facebook" },
            { key: "social_tiktok", label: "TikTok", placeholder: "https://tiktok.com/@username", icon: "i-lucide-music-2" },
            { key: "social_youtube", label: "YouTube", placeholder: "https://youtube.com/c/channel", icon: "i-lucide-youtube" },
            { key: "social_linkedin", label: "LinkedIn", placeholder: "https://linkedin.com/company/...", icon: "i-lucide-linkedin" },
        ],
    },
    {
        key: "location", label: "Lokasi", icon: "i-lucide-map-pin", title: "Lokasi & Operasional",
        fields: [
            { key: "office_address", label: "Alamat Kantor", placeholder: "Jl. Contoh No. 123, Kota, Provinsi", type: "textarea", rows: 3, full: true, required: true },
            { key: "map_embed", label: "Embed Google Maps", placeholder: '<iframe src="https://maps.google.com/..."></iframe>', type: "textarea", rows: 3, full: true, help: "Salin kode <iframe> dari Google Maps → Bagikan → Sematkan peta." },
            { key: "operating_hours", label: "Jam Operasional", placeholder: "Senin - Jumat: 08.00 - 17.00 WIB", icon: "i-lucide-clock", full: true, help: "Jam buka kantor/sekretariat." },
        ],
    },
    {
        key: "payment", label: "Pembayaran", icon: "i-lucide-landmark", title: "Pengaturan Pembayaran",
        fields: [
            { key: "bank_name", label: "Nama Bank", type: "select", options: bankOptions, required: true, full: true, help: "Pilih bank tujuan transfer." },
            { key: "bank_account_number", label: "No. Rekening", placeholder: "7123456789", icon: "i-lucide-credit-card", required: true, help: "Tanpa strip atau titik." },
            { key: "bank_account_name", label: "Nama Pemilik (a.n.)", placeholder: "Yayasan Hafiz Indonesia Emas", icon: "i-lucide-user", required: true },
        ],
    },
];

// ── Form State ─────────────────────────────────────────────────────────────
const allKeys = tabs.flatMap((t) => t.fields.map((f) => f.key));
const form = reactive<Record<string, string>>(Object.fromEntries(allKeys.map((k) => [k, ""])));
const activeTab = ref("general");

// Populate form when data arrives
watch(settings, (val) => {
    if (val?.length) allKeys.forEach((k) => (form[k] = getSettingValue(k)));
}, { immediate: true });

// ── Submit ─────────────────────────────────────────────────────────────────
const handleSubmit = async () => {
    const res = await saveAllSettings(form);
    toast.add(res.success
        ? { title: "Tersimpan!", description: "Semua pengaturan berhasil diperbarui.", color: "success", icon: "i-lucide-circle-check" }
        : { title: "Gagal Menyimpan", description: res.error, color: "error", icon: "i-lucide-alert-triangle" }
    );
};
</script>
