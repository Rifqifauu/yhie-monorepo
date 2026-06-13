<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Media"
            icon="i-lucide-images"
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
                            imagePreview || coverOf(media) || '/placeholder.png'
                        "
                        alt="Preview Media"
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
                            label="Kategori"
                            name="category"
                            required
                            help="Gunakan koma untuk memisahkan jika lebih dari satu."
                        >
                            <UInput
                                v-model="form.category"
                                placeholder="Contoh: galeri, edukasi, beranda"
                                icon="i-lucide-folder"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Ganti Gambar (Opsional)"
                            name="image"
                            help="Pilih berkas baru jika ingin mengganti gambar saat ini."
                        >
                            <input
                                type="file"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-gray-800 dark:file:text-gray-300 cursor-pointer border border-gray-200 dark:border-gray-700 rounded-md p-1 bg-gray-50 dark:bg-gray-950"
                                @change="onFileChange"
                            />
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
                </form>

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

definePageMeta({
    layout: "admin",
});

const id = useRoute().params.id as string;
const router = useRouter();
const toast = useToast();

const { fetchDetail, updateMedia, isSubmitting, coverOf } = useGallery();

// Request detail media dari API (Top-Level Await untuk SSR)
const { data: apiResponse, pending, refresh } = await fetchDetail(id);

// FIXED: Menggunakan computed agar data selalu reaktif saat refresh() dipicu
const media = computed(() => apiResponse.value?.data);

// UI States
const isEditing = ref(false);
const isDeleteModalOpen = ref(false);

// Image Upload States
const selectedFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

// Form Reactivity State
const form = reactive({
    title_id: "",
    title_en: "",
    category: "",
    description_id: "",
    description_en: "",
});

// Masuk ke Mode Pengeditan dan Suntik Data Lama ke Form
function startEdit() {
    if (!media.value) return;

    form.title_id = media.value.title_id || "";
    form.title_en = media.value.title_en || "";
    form.category = media.value.category || "";
    form.description_id = media.value.description_id || "";
    form.description_en = media.value.description_en || "";

    selectedFile.value = null;
    imagePreview.value = null;
    isEditing.value = true;
}

// Batalkan Mode Pengeditan
function cancelEdit() {
    isEditing.value = false;
    selectedFile.value = null;
    imagePreview.value = null;
}

// Deteksi pemilihan file gambar lokal untuk pratinjau biner
function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        selectedFile.value = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

// Eksekusi Update Menggunakan FormData via Composable
const handleUpdate = async () => {
    const formData = new FormData();
    formData.append("title_id", form.title_id);
    formData.append("title_en", form.title_en);
    formData.append("category", form.category);
    formData.append("description_id", form.description_id);
    formData.append("description_en", form.description_en);

    if (selectedFile.value) {
        formData.append("image", selectedFile.value);
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
        imagePreview.value = null;
        selectedFile.value = null;
        refresh(); // Otomatis memicu update pada computed 'media'
    } else {
        toast.add({
            title: "Gagal memperbarui",
            description: result.error,
            color: "danger",
            icon: "i-lucide-circle-alert",
        });
    }
};

// Pemicu Modal Penghapusan Data
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
        color: "danger",
        icon: "i-lucide-circle-alert",
    });
}
</script>
