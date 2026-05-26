# Yhie Monorepo

Selamat datang di repositori **yhie-monorepo**. Projek ini menggunakan arsitektur monorepo yang memisahkan bagian backend, frontend, dan LMS ke dalam satu repositori terpusat.

## 💻 Tech Stack

* **Backend:** [Laravel 13.x](https://laravel.com) (PHP 8.3+) — Sebagai core API & business logic.
* **Frontend:** [Nuxt 4.x](https://nuxt.com) (Vue 3 & TypeScript) — Sebagai Web UI Utama/User Interface.
* **LMS:** [Moodle](https://moodle.org) (PHP) — Sebagai platform manajemen pembelajaran & kursus.

---

## 📁 Struktur Folder

```text
yhie-monorepo/
├── backend/          # Aplikasi API (Laravel)
├── frontend/         # Aplikasi Web Utama (Nuxt)
└── lms/              # Platform E-Learning (Moodle)
```

---

## ⚙️ Instalasi

### Prasyarat

Sebelum memulai instalasi, pastikan sistem kamu sudah memenuhi prasyarat berikut:
* **PHP** >= 8.2
* **Node.js** >= 18.x & **npm/yarn/pnpm/bun**
* **Composer**
* **Database** (MySQL / PostgreSQL / dll)

### 1. Clone Repositori

```bash
git clone [https://github.com/username/yhie-monorepo.git](https://github.com/username/yhie-monorepo.git)
cd yhie-monorepo
```

### 2. Setup Backend (Laravel)

Buka terminal dan masuk ke direktori `backend`:

```bash
cd backend
```

Langkah-langkah instalasi backend:
1. Install dependensi PHP menggunakan Composer:
```bash
   composer install
   ```
2. Salin file environment:
```bash
   cp .env.example .env
   ```
3. Generate application key:
```bash
   php artisan key:generate
   ```
4. Sesuaikan konfigurasi database (kredensial, nama DB) di dalam file `.env`.
5. Jalankan migrasi database:
```bash
   php artisan migrate
   ```
6. Jalankan local server:
```bash
   php artisan serve
   ```
   *(Secara default, API akan berjalan di `http://localhost:8000`)*

### 3. Setup Frontend (Nuxt)

Buka terminal baru dan masuk ke direktori `frontend`:

```bash
cd frontend
```

Langkah-langkah instalasi frontend:
1. Install dependensi Node.js:
```bash
   npm install
   ```
2. Salin file environment:
```bash
   cp .env.example .env
   ```
3. Sesuaikan endpoint API backend (misal: `NUXT_PUBLIC_API_URLE=http://localhost:8000/api`) di dalam file `.env`.
4. Jalankan development server:
```bash
   npm run dev
   ```
   *(Secara default, Frontend akan berjalan di `http://localhost:3000`)*

### 4. Setup LMS (Moodle)

*Belum ada dokumentasi instalasi untuk bagian ini.*
