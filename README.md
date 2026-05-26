# Yhie Monorepo

Selamat datang di repositori **yhie-monorepo**. Projek ini menggunakan arsitektur monorepo yang memisahkan bagian backend, frontend, dan LMS ke dalam satu repositori terpusat.

##  Tech Stack

* **Backend:** [Laravel 12.x](https://laravel.com) (PHP 8.2+) — Sebagai core API & business logic.
* **Frontend:** [Nuxt 4.x](https://nuxt.com) (Vue 3 & TypeScript) — Sebagai Web UI Utama/User Interface.
* **LMS:** [Moodle](https://moodle.org) (PHP) — Sebagai platform manajemen pembelajaran & kursus.

---

##  Struktur Folder

```text
yhie-monorepo/
├── backend/          # Aplikasi API (Laravel)
├── frontend/         # Aplikasi Web Utama (Nuxt)
└── lms/              # Platform E-Learning (Moodle)
