<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Article"
            icon="i-lucide-images"
            parentRoute="/admin/articles"
            :childTitle="isEditing ? 'Edit Article' : 'Lihat Article'"
            :description="
                isEditing
                    ? 'Ubah informasi artikel dan perbarui berkas di sini.'
                    : 'Lihat informasi detail artikel dan lakukan perubahan jika diperlukan.'
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
                            >Edit Article</UButton
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
            <span class="text-sm text-gray-500">Memuat data article...</span>
        </div>

        <div
            v-else-if="article"
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 items-start"
        >
            <div
                class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-5 flex flex-col gap-4 sticky top-6"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                    >
                        Pratinjau Gambar
                    </span>
                    <span
                        v-if="isEditing && imagePreviews.length > 0"
                        class="text-[10px] bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 px-2 py-0.5 rounded font-medium"
                    >
                        {{ imagePreviews.length }} Berkas Baru
                    </span>
                    <span
                        v-else-if="article?.image && article.image.length > 0"
                        class="text-[10px] bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 px-2 py-0.5 rounded font-medium"
                    >
                        {{ article.image.length }} Berkas Tersimpan
                    </span>
                </div>

                <!-- Preview file baru yang baru dipilih (mode edit) -->
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
                            :alt="`Preview Artikel ${index + 1}`"
                            class="w-full h-full object-contain rounded-md transition-transform duration-200 group-hover:scale-[1.02]"
                        />
                    </div>
                </div>

                <!-- Preview gambar lama dari server -->
                <div
                    v-else-if="article?.image && article.image.length > 0"
                    class="grid grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-1"
                >
                    <div
                        v-for="(img, index) in article.image"
                        :key="`old-${index}`"
                        class="relative group aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                    >
                        <img
                            :src="imageUrl(img.url || img.file_path || img)"
                            :alt="`Gambar Artikel ${index + 1}`"
                            class="w-full h-full object-contain rounded-md transition-transform duration-200 group-hover:scale-[1.02]"
                        />
                    </div>
                </div>

                <!-- Fallback kosong -->
                <div
                    v-else
                    class="relative group aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                >
                    <img
                        src="/placeholder.jpg"
                        alt="Preview Artikel"
                        class="w-full h-full object-contain rounded-md opacity-40 grayscale"
                    />
                </div>
            </div>

            <div
                class="lg:col-span-2 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6"
            >
                <form
                    v-if="isEditing"
                    @submit.prevent="handleUpdate"
                    class="space-y-6"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Judul Artikel (Bahasa Indonesia)"
                            name="title_id"
                            required
                        >
                            <UInput
                                v-model="form.title_id"
                                placeholder="Masukkan judul artikel (Indonesia)"
                                icon="i-lucide-languages"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Judul Artikel (Bahasa Inggris)"
                            name="title_en"
                            required
                        >
                            <UInput
                                v-model="form.title_en"
                                placeholder="Masukkan judul artikel (Inggris)"
                                icon="i-lucide-globe"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <!-- Baris 2: Slug URL (ID & EN) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Slug URL (Bahasa Indonesia)"
                            name="slug_id"
                            required
                        >
                            <UInput
                                v-model="form.slug_id"
                                placeholder="Masukkan slug URL (Indonesia)"
                                icon="i-lucide-link"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Slug URL (Bahasa Inggris)"
                            name="slug_en"
                            required
                        >
                            <UInput
                                v-model="form.slug_en"
                                placeholder="Masukkan slug URL (Inggris)"
                                icon="i-lucide-link"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <!-- Baris 3: Kategori -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Kategori"
                            name="category_id"
                            required
                        >
                            <USelect
                                v-model="form.category_id"
                                :items="categoryOptions"
                                placeholder="Pilih kategori..."
                                icon="i-lucide-folder"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Status Publikasi"
                            name="is_published"
                        >
                            <USwitch
                                v-model="form.is_published"
                                label="Publikasikan artikel ini"
                            />
                        </UFormField>
                    </div>

                    <!-- Baris 4: Upload Gambar Multiple -->
                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Unggah Gambar Baru (Maks 2MB/file)"
                            name="image"
                            help="Pilih satu atau beberapa gambar. Gambar baru akan menggantikan gambar lama jika diisi."
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

                    <!-- Pembatas Visual Section Deskripsi -->
                    <div class="relative flex py-2 items-center">
                        <div
                            class="flex-grow border-t border-gray-100 dark:border-gray-800"
                        ></div>
                        <span
                            class="flex-shrink mx-4 text-gray-400 text-xs font-semibold uppercase tracking-wider"
                            >Deskripsi Artikel</span
                        >
                        <div
                            class="flex-grow border-t border-gray-100 dark:border-gray-800"
                        ></div>
                    </div>

                    <!-- Baris 5: Konten Lengkap -->
                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Konten (Bahasa Indonesia)"
                            name="content_id"
                        >
                            <RichEditor
                                v-model="form.content_id"
                                placeholder="Tulis konten artikel lengkap dalam Bahasa Indonesia di sini..."
                                class="w-full"
                                editorClass="min-h-[200px] text-lg"
                            />
                        </UFormField>

                        <UFormField
                            label="Konten (Bahasa Inggris)"
                            name="content_en"
                        >
                            <RichEditor
                                v-model="form.content_en"
                                placeholder="Write complete article content in English here..."
                                class="w-full"
                                editorClass="min-h-[200px] text-lg"
                            />
                        </UFormField>
                    </div>
                </form>

                <!-- MODE READ-ONLY TAMPILAN DETAIL -->
                <div v-else class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Judul Artikel (Indonesia)
                            </h3>
                            <p
                                class="text-base font-semibold text-gray-900 dark:text-white"
                            >
                                {{ article?.title_id || "-" }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Judul Artikel (Inggris)
                            </h3>
                            <p
                                class="text-base font-medium text-gray-600 dark:text-gray-300"
                            >
                                {{ article?.title_en || "-" }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Slug URL (Indonesia)
                            </h3>
                            <span
                                class="text-xs font-mono font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 px-2.5 py-1 rounded-md inline-block"
                            >
                                {{ article?.slug_id || "-" }}
                            </span>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Slug URL (Inggris)
                            </h3>
                            <span
                                class="text-xs font-mono font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 px-2.5 py-1 rounded-md inline-block"
                            >
                                {{ article?.slug_en || "-" }}
                            </span>
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
                                {{ article?.category_id || "Uncategorized" }}
                            </span>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Status
                            </h3>
                            <span
                                :class="[
                                    'text-xs font-medium px-2.5 py-1 rounded-md inline-block',
                                    article?.is_published
                                        ? 'bg-success-50 text-success-700 dark:bg-success-950/40 dark:text-success-300 border border-success-100 dark:border-success-900'
                                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700',
                                ]"
                            >
                                {{
                                    article?.is_published
                                        ? "Published"
                                        : "Draft"
                                }}
                            </span>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800" />

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Konten (Indonesia)
                            </h3>
                            <div
                                class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-950/50 p-4 rounded-lg border border-gray-100 dark:border-gray-800 prose prose-sm dark:prose-invert max-w-none"
                                v-html="
                                    article?.content_id || 'Tidak ada konten.'
                                "
                            ></div>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Konten (Inggris)
                            </h3>
                            <div
                                class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-950/50 p-4 rounded-lg border border-gray-100 dark:border-gray-800 prose prose-sm dark:prose-invert max-w-none"
                                v-html="
                                    article?.content_en || 'Tidak ada konten.'
                                "
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <AdminDeleteModal
            v-model:open="isDeleteModalOpen"
            :id="article?.id"
            :title="article?.title_id || article?.title_en || 'Artikel ini'"
            endpoint="api/articles"
            @success="handleDeleteSuccess"
            @error="handleDeleteError"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import RichEditor from "~/components/RichEditor.vue";

definePageMeta({
    layout: "admin",
});

const slug = useRoute().params.slug as string;
const router = useRouter();
const toast = useToast();

const { fetchDetail, isSubmitting, imageUrl, updateArticle } = useArticles();

const { data: apiResponse, pending, refresh } = await fetchDetail(slug);

const article = computed(() => apiResponse.value?.data);

const isEditing = ref(false);
const isDeleteModalOpen = ref(false);

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
    content_id: "",
    content_en: "",
    slug_id: "",
    slug_en: "",
    category_id: "",
    is_published: false,
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
    if (!article.value) return;

    form.title_id = article.value.title_id || "";
    form.title_en = article.value.title_en || "";
    form.content_id = article.value.content_id || "";
    form.content_en = article.value.content_en || "";
    form.slug_id = article.value.slug_id || "";
    form.slug_en = article.value.slug_en || "";
    form.category_id = article.value.category_id || "";
    form.is_published = !!article.value.is_published;

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
    if (fileInputRef.value) fileInputRef.value.value = "";
}

function triggerFileInput() {
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
}

// Validasi ukuran file
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

function removeFile(index: number) {
    const removedItem = selectedFiles.value[index];
    URL.revokeObjectURL(removedItem.preview);
    selectedFiles.value.splice(index, 1);
}

const handleUpdate = async () => {
    const formData = new FormData();
    formData.append("title_id", form.title_id);
    formData.append("title_en", form.title_en);
    formData.append("slug_id", form.slug_id);
    formData.append("slug_en", form.slug_en);
    formData.append("content_id", form.content_id);
    formData.append("content_en", form.content_en);
    formData.append("category_id", form.category_id);
    formData.append("is_published", form.is_published ? "1" : "0");

    // Untuk update via FormData, Laravel butuh method spoofing
    formData.append("_method", "PUT");

    if (selectedFiles.value.length > 0) {
        selectedFiles.value.forEach((item) => {
            formData.append("image[]", item.file);
        });
    }

    const result = await updateArticle(article.value?.id, formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Informasi detail artikel telah diperbarui.",
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
    if (article.value?.id) {
        isDeleteModalOpen.value = true;
    }
}

function handleDeleteSuccess() {
    isDeleteModalOpen.value = false;
    toast.add({
        title: "Berhasil!",
        description: "Artikel telah berhasil dihapus dari sistem.",
        color: "success",
        icon: "i-lucide-circle-check",
    });
    router.push("/admin/articles");
}

function handleDeleteError(errorMessage: string) {
    toast.add({
        title: "Gagal menghapus artikel",
        description:
            errorMessage || "Terjadi masalah saat memproses permintaan Anda.",
        color: "error",
        icon: "i-lucide-circle-alert",
    });
}
</script>
