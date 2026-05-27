<template>
  <div>

    <!-- ─── HERO ─────────────────────────────────────────────────────────── -->
    <section class="relative bg-blue-900 text-white overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 opacity-90" />
      <UContainer class="relative z-10 py-24 md:py-36">
        <div class="max-w-3xl">
          <p class="text-blue-300 text-sm font-semibold uppercase tracking-widest mb-4">
            Wisuda Internasional · 30 Agustus 2026
          </p>
          <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">
            Wisata Sambil <br />
            <span class="text-yellow-400">Wisuda 2026</span>
          </h1>
          <p class="text-blue-100 text-lg md:text-xl leading-relaxed mb-8 max-w-2xl">
            Dapatkan gelar <strong>B.Sc. / M.Sc. / Dr.Hc</strong> terakreditasi IAO.
            Program sertifikasi hafiz dan maulana bertaraf internasional
            di Desa Wisata Tejasari, Purbalingga.
          </p>
          <div class="flex flex-wrap gap-4">
            <UButton to="/programs" size="lg" class="bg-yellow-400 hover:bg-yellow-300 text-blue-900 font-bold">
              Lihat Program
            </UButton>
            <UButton to="/programs" size="lg" variant="outline" class="border-white text-white hover:bg-white/10">
              Daftar Sekarang
            </UButton>
          </div>
        </div>
      </UContainer>
    </section>

    <!-- ─── PROGRAM CARDS ─────────────────────────────────────────────────── -->
    <section class="py-20 bg-gray-50">
      <UContainer>

        <div class="text-center mb-12">
          <p class="text-blue-800 text-sm font-semibold uppercase tracking-widest mb-2">Program Unggulan</p>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Program Sertifikasi</h2>
          <p class="text-gray-500 mt-3 max-w-xl mx-auto">
            Pilih program sertifikasi sesuai kompetensi Anda dan raih pengakuan akademik internasional.
          </p>
        </div>

        <!-- Loading -->
        <div v-if="programsPending" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <USkeleton v-for="n in 3" :key="n" class="h-64 rounded-xl" />
        </div>

        <!-- Error -->
        <UAlert
          v-else-if="programsError"
          icon="i-heroicons-exclamation-triangle"
          color="error"
          title="Gagal memuat program."
          class="max-w-md mx-auto"
        />

        <!-- Data -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <NuxtLink
            v-for="program in programs"
            :key="program.id"
            :to="`/programs/${program.slug_id}`"
            class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-200"
          >
            <div class="h-48 bg-blue-100 overflow-hidden">
              <img
                v-if="program.image_path"
                :src="program.image_path"
                :alt="program.title_id"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <UIcon name="i-heroicons-academic-cap" class="w-16 h-16 text-blue-300" />
              </div>
            </div>
            <div class="p-5">
              <h3 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-blue-800 transition-colors">
                {{ program.title_id }}
              </h3>
              <p class="text-gray-500 text-sm line-clamp-2">{{ program.description_id }}</p>
              <p class="mt-3 text-blue-800 font-semibold text-sm">
                Rp {{ Number(program.price_id).toLocaleString('id-ID') }}
              </p>
            </div>
          </NuxtLink>
        </div>

        <div class="text-center mt-10">
          <UButton to="/programs" variant="outline" class="border-blue-800 text-blue-800 hover:bg-blue-50">
            Lihat Semua Program
          </UButton>
        </div>

      </UContainer>
    </section>

    <!-- ─── JADWAL UPCOMING ────────────────────────────────────────────────── -->
    <section class="py-20 bg-white">
      <UContainer>

        <div class="text-center mb-12">
          <p class="text-blue-800 text-sm font-semibold uppercase tracking-widest mb-2">Agenda</p>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Jadwal Kegiatan</h2>
        </div>

        <div v-if="schedulesPending" class="space-y-4 max-w-2xl mx-auto">
          <USkeleton v-for="n in 4" :key="n" class="h-20 rounded-xl" />
        </div>

        <div v-else-if="schedulesError">
          <UAlert icon="i-heroicons-exclamation-triangle" color="error" title="Gagal memuat jadwal." />
        </div>

        <div v-else class="max-w-2xl mx-auto space-y-4">
          <div
            v-for="schedule in schedules"
            :key="schedule.id"
            class="flex gap-4 p-5 bg-gray-50 rounded-xl border border-gray-100"
          >
            <!-- Tanggal -->
            <div class="flex-shrink-0 w-14 text-center">
              <p class="text-2xl font-bold text-blue-800 leading-none">
                {{ new Date(schedule.start_date).getDate() }}
              </p>
              <p class="text-xs text-gray-500 uppercase mt-1">
                {{ new Date(schedule.start_date).toLocaleString('id-ID', { month: 'short' }) }}
              </p>
            </div>
            <!-- Info -->
            <div>
              <p class="font-semibold text-gray-900">{{ schedule.title_id }}</p>
              <p class="text-sm text-gray-500 mt-1">{{ schedule.description_id }}</p>
            </div>
          </div>

          <p v-if="!schedules?.length" class="text-center text-gray-400 py-8">
            Belum ada jadwal mendatang.
          </p>
        </div>

      </UContainer>
    </section>

    <!-- ─── GALERI SINGKAT ─────────────────────────────────────────────────── -->
    <section class="py-20 bg-gray-50">
      <UContainer>

        <div class="text-center mb-12">
          <p class="text-blue-800 text-sm font-semibold uppercase tracking-widest mb-2">Galeri</p>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Dokumentasi Kegiatan</h2>
        </div>

        <div v-if="mediaPending" class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <USkeleton v-for="n in 8" :key="n" class="h-40 rounded-xl" />
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <NuxtLink
            v-for="item in mediaItems"
            :key="item.id"
            :to="`/gallery/${item.slug_id}`"
            class="group relative h-40 bg-blue-100 rounded-xl overflow-hidden"
          >
            <img
              v-if="item.image?.url"
              :src="item.image.url"
              :alt="item.title_id"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-200 flex items-end p-3">
              <p class="text-white text-xs font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-200 line-clamp-2">
                {{ item.title_id }}
              </p>
            </div>
          </NuxtLink>
        </div>

        <div class="text-center mt-10">
          <UButton to="/gallery" variant="outline" class="border-blue-800 text-blue-800 hover:bg-blue-50">
            Lihat Semua Galeri
          </UButton>
        </div>

      </UContainer>
    </section>

    <!-- ─── MITRA / PARTNER ───────────────────────────────────────────────── -->
    <section class="py-16 bg-white border-t border-gray-100">
      <UContainer>
        <p class="text-center text-sm text-gray-400 uppercase tracking-widest mb-8">Mitra & Akreditasi</p>
        <div v-if="partnersPending" class="flex justify-center gap-8">
          <USkeleton v-for="n in 4" :key="n" class="h-12 w-24 rounded" />
        </div>
        <div v-else class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
          <img
            v-for="partner in partners"
            :key="partner.id"
            :src="partner.logo"
            :alt="partner.name"
            class="h-10 object-contain grayscale hover:grayscale-0 transition-all duration-200 opacity-60 hover:opacity-100"
          />
        </div>
      </UContainer>
    </section>

  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const base   = config.public.apiBase

// Programs
const { data: programs, pending: programsPending, error: programsError } = await useFetch<any[]>(
  `${base}/programs`
)

// Schedules upcoming
const { data: schedules, pending: schedulesPending, error: schedulesError } = await useFetch<any[]>(
  `${base}/schedules/upcoming`
)

// Media (8 item saja untuk homepage)
const { data: mediaData, pending: mediaPending } = await useFetch<any>(
  `${base}/media`, { query: { per_page: 8 } }
)
const mediaItems = computed(() => mediaData.value?.data ?? [])

// Partners
const { data: partners, pending: partnersPending } = await useFetch<any[]>(
  `${base}/partners`
)

// SEO
useSeoMeta({
  title: 'YHIE — Wisata Sambil Wisuda 2026',
  description: 'Program sertifikasi hafiz dan maulana bertaraf internasional terakreditasi IAO. Wisuda 30 Agustus 2026 di Desa Wisata Tejasari, Purbalingga.',
  ogTitle: 'YHIE — Wisata Sambil Wisuda 2026',
  ogDescription: 'Dapatkan gelar B.Sc. / M.Sc. / Dr.Hc terakreditasi IAO.',
})
</script>