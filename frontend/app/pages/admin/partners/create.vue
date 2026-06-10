<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Tambah Partner"
            icon="i-lucide-plus-circle"
            parentRoute="/admin/partners"
            childTitle="Tambah Baru"
            description="Tambahkan data partner baru ke dalam sistem YHIE Admin."
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UButton
                        color="neutral"
                        variant="ghost"
                        icon="i-lucide-x"
                        :disabled="isSubmitting"
                        @click="router.push('/admin/partners')"
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
                    >Logo Preview</span
                >
                <div
                    class="relative aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 p-2 flex items-center justify-center shadow-inner"
                >
                    <img
                        :src="imagePreview || '/placeholder.png'"
                        alt="Logo Preview"
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
                            label="Nama Partner (Indonesia)"
                            name="name_id"
                            required
                        >
                            <UInput
                                v-model="form.name_id"
                                placeholder="Nama partner..."
                                size="lg"
                            />
                        </UFormField>
                        <UFormField
                            label="Nama Partner (Inggris)"
                            name="name_en"
                            required
                        >
                            <UInput
                                v-model="form.name_en"
                                placeholder="Partner name..."
                                size="lg"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Slug URL (Indonesia)"
                            name="slug_id"
                            required
                        >
                            <UInput
                                v-model="form.slug_id"
                                placeholder="contoh-slug-id"
                                size="lg"
                            />
                        </UFormField>
                        <UFormField
                            label="Slug URL (Inggris)"
                            name="slug_en"
                            required
                        >
                            <UInput
                                v-model="form.slug_en"
                                placeholder="example-slug-en"
                                size="lg"
                            />
                        </UFormField>
                    </div>

                    <UFormField label="Logo Partner" name="logo" required>
                        <input
                            type="file"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 cursor-pointer border border-gray-200 rounded-md p-1"
                            @change="onFileChange"
                        />
                    </UFormField>

                    <div class="grid grid-cols-1 gap-6">
                        <UFormField
                            label="Deskripsi (Indonesia)"
                            name="description_id"
                        >
                            <UTextarea
                                v-model="form.description_id"
                                :rows="4"
                                size="lg"
                                required
                            />
                        </UFormField>
                        <UFormField
                            label="Deskripsi (Inggris)"
                            name="description_en"
                        >
                            <UTextarea
                                v-model="form.description_en"
                                :rows="4"
                                size="lg"
                                required
                            />
                        </UFormField>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: "admin" });

const router = useRouter();
const toast = useToast();
const { createPartner, isSubmitting } = usePartners(); // Pastikan Anda memiliki fungsi createPartner di composable

const imagePreview = ref<string | null>(null);
const selectedFile = ref<File | null>(null);

const form = reactive({
    name_id: "",
    name_en: "",
    description_id: "",
    description_en: "",
    slug_id: "",
    slug_en: "",
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
    Object.entries(form).forEach(([key, value]) => formData.append(key, value));

    if (selectedFile.value) {
        formData.append("logo", selectedFile.value);
    }

    const result = await createPartner(formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Partner baru telah dibuat.",
            color: "success",
        });
        router.push("/admin/partners");
    } else {
        toast.add({
            title: "Gagal",
            description: result.error,
            color: "danger",
        });
    }
};
</script>
