<template>
    <UModal v-model:open="openModel" :title="title">
        <template #content>
            <div class="p-6 space-y-6">
                <div class="space-y-1">
                    <h3
                        class="text-lg font-semibold text-gray-950 dark:text-white"
                    >
                        Konfirmasi Hapus
                    </h3>
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus
                        <strong>{{ title || "data ini" }}</strong
                        >? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <UButton
                        label="Batal"
                        color="neutral"
                        variant="ghost"
                        @click="openModel = false"
                    />
                    <UButton
                        label="Hapus"
                        color="error"
                        :loading="loading"
                        @click="deleteItem"
                    />
                </div>
            </div>
        </template>
    </UModal>
</template>

<script setup lang="ts">
import { ref } from "vue";

const client = useSanctumClient();
const loading = ref(false);

const props = defineProps({
    title: String,
    id: [Number, String],
    endpoint: String,
});

const openModel = defineModel<boolean>("open", { default: false });

const emit = defineEmits(["success", "error"]);

async function deleteItem() {
    console.log("Mencoba menghapus dengan data:", {
        id: props.id,
        endpoint: props.endpoint,
    });

    if (!props.id || !props.endpoint) {
        console.error(
            "Gagal menghapus: properti 'id' atau 'endpoint' tidak didefinisikan!",
        );
        return;
    }

    loading.value = true;
    try {
        const cleanEndpoint = props.endpoint.replace(/\/$/, "");

        await client(`${cleanEndpoint}/${props.id}`, {
            method: "DELETE",
        });

        emit("success");
        openModel.value = false;
    } catch (err: any) {
        console.error("Terjadi error saat request API:", err);

        const backendMessage =
            err.data?.message || err.response?._data?.message || err.message;
        emit("error", backendMessage);
    } finally {
        loading.value = false;
    }
}
</script>
