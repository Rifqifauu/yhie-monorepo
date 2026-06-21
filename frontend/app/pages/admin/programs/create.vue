<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Tambah Program"
            icon="i-lucide-plus-circle"
            parentRoute="/admin/programs"
            childTitle="Tambah Baru"
            description="Tambahkan data program baru ke dalam sistem."
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UButton
                        color="neutral"
                        variant="ghost"
                        icon="i-lucide-x"
                        :disabled="isSubmitting"
                        @click="router.push('/admin/programs')"
                        >Batal</UButton
                    >
                    <UButton
                        color="primary"
                        variant="solid"
                        icon="i-lucide-save"
                        :loading="isSubmitting"
                        @click="handleCreate"
                        >Simpan Program</UButton
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
                    >Preview Gambar</span
                >
                <div
                    class="relative aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                >
                    <img
                        :src="imagePreview || '/placeholder.jpg'"
                        alt="Preview Program"
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
                            label="Judul Program (ID)"
                            name="title_id"
                            required
                        >
                            <UInput
                                v-model="form.title_id"
                                placeholder="Nama program..."
                                size="lg"
                            />
                        </UFormField>
                        <UFormField
                            label="Title Program (EN)"
                            name="title_en"
                            required
                        >
                            <UInput
                                v-model="form.title_en"
                                placeholder="Program title..."
                                size="lg"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Slug URL (ID)"
                            name="slug_id"
                            required
                        >
                            <UInput
                                v-model="form.slug_id"
                                placeholder="slug-id"
                                size="lg"
                            />
                        </UFormField>
                        <UFormField
                            label="Slug URL (EN)"
                            name="slug_en"
                            required
                        >
                            <UInput
                                v-model="form.slug_en"
                                placeholder="slug-en"
                                size="lg"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField label="Harga (ID)" name="price_id">
                            <UInput
                                v-model="form.price_id"
                                placeholder="50000"
                                size="lg"
                            />
                        </UFormField>
                        <UFormField label="Price (EN)" name="price_en">
                            <UInput
                                v-model="form.price_en"
                                placeholder="5"
                                size="lg"
                            />
                        </UFormField>
                    </div>

                    <UFormField
                        label="Gambar Program"
                        name="image_path"
                        required
                    >
                        <input
                            type="file"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 cursor-pointer border border-gray-200 rounded-md p-1"
                            @change="onFileChange"
                        />
                    </UFormField>

                    <UFormField label="Deskripsi (ID)" name="description_id">
                        <UTextarea
                            v-model="form.description_id"
                            :rows="4"
                            size="lg"
                        />
                    </UFormField>
                    <UFormField label="Description (EN)" name="description_en">
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
const { createProgram, isSubmitting } = usePrograms();

const imagePreview = ref<string | null>(null);
const selectedFile = ref<File | null>(null);

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

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        selectedFile.value = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

const handleCreate = async () => {
    const formData = new FormData();
    Object.entries(form).forEach(([key, value]) => {
        if (value) formData.append(key, value);
    });

    if (selectedFile.value) {
        formData.append("image_path", selectedFile.value);
    }

    const result = await createProgram(formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Program baru telah dibuat.",
            color: "success",
        });
        router.push("/admin/programs");
    } else {
        toast.add({
            title: "Gagal",
            description: result.error,
            color: "danger",
        });
    }
};
</script>
