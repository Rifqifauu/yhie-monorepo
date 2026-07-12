<template>
    <NuxtLink
        :to="to"
        class="group relative overflow-hidden rounded-3xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/70 dark:bg-emerald-950/40 shadow-sm hover:-translate-y-1 hover:shadow-2xl transition-all"
    >
        <!-- Image -->
        <div class="relative aspect-[4/3] overflow-hidden">
            <NuxtImg
                v-if="imageSrc"
                :src="imageSrc"
                :alt="title"
                width="640"
                height="480"
                format="webp"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div
                v-else
                class="w-full h-full flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/60"
            >
                <UIcon
                    name="i-lucide-image"
                    class="w-10 h-10 text-emerald-500"
                />
            </div>
            <div
                class="absolute inset-0 bg-gradient-to-t from-emerald-950/40 via-transparent to-transparent"
            ></div>
            <span
                class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold bg-white/80 text-emerald-800"
            >
                Program
            </span>
        </div>

        <!-- Content -->
        <div class="p-5">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-emerald-600 dark:text-emerald-300">
                    {{ locale === "en" ? "Price" : "Harga" }}
                </span>
                <span
                    class="font-semibold text-emerald-800 dark:text-amber-300"
                >
                    {{ price }}
                </span>
            </div>
            <h3
                class="text-xl font-serif font-bold text-slate-900 dark:text-emerald-50 line-clamp-2"
            >
                {{ title }}
            </h3>
            <p
                class="mt-2 text-sm text-slate-600 dark:text-emerald-100/70 line-clamp-3"
            >
                {{ description }}
            </p>
            <div
                class="mt-4 flex items-center justify-between text-sm font-semibold text-emerald-700"
            >
                <span class="dark:text-emerald-200">{{
                    locale === "en" ? "View details" : "Lihat detail"
                }}</span>
                <UIcon name="i-lucide-arrow-right" class="w-4 h-4" />
            </div>
        </div>
    </NuxtLink>
</template>

<script setup lang="ts">
const { locale } = useI18n();

defineProps<{
    to: string;
    imageSrc: string;
    title: string;
    description: string;
    price: string;
}>();
</script>
