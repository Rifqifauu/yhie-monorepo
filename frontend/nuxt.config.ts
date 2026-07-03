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
  vite: {
    optimizeDeps: {
      include: [
        "@nuxt/ui > prosemirror-state",
        "@nuxt/ui > prosemirror-transform",
        "@nuxt/ui > prosemirror-model",
        "@nuxt/ui > prosemirror-view",
        "@nuxt/ui > prosemirror-gapcursor",
      ],
    },
  },

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

  css: ["~/assets/css/main.css"],

  // Pengaturan Dasar Utama Modul Nuxt Sanctum
  sanctum: {
    // Menyesuaikan dengan host lokal kamu saat development
    origin:
      process.env.NODE_ENV === "development"
        ? "https://local.sertifikasihafizh.xyz:3000"
        : "https://sertifikasihafizh.xyz",
    baseUrl: "https://api.sertifikasihafizh.xyz",
    mode: "cookie",
    redirectIfUnauthenticated: true,
    redirect: {
      keepRequestedRoute: true,
      onLogin: "/admin",
      onLogout: "/login",
      onAuthOnly: "/login",
      onGuestOnly: "/admin",
    },
  },
  // Menyalin trik Proxy dari project yang works
  routeRules: {
    "/sanctum/**": {
      proxy: "https://api.sertifikasihafizh.xyz/sanctum/**",
    },
    "/api/**": {
      proxy: "https://api.sertifikasihafizh.xyz/api/**",
      cors: true,
    },
  },

  // ==========================================
  // CONFIG ENVIROMENT: DEVELOPMENT (npm run dev)
  // ==========================================
  $development: {
    devtools: { enabled: true },

    // Agar tidak perlu ngetik --host dan --https lagi
    devServer: {
      host: "local.sertifikasihafizh.xyz",
      port: 3000, // Pastikan port ini kosong di Mac kamu
      https: true,
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
    devtools: { enabled: false },

    runtimeConfig: {
      public: {
        apiBase: "https://api.sertifikasihafizh.xyz/api",
      },
    },
  },
});
