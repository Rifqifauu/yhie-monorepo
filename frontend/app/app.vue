<template>
    <UApp>
        <NuxtLayout>
            <NuxtPage />
        </NuxtLayout>
    </UApp>
</template>
<script setup lang="ts">
// Terapkan favicon & deskripsi SEO global dari Settings (key: favicon_url, meta_description).
const { faviconUrl, metaDescription } = useSettings();

useHead({
    link: computed(() =>
        faviconUrl.value
            ? [{ rel: "icon", href: faviconUrl.value }]
            : [],
    ),
    meta: computed(() =>
        metaDescription.value
            ? [{ name: "description", content: metaDescription.value }]
            : [],
    ),
});
</script>
