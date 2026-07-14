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
        <UForm v-else :schema="schema" :state="form" class="space-y-6 flex-1 flex flex-col" @submit="handleSubmit"
            @error="onSubmitError">
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
                                <!-- Image upload -->
                                <div v-else-if="f.type === 'file'" class="flex items-center gap-3">
                                    <img v-if="form[f.key]" :src="previewSrc(form[f.key])" :alt="f.label"
                                        class="h-12 w-12 object-contain rounded border border-gray-200 dark:border-gray-700 bg-white p-1" />
                                    <UButton color="neutral" variant="soft" size="sm" icon="i-lucide-upload"
                                        :loading="uploadingKey === f.key" @click="triggerFileInput(f.key)">
                                        {{ form[f.key] ? "Ganti Gambar" : "Unggah Gambar" }}
                                    </UButton>
                                    <input :ref="(el) => setFileInputRef(f.key, el as HTMLInputElement)" type="file"
                                        accept="image/*" class="hidden" @change="handleImageUpload(f.key, $event)" />
                                </div>
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

        <!-- Keamanan: form terpisah & disimpan sendiri (bukan bagian dari form
             pengaturan umum di atas), supaya menyimpan tab lain tidak pernah
             ikut menghapus/menimpa passcode ini secara tidak sengaja. -->
        <div v-if="!pending && !error" class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6 space-y-4 max-w-3xl">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <UIcon name="i-lucide-shield-check" class="w-4 h-4" />
                    Keamanan Halaman Login
                </h3>
                <UBadge :color="gateEnabled ? 'success' : 'neutral'" variant="subtle">
                    {{ gateEnabled ? 'Aktif' : 'Tidak aktif' }}
                </UBadge>
            </div>
            <UFormField label="Passcode URL Login"
                help="Kalau diisi, pengunjung harus memasukkan passcode ini dulu sebelum bisa membuka halaman /login. Kosongkan lalu simpan untuk menonaktifkan gerbang ini.">
                <UInput v-model="passcodeInput" type="text" placeholder="Kosongkan untuk menonaktifkan" icon="i-lucide-key-round" size="lg" class="w-full" />
            </UFormField>
            <div class="flex justify-end">
                <UButton color="primary" variant="soft" size="lg" icon="i-lucide-save" :loading="passcodeSaving" @click="handleSavePasscode">
                    Simpan Passcode
                </UButton>
            </div>
        </div>

        <!-- Gambar Hero Section: bisa nambah/hapus gambar satu-satu, langsung
             tersimpan ke server begitu diunggah/dihapus (bukan bagian dari
             form bulk-save umum di atas). -->
        <div v-if="!pending && !error" class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6 space-y-4 max-w-3xl">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-2">
                <UIcon name="i-lucide-images" class="w-4 h-4" />
                Gambar Hero Section (Beranda)
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Gambar-gambar ini tampil bergantian (slider) di bagian hero halaman beranda.
            </p>

            <div v-if="heroImagePaths.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <div v-for="path in heroImagePaths" :key="path" class="relative group">
                    <img :src="previewSrc(path)" alt="Gambar hero"
                        class="w-full aspect-video object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
                    <UButton color="error" variant="solid" icon="i-lucide-trash" size="xs"
                        class="absolute top-1.5 right-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
                        :loading="removingHeroPath === path" @click="handleRemoveHeroImage(path)" />
                </div>
            </div>
            <p v-else class="text-sm text-gray-400 italic">
                Belum ada gambar - slider hero pakai gambar bawaan sementara.
            </p>

            <div>
                <input ref="heroFileInputRef" type="file" accept="image/*" class="hidden" @change="handleAddHeroImage" />
                <UButton color="primary" variant="soft" icon="i-lucide-plus" :loading="addingHeroImage"
                    @click="heroFileInputRef?.click()">
                    Tambah Gambar
                </UButton>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from "vue";
import { z } from "zod";
import { useSettings } from "~/composables/useSettings";

definePageMeta({ layout: "admin" });

// Validasi field wajib (*), format No. WhatsApp, dan batas panjang Deskripsi SEO.
const schema = z.object({
    site_name: z.string().min(1, "Nama Website wajib diisi"),
    tagline: z.string(),
    site_description: z.string(),
    meta_description: z
        .string()
        .max(160, "Deskripsi SEO maksimal 160 karakter"),
    logo_url: z.string(),
    favicon_url: z.string(),

    contact_email: z.string().min(1, "Alamat Email wajib diisi"),
    contact_phone: z.string(),
    wa_number: z
        .string()
        .min(1, "No. WhatsApp wajib diisi")
        .regex(/^\d+$/, "No. WhatsApp hanya boleh berisi angka"),
    instagram_account: z.string(),
    facebook_account: z.string(),

    office_address: z.string().min(1, "Alamat Kantor wajib diisi"),
    operating_hours: z.string(),
    gmap_embed_map: z.string(),

    about_history_id: z.string(),
    about_history_en: z.string(),
    about_vision_id: z.string(),
    about_vision_en: z.string(),
    about_mission_id: z.string(),
    about_mission_en: z.string(),

    bank_name: z.string().min(1, "Nama Bank wajib dipilih"),
    bank_account_number: z.string().min(1, "No. Rekening wajib diisi"),
    bank_account_name: z.string().min(1, "Nama Pemilik wajib diisi"),
});

const toast = useToast();
const {
    settings, pending, error, refresh, isSubmitting, getSettingValue, saveAllSettings,
    updateSetting, uploadSettingImage, heroImagePaths, addHeroImage, removeHeroImage,
} = useSettings();

// ── Upload gambar (logo/favicon) ────────────────────────────────────────────
const fileUrl = useFileUrl();
const previewSrc = (value: string) => (value?.startsWith("/storage/") ? fileUrl(value) : value);

const fileInputs: Record<string, HTMLInputElement | null> = {};
const setFileInputRef = (key: string, el: HTMLInputElement | null) => { fileInputs[key] = el; };
const triggerFileInput = (key: string) => fileInputs[key]?.click();

const uploadingKey = ref<string | null>(null);
const handleImageUpload = async (key: string, event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    uploadingKey.value = key;
    try {
        form[key] = await uploadSettingImage(key, file);
        toast.add({
            title: "Tersimpan!",
            description: `${key === "logo_url" ? "Logo" : "Favicon"} berhasil diunggah.`,
            color: "success",
            icon: "i-lucide-circle-check",
        });
    } catch (err: any) {
        toast.add({
            title: "Gagal Mengunggah",
            description: err.data?.message || err.message || "Gagal mengunggah gambar.",
            color: "error",
            icon: "i-lucide-alert-triangle",
        });
    } finally {
        uploadingKey.value = null;
        input.value = "";
    }
};

// ── Gambar hero section (nambah/hapus, terpisah dari form umum) ────────────
const heroFileInputRef = ref<HTMLInputElement | null>(null);
const addingHeroImage = ref(false);
const removingHeroPath = ref<string | null>(null);

const handleAddHeroImage = async (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    addingHeroImage.value = true;
    try {
        await addHeroImage(file);
        toast.add({ title: "Tersimpan!", description: "Gambar hero berhasil ditambahkan.", color: "success", icon: "i-lucide-circle-check" });
    } catch (err: any) {
        toast.add({ title: "Gagal Menambahkan", description: err.data?.message || err.message || "Gagal menambahkan gambar.", color: "error", icon: "i-lucide-alert-triangle" });
    } finally {
        addingHeroImage.value = false;
        input.value = "";
    }
};

const handleRemoveHeroImage = async (path: string) => {
    removingHeroPath.value = path;
    try {
        await removeHeroImage(path);
        toast.add({ title: "Terhapus!", description: "Gambar hero berhasil dihapus.", color: "success", icon: "i-lucide-circle-check" });
    } catch (err: any) {
        toast.add({ title: "Gagal Menghapus", description: err.data?.message || err.message || "Gagal menghapus gambar.", color: "error", icon: "i-lucide-alert-triangle" });
    } finally {
        removingHeroPath.value = null;
    }
};

// ── Keamanan: gerbang passcode /login (terpisah dari form umum di atas) ────
const client = useSanctumClient();
const passcodeInput = ref("");
const passcodeSaving = ref(false);
const gateEnabled = ref(false);

const { data: gateStatus, refresh: refreshGateStatus } = await useAsyncData(
    "login-gate-status",
    () => client<{ enabled: boolean }>("/api/login-gate/status"),
);
watch(gateStatus, (val) => { gateEnabled.value = !!val?.enabled; }, { immediate: true });

const handleSavePasscode = async () => {
    passcodeSaving.value = true;
    try {
        await updateSetting("login_passcode", passcodeInput.value);
        await refreshGateStatus();
        passcodeInput.value = "";
        toast.add({
            title: "Tersimpan!",
            description: gateEnabled.value
                ? "Gerbang passcode halaman login diaktifkan."
                : "Gerbang passcode halaman login dinonaktifkan.",
            color: "success",
            icon: "i-lucide-circle-check",
        });
    } catch (err: any) {
        toast.add({
            title: "Gagal Menyimpan",
            description: err.data?.message || err.message || "Gagal menyimpan passcode.",
            color: "error",
            icon: "i-lucide-alert-triangle",
        });
    } finally {
        passcodeSaving.value = false;
    }
};

// ── Field Definitions ──────────────────────────────────────────────────────
interface Field {
    key: string; label: string; placeholder?: string; icon?: string;
    type?: string; help?: string; required?: boolean; full?: boolean;
    rows?: number; options?: string[];
}
interface Tab { key: string; label: string; icon: string; title: string; fields: Field[]; }

const bankOptions = ["Bank Syariah Indonesia (BSI)", "BCA", "BCA Syariah", "BNI", "BNI Syariah", "BRI", "BRI Syariah", "Mandiri", "Mandiri Syariah", "CIMB Niaga", "BTPN", "Bank Muamalat", "Bank Mega", "Lainnya"];

// Field mengikuti key yang ada di API (settings). Semua tampil di footer.
const tabs: Tab[] = [
    {
        key: "general", label: "Informasi Umum", icon: "i-lucide-globe", title: "Informasi Dasar Website",
        fields: [
            { key: "site_name", label: "Nama Website", placeholder: "Yayasan Hafizh Indonesia Emas", icon: "i-lucide-type", required: true },
            { key: "tagline", label: "Slogan / Tagline", placeholder: "Membangun Generasi Hafizh Berakhlak Mulia", icon: "i-lucide-quote", full: true, help: "Tampil di bawah nama di footer." },
            { key: "site_description", label: "Deskripsi Singkat", placeholder: "Deskripsi singkat yayasan...", type: "textarea", rows: 2, full: true, help: "Tampil sebagai bio singkat di footer." },
            { key: "meta_description", label: "Deskripsi SEO", placeholder: "Deskripsi untuk mesin pencari Google...", type: "textarea", rows: 2, full: true, help: "Tampil di hasil pencarian Google (<head>). Maks. ~160 karakter." },
            { key: "logo_url", label: "Logo", type: "file", full: true, help: "Logo utama (header & footer)." },
            { key: "favicon_url", label: "Favicon", type: "file", full: true, help: "Ikon kecil di tab browser." },
        ],
    },
    {
        key: "contact", label: "Kontak", icon: "i-lucide-contact", title: "Kontak",
        fields: [
            { key: "contact_email", label: "Alamat Email", placeholder: "info@yhie.or.id", icon: "i-lucide-mail", type: "email", required: true },
            { key: "contact_phone", label: "No. Telepon", placeholder: "+62 812-3456-7890", icon: "i-lucide-phone" },
            { key: "wa_number", label: "No. WhatsApp", placeholder: "628123456789", icon: "i-lucide-message-square", required: true, help: "Format internasional tanpa +, misal 628xxx." },
            { key: "instagram_account", label: "Instagram", placeholder: "https://instagram.com/username", icon: "i-lucide-instagram", full: true, help: "Kosongkan jika belum ada akun." },
            { key: "facebook_account", label: "Facebook", placeholder: "https://facebook.com/page", icon: "i-lucide-facebook", full: true, help: "Kosongkan jika belum ada akun." },
        ],
    },
    {
        key: "location", label: "Lokasi", icon: "i-lucide-map-pin", title: "Lokasi Kantor",
        fields: [
            { key: "office_address", label: "Alamat Kantor", placeholder: "Jl. Contoh No. 123, Kota, Provinsi", type: "textarea", rows: 3, full: true, required: true, help: "Tampil di footer." },
            { key: "operating_hours", label: "Jam Operasional", placeholder: "Senin - Jumat: 08.00 - 17.00 WIB", icon: "i-lucide-clock", full: true },
            { key: "gmap_embed_map", label: "Embed Google Maps", placeholder: '<iframe src="https://maps.google.com/..."></iframe>', type: "textarea", rows: 3, full: true, help: "Salin kode <iframe> dari Google Maps → Bagikan → Sematkan peta." },
        ],
    },
    {
        key: "about", label: "Tentang", icon: "i-lucide-info", title: "Konten Tentang (ringkas, untuk footer)",
        fields: [
            { key: "about_history_id", label: "Sejarah (Indonesia)", type: "textarea", rows: 3, full: true },
            { key: "about_history_en", label: "Sejarah (English)", type: "textarea", rows: 3, full: true },
            { key: "about_vision_id", label: "Visi (Indonesia)", type: "textarea", rows: 2, full: true },
            { key: "about_vision_en", label: "Visi (English)", type: "textarea", rows: 2, full: true },
            { key: "about_mission_id", label: "Misi (Indonesia)", type: "textarea", rows: 3, full: true },
            { key: "about_mission_en", label: "Misi (English)", type: "textarea", rows: 3, full: true },
        ],
    },
    {
        key: "payment", label: "Pembayaran", icon: "i-lucide-landmark", title: "Pengaturan Pembayaran",
        fields: [
            { key: "bank_name", label: "Nama Bank", type: "select", options: bankOptions, required: true, full: true, help: "Pilih bank tujuan transfer." },
            { key: "bank_account_number", label: "No. Rekening", placeholder: "7123456789", icon: "i-lucide-credit-card", required: true, help: "Tanpa strip atau titik." },
            { key: "bank_account_name", label: "Nama Pemilik (a.n.)", placeholder: "Yayasan Hafizh Indonesia Emas", icon: "i-lucide-user", required: true },
        ],
    },
];

// ── Form State ─────────────────────────────────────────────────────────────
const allKeys = tabs.flatMap((t) => t.fields.map((f) => f.key));
const form = reactive<Record<string, string>>(Object.fromEntries(allKeys.map((k) => [k, ""])));
const activeTab = ref("general");

// Field -> tab, dipakai untuk lompat ke tab yang errornya tersembunyi.
const fieldToTab: Record<string, string> = {};
tabs.forEach((t) => t.fields.forEach((f) => (fieldToTab[f.key] = t.key)));

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

// Validasi gagal bisa terjadi di tab yang sedang tersembunyi (v-show) - pindahkan
// ke tab pertama yang bermasalah supaya pesan errornya tidak nyangkut senyap.
const onSubmitError = (event: { errors?: { name?: string; message: string }[] }) => {
    const firstError = event.errors?.[0];
    if (!firstError) return;

    const tabKey = firstError.name ? fieldToTab[firstError.name] : undefined;
    if (tabKey) activeTab.value = tabKey;

    toast.add({
        title: "Form belum lengkap",
        description: firstError.message || "Periksa kembali isian pada tab yang ditandai.",
        color: "error",
        icon: "i-lucide-alert-triangle",
    });
};
</script>
