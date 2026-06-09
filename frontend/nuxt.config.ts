export default defineNuxtConfig({
  // Menonaktifkan SSR secara global sesuai kebutuhan arsitektur SPA kamu
  ssr: false,

  compatibilityDate: "2025-07-15",

  modules: [
    "@nuxt/ui",
    "@nuxt/image",
    "@nuxtjs/i18n",
    "nuxt-aos",
    "nuxt-auth-sanctum",
  ],

  // Konfigurasi Lokalisasi Bahasa (i18n)
  i18n: {
    lazy: true,
    langDir: "locales/",
    locales: [
      { code: "id", file: "id.json", name: "Indonesia" },
      { code: "en", file: "en.json", name: "English" },
    ],
    defaultLocale: "id",
    strategy: "prefix_except_default",
  },

  // File CSS Global (Tailwind / Custom CSS)
  css: ["~/assets/css/main.css"],

  // Pengaturan Dasar Utama Modul Nuxt Sanctum
  sanctum: {
    baseUrl: "https://api.sertifikasihafizh.xyz", // Domain API Utama (Fallback)
    mode: "cookie", // 'cookie' untuk SPA + Sanctum bawaan, atau 'token' jika menggunakan API Token
    ssr: false,
    redirect: {
      keepRequestedRoute: true,
      onLogin: "/admin/dashboard", // Rute setelah sukses login
      onLogout: "/login", // Rute setelah sukses logout
      onAuthOnly: "/login", // Rute jika user tidak terautentikasi (Middleware)
    },
  },

  // ==========================================
  // CONFIG ENVIROMENT: DEVELOPMENT (npm run dev)
  // ==========================================
  $development: {
    devtools: { enabled: true },

    sanctum: {
      // Dipastikan konsisten menembak langsung ke server API agar useSanctumClient() tidak bingung
      baseUrl: "https://api.sertifikasihafizh.xyz",
    },

    runtimeConfig: {
      public: {
        apiBase: "https://api.sertifikasihafizh.xyz/api",
      },
    },
  },

  // ==========================================
  // CONFIG ENVIROMENT: PRODUCTION (Build & Generate)
  // ==========================================
  $production: {
    devtools: { enabled: false }, // Dimatikan demi performa & keamanan di server produksi

    sanctum: {
      baseUrl: "https://api.sertifikasihafizh.xyz",
    },

    runtimeConfig: {
      public: {
        apiBase: "https://api.sertifikasihafizh.xyz/api",
      },
    },
  },
});
