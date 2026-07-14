// Terapkan tema otomatis (malam = gelap, siang = terang) begitu app dimuat,
// kecuali user sudah pernah pilih tema manual lewat toggle (lihat useThemeMode).
export default defineNuxtPlugin(() => {
    const { applyAutoThemeIfNeeded } = useThemeMode();
    applyAutoThemeIfNeeded();
});
