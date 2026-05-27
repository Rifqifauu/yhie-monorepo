# Design Document — Website Profil YHIE
**Yayasan Hafiz Indonesia Emas**
`yhie.org` | `sertifikasihafiz.com`
*Versi 1.0 — Mei 2026*

---

## 1. Ringkasan Proyek

Website profil YHIE adalah wajah publik lembaga, media promosi, dan pintu pendaftaran resmi Program Wisata Sambil Wisuda 2026. Dibangun dengan arsitektur Jamstack (Nuxt.js SSG + Laravel API), website ini harus mampu:

- Membangun kepercayaan calon peserta dari Indonesia dan kawasan ASEAN.
- Mengonversi pengunjung menjadi peserta terdaftar.
- Tampil profesional, Islami, dan berkelas internasional.
- Memuat cepat di perangkat mobile dengan SEO kuat.

---

## 2. Identitas Visual

### 2.1 Tema & Karakter

| Atribut | Keterangan |
|---|---|
| **Tone desain** | Islami-Prestisius — hangat namun formal, layaknya lembaga akademik internasional |
| **Referensi nuansa** | Sertifikat perguruan tinggi timur tengah, situs wisuda universitas, branding destinasi wisata alam |
| **Elemen khas** | Ornamen arabesque halus, tipografi serif elegan, gradien emas-hijau, foto alam Purbalingga |
| **Kesan yang ingin ditinggalkan** | "Ini lembaga yang serius, terpercaya, dan acara ini layak saya hadiri." |

### 2.2 Palet Warna

```
Primary     ─ Hijau Zamrud    #1B6B3A   (kepercayaan, Islam, alam)
Primary Dark─ Hijau Tua       #0F3D22   (hover state, footer bg)
Primary Soft─ Hijau Muda      #E8F5EE   (background section alternating)
Gold        ─ Emas Prestige   #C9A84C   (badge, highlight, border aksen)
Gold Light  ─ Emas Muda       #F0D080   (background badge, shimmer)
Gold Dark   ─ Emas Tua        #8B6914   (teks badge gelap)
CTA         ─ Merah Aksi      #C0392B   (tombol utama "Daftar Sekarang")
CTA Hover   ─ Merah Gelap     #922B21   (hover tombol CTA)
Text Dark   ─ Charcoal        #1C1C2E   (heading utama)
Text Mid    ─ Abu Sedang      #4A5568   (body text)
Text Light  ─ Abu Terang      #718096   (caption, label, placeholder)
Surface     ─ Putih Gading    #FDFBF7   (background utama)
Surface Alt ─ Krem Terang     #F5F1E8   (section zebra)
Border      ─ Abu Sangat Terang #E2D9C8 (border kartu, divider)
```

### 2.3 Tipografi

| Peran | Font | Import |
|---|---|---|
| **Display / Heading besar** | Playfair Display | Google Fonts |
| **Heading menengah** | Cormorant Garamond | Google Fonts |
| **Body / UI** | DM Sans | Google Fonts |
| **Label kecil / tombol** | DM Sans Medium | Google Fonts |
| **Teks Arab / ayat Al-Qur'an** | Amiri | Google Fonts |

```css
/* Skala tipografi */
--text-xs:    0.75rem;   /* 12px — caption */
--text-sm:    0.875rem;  /* 14px — label, badge */
--text-base:  1rem;      /* 16px — body */
--text-lg:    1.125rem;  /* 18px — body besar */
--text-xl:    1.25rem;   /* 20px — subheading */
--text-2xl:   1.5rem;    /* 24px — H3 */
--text-3xl:   1.875rem;  /* 30px — H2 */
--text-4xl:   2.25rem;   /* 36px — H1 halaman dalam */
--text-5xl:   3rem;      /* 48px — Hero heading */
--text-6xl:   3.75rem;   /* 60px — Hero heading besar */
```

### 2.4 Spacing & Layout Grid

```css
/* Spacing scale (8px base) */
--space-1:  0.25rem;   /* 4px */
--space-2:  0.5rem;    /* 8px */
--space-3:  0.75rem;   /* 12px */
--space-4:  1rem;      /* 16px */
--space-6:  1.5rem;    /* 24px */
--space-8:  2rem;      /* 32px */
--space-12: 3rem;      /* 48px */
--space-16: 4rem;      /* 64px */
--space-24: 6rem;      /* 96px */
--space-32: 8rem;      /* 128px */

/* Kontainer */
--container-max:  1280px;
--container-xl:   1440px;
--container-sm:   768px;

/* Border radius */
--radius-sm:   4px;
--radius-md:   8px;
--radius-lg:   12px;
--radius-xl:   16px;
--radius-2xl:  24px;
--radius-full: 9999px;
```

### 2.5 Komponen UI — Panduan Visual

#### Tombol

```
[Daftar Sekarang]          — bg #C0392B, teks putih, radius 8px, px-6 py-3, font DM Sans SemiBold
                              hover: bg #922B21, shadow-lg
                              icon anak panah kanan →

[Lihat Program]            — border 2px #1B6B3A, teks #1B6B3A, radius 8px, px-6 py-3
                              hover: bg #1B6B3A, teks putih

[Login LMS]                — bg #1B6B3A, teks putih, radius 8px, lebih kecil (px-4 py-2)
```

#### Kartu Program

```
┌─────────────────────────┐
│ ▌ border-top: 4px emas  │  ← aksen emas di atas
│                         │
│  [Ikon / Ilustrasi]     │
│                         │
│  Nama Program           │  Playfair Display, 20px
│  Deskripsi singkat      │  DM Sans, 14px, text-mid
│                         │
│  [Selengkapnya →]       │  link hijau
└─────────────────────────┘
  shadow: 0 4px 20px rgba(0,0,0,0.07)
  radius: 12px
  bg: white
  hover: shadow lebih dalam, slight translateY(-4px)
```

#### Badge Akreditasi

```
┌──────────────────────────┐
│ ★  Terakreditasi IAO     │  bg #F0D080, teks #8B6914, radius-full
│    International         │  font DM Sans Medium 12px
└──────────────────────────┘
```

#### Divider Section

```
─────── ✦ ───────           ornamen bintang geometris kecil, warna emas
```

---

## 3. Sitemap & Struktur Halaman

```
yhie.org /
│
├── /                         Home
│
├── /program                  Program Sertifikasi (landing)
│   ├── /program/hafiz        Sertifikasi Hafiz
│   │     Detail: Pratama (B.Sc.), Madya (M.Sc.), Utama (Dr.Hc.)
│   ├── /program/maulana      Sertifikasi Maulana
│   │     Detail: Pratama, Madya, Utama
│   └── /program/biaya        Mekanisme & Biaya
│         Rincian paket, cicilan, form pendaftaran
│
├── /wisata                   Destinasi Wisata Tejasari
│   ├── Profil Desa Wisata
│   ├── Fasilitas & Wahana
│   └── Paket Wisata
│
├── /wisuda                   Wisuda Internasional 30 Agustus 2026
│   ├── Jadwal Acara
│   ├── Lokasi & Rute
│   ├── Panduan Kehadiran
│   ├── Tamu Kehormatan
│   └── Galeri & Dokumentasi
│
├── /lms                      Sistem Pembelajaran
│   └── Redirect → lms.yhie.org
│
├── /tentang                  Tentang Kami
│   ├── Sejarah & Visi Misi
│   ├── Struktur Kepengurusan
│   └── Dokumen Akreditasi IAO (unduhan)
│
├── /berita                   Berita & Artikel
│   └── /berita/[slug]        Detail Artikel
│
├── /verifikasi               Verifikasi Ijazah (publik)
│
└── /kontak                   Hubungi Kami
```

---

## 4. Layout Per Halaman

### 4.1 Header (Global — Sticky)

```
┌─────────────────────────────────────────────────────────────────┐
│ [Logo YHIE]  [Logo IAO]    Program  Wisata  Wisuda  Tentang  LMS │
│                                                    [ID][EN]  [Daftar] │
└─────────────────────────────────────────────────────────────────┘

Mobile:
┌──────────────────────────────────┐
│ [Logo YHIE]           [≡ Menu]   │
└──────────────────────────────────┘
  → Drawer dari kanan, overlay gelap

Scroll behavior:
  - Transparan di atas hero
  - Solid putih + shadow saat di-scroll
  - Tombol "Daftar" selalu merah dan visible
```

**Detail:**
- Logo YHIE dan Logo IAO berdampingan — menegaskan kredibilitas akreditasi.
- Pemilih bahasa `[ID] [EN]` di pojok kanan sebelum tombol Daftar.
- Menu aktif diberi underline emas.
- Dropdown halus untuk menu "Program Sertifikasi".

---

### 4.2 Home — Section per Section

#### Section 1: Hero

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│   [foto/video loop Tejasari]  ← background, overlay gradien    │
│   overlay: linear-gradient(135deg, rgba(15,61,34,0.75), rgba(0,0,0,0.3))
│                                                                 │
│        ┌──────────────────────────────────────────┐            │
│        │  ★ Terakreditasi IAO • 30 Agustus 2026   │  badge kecil│
│        │                                          │            │
│        │  WISATA SAMBIL WISUDA 2026               │  H1, putih │
│        │                                          │  Playfair  │
│        │  Raih Gelar B.Sc. / M.Sc. / Dr.Hc.      │            │
│        │  Internasional di Alam Purbalingga        │  sub, putih│
│        │                                          │            │
│        │  [Lihat Program]   [Daftar Sekarang →]   │            │
│        │                                          │            │
│        │  📍 Desa Wisata Tejasari, Purbalingga    │  kecil     │
│        └──────────────────────────────────────────┘            │
│                                                                 │
│  ▼ scroll indicator (animasi bounce)                           │
└─────────────────────────────────────────────────────────────────┘

Tinggi: 100vh minimum, 90vh mobile
```

#### Section 2: Stat / Pencapaian (Angka)

```
┌──────────────────────────────────────────────────────────────────┐
│  bg: #1B6B3A (hijau gelap)                                        │
│                                                                   │
│   500+         4 Negara        2 Program         IAO              │
│  Peserta      ASEAN            Sertifikasi      Terakreditasi      │
│  Wisuda                                                           │
└──────────────────────────────────────────────────────────────────┘
```

#### Section 3: Tentang Kami Singkat

```
Layout 2 kolom (60/40):

Kiri:
  Heading: "Lembaga Sertifikasi Hafiz Bertaraf Internasional"
  Body: 3 paragraf singkat tentang YHIE & IAO
  [Selengkapnya tentang kami →]

Kanan:
  Kartu akreditasi IAO — foto sertifikat / logo IAO besar
  + kalimat pendek kepercayaan
```

#### Section 4: Program Unggulan (3 Kartu)

```
Heading section: "Program Sertifikasi"
Sub: "Dua jalur sertifikasi internasional untuk penghafal Al-Qur'an dan tokoh keagamaan"

┌────────────────┐  ┌────────────────┐  ┌────────────────┐
│  🕌            │  │  🌙            │  │  🏡            │
│                │  │                │  │                │
│  Sertifikasi   │  │  Sertifikasi   │  │  Desa Wisata   │
│  Hafiz         │  │  Maulana       │  │  Tejasari      │
│                │  │                │  │                │
│  B.Sc / M.Sc   │  │  Pratama s/d   │  │  Wisata setelah│
│  / Dr.Hc       │  │  Utama         │  │  wisuda        │
│                │  │                │  │                │
│  [Pelajari →]  │  │  [Pelajari →]  │  │  [Pelajari →]  │
└────────────────┘  └────────────────┘  └────────────────┘
```

#### Section 5: Highlight Wisata Tejasari

```
Layout full-width dengan foto panorama desa sebagai background

Overlay semi-transparan gelap
Di atasnya: grid foto kecil 2x2 (kolam renang, panggung, flamboyan, kuliner)
+ teks singkat keunggulan: "Wisuda sekaligus liburan keluarga"
+ [Jelajahi Destinasi →]
```

#### Section 6: Jadwal Wisuda

```
Timeline horizontal (desktop) / vertikal (mobile):

Registrasi → Pembelajaran Online → Ujian → Konfirmasi → Wisuda 30 Agt

Setiap node: tanggal + ikon + label singkat
Warna: icon hijau, garis penghubung emas
```

#### Section 7: Galeri Singkat

```
Masonry grid 6 foto: kolam renang, panggung budaya, suasana desa,
momen wisuda, pohon flamboyan, area kuliner

Hover: overlay gelap + caption singkat
Footer galeri: [Lihat Semua Foto →]
```

#### Section 8: Testimoni

```
Slider / carousel:
Setiap slide: foto peserta/ulama, nama, asal daerah/negara, kutipan singkat
Warna background: #F5F1E8 (krem)
Navigasi: panah kiri/kanan + dots
```

#### Section 9: Berita & Pengumuman (3 kartu terbaru)

```
Heading: "Berita & Pengumuman"
3 kartu artikel terbaru: thumbnail + tanggal + judul + [Baca →]
Footer: [Semua Berita →]
```

#### Section 10: CTA Akhir

```
┌────────────────────────────────────────────────────────────┐
│  bg: gradien hijau zamrud #1B6B3A → #0F3D22                │
│  ornamen arabesque sudut kanan-kiri (opacity rendah)       │
│                                                            │
│  "Siap Meraih Gelar Internasional?"                        │
│  "Daftarkan diri Anda sebelum kuota terpenuhi."            │
│                                                            │
│  [Daftar Sekarang →]   [Hubungi Admin WhatsApp]            │
└────────────────────────────────────────────────────────────┘
```

---

### 4.3 Footer (Global)

```
┌─────────────────────────────────────────────────────────────────┐
│  bg: #0F3D22                                                    │
│                                                                 │
│  [Logo YHIE putih]    Program      Wisata       Kontak          │
│                       - Hafiz      - Tejasari   - WhatsApp      │
│  Tagline singkat      - Maulana    - Wisuda      - Email        │
│  YHIE                 - Biaya      - Galeri      - Alamat       │
│                                                                 │
│  ─────────────────────────────────────────────────────         │
│  © 2026 Yayasan Hafiz Indonesia Emas  |  yhie.org              │
│  Mitra: International Accreditation Organization (IAO)         │
└─────────────────────────────────────────────────────────────────┘
```

**Floating element (semua halaman):**
```
[💬 WhatsApp Admin]   — pojok kanan bawah, fixed, bg hijau, animasi pulse
```

---

### 4.4 Halaman Program Sertifikasi

```
/program/hafiz

HERO kecil (300px):
  bg hijau, judul "Sertifikasi Hafiz", breadcrumb

INTRO:
  Penjelasan singkat program + logo IAO + badge "Terakreditasi"

TABS / ACCORDION: Pratama | Madya | Utama
  Setiap tab:
  ┌─────────────────────────────────────────────┐
  │  Gelar: B.Sc. (Hafiz Pratama)               │
  │  Persyaratan:  ✓ Usia minimal 30 tahun      │
  │                ✓ Kompetensi dasar tahfiz    │
  │  Manfaat:      ✓ Ijazah setara sarjana S1   │
  │                ✓ Akreditasi IAO             │
  │                ✓ Pengakuan internasional    │
  └─────────────────────────────────────────────┘

CTA: [Daftar Program Ini →]
```

---

### 4.5 Halaman Wisuda 30 Agustus 2026

```
/wisuda

Hero: foto prosesi wisuda / Tejasari, overlay gelap
Judul: "Wisuda Internasional — 30 Agustus 2026"

JADWAL ACARA (Timeline vertikal):
  07.00  Registrasi & Check-in Peserta
  08.00  Pembukaan & Sambutan
  09.00  Sidang Senat
  10.00  Prosesi Wisuda
  12.00  Ishoma
  13.00  Penampilan Seni & Budaya
  15.00  Penutupan
  16.00  Wisata Bebas Tejasari

LOKASI:
  Peta Google Maps embed
  + panduan rute dari Purwokerto, Banjarnegara, Yogyakarta

PANDUAN KEHADIRAN:
  3 tab: Peserta | Tamu Undangan | Wisatawan Umum

TAMU KEHORMATAN:
  Grid foto + nama + jabatan (pejabat, ulama, mitra IAO)

GALERI & DOKUMENTASI:
  Lightbox foto + embed video wisuda sebelumnya
```

---

### 4.6 Halaman Verifikasi Ijazah

```
/verifikasi

Form tunggal:
  [Masukkan Nomor Sertifikat / Scan QR Code]
  [Verifikasi]

Hasil:
  ✅ Valid — tampilkan: Nama, Gelar, Tanggal Kelulusan, Program
  ❌ Tidak ditemukan — pesan error informatif

Catatan: Data bersumber dari database LMS, diakses via Laravel API.
```

---

## 5. Desain Responsif

| Breakpoint | Lebar | Perilaku |
|---|---|---|
| **xs** | < 480px | Satu kolom, font lebih kecil, hero 100vh |
| **sm** | 480–768px | Satu kolom, grid 2 kolom untuk kartu |
| **md** | 768–1024px | Dua kolom layout, navigasi penuh |
| **lg** | 1024–1280px | Layout standar desktop |
| **xl** | > 1280px | Max-width 1280px, konten terpusat |

**Prioritas Mobile:**
- Tombol CTA selalu sticky di bawah layar mobile (hanya di halaman Home dan Program).
- Menu hamburger dengan drawer kanan.
- Hero text lebih kecil (38px), sub-text (16px).
- Kartu program full-width, scroll horizontal untuk galeri.

---

## 6. Animasi & Interaksi

| Elemen | Animasi |
|---|---|
| Hero heading | Fade + slide up, staggered per baris, 0.8s |
| Section heading | Fade in dari bawah saat masuk viewport |
| Kartu program | Hover: translateY(-6px) + shadow lebih dalam |
| Angka statistik | Count-up animation saat masuk viewport |
| Galeri foto | Hover: scale(1.05) + overlay caption |
| Tombol CTA | Pulse animation ringan (2s loop) |
| WhatsApp floating | Pulse hijau setiap 3 detik |
| Page transition | Fade antara halaman (Nuxt) |

---

## 7. SEO & Meta Tags

Setiap halaman memiliki meta tag spesifik:

```html
<!-- Home -->
<title>YHIE — Wisuda Internasional Hafiz & Maulana | 30 Agustus 2026 Purbalingga</title>
<meta name="description" content="Raih gelar B.Sc./M.Sc./Dr.Hc. terakreditasi IAO
  sambil berwisata di Desa Tejasari, Purbalingga. Daftar Program Wisata Sambil
  Wisuda 2026 Yayasan Hafiz Indonesia Emas." />

<!-- Open Graph -->
<meta property="og:title" content="Wisata Sambil Wisuda 2026 — YHIE" />
<meta property="og:image" content="/og-image-wisuda.jpg" />
<meta property="og:type" content="website" />

<!-- Schema.org Event -->
<script type="application/ld+json">
{
  "@type": "Event",
  "name": "Wisuda Internasional YHIE 2026",
  "startDate": "2026-08-30",
  "location": "Desa Wisata Tejasari, Purbalingga, Jawa Tengah",
  "organizer": "Yayasan Hafiz Indonesia Emas"
}
</script>
```

**Kata kunci target:**

| Segmen | Kata Kunci |
|---|---|
| Program tahfiz | "Sertifikasi Hafiz", "Kursus Hafiz bersertifikat", "Ijazah Hafiz internasional" |
| Program maulana | "Gelar Maulana", "Sertifikasi keagamaan Islam" |
| Acara wisuda | "Wisuda Al-Qur'an 2026", "Wisuda Hafiz Purbalingga" |
| Wisata | "Wisata Purbalingga 2026", "Desa Wisata Tejasari" |
| ASEAN (EN) | "Quran Certification ASEAN", "International Hafiz Degree IAO" |

---

## 8. Integrasi Backend (API)

Website profil mengonsumsi data dari Laravel API via `useFetch` / `$fetch` Nuxt.

| Endpoint | Fungsi |
|---|---|
| `POST /api/register` | Formulir pendaftaran peserta |
| `GET /api/programs` | Daftar program (SSG pre-fetch) |
| `GET /api/news` | Artikel berita (SSG + ISR) |
| `GET /api/gallery` | Foto galeri |
| `GET /api/testimonials` | Data testimoni |
| `POST /api/verify-certificate` | Verifikasi ijazah publik |
| `POST /api/contact` | Formulir kontak |

**Autentikasi:** Laravel Sanctum (Bearer token untuk area terproteksi, publik untuk endpoint di atas).

---

## 9. Performa & Kualitas

| Target | Nilai |
|---|---|
| **Lighthouse Performance** | ≥ 90 |
| **Lighthouse SEO** | ≥ 95 |
| **Lighthouse Accessibility** | ≥ 85 |
| **First Contentful Paint** | < 1.5 detik |
| **Time to Interactive** | < 3 detik |
| **Core Web Vitals** | Pass semua (LCP, CLS, FID) |

**Strategi:**
- Nuxt SSG: HTML pre-rendered, CDN global (Cloudflare Pages).
- Gambar: format WebP, lazy loading, `srcset` responsif.
- Font: preload Google Fonts, `font-display: swap`.
- CSS: Tailwind purge — hanya class yang dipakai yang di-include.
- JS: code splitting otomatis Nuxt per halaman.

---

## 10. Aksesibilitas

- Semua gambar memiliki atribut `alt` deskriptif.
- Kontras warna teks minimal AA (WCAG 2.1).
- Navigasi keyboard penuh (focus indicator terlihat).
- Landmark HTML5 semantik: `<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`.
- Form pendaftaran dengan `label` yang benar untuk setiap input.
- ARIA label pada tombol tanpa teks (ikon saja).

---

## 11. Aset yang Diperlukan

| Aset | Spesifikasi |
|---|---|
| Logo YHIE (full + ikon) | SVG, putih & berwarna |
| Logo IAO | SVG/PNG transparan |
| Foto Hero | Min 1920×1080px, WebP, ≥ 3 variasi (hero, landscape, momen wisuda) |
| Foto Fasilitas Tejasari | Min 8 foto: kolam, panggung, flamboyan, kuliner, dll |
| Foto Kegiatan / Wisuda | Min 12 foto dokumentasi |
| Video Loop Hero | 1920×1080, ≤ 10MB, MP4, silent (opsional) |
| Foto Testimoni | 6–10 foto peserta/ulama, square, 400×400px |
| Foto Pengurus | 6–8 foto formal struktural kepengurusan |
| Dokumen akreditasi IAO | PDF untuk halaman unduhan |

---

## 12. Stack Teknologi (Website Profil)

| Layer | Teknologi | Versi |
|---|---|---|
| **Framework Frontend** | Nuxt.js (Vue 3) | 3.x |
| **Mode Rendering** | Static Site Generation (SSG) | — |
| **Styling** | Tailwind CSS | 3.x |
| **State Management** | Pinia | — |
| **HTTP Client** | `$fetch` / `useFetch` (bawaan Nuxt) | — |
| **i18n** | `@nuxtjs/i18n` | — |
| **SEO** | `@nuxtjs/seo` | — |
| **Animasi** | CSS transitions + `@vueuse/motion` | — |
| **Gambar** | `@nuxt/image` (WebP + lazy load) | — |
| **Form** | `VeeValidate` + `Zod` | — |
| **Deployment** | Cloudflare Pages / Vercel | — |
| **Backend API** | Laravel + Sanctum | Latest LTS |

---

*Dokumen ini adalah panduan desain untuk Website Profil YHIE.*
*Untuk desain LMS, lihat: `design-lms.md`*

*Disusun oleh Tim Teknologi Informasi — Yayasan Hafiz Indonesia Emas (YHIE)*
*yhie.org | sertifikasihafiz.com — Versi 1.0, Mei 2026*
