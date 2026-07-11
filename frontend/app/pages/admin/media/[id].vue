<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Media"
            icon="i-lucide-images"
            parentRoute="/admin/media"
            :childTitle="isEditing ? 'Edit Media' : 'Lihat Media'"
            :description="
                isEditing
                    ? 'Ubah informasi media dan perbarui berkas di sini.'
                    : 'Lihat informasi detail media dan lakukan perubahan jika diperlukan.'
            "
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <template v-if="!isEditing">
                        <UButton
                            color="neutral"
                            variant="soft"
                            icon="i-lucide-edit"
                            @click="startEdit"
                            >Edit Media</UButton
                        >
                        <UButton
                            color="error"
                            variant="solid"
                            icon="i-lucide-trash"
                            @click="triggerDelete"
                            >Hapus Data</UButton
                        >
                    </template>

                    <template v-else>
                        <UButton
                            color="neutral"
                            variant="ghost"
                            icon="i-lucide-x"
                            :disabled="isSubmitting"
                            @click="cancelEdit"
                            >Batal</UButton
                        >
                        <UButton
                            color="primary"
                            variant="solid"
                            icon="i-lucide-save"
                            :loading="isSubmitting"
                            @click="handleUpdate"
                            >Simpan Perubahan</UButton
                        >
                    </template>
                </div>
            </template>
        </AdminHeader>

        <div v-if="pending" class="flex justify-center py-12">
            <span class="text-sm text-gray-500">Memuat data media...</span>
        </div>

        <div
            v-else-if="media"
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 items-start"
        >
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
                        v-if="isEditing && imagePreviews.length > 0"
                        class="text-[10px] bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 px-2 py-0.5 rounded font-medium"
                    >
                        {{ imagePreviews.length }} Berkas Baru
                    </span>
                    <span
                        v-else-if="media?.image && media.image.length > 0"
                        class="text-[10px] bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 px-2 py-0.5 rounded font-medium"
                    >
                        {{ media.image.length }} Berkas Tersimpan
                    </span>
                </div>

                <div
                    v-if="isEditing && imagePreviews.length > 0"
                    class="grid grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-1"
                >
                    <div
                        v-for="(preview, index) in imagePreviews"
                        :key="`new-${index}`"
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
                    v-else-if="media?.image && media.image.length > 0"
                    class="grid grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-1"
                >
                    <div
                        v-for="(img, index) in media.image"
                        :key="`old-${index}`"
                        class="relative group aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                    >
                        <img
                            :src="coverOf(media)"
                            :alt="`Media Tersimpan ${index + 1}`"
                            class="w-full h-full object-contain rounded-md transition-transform duration-200 group-hover:scale-[1.02]"
                        />
                    </div>
                </div>

                <div
                    v-else
                    class="relative group aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                >
                    <img
                        :src="coverOf(media) || '/placeholder.png'"
                        alt="Preview Media"
                        class="w-full h-full object-contain rounded-md transition-transform duration-200 group-hover:scale-[1.02]"
                    />
                </div>
            </div>

            <div
                class="lg:col-span-2 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6"
            >
                <UForm
                    v-if="isEditing"
                    :schema="schema"
                    :state="form"
                    @submit="handleUpdate"
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
                            label="Unggah Gambar Baru (Maks 2MB/file)"
                            name="image"
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
                                        Pilih Gambar Baru
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

                <div v-else class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Judul (Indonesia)
                            </h3>
                            <p
                                class="text-base font-semibold text-gray-900 dark:text-white"
                            >
                                {{ media?.title_id || "-" }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="font-mono text-xs"
                                    >Slug: {{ media?.slug_id || "-" }}</span
                                >
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Judul (Inggris)
                            </h3>
                            <p
                                class="text-base font-medium text-gray-600 dark:text-gray-300"
                            >
                                {{ media?.title_en || "-" }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="font-mono text-xs"
                                    >Slug: {{ media?.slug_en || "-" }}</span
                                >
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Kategori
                            </h3>
                            <span
                                class="text-xs font-mono font-medium text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-950/40 border border-primary-100 dark:border-primary-900 px-2.5 py-1 rounded-md inline-block"
                            >
                                {{ media?.category || "Uncategorized" }}
                            </span>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800" />

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Deskripsi (Indonesia)
                            </h3>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed bg-gray-50 dark:bg-gray-950/50 p-4 rounded-lg border border-gray-100 dark:border-gray-800"
                            >
                                {{
                                    media?.description_id ||
                                    "Tidak ada deskripsi."
                                }}
                            </p>
                        </div>

                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Deskripsi (Inggris)
                            </h3>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed bg-gray-50 dark:bg-gray-950/50 p-4 rounded-lg border border-gray-100 dark:border-gray-800"
                            >
                                {{
                                    media?.description_en ||
                                    "Tidak ada deskripsi."
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <AdminDeleteModal
            v-model:open="isDeleteModalOpen"
            :id="media?.id"
            :title="media?.title_id || media?.title_en || 'Media ini'"
            endpoint="api/media"
            @success="handleDeleteSuccess"
            @error="handleDeleteError"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import { z } from "zod";

definePageMeta({
    layout: "admin",
});

const schema = z.object({
    title_id: z.string().min(1, "Judul (ID) wajib diisi"),
    title_en: z.string().min(1, "Judul (EN) wajib diisi"),
    slug_id: z.string().min(1, "Slug (ID) wajib diisi"),
    slug_en: z.string().min(1, "Slug (EN) wajib diisi"),
    category: z.string().min(1, "Kategori wajib diisi"),
    description_id: z.string().max(100, "Deskripsi maksimal 100 karakter").optional(),
    description_en: z.string().max(100, "Deskripsi maksimal 100 karakter").optional(),
});

const id = useRoute().params.id as string;
const router = useRouter();
const toast = useToast();

const { fetchDetail, updateMedia, isSubmitting, coverOf } = useGallery();

const { data: apiResponse, pending, refresh } = await fetchDetail(id);
const media = computed(() => apiResponse.value?.data);

const isEditing = ref(false);
const isDeleteModalOpen = ref(false);

// Interface khusus untuk Repeater Image
interface ImageFile {
    id: string;
    file: File;
    preview: string;
    sizeMB: string;
}

const fileInputRef = ref<HTMLInputElement | null>(null);
const selectedFiles = ref<ImageFile[]>([]);

// Derived state: Mengambil daftar URL preview dari selectedFiles untuk ditampilkan di grid kiri
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

function startEdit() {
    if (!media.value) return;

    form.title_id = media.value.title_id || "";
    form.title_en = media.value.title_en || "";
    form.slug_id = media.value.slug_id || "";
    form.slug_en = media.value.slug_en || "";
    form.category = media.value.category || "";
    form.description_id = media.value.description_id || "";
    form.description_en = media.value.description_en || "";

    clearSelectedFiles();
    isEditing.value = true;
}

function cancelEdit() {
    isEditing.value = false;
    clearSelectedFiles();
}

function clearSelectedFiles() {
    selectedFiles.value.forEach((item) => URL.revokeObjectURL(item.preview));
    selectedFiles.value = [];
    if (fileInputRef.value) fileInputRef.value.value = ""; // Reset input
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

        // Jika lebih besar dari 2MB, tolak filenya
        if (sizeInMB > MAX_SIZE_MB) {
            hasOversized = true;
            return; // Skip file ini, lanjut ke file berikutnya di loop
        }

        // Masukkan file valid ke state
        selectedFiles.value.push({
            id: Math.random().toString(36).substring(7), // ID unik untuk key v-for
            file: file,
            preview: URL.createObjectURL(file),
            sizeMB: sizeInMB.toFixed(2), // Format 2 desimal
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

    // Reset isi input biar file yang sama bisa dipilih lagi kalau baru saja dihapus
    target.value = "";
}

// Hapus file spesifik dari repeater
function removeFile(index: number) {
    const removedItem = selectedFiles.value[index];
    URL.revokeObjectURL(removedItem.preview); // Cegah memory leak
    selectedFiles.value.splice(index, 1);
}

const handleUpdate = async () => {
    const formData = new FormData();
    formData.append("title_id", form.title_id);
    formData.append("title_en", form.title_en);
    formData.append("slug_id", form.slug_id);
    formData.append("slug_en", form.slug_en);
    formData.append("category", form.category);
    formData.append("description_id", form.description_id);
    formData.append("description_en", form.description_en);

    // Ambil object .file-nya saja untuk dikirim ke Laravel API
    if (selectedFiles.value.length > 0) {
        selectedFiles.value.forEach((item) => {
            formData.append("image[]", item.file);
        });
    }

    const result = await updateMedia(id, formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Informasi detail media telah diperbarui.",
            color: "success",
            icon: "i-lucide-circle-check",
        });
        isEditing.value = false;
        clearSelectedFiles();
        refresh();
    } else {
        toast.add({
            title: "Gagal memperbarui",
            description: result.error,
            color: "error",
            icon: "i-lucide-circle-alert",
        });
    }
};

function triggerDelete() {
    if (media.value?.id) {
        isDeleteModalOpen.value = true;
    }
}

function handleDeleteSuccess() {
    isDeleteModalOpen.value = false;
    toast.add({
        title: "Berhasil!",
        description: "Media telah berhasil dihapus dari sistem.",
        color: "success",
        icon: "i-lucide-circle-check",
    });
    router.push("/admin/media");
}

function handleDeleteError(errorMessage: string) {
    toast.add({
        title: "Gagal menghapus media",
        description:
            errorMessage || "Terjadi masalah saat memproses permintaan Anda.",
        color: "error",
        icon: "i-lucide-circle-alert",
    });
}
</script>
