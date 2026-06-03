<template>
    <div v-if="totalPages > 1" class="flex justify-center mt-12">
        <div
            class="flex items-center gap-2 bg-white/80 dark:bg-emerald-950/50 border border-emerald-200/70 dark:border-emerald-800/60 rounded-2xl px-3 py-2 shadow-sm"
        >
            <UButton
                icon="i-lucide-chevron-left"
                variant="outline"
                :disabled="page === 1"
                @click="$emit('changePage', page - 1)"
            />
            <div
                v-for="(item, index) in pageItems"
                :key="`page-${index}`"
            >
                <span
                    v-if="item === 'ellipsis'"
                    class="w-9 h-9 grid place-items-center text-slate-400"
                >
                    ...
                </span>
                <button
                    v-else
                    class="w-9 h-9 rounded-xl text-sm font-semibold transition-colors"
                    :class="
                        item === page
                            ? 'bg-emerald-700 text-white'
                            : 'text-slate-600 hover:bg-emerald-100 dark:text-emerald-100/80 dark:hover:bg-emerald-900/60'
                    "
                    @click="$emit('changePage', item as number)"
                >
                    {{ item }}
                </button>
            </div>
            <UButton
                icon="i-lucide-chevron-right"
                variant="outline"
                :disabled="page === totalPages"
                @click="$emit('changePage', page + 1)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    page: number;
    totalPages: number;
    pageItems: Array<number | "ellipsis">;
}>();

defineEmits<{
    changePage: [page: number];
}>();
</script>
