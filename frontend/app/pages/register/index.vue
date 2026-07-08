<template></template>
<script setup lang="ts">
// Pendaftaran selalu terikat ke program tertentu (butuh program_id),
// jadi arahkan ke daftar program agar pengguna memilih program dulu.
const localePath = useLocalePath();
await navigateTo(localePath("/program"), { replace: true });
</script>
