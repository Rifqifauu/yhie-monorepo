// Mode tema: "auto" (ikut waktu - malam gelap, siang terang) atau "manual"
// (user sudah pilih sendiri lewat toggle, jangan pernah ditimpa otomatis lagi).
const STORAGE_KEY = "yhie-theme-mode";
const NIGHT_START_HOUR = 18;
const NIGHT_END_HOUR = 6;

const isNightNow = () => {
    const hour = new Date().getHours();
    return hour >= NIGHT_START_HOUR || hour < NIGHT_END_HOUR;
};

export const useThemeMode = () => {
    const colorMode = useColorMode();

    // Dipanggil sekali saat app dimuat - kalau user belum pernah pilih manual,
    // set tema sesuai jam saat ini. Kalau sudah pernah manual, biarkan.
    const applyAutoThemeIfNeeded = () => {
        if (!import.meta.client) return;
        const mode = localStorage.getItem(STORAGE_KEY);
        if (mode === "manual") return;

        colorMode.preference = isNightNow() ? "dark" : "light";
    };

    // Dipanggil dari tombol toggle - begitu user pilih manual, auto-theme
    // berhenti nimpa pilihan itu di kunjungan berikutnya.
    const toggleTheme = () => {
        if (import.meta.client) {
            localStorage.setItem(STORAGE_KEY, "manual");
        }
        colorMode.preference = colorMode.value === "dark" ? "light" : "dark";
    };

    return { colorMode, applyAutoThemeIfNeeded, toggleTheme };
};
