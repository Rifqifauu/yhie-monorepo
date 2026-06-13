<template>
    <div>
        <!-- Loading -->
        <div
            v-if="pending"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <USkeleton v-for="n in 6" :key="n" class="h-80 rounded-3xl" />
        </div>

        <!-- Error -->
        <UAlert
            v-else-if="error"
            icon="i-heroicons-exclamation-triangle"
            color="red"
            variant="soft"
            title="Gagal memuat program"
            description="Silakan coba lagi beberapa saat."
            class="max-w-xl mx-auto"
        >
            <template #actions>
                <UButton size="sm" color="red" @click="$emit('retry')">
                    Coba lagi
                </UButton>
            </template>
        </UAlert>

        <!-- Empty -->
        <div v-else-if="!programs.length" class="text-center py-16">
            <EmptyData
                title="Program"
                description="Belum ada program untuk kata kunci ini."
            />
        </div>

        <!-- Grid -->
        <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    pending: boolean;
    error: any;
    programs: any[];
}>();

defineEmits<{
    retry: [];
}>();
</script>
