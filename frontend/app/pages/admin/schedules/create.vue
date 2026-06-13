<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Tambah Jadwal"
            icon="i-lucide-calendar-plus"
            parentRoute="/admin/schedules"
            childTitle="Tambah Baru"
            description="Tambahkan data jadwal kegiatan baru ke dalam sistem."
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <UButton
                        color="neutral"
                        variant="ghost"
                        icon="i-lucide-x"
                        :disabled="isSubmitting"
                        @click="router.push('/admin/schedules')"
                        >Batal</UButton
                    >
                    <UButton
                        color="primary"
                        variant="solid"
                        icon="i-lucide-save"
                        :loading="isSubmitting"
                        @click="handleCreate"
                        >Simpan Jadwal</UButton
                    >
                </div>
            </template>
        </AdminHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 items-start">
            <div
                class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-5 sticky top-6"
            >
                <h4 class="font-semibold mb-2">Instruksi</h4>
                <p class="text-sm text-gray-500">
                    Masukkan detail jadwal dengan lengkap. Pastikan tanggal
                    mulai dan tanggal selesai diisi dengan benar.
                </p>
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
                                placeholder="Contoh: Pembukaan..."
                                size="lg"
                            />
                        </UFormField>
                        <UFormField
                            label="Title (English)"
                            name="title_en"
                            required
                        >
                            <UInput
                                v-model="form.title_en"
                                placeholder="Example: Opening..."
                                size="lg"
                            />
                        </UFormField>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <UFormField
                            label="Tanggal Mulai"
                            name="start_date"
                            required
                        >
                            <UInput
                                type="date"
                                v-model="form.start_date"
                                size="lg"
                            />
                        </UFormField>
                        <UFormField
                            label="Tanggal Selesai"
                            name="end_date"
                            required
                        >
                            <UInput
                                type="date"
                                v-model="form.end_date"
                                size="lg"
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
                        label="Description (English)"
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
const { createSchedule, isSubmitting } = useSchedules();

const form = reactive({
    title_id: "",
    title_en: "",
    description_id: "",
    description_en: "",
    start_date: "",
    end_date: "",
});

const handleCreate = async () => {
    const formData = new FormData();
    Object.entries(form).forEach(([key, value]) => {
        formData.append(key, value);
    });

    const result = await createSchedule(formData);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Jadwal baru telah ditambahkan.",
            color: "success",
        });
        router.push("/admin/schedules");
    } else {
        toast.add({
            title: "Gagal",
            description: result.error || "Terjadi kesalahan.",
            color: "danger",
        });
    }
};
</script>
