<template>
    <div>
        <AdminHeader
            title="Beranda"
            description="Pantau performa konten, aktivitas tim, dan status sistem dari satu tempat."
        />
        <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"></div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    layout: "admin",
});
</script>
