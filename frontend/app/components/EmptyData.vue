<template>
    <div
        class="relative group flex flex-col items-center justify-center p-12 md:p-16 text-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-3xl bg-white/70 dark:bg-gray-900/70 transition-all duration-300 backdrop-blur-sm overflow-hidden"
    >
        <div
            class="absolute inset-0 z-0 opacity-10 dark:opacity-5 pointer-events-none"
            style="
                background-image: url(&quot;data:image/svg+xml,%3Csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23a1a1aa' fill-rule='evenodd'%3E%3Ccircle cx='1' cy='1' r='1'/%3E%3C/g%3E%3C/svg%3E&quot;);
                background-repeat: repeat;
            "
        ></div>
        <div
            class="relative z-10 mb-8 flex items-center justify-center w-36 h-36 group-hover:scale-110 group-hover:-translate-y-1 transition-all duration-300 ease-out"
        >
            <div
                class="absolute w-24 h-24 top-1 right-1 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-700 dark:to-emerald-800 opacity-70 rotate-15 shadow-lg"
            ></div>
            <div
                class="absolute w-28 h-28 p-6 rounded-3xl bg-white/30 dark:bg-gray-800/30 backdrop-blur-md border border-emerald-500/80 dark:border-gray-700/20 shadow-xl group-hover:shadow-2xl transition-all duration-300"
            >
                <div class="flex items-center justify-center w-full h-full">
                    <slot name="icon">
                        <UIcon
                            v-if="icon"
                            :name="icon"
                            class="w-12 h-12 md:w-14 md:h-14 text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors duration-300"
                        />
                    </slot>
                </div>
            </div>
        </div>
        <h3
            class="relative z-10 text-xl md:text-2xl font-extrabold text-gray-900 dark:text-white mb-3 tracking-tight"
        >
            {{ displayTitle }}
        </h3>
        <p
            v-if="description"
            class="relative z-10 text-sm md:text-base text-gray-500 dark:text-gray-400 max-w-sm leading-relaxed"
            :class="{ 'mb-10': $slots.action }"
        >
            {{ description }}
        </p>
        <div
            v-if="$slots.action"
            class="relative z-10 mt-2 transform group-hover:-translate-y-1 transition-transform duration-300"
        >
            <slot name="action" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, useSlots } from "vue";

interface Props {
    title?: string;
    customTitle?: string;
    description?: string;
    icon?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: "",
    customTitle: "",
    description:
        "Tidak ditemukan hasil yang cocok. Silakan periksa kembali filter Anda atau coba kata kunci lain.",
    icon: "i-heroicons-magnifying-glass",
});

const $slots = useSlots();

const displayTitle = computed(() => {
    if (props.customTitle) return props.customTitle;
    if (props.title) return `Tidak ada hasil untuk ${props.title}`;
    return "Tidak ditemukan data";
});
</script>
