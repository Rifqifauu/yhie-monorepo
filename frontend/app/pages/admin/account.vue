<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Akun Saya"
            icon="i-lucide-user-cog"
            description="Perbarui nama, email, dan password akun admin Anda."
            :showCreate="false"
        >
            <template #actions>
                <UButton
                    color="primary"
                    variant="solid"
                    icon="i-lucide-save"
                    :loading="isSubmitting"
                    @click="handleSubmit"
                >
                    Simpan Perubahan
                </UButton>
            </template>
        </AdminHeader>

        <div
            class="max-w-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm p-6"
        >
            <UForm
                :schema="schema"
                :state="form"
                class="space-y-6"
                @submit="handleSubmit"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <UFormField label="Nama" name="name" required>
                        <UInput v-model="form.name" size="lg" class="w-full" />
                    </UFormField>
                    <UFormField label="Email" name="email" required>
                        <UInput
                            v-model="form.email"
                            type="email"
                            size="lg"
                            class="w-full"
                        />
                    </UFormField>
                </div>

                <div class="relative flex py-2 items-center">
                    <div
                        class="flex-grow border-t border-gray-100 dark:border-gray-800"
                    ></div>
                    <span
                        class="flex-shrink mx-4 text-gray-400 text-xs font-semibold uppercase tracking-wider"
                        >Ubah Password (Opsional)</span
                    >
                    <div
                        class="flex-grow border-t border-gray-100 dark:border-gray-800"
                    ></div>
                </div>

                <UFormField
                    label="Password Saat Ini"
                    name="current_password"
                    help="Wajib diisi hanya jika ingin mengubah password."
                >
                    <UInput
                        v-model="form.current_password"
                        type="password"
                        size="lg"
                        class="w-full"
                    />
                </UFormField>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <UFormField label="Password Baru" name="password">
                        <UInput
                            v-model="form.password"
                            type="password"
                            size="lg"
                            class="w-full"
                        />
                    </UFormField>
                    <UFormField
                        label="Konfirmasi Password Baru"
                        name="password_confirmation"
                    >
                        <UInput
                            v-model="form.password_confirmation"
                            type="password"
                            size="lg"
                            class="w-full"
                        />
                    </UFormField>
                </div>
            </UForm>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import { z } from "zod";

definePageMeta({ layout: "admin" });

const toast = useToast();
const { user, isSubmitting, updateAccount } = useAccount();

const form = reactive({
    name: user.value?.name || "",
    email: user.value?.email || "",
    current_password: "",
    password: "",
    password_confirmation: "",
});

const schema = z
    .object({
        name: z.string().min(1, "Nama wajib diisi"),
        email: z.string().min(1, "Email wajib diisi").email("Format email tidak valid"),
        current_password: z.string(),
        password: z.string(),
        password_confirmation: z.string(),
    })
    .refine((data) => !data.password || data.password.length >= 8, {
        message: "Password baru minimal 8 karakter",
        path: ["password"],
    })
    .refine((data) => !data.password || data.current_password, {
        message: "Password saat ini wajib diisi untuk mengubah password",
        path: ["current_password"],
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: "Konfirmasi password tidak cocok",
        path: ["password_confirmation"],
    });

const handleSubmit = async () => {
    const validation = schema.safeParse(form);
    if (!validation.success) {
        toast.add({
            title: "Data belum lengkap",
            description:
                validation.error.issues[0]?.message ||
                "Periksa kembali form Anda.",
            color: "error",
        });
        return;
    }

    const payload: Record<string, string> = {
        name: form.name,
        email: form.email,
    };
    if (form.password) {
        payload.current_password = form.current_password;
        payload.password = form.password;
        payload.password_confirmation = form.password_confirmation;
    }

    const result = await updateAccount(payload);

    if (result.success) {
        toast.add({
            title: "Berhasil!",
            description: "Akun Anda telah diperbarui.",
            color: "success",
        });
        form.current_password = "";
        form.password = "";
        form.password_confirmation = "";
    } else {
        toast.add({
            title: "Gagal",
            description: result.error,
            color: "error",
        });
    }
};
</script>
