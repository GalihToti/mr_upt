# Sistem Informasi Maintenance & Repair - UPT Komputer UNIPMA

Proyek ini adalah Sistem Informasi **Maintenance & Repair** yang dikembangkan khusus untuk **UPT Komputer Universitas PGRI Madiun (UNIPMA)**. Sistem ini bertujuan untuk memfasilitasi pelaporan kerusakan, pelacakan status perbaikan, dan manajemen pemeliharaan perangkat keras maupun perangkat lunak di lingkungan kampus.

## 🚀 Fitur Utama

*   **Sistem Tiket (Helpdesk):** Dosen, staf, atau mahasiswa dapat melaporkan kerusakan perangkat komputer/jaringan.
*   **Tracking Perbaikan:** Memantau status laporan kerusakan (Menunggu, Dikerjakan, Selesai, Dibatalkan).
*   **Manajemen Inventaris:** Pencatatan aset perangkat keras (PC, Printer, Proyektor, Router, dll) yang berada di bawah pengawasan UPT Komputer.
*   **Log Perbaikan (Riwayat):** Mencatat setiap tindakan perbaikan yang dilakukan oleh teknisi beserta suku cadang yang digunakan.
*   **Laporan & Statistik:** Rekapitulasi kerusakan per bulan/tahun untuk evaluasi dan pengadaan.

## 🛠️ Teknologi yang Digunakan

*(Silakan ubah bagian ini sesuai dengan stack teknologi yang Anda gunakan)*
*   **Backend:** [PHP / Laravel 11 / CodeIgniter]
*   **Frontend:** [Bootstrap 5 / TailwindCSS / Vue.js]
*   **Database:** [MySQL / MariaDB]
*   **Web Server:** [Apache / Nginx / Laragon]

## 📋 Prasyarat Sistem

Sebelum menginstal proyek ini, pastikan sistem Anda sudah terpasang:
*   PHP >= 8.1
*   Composer
*   MySQL/MariaDB
*   Node.js & NPM (jika menggunakan frontend build tools)

## ⚙️ Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

1. **Clone Repositori**
   ```bash
   git clone https://github.com/[username_anda]/[nama_repo].git
   cd [nama_repo]
   ```

2. **Instal Dependensi (Contoh untuk Laravel)**
   ```bash
   composer install
   npm install
   ```

3. **Pengaturan Database**
   * Salin file `.env.example` menjadi `.env`:
     ```bash
     cp .env.example .env
     ```
   * Buka file `.env` dan sesuaikan konfigurasi database Anda:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=nama_database_unipma
     DB_USERNAME=root
     DB_PASSWORD=
     ```

4. **Generate Key & Migrasi Database**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di `http://localhost:8000`.

## 👥 Kontributor / Tim Pengembang

*   **[Nama Anda]** - *Lead Developer / Programmer*
*   **[Nama Anggota Tim]** - *System Analyst*
*   **[Nama Anggota Tim]** - *UI/UX Designer*

## 📞 Kontak & Dukungan

Jika ada kendala dalam sistem ini, silakan hubungi UPT Komputer UNIPMA melalui:
*   **Email:** [email_upt@unipma.ac.id]
*   **Telepon/WA:** [Nomor Kontak]
*   **Ruang:** Gedung [Nama Gedung], Lantai [X], Kampus UNIPMA.

---
*Dibuat untuk keperluan akademik dan manajemen internal UPT Komputer UNIPMA.*