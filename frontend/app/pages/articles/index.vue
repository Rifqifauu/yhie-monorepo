<template>
  <div>

    <!-- ─── PAGE HEADER ───────────────────────────────────────────────────── -->
    <section class="bg-blue-900 text-white py-16">
      <UContainer>
        <div class="max-w-2xl">
          <p class="text-blue-300 text-sm font-semibold uppercase tracking-widest mb-3">
            Berita & Artikel
          </p>
          <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Informasi & <span class="text-yellow-400">Kabar Terbaru</span>
          </h1>
          <p class="text-blue-100 text-lg leading-relaxed">
            Liputan kegiatan yayasan, tulisan keagamaan, dan informasi wisata
            Desa Tejasari terkini.
          </p>
        </div>
      </UContainer>
    </section>

    <!-- ─── FILTER KATEGORI ───────────────────────────────────────────────── -->
    <section class="bg-white border-b border-gray-100 sticky top-16 z-40">
      <UContainer>
        <div class="flex items-center gap-2 py-3 overflow-x-auto scrollbar-none">
          <button
            v-for="cat in categories"
            :key="cat.value"
            class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors"
            :class="activeCategory === cat.value
              ? 'bg-blue-900 text-white'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
            @click="setCategory(cat.value)"
          >
            {{ cat.label }}
          </button>
        </div>
      </UContainer>
    </section>

    <!-- ─── ARTICLE LIST ──────────────────────────────────────────────────── -->
    <section class="py-16 bg-gray-50">
      <UContainer>

        <!-- Loading -->
        <div v-if="pending" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <USkeleton v-for="n in 9" :key="n" class="h-72 rounded-xl" />
        </div>

        <!-- Error -->
        <UAlert
          v-else-if="error"
          icon="i-heroicons-exclamation-triangle"
          color="error"
          title="Gagal memuat artikel."
          description="Silakan coba beberapa saat lagi."
          class="max-w-md mx-auto"
        />

        <!-- Empty -->
        <div v-else-if="!articles.length" class="text-center py-20 text-gray-400">
          <UIcon name="i-heroicons-newspaper" class="w-12 h-12 mx-auto mb-3" />
          <p>Belum ada artikel di kategori ini.</p>
        </div>

        <!-- Data -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <NuxtLink
            v-for="article in articles"
            :key="article.id"
            :to="`/articles/${article.slug_id}`"
            class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-200"
          >
            <!-- Gambar -->
            <div class="h-48 bg-blue-100 overflow-hidden">
              <img
                v-if="article.image?.url"
                :src="article.image.url"
                :alt="article.title_id"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <UIcon name="i-heroicons-newspaper" class="w-14 h-14 text-blue-200" />
              </div>
            </div>

            <!-- Konten -->
            <div class="p-5">
              <!-- Kategori + Tanggal -->
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-blue-700 bg-blue-50 px-2 py-1 rounded-full">
                  {{ article.category }}
                </span>
                <span class="text-xs text-gray-400">
                  {{ formatDate(article.created_at) }}
                </span>
              </div>

              <h2 class="font-bold text-gray-900 text-lg leading-snug mb-2 line-clamp-2 group-hover:text-blue-800 transition-colors">
                {{ article.title_id }}
              </h2>

              <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">
                {{ stripHtml(article.content_id) }}
              </p>

              <p class="mt-4 text-sm text-blue-700 font-medium group-hover:underline">
                Baca selengkapnya →
              </p>
            </div>
          </NuxtLink>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center mt-12">
          <div class="flex items-center gap-2">
            <UButton
              icon="i-heroicons-chevron-left"
              variant="outline"
              :disabled="currentPage === 1"
              class="border-gray-300"
              @click="changePage(currentPage - 1)"
            />
            <button
              v-for="page in totalPages"
              :key="page"
              class="w-9 h-9 rounded-lg text-sm font-medium transition-colors"
              :class="page === currentPage
                ? 'bg-blue-900 text-white'
                : 'text-gray-600 hover:bg-gray-100'"
              @click="changePage(page)"
            >
              {{ page }}
            </button>
            <UButton
              icon="i-heroicons-chevron-right"
              variant="outline"
              :disabled="currentPage === totalPages"
              class="border-gray-300"
              @click="changePage(currentPage + 1)"
            />
          </div>
        </div>

      </UContainer>
    </section>

  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const base   = config.public.apiBase

// ── Kategori filter ────────────────────────────────────────────────────────
const categories = [
  { label: 'Semua',      value: '' },
  { label: 'Kegiatan',   value: 'kegiatan' },
  { label: 'Keagamaan',  value: 'keagamaan' },
  { label: 'Wisata',     value: 'wisata' },
  { label: 'Pengumuman', value: 'pengumuman' },
]

const activeCategory = ref('')
const currentPage    = ref(1)

// ── URL fetch dinamis berdasarkan kategori & halaman ──────────────────────
const fetchUrl = computed(() => {
  const base_url = activeCategory.value
    ? `${base}/articles/category/${activeCategory.value}`
    : `${base}/articles/published`
  return base_url
})

const { data, pending, error, refresh } = await useFetch<any>(fetchUrl, {
  query: computed(() => ({ page: currentPage.value, per_page: 9 })),
  watch: [fetchUrl, currentPage],
})

const articles   = computed(() => data.value?.data ?? [])
const totalPages = computed(() => data.value?.last_page ?? 1)

// ── Actions ────────────────────────────────────────────────────────────────
function setCategory(val: string) {
  activeCategory.value = val
  currentPage.value    = 1
}

function changePage(page: number) {
  currentPage.value = page
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// ── Helpers ────────────────────────────────────────────────────────────────
function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
  })
}

function stripHtml(html: string): string {
  return html?.replace(/<[^>]*>/g, '') ?? ''
}

// ── SEO ────────────────────────────────────────────────────────────────────
useSeoMeta({
  title: 'Berita & Artikel — YHIE',
  description: 'Liputan kegiatan, tulisan keagamaan, dan informasi wisata Desa Tejasari dari Yayasan Hafiz Indonesia Emas.',
  ogTitle: 'Berita & Artikel — YHIE',
  ogDescription: 'Kabar terbaru dari Yayasan Hafiz Indonesia Emas.',
})
</script>
