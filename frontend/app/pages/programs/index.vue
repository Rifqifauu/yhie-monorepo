<template>
  <div>

    <!-- ─── PAGE HEADER ───────────────────────────────────────────────────── -->
    <section class="bg-blue-900 text-white py-16">
      <UContainer>
        <div class="max-w-2xl">
          <p class="text-blue-300 text-sm font-semibold uppercase tracking-widest mb-3">
            Program Sertifikasi
          </p>
          <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Raih Gelar Akademik <br />
            <span class="text-yellow-400">Bertaraf Internasional</span>
          </h1>
          <p class="text-blue-100 text-lg leading-relaxed">
            Sertifikasi Hafiz dan Maulana terakreditasi IAO — setara gelar
            B.Sc., M.Sc., hingga Dr.Hc. dari lembaga internasional.
          </p>
        </div>
      </UContainer>
    </section>

    <!-- ─── PROGRAM LIST ──────────────────────────────────────────────────── -->
    <section class="py-20 bg-gray-50">
      <UContainer>

        <!-- Loading -->
        <div v-if="pending" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <USkeleton v-for="n in 6" :key="n" class="h-72 rounded-xl" />
        </div>

        <!-- Error -->
        <UAlert
          v-else-if="error"
          icon="i-heroicons-exclamation-triangle"
          color="error"
          title="Gagal memuat data program."
          description="Silakan coba beberapa saat lagi."
          class="max-w-md mx-auto"
        />

        <!-- Empty -->
        <div v-else-if="!programs?.length" class="text-center py-20 text-gray-400">
          <UIcon name="i-heroicons-academic-cap" class="w-12 h-12 mx-auto mb-3" />
          <p>Belum ada program tersedia.</p>
        </div>

        <!-- Data -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <NuxtLink
            v-for="program in programs"
            :key="program.id"
            :to="`/programs/${program.slug_id}`"
            class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-200"
          >
            <!-- Gambar -->
            <div class="h-52 bg-blue-100 overflow-hidden">
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

            <!-- Konten -->
            <div class="p-6">
              <h2 class="font-bold text-gray-900 text-xl mb-2 group-hover:text-blue-800 transition-colors">
                {{ program.title_id }}
              </h2>
              <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">
                {{ program.description_id }}
              </p>

              <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-blue-800 font-bold text-lg">
                  Rp {{ Number(program.price_id).toLocaleString('id-ID') }}
                </p>
                <span class="text-sm text-blue-600 font-medium group-hover:underline">
                  Selengkapnya →
                </span>
              </div>
            </div>
          </NuxtLink>
        </div>

      </UContainer>
    </section>

    <!-- ─── CTA SECTION ───────────────────────────────────────────────────── -->
    <section class="py-20 bg-blue-900 text-white text-center">
      <UContainer>
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          Siap Mendaftar?
        </h2>
        <p class="text-blue-200 text-lg mb-8 max-w-xl mx-auto">
          Wisuda Internasional 30 Agustus 2026 di Desa Wisata Tejasari, Purbalingga.
          Tempat terbatas.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
          <UButton
            size="lg"
            class="bg-yellow-400 hover:bg-yellow-300 text-blue-900 font-bold"
            to="https://wa.me/628123456789"
            target="_blank"
          >
            Hubungi Admin via WhatsApp
          </UButton>
          <UButton
            size="lg"
            variant="outline"
            class="border-white text-white hover:bg-white/10"
            to="/contact"
          >
            Pelajari Lebih Lanjut
          </UButton>
        </div>
      </UContainer>
    </section>

  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const base   = config.public.apiBase

const { data: programs, pending, error } = await useFetch<any[]>(
  `${base}/programs`
)

useSeoMeta({
  title: 'Program Sertifikasi — YHIE',
  description: 'Program sertifikasi hafiz dan maulana terakreditasi IAO. Raih gelar B.Sc., M.Sc., Dr.Hc. bertaraf internasional.',
  ogTitle: 'Program Sertifikasi — YHIE',
  ogDescription: 'Sertifikasi Hafiz dan Maulana terakreditasi IAO.',
})
</script>