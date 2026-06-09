<template>
    <div class="space-y-6 flex flex-col h-full mx-auto w-full">
        <AdminHeader
            title="Pengaturan"
            icon="i-lucide-cog"
            description="Atur informasi situs, kontak, dan konten 'About'."
        />

        <UCard
            class="bg-white dark:bg-slate-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm"
        >
            <div class="p-6">
                <form
                    @submit.prevent="saveSettings"
                    class="grid gap-4 md:grid-cols-2"
                >
                    <label class="space-y-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Nama Situs</span
                        >
                        <UInput
                            v-model="form.siteName"
                            placeholder="Nama situs"
                        />
                    </label>

                    <label class="space-y-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Email Kontak</span
                        >
                        <UInput
                            v-model="form.contactEmail"
                            placeholder="email@domain.tld"
                        />
                    </label>

                    <label class="space-y-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Deskripsi Situs</span
                        >
                        <textarea
                            v-model="form.siteDescription"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"
                        />
                    </label>

                    <label class="space-y-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >No. Telepon</span
                        >
                        <UInput
                            v-model="form.contactPhone"
                            placeholder="+62 8xx-xxxx-xxxx"
                        />
                    </label>

                    <label class="space-y-2 md:col-span-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Tentang - Sejarah</span
                        >
                        <textarea
                            v-model="form.aboutHistory"
                            rows="5"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"
                        />
                    </label>

                    <label class="space-y-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Visi</span
                        >
                        <textarea
                            v-model="form.aboutVision"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"
                        />
                    </label>

                    <label class="space-y-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Misi</span
                        >
                        <textarea
                            v-model="form.aboutMission"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"
                        />
                    </label>

                    <label class="space-y-2 md:col-span-2">
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Nomor WA (untuk tautan WA)</span
                        >
                        <UInput
                            v-model="form.waNumber"
                            placeholder="62812xxxx"
                        />
                    </label>
                </form>

                <div class="mt-4 flex justify-end gap-3">
                    <UButton
                        color="neutral"
                        variant="soft"
                        @click="refreshSettings"
                        >Muat ulang</UButton
                    >
                    <UButton
                        color="primary"
                        :loading="saving"
                        @click="saveSettings"
                        >Simpan perubahan</UButton
                    >
                </div>
            </div>
        </UCard>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";

definePageMeta({ layout: "admin" });

const {
    settings,
    isPending,
    error,
    getSetting,
    siteName,
    siteDescription,
    contactEmail,
    contactPhone,
    waNumber,
    aboutHistory,
    aboutVision,
    aboutMission,
    refresh,
} = useSettings();

const form = ref({
    siteName: "",
    siteDescription: "",
    contactEmail: "",
    contactPhone: "",
    waNumber: "",
    aboutHistory: "",
    aboutVision: "",
    aboutMission: "",
});

// sync form when settings load/refresh
watch(
    [
        siteName,
        siteDescription,
        contactEmail,
        contactPhone,
        waNumber,
        aboutHistory,
        aboutVision,
        aboutMission,
    ],
    () => {
        form.value.siteName = String(siteName.value ?? "");
        form.value.siteDescription = String(siteDescription.value ?? "");
        form.value.contactEmail = String(contactEmail.value ?? "");
        form.value.contactPhone = String(contactPhone.value ?? "");
        form.value.waNumber = String(waNumber.value ?? "");
        form.value.aboutHistory = String(aboutHistory.value ?? "");
        form.value.aboutVision = String(aboutVision.value ?? "");
        form.value.aboutMission = String(aboutMission.value ?? "");
    },
    { immediate: true },
);

const client = useSanctumClient();
const saving = ref(false);
const toast = useToast();

async function saveSettings() {
    saving.value = true;
    try {
        const payload = [
            { key: "site_name", value: form.value.siteName },
            { key: "site_description", value: form.value.siteDescription },
            { key: "contact_email", value: form.value.contactEmail },
            { key: "contact_phone", value: form.value.contactPhone },
            { key: "wa_number", value: form.value.waNumber },
            { key: "about_history_id", value: form.value.aboutHistory },
            { key: "about_vision_id", value: form.value.aboutVision },
            { key: "about_mission_id", value: form.value.aboutMission },
        ];

        await client("/api/settings", { method: "POST", body: payload });

        toast.add({
            title: "Berhasil",
            description: "Pengaturan berhasil disimpan.",
            color: "success",
            icon: "i-lucide-circle-check",
        });
        await refresh();
    } catch (e: any) {
        console.error(e);
        toast.add({
            title: "Gagal menyimpan",
            description: e?.message || "Terjadi kesalahan",
            color: "red",
            icon: "i-lucide-triangle-alert",
        });
    } finally {
        saving.value = false;
    }
}

function refreshSettings() {
    refresh();
    toast.add({
        title: "Dimuat ulang",
        description: "Pengaturan diperbarui dari server.",
        color: "info",
        icon: "i-lucide-rotate-cw",
    });
}
</script>
