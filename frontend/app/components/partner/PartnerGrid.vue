<template>
    <div>
        <!-- Loading -->
        <div v-if="pending" class="space-y-4">
            <USkeleton
                v-for="n in 5"
                :key="n"
                class="h-28 rounded-2xl"
            />
        </div>

        <!-- Error -->
        <UAlert
            v-else-if="error"
            icon="i-heroicons-exclamation-triangle"
            color="error"
            variant="soft"
            :title="locale === 'en' ? 'Failed to load partners' : 'Gagal memuat data partner'"
            :description="locale === 'en' ? 'Please try again later.' : 'Silakan coba lagi beberapa saat.'"
            class="max-w-xl mx-auto"
        >
            <template #actions>
                <UButton size="sm" color="error" @click="$emit('retry')">
                    {{ locale === 'en' ? 'Retry' : 'Coba lagi' }}
                </UButton>
            </template>
        </UAlert>

        <!-- Empty -->
        <div v-else-if="!partners.length" class="text-center py-16">
            <div
                class="mx-auto w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center"
            >
                <UIcon
                    name="i-lucide-building-2"
                    class="w-8 h-8 text-emerald-600"
                />
            </div>
            <p class="mt-4 text-slate-500 dark:text-emerald-100/70">
                {{ locale === 'en' ? 'No partners found for this keyword.' : 'Belum ada mitra untuk kata kunci ini.' }}
            </p>
        </div>

        <!-- List -->
        <div v-else class="space-y-4">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
const { locale } = useI18n();

defineProps<{
    pending: boolean;
    error: any;
    partners: any[];
}>();

defineEmits<{
    retry: [];
}>();
</script>
