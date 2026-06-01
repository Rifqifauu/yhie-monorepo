<template>
    <div
        class="w-full mx-auto bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900 dark:to-emerald-800 px-4 py-8 border-t border-emerald-800 dark:border-emerald-100"
    >
        <h2
            class="text-3xl md:text-6xl font-bold font-serif mb-8 text-center text-gray-800 dark:text-gray-100"
        >
            {{ t("programs.title") }}
        </h2>

        <div
            v-if="status === 'pending'"
            class="flex flex-col items-center justify-center py-12 gap-4"
        >
            <UIcon
                name="i-heroicons-arrow-path"
                class="w-10 h-10 animate-spin text-primary"
            />
            <span class="text-sm text-gray-500">Memuat data...</span>
        </div>

        <div
            v-else-if="error"
            class="max-w-xl mx-auto my-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border"
        >
            <UAlert
                icon="i-heroicons-exclamation-triangle"
                color="red"
                variant="soft"
                title="Gagal memuat data"
                description="Terjadi kesalahan saat mengambil data program dari server."
            >
                <template #actions>
                    <UButton
                        size="sm"
                        color="red"
                        icon="i-heroicons-arrow-path-20-solid"
                        @click="refresh"
                    >
                        Coba Lagi
                    </UButton>
                </template>
            </UAlert>
        </div>

        <div v-else>
            <div
                v-if="data && data.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 gap-6"
            >
                <UCard
                    v-for="program in data"
                    :key="program.id"
                    class="transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                    :ui="{ body: { padding: 'px-6 py-5' } }"
                >
                    <template #header>
                        <h3
                            class="text-2xl font-bold font-serif text-gray-950 dark:text-white"
                        >
                            {{ program.name }}
                        </h3>
                    </template>

                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ program.description }}
                    </p>
                </UCard>
            </div>

            <div v-else class="text-center py-12">
                <UAlert
                    icon="i-heroicons-information-circle"
                    color="gray"
                    variant="soft"
                    title="Kosong"
                    description="Belum ada program yang tersedia saat ini."
                    class="max-w-md mx-auto"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
// Menggunakan i18n
const { t } = useI18n();

// Memanggil API menggunakan useAsyncData dan Sanctum
const client = useSanctumClient();
const { data, status, error, refresh } = await useAsyncData("programs", () =>
    client("/api/programs"),
);
</script>
