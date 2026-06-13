<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Program"
            icon="i-lucide-images"
            parentRoute="/admin/programs"
            :childTitle="isEditing ? 'Edit Program' : 'Lihat Program'"
            :description="
                isEditing
                    ? 'Ubah informasi program dan perbarui berkas di sini.'
                    : 'Lihat informasi detail program dan lakukan perubahan jika diperlukan.'
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
                            >Edit Program</UButton
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
            <span class="text-sm text-gray-500">Memuat data program...</span>
        </div>

        <div
            v-else-if="program"
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 items-start"
        >
            <!-- Kolom Kiri: Gambar Utama Program -->
            <div
                class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-5 flex flex-col gap-4 sticky top-6"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                    >
                        Gambar Program
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
                    <!-- SESUAI INTERFACE: Menggunakan program?.image_path -->
                    <img
                        :src="
                            imagePreview ||
                            imageUrl(program?.image_path) ||
                            '/placeholder.png'
                        "
                        alt="Preview Program"
                        class="w-full h-full object-contain rounded-md transition-transform duration-200 group-hover:scale-[1.02]"
                    />
                </div>
            </div>

            <!-- Kolom Kanan: Detail Informasi & Form Input -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6"
            >
                <!-- MODE EDIT -->
                <form
                    v-if="isEditing"
                    @submit.prevent="handleUpdate"
                    class="space-y-6"
                >
                    <!-- Baris 1: Judul Program (ID & EN) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Judul Program (Bahasa Indonesia)"
                            name="title_id"
                            required
                        >
                            <UInput
                                v-model="form.title_id"
                                placeholder="Masukkan judul program (Indonesia)"
                                icon="i-lucide-languages"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Judul Program (Bahasa Inggris)"
                            name="title_en"
                            required
                        >
                            <UInput
                                v-model="form.title_en"
                                placeholder="Enter program title (English)"
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
                                placeholder="contoh-slug-program-id"
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
                                placeholder="example-program-slug-en"
                                icon="i-lucide-link"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <!-- Baris 3: Informasi Harga (ID & EN) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Harga (Bahasa Indonesia)"
                            name="price_id"
                        >
                            <UInput
                                v-model="form.price_id"
                                placeholder="Contoh: Gratis atau 50000"
                                icon="i-lucide-banknote"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Harga (Bahasa Inggris)"
                            name="price_en"
                        >
                            <UInput
                                v-model="form.price_en"
                                placeholder="Contoh: Free atau 5"
                                icon="i-lucide-dollar-sign"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <!-- Baris 4: File Upload Gambar -->
                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Ganti Gambar Program (Opsional)"
                            name="image"
                            help="Pilih berkas baru jika ingin memperbarui gambar program saat ini."
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
                            >Deskripsi Program</span
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
                                placeholder="Tulis deskripsi program lengkap dalam Bahasa Indonesia di sini..."
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
                                placeholder="Write complete program description in English here..."
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
                                Judul Program (Indonesia)
                            </h3>
                            <p
                                class="text-base font-semibold text-gray-900 dark:text-white"
                            >
                                {{ program?.title_id || "-" }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Judul Program (Inggris)
                            </h3>
                            <p
                                class="text-base font-medium text-gray-600 dark:text-gray-300"
                            >
                                {{ program?.title_en || "-" }}
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
                                {{ program?.slug_id || "-" }}
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
                                {{ program?.slug_en || "-" }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Harga (Indonesia)
                            </h3>
                            <p
                                class="text-base font-medium text-gray-900 dark:text-white"
                            >
                                Rp {{ program?.price_id || "-" }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Harga (Inggris)
                            </h3>
                            <p
                                class="text-base font-medium text-gray-600 dark:text-gray-300"
                            >
                                ${{ program?.price_en || "-" }}
                            </p>
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
                                    program?.description_id ||
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
                                    program?.description_en ||
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
            :id="program?.id"
            :title="program?.title_id || program?.title_en || 'Program ini'"
            endpoint="api/programs"
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

const { fetchDetail, updateProgram, isSubmitting, imageUrl } = usePrograms();

const { data: apiResponse, pending, refresh } = await fetchDetail(slug);

const program = computed(() => apiResponse.value?.data);

const isEditing = ref(false);
const isDeleteModalOpen = ref(false);

const selectedFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

// Form Reactive State disesuaikan dengan interface Program
const form = reactive({
    title_id: "",
    title_en: "",
    description_id: "",
    description_en: "",
    slug_id: "",
    slug_en: "",
    price_id: "",
    price_en: "",
});

function startEdit() {
    if (!program.value) return;

    // Menyuntikkan data Program lama ke form edit
    form.title_id = program.value.title_id || "";
    form.title_en = program.value.title_en || "";
    form.description_id = program.value.description_id || "";
    form.description_en = program.value.description_en || "";
    form.slug_id = program.value.slug_id || "";
    form.slug_en = program.value.slug_en || "";
    form.price_id = program.value.price_id?.toString() || "";
    form.price_en = program.value.price_en?.toString() || "";

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
    formData.append("description_id", form.description_id);
    formData.append("description_en", form.description_en);

    // Opsional appending untuk kolom harga
    if (form.price_id) formData.append("price_id", form.price_id);
    if (form.price_en) formData.append("price_en", form.price_en);

    // Mengirim biner file gambar program baru jika dipilih
    if (selectedFile.value) {
        formData.append("image", selectedFile.value);
    }

    const result = await updateProgram(id, formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Informasi detail program telah diperbarui.",
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
    if (program.value?.id) {
        isDeleteModalOpen.value = true;
    }
}

function handleDeleteSuccess() {
    isDeleteModalOpen.value = false;
    toast.add({
        title: "Berhasil!",
        description: "Program telah berhasil dihapus dari sistem.",
        color: "success",
        icon: "i-lucide-circle-check",
    });
    router.push("/admin/programs");
}

function handleDeleteError(errorMessage: string) {
    toast.add({
        title: "Gagal menghapus program",
        description:
            errorMessage || "Terjadi masalah saat memproses permintaan Anda.",
        color: "danger",
        icon: "i-lucide-circle-alert",
    });
}
</script>
