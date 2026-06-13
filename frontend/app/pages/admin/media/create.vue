<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Tambah Media"
            icon="i-lucide-plus-circle"
            parentRoute="/admin/media"
            childTitle="Tambah Baru"
            description="Tambahkan data media (galeri/konten) baru ke dalam sistem."
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UButton
                        color="neutral"
                        variant="ghost"
                        icon="i-lucide-x"
                        :disabled="isSubmitting"
                        @click="router.push('/admin/media')"
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
                <span
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                    >Pratinjau Berkas</span
                >
                <div
                    class="relative aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                >
                    <img
                        :src="imagePreview || '/placeholder.png'"
                        alt="Preview Media"
                        class="w-full h-full object-contain rounded-md"
                    />
                </div>
            </div>

            <div
                class="lg:col-span-2 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6"
            >
                <form @submit.prevent="handleCreate" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Judul (Bahasa Indonesia)"
                            name="title_id"
                            required
                        >
                            <UInput
                                v-model="form.title_id"
                                placeholder="Masukkan judul..."
                                size="lg"
                            />
                        </UFormField>
                        <UFormField
                            label="Judul (Bahasa Inggris)"
                            name="title_en"
                            required
                        >
                            <UInput
                                v-model="form.title_en"
                                placeholder="Enter title..."
                                size="lg"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Kategori"
                            name="category"
                            required
                            help="Gunakan koma untuk memisahkan kategori."
                        >
                            <UInput
                                v-model="form.category"
                                placeholder="Contoh: galeri, edukasi"
                                size="lg"
                            />
                        </UFormField>
                        <UFormField label="Pilih Gambar" name="image" required>
                            <input
                                type="file"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 cursor-pointer border border-gray-200 rounded-md p-1"
                                @change="onFileChange"
                            />
                        </UFormField>
                    </div>

                    <UFormField
                        label="Deskripsi (Bahasa Indonesia)"
                        name="description_id"
                    >
                        <UTextarea
                            v-model="form.description_id"
                            :rows="4"
                            size="lg"
                        />
                    </UFormField>
                    <UFormField
                        label="Deskripsi (Bahasa Inggris)"
                        name="description_en"
                    >
                        <UTextarea
                            v-model="form.description_en"
                            :rows="4"
                            size="lg"
                        />
                    </UFormField>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: "admin" });

const router = useRouter();
const toast = useToast();
// Pastikan fungsi createMedia sudah ada di useGallery()
const { createMedia, isSubmitting } = useGallery();

const imagePreview = ref<string | null>(null);
const selectedFile = ref<File | null>(null);

const form = reactive({
    title_id: "",
    title_en: "",
    category: "",
    description_id: "",
    description_en: "",
});

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        selectedFile.value = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

const handleCreate = async () => {
    if (!selectedFile.value) {
        toast.add({
            title: "Gagal",
            description: "Mohon pilih gambar media.",
            color: "danger",
        });
        return;
    }

    const formData = new FormData();
    Object.entries(form).forEach(([key, value]) => formData.append(key, value));
    formData.append("image", selectedFile.value);

    const result = await createMedia(formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Data media baru telah ditambahkan.",
            color: "success",
        });
        router.push("/admin/media");
    } else {
        toast.add({
            title: "Gagal",
            description: result.error || "Terjadi kesalahan.",
            color: "danger",
        });
    }
};
</script>
