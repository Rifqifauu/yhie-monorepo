// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },
  modules: ["@nuxt/ui", "@nuxt/image", "@nuxtjs/i18n", "nuxt-aos"],
  i18n: {
    lazy: true,
    langDir: "locales/",
    locales: [
      {
        code: "id",
        file: "id.json",
        name: "Indonesia",
      },
      {
        code: "en",
        file: "en.json",
        name: "English",
      },
    ],
    defaultLocale: "id",
    strategy: "prefix_except_default",
  },
  css: ["~/assets/css/main.css"],
  runtimeConfig: {
    public: {
      apiBase: "http://localhost:8000/api",
    },
  },
});