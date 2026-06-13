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
                            color="danger"
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
                        Gambar Artikel
                    </span>
                    <span
                        v-if="isEditing && imagePreview"
                        class="text-[10px] bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 px-2 py-0.5 rounded font-medium"
                    >
                        Berkas Baru
                    </span>
                </div>

                <div
                    class="relative group aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                >
                    <img
                        :src="
                            imagePreview ||
                            imageUrl(article?.image_path) ||
                            '/placeholder.png'
                        "
                        alt="Preview Artikel"
                        class="w-full h-full object-contain rounded-md transition-transform duration-200 group-hover:scale-[1.02]"
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

                    <!-- Baris 4: File Upload Gambar -->
                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Ganti Gambar Artikel (Opsional)"
                            name="image"
                            help="Pilih berkas baru jika ingin memperbarui gambar artikel saat ini."
                        >
                            <input
                                type="file"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-gray-800 dark:file:text-gray-300 cursor-pointer border border-gray-200 dark:border-gray-700 rounded-md p-1 bg-gray-50 dark:bg-gray-950"
                                @change="onFileChange"
                            />
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

                    <!-- Baris 5: Deskripsi Lengkap -->
                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Deskripsi (Bahasa Indonesia)"
                            name="description_id"
                        >
                            <UTextarea
                                v-model="form.description_id"
                                placeholder="Tulis deskripsi artikel lengkap dalam Bahasa Indonesia di sini..."
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
                                placeholder="Tulis deskripsi artikel lengkap dalam Bahasa Inggris di sini..."
                                :rows="4"
                                size="lg"
                                class="w-full"
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

                    <hr class="border-gray-100 dark:border-gray-800" />

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Konten (Indonesia)
                            </h3>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed bg-gray-50 dark:bg-gray-950/50 p-4 rounded-lg border border-gray-100 dark:border-gray-800"
                            >
                                {{ article?.content_id || "Tidak ada konten." }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Konten (Inggris)
                            </h3>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed bg-gray-50 dark:bg-gray-950/50 p-4 rounded-lg border border-gray-100 dark:border-gray-800"
                            >
                                {{ article?.content_en || "Tidak ada konten." }}
                            </p>
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

definePageMeta({
    layout: "admin",
});

const slug = useRoute().params.slug as string;
const router = useRouter();
const toast = useToast();

const { fetchDetail, updateArticle, isSubmitting, imageUrl } = useArticles();

const { data: apiResponse, pending, refresh } = await fetchDetail(slug);

const article = computed(() => apiResponse.value?.data);

const isEditing = ref(false);
const isDeleteModalOpen = ref(false);

const selectedFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

const form = reactive({
    title_id: "",
    title_en: "",
    content_id: "",
    content_en: "",
    slug_id: "",
    slug_en: "",
});

function startEdit() {
    if (!article.value) return;

    form.title_id = article.value.title_id || "";
    form.title_en = article.value.title_en || "";
    form.content_id = article.value.content_id || "";
    form.content_en = article.value.content_en || "";
    form.slug_id = article.value.slug_id || "";
    form.slug_en = article.value.slug_en || "";

    selectedFile.value = null;
    imagePreview.value = null;
    isEditing.value = true;
}

function cancelEdit() {
    isEditing.value = false;
    selectedFile.value = null;
    imagePreview.value = null;
}

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        selectedFile.value = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

const handleUpdate = async () => {
    const formData = new FormData();
    formData.append("title_id", form.title_id);
    formData.append("title_en", form.title_en);
    formData.append("slug_id", form.slug_id);
    formData.append("slug_en", form.slug_en);
    formData.append("content_id", form.content_id);
    formData.append("content_en", form.content_en);

    if (selectedFile.value) {
        formData.append("image", selectedFile.value);
    }

    const result = await updateArticle(id, formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Informasi detail artikel telah diperbarui.",
            color: "success",
            icon: "i-lucide-circle-check",
        });
        isEditing.value = false;
        imagePreview.value = null;
        selectedFile.value = null;
        refresh(); // Memperbarui data layar
    } else {
        toast.add({
            title: "Gagal memperbarui",
            description: result.error,
            color: "danger",
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
        color: "danger",
        icon: "i-lucide-circle-alert",
    });
}
</script>
