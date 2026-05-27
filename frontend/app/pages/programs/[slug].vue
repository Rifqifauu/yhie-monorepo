<template>
  <div>

    <!-- Loading -->
    <div v-if="pending" class="py-32">
      <UContainer>
        <div class="max-w-3xl mx-auto space-y-4">
          <USkeleton class="h-10 w-2/3 rounded-xl" />
          <USkeleton class="h-6 w-1/3 rounded-xl" />
          <USkeleton class="h-64 rounded-xl mt-6" />
          <USkeleton class="h-40 rounded-xl" />
        </div>
      </UContainer>
    </div>

    <!-- Error / Not Found -->
    <div v-else-if="error" class="py-32 text-center">
      <UContainer>
        <UIcon name="i-heroicons-exclamation-circle" class="w-12 h-12 text-red-400 mx-auto mb-4" />
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Program tidak ditemukan</h1>
        <p class="text-gray-500 mb-6">Program yang Anda cari tidak tersedia atau telah dihapus.</p>
        <UButton to="/programs" variant="outline" class="border-blue-800 text-blue-800">
          Kembali ke Daftar Program
        </UButton>
      </UContainer>
    </div>

    <!-- Data -->
    <template v-else-if="program">

      <!-- ─── PAGE HEADER ─────────────────────────────────────────────────── -->
      <section class="bg-blue-900 text-white py-16">
        <UContainer>

          <!-- Breadcrumb -->
          <nav class="flex items-center gap-2 text-sm text-blue-300 mb-6">
            <NuxtLink to="/" class="hover:text-white transition-colors">Beranda</NuxtLink>
            <span>/</span>
            <NuxtLink to="/programs" class="hover:text-white transition-colors">Program</NuxtLink>
            <span>/</span>
            <span class="text-white">{{ program.title_id }}</span>
          </nav>

          <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
              {{ program.title_id }}
            </h1>
            <p class="text-blue-200 text-lg leading-relaxed">
              {{ program.description_id }}
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-4">
              <span class="text-3xl font-bold text-yellow-400">
                Rp {{ Number(program.price_id).toLocaleString('id-ID') }}
              </span>
              <UButton
                size="lg"
                class="bg-yellow-400 hover:bg-yellow-300 text-blue-900 font-bold"
                to="https://wa.me/628123456789"
                target="_blank"
              >
                Daftar Sekarang
              </UButton>
            </div>
          </div>

        </UContainer>
      </section>

      <!-- ─── KONTEN UTAMA ────────────────────────────────────────────────── -->
      <section class="py-20 bg-gray-50">
        <UContainer>
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <!-- Gambar + Info Singkat -->
            <div class="lg:col-span-1 space-y-6">

              <div class="rounded-xl overflow-hidden shadow-sm bg-white border border-gray-100">
                <img
                  v-if="program.image_path"
                  :src="program.image_path"
                  :alt="program.title_id"
                  class="w-full h-56 object-cover"
                />
                <div v-else class="w-full h-56 bg-blue-100 flex items-center justify-center">
                  <UIcon name="i-heroicons-academic-cap" class="w-20 h-20 text-blue-300" />
                </div>
              </div>

              <!-- Info Card -->
              <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-gray-900 text-lg">Informasi Program</h3>

                <div class="flex items-start gap-3">
                  <UIcon name="i-heroicons-banknotes" class="w-5 h-5 text-blue-700 mt-0.5 flex-shrink-0" />
                  <div>
                    <p class="text-xs text-gray-400">Biaya Pendaftaran (IDR)</p>
                    <p class="font-semibold text-gray-800">
                      Rp {{ Number(program.price_id).toLocaleString('id-ID') }}
                    </p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <UIcon name="i-heroicons-currency-dollar" class="w-5 h-5 text-blue-700 mt-0.5 flex-shrink-0" />
                  <div>
                    <p class="text-xs text-gray-400">Biaya Pendaftaran (USD)</p>
                    <p class="font-semibold text-gray-800">
                      $ {{ Number(program.price_en).toLocaleString('en-US') }}
                    </p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <UIcon name="i-heroicons-calendar-days" class="w-5 h-5 text-blue-700 mt-0.5 flex-shrink-0" />
                  <div>
                    <p class="text-xs text-gray-400">Wisuda</p>
                    <p class="font-semibold text-gray-800">30 Agustus 2026</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <UIcon name="i-heroicons-map-pin" class="w-5 h-5 text-blue-700 mt-0.5 flex-shrink-0" />
                  <div>
                    <p class="text-xs text-gray-400">Lokasi</p>
                    <p class="font-semibold text-gray-800">Desa Wisata Tejasari, Purbalingga</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <UIcon name="i-heroicons-check-badge" class="w-5 h-5 text-blue-700 mt-0.5 flex-shrink-0" />
                  <div>
                    <p class="text-xs text-gray-400">Akreditasi</p>
                    <p class="font-semibold text-gray-800">International Accreditation Organization (IAO)</p>
                  </div>
                </div>

                <UButton
                  block
                  size="lg"
                  class="bg-blue-900 hover:bg-blue-800 mt-2"
                  to="https://wa.me/628123456789"
                  target="_blank"
                >
                  Daftar via WhatsApp
                </UButton>
              </div>

            </div>

            <!-- Deskripsi Panjang -->
            <div class="lg:col-span-2">
              <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Tentang Program</h2>
                <p class="text-gray-600 leading-relaxed whitespace-pre-line">
                  {{ program.description_id }}
                </p>

                <!-- Keunggulan -->
                <div class="mt-10">
                  <h3 class="text-xl font-bold text-gray-900 mb-5">Keunggulan Program</h3>
                  <ul class="space-y-3">
                    <li
                      v-for="(item, i) in highlights"
                      :key="i"
                      class="flex items-start gap-3"
                    >
                      <UIcon name="i-heroicons-check-circle" class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                      <span class="text-gray-700">{{ item }}</span>
                    </li>
                  </ul>
                </div>

              </div>
            </div>

          </div>
        </UContainer>
      </section>

      <!-- ─── PROGRAM LAINNYA ─────────────────────────────────────────────── -->
      <section v-if="otherPrograms.length" class="py-16 bg-white border-t border-gray-100">
        <UContainer>
          <h2 class="text-2xl font-bold text-gray-900 mb-8">Program Lainnya</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <NuxtLink
              v-for="other in otherPrograms"
              :key="other.id"
              :to="`/programs/${other.slug_id}`"
              class="group bg-gray-50 rounded-xl border border-gray-100 p-5 hover:shadow-md hover:-translate-y-1 transition-all duration-200"
            >
              <h3 class="font-bold text-gray-900 group-hover:text-blue-800 transition-colors mb-1">
                {{ other.title_id }}
              </h3>
              <p class="text-sm text-gray-500 line-clamp-2">{{ other.description_id }}</p>
              <p class="mt-3 text-blue-800 font-semibold text-sm">
                Rp {{ Number(other.price_id).toLocaleString('id-ID') }}
              </p>
            </NuxtLink>
          </div>
        </UContainer>
      </section>

    </template>

  </div>
</template>

<script setup lang="ts">
const route  = useRoute()
const config = useRuntimeConfig()
const base   = config.public.apiBase

// Fetch detail program berdasarkan slug
const { data: program, pending, error } = await useFetch<any>(
  `${base}/programs/${route.params.slug}`
)

// Fetch semua program untuk "Program Lainnya"
const { data: allPrograms } = await useFetch<any[]>(`${base}/programs`)

// Filter program lain selain yang sedang dibuka
const otherPrograms = computed(() =>
  (allPrograms.value ?? []).filter(p => p.slug_id !== route.params.slug).slice(0, 3)
)

// Highlight statis — bisa nanti dijadikan field di database
const highlights = [
  'Ijazah terakreditasi International Accreditation Organization (IAO)',
  'Setara gelar akademik internasional yang diakui di kawasan ASEAN',
  'Proses ujian fleksibel: daring dan tatap muka',
  'Prosesi wisuda resmi di Desa Wisata Tejasari, Purbalingga',
  'Kode verifikasi ijazah unik yang dapat dicek secara publik',
  'Tersedia paket akomodasi dan wisata untuk peserta dan keluarga',
]

// SEO dinamis
useSeoMeta({
  title: computed(() => program.value ? `${program.value.title_id} — YHIE` : 'Program — YHIE'),
  description: computed(() => program.value?.description_id ?? ''),
  ogTitle: computed(() => program.value?.title_id ?? 'Program YHIE'),
  ogDescription: computed(() => program.value?.description_id ?? ''),
  ogImage: computed(() => program.value?.image_path ?? ''),
})
</script>
