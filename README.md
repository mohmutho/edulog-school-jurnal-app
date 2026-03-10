# EduLog: School Journal & Attendance System 🍎

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-red)](https://laravel.com)
[![Vue 3](https://img.shields.io/badge/Vue.js-3.x-green)](https://vuejs.org)
[![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4.0-38bdf8)](https://tailwindcss.com)

EduLog adalah solusi manajemen kegiatan belajar mengajar yang dirancang untuk menjembatani celah administrasi di sekolah. Dibangun dengan fokus pada efisiensi guru dan transparansi data kehadiran siswa.

## ⚠️ Latar Belakang & Masalah
Banyak sekolah masih menggunakan jurnal fisik yang rentan hilang, sulit direkap, dan tidak memberikan data *real-time* kepada kepala sekolah atau orang tua. Sebagai tenaga pendidik, saya memahami bahwa beban administrasi sering kali mengurangi waktu efektif mengajar.

## 🚀 Solusi Teknis
EduLog mentransformasi proses tersebut menjadi digital:
- **Real-time Attendance**: Pencatatan kehadiran instan dengan sinkronisasi ke jurnal kelas.
- **Activity Journaling**: Dokumentasi materi harian yang terintegrasi dengan jadwal pelajaran.
- **High Performance UI**: Menggunakan **Inertia.js** untuk pengalaman Single Page Application (SPA) tanpa kehilangan kemudahan routing Laravel.
- **Modern Styling**: Implementasi **Tailwind v4** untuk desain yang sangat ringan dan responsif.

## 🏗️ Arsitektur & Teknologi
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 (Composition API) via Inertia.js
- **Styling**: Tailwind CSS v4
- **Database**: MySQL / PostgreSQL
- **State Management**: Reactive Vue Composition API

## 🛠️ Instalasi
1. Clone repository:
    git clone [https://github.com/username/project-name.git](https://github.com/username/project-name.git)
2. Install Dependencies:
    composer install && npm install
3. Setup .env dan database :
    cp .env.example .env
    php artisan key:generate
4. Jalan Migrasi Laravel dan Server :
    php artisan migrate --seed
    npm run dev
    php artisan serve
