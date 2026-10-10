# Sistem Informasi Maintenance & Repair - UPT Komputer UNIPMA

Proyek ini adalah Sistem Informasi **Maintenance & Repair** yang dikembangkan khusus untuk **UPT Komputer Universitas PGRI Madiun (UNIPMA)**. Sistem ini bertujuan untuk memfasilitasi pelaporan kerusakan, pelacakan status perbaikan, dan manajemen pemeliharaan perangkat keras maupun perangkat lunak di lingkungan kampus.

## 🚀 Fitur Utama

* **Sistem Tiket (Helpdesk):** Dosen, staf, atau mahasiswa dapat melaporkan kerusakan perangkat komputer/jaringan.
* **Tracking Perbaikan:** Memantau status laporan kerusakan (Menunggu, Dikerjakan, Selesai, Dibatalkan).
* **Manajemen Inventaris:** Pencatatan aset perangkat keras (PC, Printer, Proyektor, Router, dll) yang berada di bawah pengawasan UPT Komputer.
* **Log Perbaikan (Riwayat):** Mencatat setiap tindakan perbaikan yang dilakukan oleh teknisi beserta suku cadang yang digunakan.
* **Laporan & Statistik:** Rekapitulasi kerusakan per bulan/tahun untuk evaluasi dan pengadaan.

## 🛠️ Teknologi yang Digunakan

* **Backend:** PHP Native
* **Frontend:** HTML5, CSS3 dengan **Tailwind CSS**, JavaScript
* **Database:** MySQL 
* **Web Server:** Apache via Laragon

## 📋 Prasyarat Sistem

Sebelum menjalankan proyek ini, pastikan sistem Anda sudah terpasang:

* Web Server lokal seperti **Laragon** atau **XAMPP** (dengan PHP >= 8.0)
* MySQL (sudah satu paket di dalam Laragon/XAMPP)
* Node.js & NPM (Opsional, hanya jika Anda perlu melakukan *compile/build* ulang file Tailwind CSS secara lokal)

## ⚙️ Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

1. **Persiapkan Folder Proyek**
   * *Clone* repositori ini atau *download* sebagai file ZIP.
   * Ekstrak dan pindahkan folder proyek ke dalam direktori *document root* server lokal Anda:
     * Jika menggunakan **Laragon**: pindahkan ke `C:\laragon\www\`
     * Jika menggunakan **XAMPP**: pindahkan ke `C:\xampp\htdocs\`

2. **Pengaturan Database**
   * Buka browser dan akses **phpMyAdmin** (biasanya di `http://localhost/phpmyadmin`).
   * Buat database baru, misalnya dengan nama `db_maintenance_unipma`.
   * Pilih database tersebut, lalu klik menu **Import**.
   * Cari dan *upload* file database SQL yang telah disediakan (misalnya: `database.sql` yang ada di dalam folder proyek), lalu klik **Go** atau **Import**.

3. **Konfigurasi Koneksi PHP**
   * Buka folder proyek menggunakan teks editor (VS Code, Sublime Text, dll).
   * Cari file pengaturan koneksi database (biasanya bernama `koneksi.php`, `config.php`, atau `database.php`).
   * Sesuaikan baris konfigurasi dengan *environment* lokal Anda:
     ```php
     $host     = "localhost";
     $user     = "root";
     $password = ""; // Kosongkan jika default Laragon/XAMPP
     $database = "db_maintenance_unipma"; // Sesuaikan dengan nama database yang dibuat
     ```

4. **Kompilasi Tailwind CSS (Opsional)**
   * *Catatan: Lewati langkah ini jika file CSS hasil build (misal: `style.css` atau `output.css`) sudah tersedia di folder proyek atau menggunakan CDN.*
   * Buka terminal di dalam folder proyek, lalu jalankan:
     ```bash
     npm install
     npx tailwindcss -i ./src/input.css -o ./dist/output.css --watch
     ```

5. **Jalankan Aplikasi**
   * Buka browser dan ketikkan alamat proyek Anda, contohnya: 
     `http://localhost/nama_folder_proyek/` (untuk XAMPP) atau `http://nama_folder_proyek.test/` (untuk Laragon).

## 👥 Kontributor / Tim Pengembang

* **[Galih Toti Ilham Prayoga]** - *Lead Developer / Programmer*
* **[Indra Wibisana]** - *UI/UX Designer*
* **[5 Orang lainnya]** - *System Analyst*

*Dibuat untuk keperluan akademik dan manajemen internal UPT Komputer UNIPMA.*