<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Tambah Media"
            icon="i-lucide-plus-circle"
            parentRoute="/admin/media"
            childTitle="Tambah Baru"
            description="Tambahkan data media (galeri/konten) baru dengan satu atau beberapa gambar ke dalam sistem."
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UButton
                        color="neutral"
                        variant="ghost"
                        icon="i-lucide-x"
                        :disabled="isSubmitting"
                        @click="handleCancel"
                        >Batal</UButton
                    >
                    <UButton
                        color="primary"
                        variant="solid"
                        icon="i-lucide-save"
                        :loading="isSubmitting"
                        @click="handleCreate"
                        >Simpan Data</UButton
                    >
                </div>
            </template>
        </AdminHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 items-start">
            <div
                class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-5 flex flex-col gap-4 sticky top-6"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                    >
                        Pratinjau Berkas
                    </span>
                    <span
                        v-if="imagePreviews.length > 0"
                        class="text-[10px] bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 px-2 py-0.5 rounded font-medium"
                    >
                        {{ imagePreviews.length }} Berkas Dipilih
                    </span>
                </div>

                <div
                    v-if="imagePreviews.length > 0"
                    class="grid grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-1"
                >
                    <div
                        v-for="(preview, index) in imagePreviews"
                        :key="index"
                        class="relative group aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                    >
                        <img
                            :src="preview"
                            :alt="`Preview Media ${index + 1}`"
                            class="w-full h-full object-contain rounded-md transition-transform duration-200 group-hover:scale-[1.02]"
                        />
                    </div>
                </div>

                <div
                    v-else
                    class="relative group aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                >
                    <img
                        src="/placeholder.jpg"
                        alt="Preview Media"
                        class="w-full h-full object-contain rounded-md opacity-40 grayscale"
                    />
                </div>
            </div>

            <div
                class="lg:col-span-2 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6"
            >
                <UForm
                    :schema="schema"
                    :state="form"
                    @submit="handleCreate"
                    class="space-y-6"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Judul (Bahasa Indonesia)"
                            name="title_id"
                            required
                        >
                            <UInput
                                v-model="form.title_id"
                                placeholder="Masukkan judul dalam Indonesia"
                                icon="i-lucide-languages"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Judul (Bahasa Inggris)"
                            name="title_en"
                            required
                        >
                            <UInput
                                v-model="form.title_en"
                                placeholder="Enter title in English"
                                icon="i-lucide-globe"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Slug (Bahasa Indonesia)"
                            name="slug_id"
                            required
                        >
                            <UInput
                                v-model="form.slug_id"
                                placeholder="Contoh: judul-media-kita"
                                icon="i-lucide-link"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Slug (Bahasa Inggris)"
                            name="slug_en"
                            required
                        >
                            <UInput
                                v-model="form.slug_en"
                                placeholder="Example: our-media-title"
                                icon="i-lucide-link"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Kategori"
                            name="category"
                            required
                        >
                            <USelect
                                v-model="form.category"
                                :items="categoryOptions"
                                placeholder="Pilih kategori..."
                                icon="i-lucide-folder"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Unggah Gambar (Maks 2MB/file)"
                            name="image"
                            required
                        >
                            <div class="space-y-3">
                                <div
                                    v-if="selectedFiles.length > 0"
                                    class="flex flex-col gap-2 max-h-48 overflow-y-auto pr-1"
                                >
                                    <div
                                        v-for="(item, index) in selectedFiles"
                                        :key="item.id"
                                        class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg"
                                    >
                                        <img
                                            :src="item.preview"
                                            class="w-10 h-10 object-cover rounded-md border border-gray-200 dark:border-gray-700"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate"
                                            >
                                                {{ item.file.name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ item.sizeMB }} MB
                                            </p>
                                        </div>
                                        <UButton
                                            color="error"
                                            variant="ghost"
                                            icon="i-lucide-trash"
                                            size="sm"
                                            @click="removeFile(index)"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        multiple
                                        class="hidden"
                                        ref="fileInputRef"
                                        @change="onFileAdded"
                                    />
                                    <UButton
                                        color="primary"
                                        variant="soft"
                                        icon="i-lucide-plus"
                                        class="w-full justify-center border border-dashed border-primary-300 dark:border-primary-800"
                                        @click="triggerFileInput"
                                    >
                                        Pilih Gambar
                                    </UButton>
                                </div>
                            </div>
                        </UFormField>
                    </div>

                    <div class="relative flex py-2 items-center">
                        <div
                            class="flex-grow border-t border-gray-100 dark:border-gray-800"
                        ></div>
                        <span
                            class="flex-shrink mx-4 text-gray-400 text-xs font-semibold uppercase tracking-wider"
                            >Deskripsi Konten</span
                        >
                        <div
                            class="flex-grow border-t border-gray-100 dark:border-gray-800"
                        ></div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Deskripsi (Bahasa Indonesia)"
                            name="description_id"
                        >
                            <UTextarea
                                v-model="form.description_id"
                                placeholder="Tulis deskripsi media lengkap dalam Bahasa Indonesia di sini..."
                                :rows="4"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Deskripsi (Bahasa Inggris)"
                            name="description_en"
                        >
                            <UTextarea
                                v-model="form.description_en"
                                placeholder="Write complete media description in English here..."
                                :rows="4"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>
                </UForm>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import { z } from "zod";

definePageMeta({ layout: "admin" });

const schema = z.object({
    title_id: z.string().min(1, "Judul (ID) wajib diisi"),
    title_en: z.string().min(1, "Judul (EN) wajib diisi"),
    slug_id: z.string().min(1, "Slug (ID) wajib diisi"),
    slug_en: z.string().min(1, "Slug (EN) wajib diisi"),
    category: z.string().min(1, "Kategori wajib diisi"),
    description_id: z
        .string()
        .max(100, "Deskripsi maksimal 100 karakter")
        .optional(),
    description_en: z
        .string()
        .max(100, "Deskripsi maksimal 100 karakter")
        .optional(),
});

const router = useRouter();
const toast = useToast();
const { createMedia, isSubmitting } = useGallery();

interface ImageFile {
    id: string;
    file: File;
    preview: string;
    sizeMB: string;
}

const fileInputRef = ref<HTMLInputElement | null>(null);
const selectedFiles = ref<ImageFile[]>([]);

const imagePreviews = computed(() =>
    selectedFiles.value.map((item) => item.preview),
);

const form = reactive({
    title_id: "",
    title_en: "",
    slug_id: "",
    slug_en: "",
    category: "",
    description_id: "",
    description_en: "",
});

// Kategori tetap (ENUM di database) - harus sinkron dengan App\Enums\ContentCategory di backend.
const categoryOptions = [
    "Umum",
    "Edukasi",
    "Akademik",
    "Berita",
    "Pengumuman",
    "Kegiatan",
];

function handleCancel() {
    clearSelectedFiles();
    router.push("/admin/media");
}

function clearSelectedFiles() {
    selectedFiles.value.forEach((item) => URL.revokeObjectURL(item.preview));
    selectedFiles.value = [];
    if (fileInputRef.value) fileInputRef.value.value = "";
}

function triggerFileInput() {
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
}

// Logic validasi ukuran file
function onFileAdded(event: Event) {
    const target = event.target as HTMLInputElement;
    if (!target.files || target.files.length === 0) return;

    const newFiles = Array.from(target.files);
    let hasOversized = false;
    const MAX_SIZE_MB = 2;

    newFiles.forEach((file) => {
        const sizeInMB = file.size / (1024 * 1024);

        if (sizeInMB > MAX_SIZE_MB) {
            hasOversized = true;
            return;
        }

        selectedFiles.value.push({
            id: Math.random().toString(36).substring(7),
            file: file,
            preview: URL.createObjectURL(file),
            sizeMB: sizeInMB.toFixed(2),
        });
    });

    if (hasOversized) {
        toast.add({
            title: "Peringatan",
            description: `Beberapa gambar ditolak karena ukurannya melebihi batas maksimal ${MAX_SIZE_MB}MB.`,
            color: "warning",
            icon: "i-lucide-alert-triangle",
        });
    }

    target.value = "";
}

// Hapus file spesifik dari repeater
function removeFile(index: number) {
    const removedItem = selectedFiles.value[index];
    URL.revokeObjectURL(removedItem.preview);
    selectedFiles.value.splice(index, 1);
}

const handleCreate = async () => {
    if (selectedFiles.value.length === 0) {
        toast.add({
            title: "Gagal",
            description: "Mohon pilih minimal satu gambar media.",
            color: "error",
            icon: "i-lucide-circle-alert",
        });
        return;
    }

    const formData = new FormData();

    Object.entries(form).forEach(([key, value]) => formData.append(key, value));

    // Append file gambar ke formData dengan key 'image[]'
    selectedFiles.value.forEach((item) => {
        formData.append("image[]", item.file);
    });

    const result = await createMedia(formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Data media baru telah ditambahkan.",
            color: "success",
            icon: "i-lucide-circle-check",
        });

        clearSelectedFiles();
        router.push("/admin/media");
    } else {
        toast.add({
            title: "Gagal",
            description:
                result.error || "Terjadi kesalahan saat menyimpan data.",
            color: "error",
            icon: "i-lucide-circle-alert",
        });
    }
};
</script>
