<template>
    <UModal v-model:open="openModel" :title="title || 'Pratinjau Gambar'">
        <template #content>
            <div class="p-4">
                <img
                    v-if="src"
                    :src="src"
                    :alt="title || 'Pratinjau'"
                    class="w-full h-auto max-h-[75vh] object-contain rounded-lg"
                />
                <p v-else class="text-sm text-gray-500 text-center py-8">
                    Tidak ada gambar untuk ditampilkan.
                </p>
            </div>
        </template>
    </UModal>
</template>

<script setup lang="ts">
const openModel = defineModel<boolean>("open", { default: false });

defineProps<{
    src?: string | null;
    title?: string;
}>();
</script>
