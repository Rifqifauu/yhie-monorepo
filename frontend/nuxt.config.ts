export default defineNuxtConfig({
  ssr: false,

  compatibilityDate: "2025-07-15",

  modules: [
    "@nuxt/ui",
    "@nuxt/image",
    "@nuxtjs/i18n",
    "nuxt-aos",
    "nuxt-auth-sanctum",
  ],

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

  css: ["~/assets/css/main.css"],

  // Pengaturan dasar Sanctum (yang tidak berubah antar environment)
  sanctum: {
    ssr: false,
  },

  // ==========================================
  // KHUSUS DEVELOPMENT (Lokal / npm run dev)
  // ==========================================
  $development: {
    devtools: { enabled: true },

    routeRules: {
      "/sanctum/**": { proxy: "https://api.sertifikasihafizh.xyz/sanctum/**" },
      "/api/**": { proxy: "https://api.sertifikasihafizh.xyz/api/**" },
    },

    sanctum: {
      baseUrl: "http://localhost:3000",
    },

    runtimeConfig: {
      public: {
        apiBase: "http://localhost:3000/api",
      },
    },
  },
  // ==========================================
  // KHUSUS PRODUCTION (Server / npx nuxi generate)
  // ==========================================
  $production: {
    devtools: { enabled: false }, // Dimatikan demi performa & keamanan di production

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
