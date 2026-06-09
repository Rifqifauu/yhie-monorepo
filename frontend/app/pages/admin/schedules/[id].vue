<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Partner"
            icon="i-lucide-images"
            parentRoute="/admin/partners"
            :childTitle="isEditing ? 'Edit Partner' : 'Lihat Partner'"
            :description="
                isEditing
                    ? 'Ubah informasi partner dan perbarui berkas di sini.'
                    : 'Lihat informasi detail partner dan lakukan perubahan jika diperlukan.'
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
                            >Edit Partner</UButton
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
            <span class="text-sm text-gray-500">Memuat data partner...</span>
        </div>

        <div
            v-else-if="partner"
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 items-start"
        >
            <div
                class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-5 flex flex-col gap-4 sticky top-6"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                    >
                        Logo Partner
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
                            imageUrl(partner?.logo) ||
                            '/placeholder.png'
                        "
                        alt="Preview Partner"
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
                            label="Nama Partner (Bahasa Indonesia)"
                            name="name_id"
                            required
                        >
                            <UInput
                                v-model="form.name_id"
                                placeholder="Masukkan nama partner (Indonesia)"
                                icon="i-lucide-languages"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Nama Partner (Bahasa Inggris)"
                            name="name_en"
                            required
                        >
                            <UInput
                                v-model="form.name_en"
                                placeholder="Enter partner name (English)"
                                icon="i-lucide-globe"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Slug URL (Bahasa Indonesia)"
                            name="slug_id"
                            required
                        >
                            <UInput
                                v-model="form.slug_id"
                                placeholder="contoh-slug-partner-id"
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
                                placeholder="example-partner-slug-en"
                                icon="i-lucide-link"
                                size="lg"
                                class="w-full"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Ganti Logo Partner (Opsional)"
                            name="logo"
                            help="Pilih berkas baru jika ingin memperbarui logo partner saat ini."
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
                            >Deskripsi Profil</span
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
                                placeholder="Tulis deskripsi partner lengkap dalam Bahasa Indonesia di sini..."
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
                                placeholder="Write complete partner description in English here..."
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
                                Nama Partner (Indonesia)
                            </h3>
                            <p
                                class="text-base font-semibold text-gray-900 dark:text-white"
                            >
                                {{ partner?.name_id || "-" }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Nama Partner (Inggris)
                            </h3>
                            <p
                                class="text-base font-medium text-gray-600 dark:text-gray-300"
                            >
                                {{ partner?.name_en || "-" }}
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
                                {{ partner?.slug_id || "-" }}
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
                                {{ partner?.slug_en || "-" }}
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
                                    partner?.description_id ||
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
                                    partner?.description_en ||
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
            :id="partner?.id"
            :title="partner?.name_id || partner?.name_en || 'Partner ini'"
            endpoint="api/partners"
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

const { fetchDetail, updatePartner, isSubmitting, imageUrl } = usePartners();

// Meminta detail partner dari API via Composable
const { data: apiResponse, pending, refresh } = await fetchDetail(id);

const partner = computed(() => apiResponse.value?.data);

const isEditing = ref(false);
const isDeleteModalOpen = ref(false);

const selectedFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

const form = reactive({
    name_id: "",
    name_en: "",
    description_id: "",
    description_en: "",
    slug_id: "",
    slug_en: "",
});

function startEdit() {
    if (!partner.value) return;

    form.name_id = partner.value.name_id || "";
    form.name_en = partner.value.name_en || "";
    form.description_id = partner.value.description_id || "";
    form.description_en = partner.value.description_en || "";
    form.slug_id = partner.value.slug_id || "";
    form.slug_en = partner.value.slug_en || "";

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
    formData.append("name_id", form.name_id);
    formData.append("name_en", form.name_en);
    formData.append("slug_id", form.slug_id);
    formData.append("slug_en", form.slug_en);
    formData.append("description_id", form.description_id);
    formData.append("description_en", form.description_en);

    // FIXED: Mengirim berkas dengan key 'logo' sesuai backend Laravel Anda
    if (selectedFile.value) {
        formData.append("logo", selectedFile.value);
    }

    const result = await updatePartner(id, formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Informasi detail partner telah diperbarui.",
            color: "success",
            icon: "i-lucide-circle-check",
        });
        isEditing.value = false;
        imagePreview.value = null;
        selectedFile.value = null;
        refresh();
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
    if (partner.value?.id) {
        isDeleteModalOpen.value = true;
    }
}

function handleDeleteSuccess() {
    isDeleteModalOpen.value = false;
    toast.add({
        title: "Berhasil!",
        description: "Partner telah berhasil dihapus dari sistem.",
        color: "success",
        icon: "i-lucide-circle-check",
    });
    router.push("/admin/schedules");
}

function handleDeleteError(errorMessage: string) {
    toast.add({
        title: "Gagal menghapus schedule",
        description:
            errorMessage || "Terjadi masalah saat memproses permintaan Anda.",
        color: "danger",
        icon: "i-lucide-circle-alert",
    });
}
</script>
