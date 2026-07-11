<template>
    <div class="bg-slate-50 dark:bg-emerald-950 text-slate-900 dark:text-white">
        <ProgramHero
            :page="page"
            :total-pages="totalPages"
            :total-items="totalItems"
            :from-item="fromItem"
            :to-item="toItem"
            :search-input="searchInput"
            :search-term="searchTerm"
            @update:search-input="searchInput = $event"
            @search="applySearch"
            @clear-search="clearSearch"
        />

        <section class="py-14 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <ProgramGrid
                    :pending="pending"
                    :error="error"
                    :programs="programs"
                    @retry="refresh"
                >
                    <ProgramCard
                        v-for="(program, index) in programs"
                        :key="program.id"
                        :data-aos="'fade-up'"
                        :data-aos-delay="(index % 6) * 80"
                        :to="localePath(`/program/${slugOf(program)}`)"
                        :image-src="imageUrl(program.image_path)"
                        :title="titleOf(program)"
                        :description="descOf(program)"
                        :price="formatPrice(priceOf(program))"
                    />
                </ProgramGrid>

                <ProgramPagination
                    :page="page"
                    :total-pages="totalPages"
                    :page-items="pageItems"
                    @change-page="changePage"
                />
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
const localePath = useLocalePath();

const {
    page,
    searchInput,
    searchTerm,
    programs,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pending,
    error,
    pageItems,
    titleOf,
    descOf,
    slugOf,
    priceOf,
    imageUrl,
    formatPrice,
    applySearch,
    clearSearch,
    changePage,
    refresh,
} = usePrograms();

useSeoMeta({
    title: "Programs - YHIE",
    description:
        "Daftar program Hafiz dan Maulana dengan pencarian dan pagination.",
    ogTitle: "Programs - YHIE",
    ogDescription:
        "Temukan program Hafiz dan Maulana yang sesuai dengan kebutuhan.",
});
</script>
