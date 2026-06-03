<template>
    <section
        class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50 dark:from-emerald-950 dark:via-emerald-950 dark:to-amber-950/40 border-b border-emerald-100/70 dark:border-emerald-800/60"
    >
        <!-- Background Mosque Silhouette (Positioned Left for Asymmetry) -->
        <img
            src="/shadow-mosque.webp"
            class="absolute bottom-0 left-0 w-auto h-full max-h-[70%] object-contain object-left pointer-events-none opacity-10 dark:opacity-20 dark:invert dark:brightness-150 transition-all duration-300"
            alt=""
        />
        
        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-emerald-50/60 via-white/45 to-amber-50/35 dark:from-emerald-950/65 dark:via-emerald-950/60 dark:to-amber-950/35 transition-colors duration-300"
            aria-hidden="true"
        ></div>

        <!-- Decorative Blurs -->
        <div class="absolute top-1/2 -left-24 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-amber-400/15 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20 z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column: Typography & Stats -->
                <div class="lg:col-span-7 text-left" data-aos="fade-right">
                    <span
                        class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.3em] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3.5 py-1.5 rounded-full border border-amber-200/50 dark:border-amber-900/40"
                    >
                        <UIcon name="i-lucide-camera" class="w-4 h-4" />
                        {{ locale === 'en' ? 'Visual Archive' : 'Arsip Visual YHIE' }}
                    </span>
                    <h1
                        class="mt-6 text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-emerald-950 dark:text-emerald-50 leading-[1.15]"
                    >
                        {{ locale === 'en' ? 'Capturing Moments of Spiritual & Academic Excellence' : 'Mengabadikan Momen Keunggulan Spiritual & Akademik' }}
                    </h1>
                    <p class="mt-6 text-lg text-slate-600 dark:text-emerald-100/80 leading-relaxed max-w-2xl">
                        {{ locale === 'en'
                            ? 'Witness the journey of spiritual deepening and academic distinction. Each frame captures our commitment to nurturing the next generation of gold-standard leaders.'
                            : 'Saksikan perjalanan pendalaman spiritual dan keunggulan akademik. Setiap bingkai mengabadikan komitmen kami untuk mendidik generasi pemimpin standar emas berikutnya.'
                        }}
                    </p>

                    <!-- Stats / Details Section -->
                    <div class="mt-10 pt-8 border-t border-slate-200/60 dark:border-emerald-800/40 grid grid-cols-3 gap-6 max-w-lg">
                        <div class="space-y-1">
                            <span class="block text-2xl md:text-3xl font-serif font-extrabold text-emerald-600 dark:text-amber-400">
                                50+
                            </span>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-emerald-200/50">
                                {{ locale === 'en' ? 'HD Memories' : 'Kenangan HD' }}
                            </span>
                        </div>
                        <div class="space-y-1">
                            <span class="block text-2xl md:text-3xl font-serif font-extrabold text-emerald-600 dark:text-amber-400">
                                4
                            </span>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-emerald-200/50">
                                {{ locale === 'en' ? 'Categories' : 'Kategori' }}
                            </span>
                        </div>
                        <div class="space-y-1">
                            <span class="block text-2xl md:text-3xl font-serif font-extrabold text-emerald-600 dark:text-amber-400">
                                100%
                            </span>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-emerald-200/50">
                                {{ locale === 'en' ? 'Authentic' : 'Dokumentasi Asli' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Layered Collage -->
                <div class="lg:col-span-5 relative h-[320px] sm:h-[400px] lg:h-[450px] w-full flex items-center justify-center mt-8 lg:mt-0" data-aos="fade-left" data-aos-delay="150">
                    <!-- Layer 1: Landscape (Bottom Left) -->
                    <div 
                        class="absolute left-4 bottom-6 w-3/5 aspect-[4/3] rounded-2xl -rotate-6 border-4 border-white dark:border-emerald-900 shadow-xl overflow-hidden group/collage hover:rotate-0 hover:scale-105 hover:z-30 hover:shadow-2xl transition-all duration-500 cursor-pointer"
                        title="Tourism / Wisata Alam"
                    >
                        <img 
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAybOmxtGMl-peQQGzLrgOzMdg2kncK7Nbie9zJa5vu-EUhPnFHJi066VMeWtwP6zN2IUJ1e02dVwgT2BjAI-cwZIHXiaoRQIhlnInjyGhT3A7OMRs23xdWSswjR9dmaa_slLeyAlEapZfOQuC_vdu8OvR462VwAayMJl8CQuDRJ_yzpauKc00DdomoHej0RueOIR_-5uGDDi6v01aY95b244xTFAUdZCOzvlVBG0touNRyOb8ZgaqwF72ttM_KyH0je8aX4O7iGGw"
                            class="w-full h-full object-cover grayscale-[20%] group-hover/collage:grayscale-0 transition-all duration-500"
                        />
                        <div class="absolute inset-0 bg-black/10 group-hover/collage:bg-transparent transition-colors"></div>
                    </div>

                    <!-- Layer 2: Sanctuary (Top Right) -->
                    <div 
                        class="absolute right-4 top-6 w-3/5 aspect-[4/3] rounded-2xl rotate-12 border-4 border-white dark:border-emerald-900 shadow-xl overflow-hidden group/collage hover:rotate-0 hover:scale-105 hover:z-30 hover:shadow-2xl transition-all duration-500 cursor-pointer"
                        title="Academic / Akademik"
                    >
                        <img 
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCYux_hJAayRbruvqHl9-aqkzNFmccYwDmzUXGVUpuyzZlmyetxLDHPllpgD0e3mmmjzQOf1igIMUsZyiS5gNfTPYCyyRbzCFHRenRHlZYnxfuj2pbwB2Q_2cfywr81xYqoSrtZ9YZUOHQWGy_H7xzHxx1QPdQ_X02KmFjoZHI2wNW1atlyQhgCwFyQId2J-lxWp3h9eIQkEDONw9eeddFYNgPuH-DQIm4ew9Lwlh1G0TBKes8AVbxK4jGNmlHy1sboZV-EL7XgYU8"
                            class="w-full h-full object-cover grayscale-[20%] group-hover/collage:grayscale-0 transition-all duration-500"
                        />
                        <div class="absolute inset-0 bg-black/10 group-hover/collage:bg-transparent transition-colors"></div>
                    </div>

                    <!-- Layer 3: Graduation (Center Front) -->
                    <div 
                        class="absolute w-[52%] aspect-[4/3] rounded-2xl border-4 border-white dark:border-emerald-900 shadow-2xl overflow-hidden z-20 group/collage hover:scale-110 hover:z-40 hover:shadow-emerald-500/20 transition-all duration-500 cursor-pointer"
                        title="Graduation / Wisuda"
                    >
                        <img 
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBGlsnV8sEkDOjkt4Mj0jMWi7BX3_Ke9qQPJqYzDEcDVwBJmsoNrueSrTAcfOxhptEQ05EOS5dD58_VrC0bnsuNZf3bNNY_370qj4_D3hkAE_55I4rRNrkwuvD6v0GbEZTb8D_fb6XTAAgtUCz0eEt00iQe64yQ0e3L5C_Dj1bEyQJaV7fHP_jSDIRrvTAzkP3U0nq6PWrCwEM8lWkQ4p0nfiPECAQ_5mI0yQ1o1pevntsBO1y3vz3cj0xLVJfchyWzcKRSiEktuyw"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
const { locale } = useI18n();
</script>
