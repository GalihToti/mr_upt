<?php
include 'backend/koneksi.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Register</title>
  <link rel="stylesheet" href="./assets/css/output.css">
</head>

<body class="bg-gradient-to-r from-blue-800 to-sky-600 min-h-screen flex items-center justify-center font-sans">

  <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full">
    <!-- Judul -->
    <h2 class="text-3xl font-bold text-center text-blue-800 mb-8">Register</h2>

    <!-- Form -->
    <form action="backend/proses-regist.php" method="post" class="space-y-5">
      <!-- Username -->
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" name="Username"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          placeholder="Masukkan username" required>
      </div>

      <div>
        <label for="nama_user" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="nama_user"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          placeholder="Masukkan nama lengkap" required>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="Password"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          placeholder="Masukkan password" required>
      </div>
        
        <!-- Unit/Biro/Lembaga/Fakultas/Prodi (drop-down pilihan) -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="unit">Unit/Biro/Lembaga/Fakultas/Prodi</label>
        <select id="unit" name="id_unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required> 
          <option value="" disabled selected>Pilih unit</option>
          <?php

          $unit = mysqli_query($koneksi, "SELECT * FROM unit");
          while ($b = mysqli_fetch_array($unit)) {
            echo "<option value='{$b['id_unit']}'>{$b['nama_unit']}</option>";
          }
          ?>
            <option value="99">Lainnya</option>
        </select>
           <p class="text-sm font-normal text-gray-700">*Bila unit tidak ada, pilih Lainnya</p>
      </div>
        
      <div>
 <label class="block text-sm font-medium text-gray-700 mb-1" for="unit">Unit/Biro/Lembaga/Fakultas/Prodi Lain</label>
        <input type="text" id="unitLain" name="unitLain"
          class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Isi jika unit tidak ada di daftar" />
      </div>
        
      <!-- Area untuk menampilkan error -->
      <div class="min-h-[60px]">
        <?php if (!empty($_SESSION['register_errors'])) { ?>
          <div class="text-red-600" role="alert">
            <div class="flex items-start">
              <div class="py-1 flex-shrink-0">
                <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1">
                <?php foreach ($_SESSION['register_errors'] as $error): ?>
                  <p class="text-sm mb-1"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php unset($_SESSION['register_errors']); ?>
        <?php } ?>
      </div>

      <!-- Tombol -->
      <button type="submit" 
        class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition mt-4">
        Register
      </button>
    </form>

    <div class="text-center text-sm text-gray-600 mt-6">
      <p>Sudah punya akun?</p>
      <a href="./formlogin.php" class="text-blue-600 font-medium hover:underline">Login</a>
    </div>
  </div>

</body>

</html>