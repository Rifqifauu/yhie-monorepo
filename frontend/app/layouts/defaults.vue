<template>
  <div class="min-h-screen flex flex-col bg-white">

    <!-- NAVBAR -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
      <UContainer>
        <nav class="flex items-center justify-between h-16">

          <!-- Logo -->
          <NuxtLink to="/" class="flex items-center gap-2">
            <span class="text-xl font-bold text-blue-900">YHIE</span>
            <span class="hidden sm:block text-sm text-gray-500">Yayasan Hafiz Indonesia Emas</span>
          </NuxtLink>

          <!-- Menu Desktop -->
          <ul class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-700">
            <li><NuxtLink to="/" class="hover:text-blue-800 transition-colors">Beranda</NuxtLink></li>
            <li><NuxtLink to="/programs" class="hover:text-blue-800 transition-colors">Program</NuxtLink></li>
            <li><NuxtLink to="/articles" class="hover:text-blue-800 transition-colors">Berita</NuxtLink></li>
            <li><NuxtLink to="/gallery" class="hover:text-blue-800 transition-colors">Galeri</NuxtLink></li>
            <li><NuxtLink to="/contact" class="hover:text-blue-800 transition-colors">Kontak</NuxtLink></li>
          </ul>

          <!-- CTA -->
          <div class="flex items-center gap-3">
            <UButton to="/programs" size="sm" class="hidden md:inline-flex bg-blue-900 hover:bg-blue-800">
              Daftar Sekarang
            </UButton>
            <!-- Mobile hamburger — nanti bisa dikembangkan -->
            <UButton icon="i-heroicons-bars-3" variant="ghost" class="md:hidden" />
          </div>

        </nav>
      </UContainer>
    </header>

    <!-- PAGE CONTENT -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- FOOTER -->
    <footer class="bg-blue-900 text-white mt-auto">
      <UContainer>
        <div class="py-10 grid grid-cols-1 md:grid-cols-3 gap-8">

          <div>
            <p class="text-lg font-bold mb-2">YHIE</p>
            <p class="text-sm text-blue-200 leading-relaxed">
              Yayasan Hafiz Indonesia Emas — Lembaga sertifikasi hafiz
              bertaraf internasional terakreditasi IAO.
            </p>
          </div>

          <div>
            <p class="font-semibold mb-3">Tautan Cepat</p>
            <ul class="space-y-2 text-sm text-blue-200">
              <li><NuxtLink to="/programs" class="hover:text-white transition-colors">Program Sertifikasi</NuxtLink></li>
              <li><NuxtLink to="/articles" class="hover:text-white transition-colors">Berita & Artikel</NuxtLink></li>
              <li><NuxtLink to="/gallery" class="hover:text-white transition-colors">Galeri</NuxtLink></li>
              <li><NuxtLink to="/verify" class="hover:text-white transition-colors">Verifikasi Ijazah</NuxtLink></li>
            </ul>
          </div>

          <div>
            <p class="font-semibold mb-3">Kontak</p>
            <ul class="space-y-2 text-sm text-blue-200">
              <li>Balai Desa Tejasari, Purbalingga</li>
              <li>Jawa Tengah, Indonesia</li>
              <li>
                <a href="https://wa.me/628123456789" target="_blank" class="hover:text-white transition-colors">
                  WhatsApp Admin
                </a>
              </li>
            </ul>
          </div>

        </div>
        <div class="border-t border-blue-800 py-4 text-center text-xs text-blue-300">
          &copy; {{ new Date().getFullYear() }} Yayasan Hafiz Indonesia Emas. All rights reserved.
        </div>
      </UContainer>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a
      href="https://wa.me/628123456789"
      target="_blank"
      class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-lg transition-colors"
      aria-label="Chat WhatsApp"
    >
      <UIcon name="i-heroicons-chat-bubble-left-ellipsis" class="w-6 h-6" />
    </a>

  </div>
</template>