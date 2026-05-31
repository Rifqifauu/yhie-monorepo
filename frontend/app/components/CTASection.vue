<template>
    <div class="relative bg-emerald-900 z-10 mx-auto w-full">
        <div
            class="max-w-7xl mx-auto p-4 md:p-8 lg:p-12 text-center lg:text-left"
        >
            <h2
                class="text-3xl md:text-4xl font-serif font-bold text-white mb-4"
            >
                {{ t("cta.title") }}
            </h2>

            <p class="text-emerald-100 max-w-2xl mb-8 text-lg">
                {{ t("cta.description") }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
const { t } = useI18n();
const localePath = useLocalePath();
</script>
