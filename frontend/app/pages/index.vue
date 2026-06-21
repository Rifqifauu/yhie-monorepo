<template>
    <HeroSection />
    <ProgramsSection />
    <BenefitSection />
    <GallerySection />
    <ScheduleSection />
    <ArticlesSection />
    <PartnersSection />
    <CTASection />
</template>
<script setup>
const { t, tm, rt } = useI18n();
const localePath = useLocalePath();
useSeoMeta({
    title: () => t("hero.seoTitle"),
    description: () => t("hero.seoDesc"),
    ogTitle: () => t("hero.seoTitle"),
    ogDescription: () => t("hero.seoDesc"),
    ogImage: "/quran.webp",
    ogType: "website",
    keywords: () => t("hero.keywords"),
});
</script>
