<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Schedule"
            icon="i-lucide-calendar"
            parentRoute="/admin/schedules"
            :childTitle="isEditing ? 'Edit Jadwal' : 'Lihat Jadwal'"
            :description="
                isEditing
                    ? 'Ubah informasi jadwal di sini.'
                    : 'Lihat informasi detail jadwal.'
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
                            >Edit Jadwal</UButton
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
            <span class="text-sm text-gray-500">Memuat data schedule...</span>
        </div>

        <div
            v-else-if="schedule"
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 items-start"
        >
            <div
                class="bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-5 sticky top-6"
            >
                <h4 class="font-semibold mb-2">Info Jadwal</h4>
                <p class="text-sm text-gray-500">
                    Pastikan rentang tanggal sudah benar sebelum disimpan.
                </p>
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
                            <UInput v-model="form.title_id" size="lg" />
                        </UFormField>
                        <UFormField
                            label="Title (English)"
                            name="title_en"
                            required
                        >
                            <UInput v-model="form.title_en" size="lg" />
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
                </UForm>

                <div v-else class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Judul (ID)
                            </h3>
                            <p class="text-base font-semibold">
                                {{ schedule.title_id }}
                            </p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Title (EN)
                            </h3>
                            <p class="text-base font-medium text-gray-600">
                                {{ schedule.title_en }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Tanggal Mulai
                            </h3>
                            <p class="text-sm">{{ schedule.start_date }}</p>
                        </div>
                        <div>
                            <h3
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                            >
                                Tanggal Selesai
                            </h3>
                            <p class="text-sm">{{ schedule.end_date }}</p>
                        </div>
                    </div>

                    <hr />

                    <div>
                        <h3
                            class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                        >
                            Deskripsi (ID)
                        </h3>
                        <p class="text-sm bg-gray-50 p-4 rounded-lg">
                            {{ schedule.description_id }}
                        </p>
                    </div>
                    <div>
                        <h3
                            class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5"
                        >
                            Description (EN)
                        </h3>
                        <p class="text-sm bg-gray-50 p-4 rounded-lg">
                            {{ schedule.description_en }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <AdminDeleteModal
            v-model:open="isDeleteModalOpen"
            :id="schedule?.id"
            :title="schedule?.title_id || 'Jadwal'"
            endpoint="api/schedules"
            @success="handleDeleteSuccess"
            @error="handleDeleteError"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import { z } from "zod";

interface Schedule {
    id: number | string;
    title_id: string;
    description_id: string;
    title_en: string;
    description_en: string;
    start_date: string;
    end_date: string;
}

definePageMeta({ layout: "admin" });

const id = useRoute().params.id as string;
const router = useRouter();
const toast = useToast();
const { fetchDetail, updateSchedule, isSubmitting } = useSchedules();

const { data: apiResponse, pending, refresh } = await fetchDetail(id);
const schedule = computed(() => apiResponse.value?.data as Schedule);

const isEditing = ref(false);
const isDeleteModalOpen = ref(false);

const form = reactive<Schedule>({
    id: "",
    title_id: "",
    description_id: "",
    title_en: "",
    description_en: "",
    start_date: "",
    end_date: "",
});

function startEdit() {
    if (!schedule.value) return;
    Object.assign(form, schedule.value);
    isEditing.value = true;
}

function cancelEdit() {
    isEditing.value = false;
}

const schema = z
    .object({
        title_id: z.string().min(1, "Judul (ID) wajib diisi"),
        title_en: z.string().min(1, "Judul (EN) wajib diisi"),
        description_id: z.string(),
        description_en: z.string(),
        start_date: z.string().min(1, "Tanggal mulai wajib diisi"),
        end_date: z.string().min(1, "Tanggal selesai wajib diisi"),
    })
    .refine((data) => data.end_date >= data.start_date, {
        message: "Tanggal selesai harus sama atau setelah tanggal mulai",
        path: ["end_date"],
    });

const handleUpdate = async () => {
    const validation = schema.safeParse(form);
    if (!validation.success) {
        toast.add({
            title: "Data belum lengkap",
            description: validation.error.issues[0]?.message || "Periksa kembali form Anda.",
            color: "error",
        });
        return;
    }

    const formData = new FormData();
    // Loop object form ke FormData
    (Object.keys(form) as Array<keyof Schedule>).forEach((key) => {
        formData.append(key, form[key] as string);
    });

    const result = await updateSchedule(id, formData);
    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Jadwal diperbarui.",
            color: "success",
        });
        isEditing.value = false;
        refresh();
    } else {
        toast.add({
            title: "Gagal",
            description: result.error,
            color: "warning",
        });
    }
};

function triggerDelete() {
    isDeleteModalOpen.value = true;
}
function handleDeleteSuccess() {
    router.push("/admin/schedules");
}
function handleDeleteError(err: string) {
    toast.add({ title: "Error", description: err, color: "warning" });
}
</script>
