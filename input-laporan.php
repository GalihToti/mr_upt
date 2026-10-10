<?php
include 'backend/koneksi.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: formlogin.php");
  exit;
}    
if ($_SESSION['user']['Level'] != 'admin') {
    // Jika bukan admin, redirect ke halaman lain atau tampilkan error
    header("Location: unauthorized.php"); // atau index.php
    exit;
}


$nama  = $_SESSION['user']['nama_user'];
$level = $_SESSION['user']['Level'];
$userid  = $_SESSION['user']['UserID'];

// Ambil daftar petugas dari database
$query_petugas = "SELECT * FROM petugas ORDER BY nama_petugas ASC";
$result_petugas = mysqli_query($koneksi, $query_petugas);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="assets/1.png" />
  <title>Input Laporan</title>
  <link rel="stylesheet" href="./assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 font-sans min-h-screen" >
  <!-- Navbar -->
<nav class="bg-blue-600 text-white shadow-lg fixed top-0 left-0 w-full z-50">
  <div class="container mx-auto px-4 sm:px-6 py-3">
    <div class="flex justify-between items-center">
      
      <!-- Logo -->
      <div class="flex items-center">
        <img src="assets/logo.png" alt="Logo" class="mr-2" style="width: 36px; height: 36px; object-fit: contain;">
        <span class="font-bold text-xl">UPT Komputer UNIPMA</span>
      </div>

      <!-- Menu Utama -->
      <div class="flex space-x-6 items-center">

        <a href="index.php" class="hover:bg-blue-700 rounded-lg px-4 py-2 transition-colors duration-200 flex items-center">
          <i class="fas fa-home mr-2"></i> Home
        </a>

        <!-- Dropdown Laporan (Klik) -->
<div class="relative">
  <button id="dropdownToggle" class="hover:bg-blue-700 rounded-lg px-4 py-2 transition-colors duration-200 flex items-center">
    Laporan
  <i class="fas fa-chevron-down mt-1" style="font-size: 12px; margin-left: 4px;"></i>
  </button>

    <!-- Dropdown Laporan -->
  <div id="dropdownMenu" class="hidden absolute bg-blue-600 rounded-md shadow-lg mt-4 w-48 z-50">
	<?php if ($level == 'user'): ?>
<a href="input-laporan-user.php" class="px-4 py-2 hover:bg-blue-700 transition-colors duration-200 flex items-center">
      <i class="fas fa-plus mr-2"></i> Input Laporan
    </a>      
      <?php endif; ?>
       <?php if ($level == 'admin'): ?>
      <a href="input-laporan.php" class="px-4 py-2 hover:bg-blue-700 transition-colors duration-200 flex items-center">
      <i class="fas fa-plus mr-2"></i> Input Laporan
    </a><?php endif; ?>

    <a href="laporan.php" class="px-4 py-2 hover:bg-blue-700 transition-colors duration-200 flex items-center">
      <i class="fas fa-file-alt mr-2"></i> Data Laporan
    </a>

    <?php if ($level == 'admin'): ?>
    <a href="manajemen-laporan.php" class="px-4 py-2 hover:bg-blue-700 transition-colors duration-200 flex items-center">
      <i class="fas fa-tasks mr-2"></i> Manajemen Data
    </a>
    <?php endif; ?>
  </div>
</div>

        <?php if ($level == 'admin'): ?>
        <a href="petugas.php" class="hover:bg-blue-700 rounded-lg px-4 py-2 transition-colors duration-200 flex items-center">
          <i class="fas fa-user-gear mr-2"></i> Petugas
        </a>
        <?php endif; ?>
          
          <?php if ($level == 'admin'): ?>
        <a href="admin-users.php" class="hover:bg-blue-700 rounded-lg px-4 py-2 transition-colors duration-200 flex items-center">
          <i class="fa-solid fa-user-check mr-2"></i> Kelola User
        </a>
        <?php endif; ?>


        <button onclick="openModal()" class="bg-red-500 hover:bg-red-600 rounded-lg px-4 py-2 transition-colors duration-200 flex items-center">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </button>

      </div>
    </div>
  </div>
</nav>


  <!-- Modal Logout -->
  <div id="logoutModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md mx-4">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Logout</h3>
        <p class="text-gray-600">Apakah Anda yakin ingin keluar dari sistem?</p>
      </div>
      <div class="flex justify-center gap-4">
        <a id="confirmLogout" href="backend/logout.php"
          class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
          <i class="fas fa-sign-out-alt mr-2"></i>Keluar
        </a>
        <button onclick="closeModal()"
          class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors duration-200">
          <i class="fas fa-times mr-2"></i>Batal
        </button>
      </div>
    </div>
  </div>

  <script>
    // Fungsi modal logout
    function openModal() {
      const modal = document.getElementById('logoutModal');
      modal.classList.remove('hidden');
      document.addEventListener("keydown", handleKeys);
    }

    function closeModal() {
      document.getElementById('logoutModal').classList.add('hidden');
      document.removeEventListener("keydown", handleKeys);
    }

    function handleKeys(e) {
      if (e.key === "Enter") {
        document.getElementById("confirmLogout").click();
      } else if (e.key === "Escape") {
        closeModal();
      }
    }
      
      // Fungsi Dropdown
  const toggle = document.getElementById('dropdownToggle');
  const menu = document.getElementById('dropdownMenu');

  toggle.addEventListener('click', function() {
    menu.classList.toggle('hidden');
  });

  // Klik di luar dropdown untuk menutup
  document.addEventListener('click', function(e) {
    if (!toggle.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.add('hidden');
    }
  });

  </script>
<br><br><br>
  <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Form Input Pendataan MR</h1>
    <form id="mrForm" class="space-y-6" enctype="multipart/form-data" action="./backend/proses-laporan.php" method="POST">

      <!-- 1. No Pendataan MR (auto generate) -->
      <input type="hidden" id="noMR" name="noMR" value="" />
      <!-- 2. Nomor Surat -->
      <div>
        <label class="block font-semibold mb-1" for="nomorSurat">Nomor Surat</label>
        <input type="text" id="nomorSurat" name="nomorSurat" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <!-- 3. Keperluan Surat -->
      <div>
        <label class="block font-semibold mb-1" for="keperluanSurat">Keperluan Surat</label>
        <input type="text" id="keperluanSurat" name="keperluanSurat" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <!-- 4. Lampiran (upload dokumen opsional) -->
      <div>
        <label class="block font-semibold mb-1" for="lampiran">Lampiran (opsional)</label>
        <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
          class="w-full" />
        <p class="text-sm text-gray-500 mt-1">Upload surat resmi, bukti foto, dll.</p>
      </div>
      <!-- 5. Tanggal Laporan -->
      <div>
        <label class="block font-semibold mb-1" for="tanggalLaporan">Tanggal Laporan</label>
        <input type="date" id="tanggalLaporan" name="tanggalLaporan" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <!-- 6. Tanggal Pengecekan -->
      <div>
        <label class="block font-semibold mb-1" for="tanggalPengecekan">Tanggal Pengecekan</label>
        <input type="date" id="tanggalPengecekan" name="tanggalPengecekan" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <!-- 7. Perangkat (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Perangkat</label>
        <div id="perangkatList" class="space-y-2">
          <input type="text" name="perangkat[]" required
            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan perangkat" />
        </div>
        <button type="button" id="addPerangkat" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Perangkat
        </button>
      </div>
      <!-- 8. Keluhan (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Keluhan</label>
        <div id="keluhanList" class="space-y-2">
          <input type="text" name="keluhan[]" required
            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan keluhan" />
        </div>
        <button type="button" id="addKeluhan" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Keluhan
        </button>
      </div>
      <!-- 9. Hasil Pengecekan (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Hasil Pengecekan</label>
        <div id="hasilList" class="space-y-2">
          <input type="text" name="hasilPengecekan[]" required
            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan hasil pengecekan" />
        </div>
        <button type="button" id="addHasil" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Hasil Pengecekan
        </button>
      </div>
      <!-- 10. Unit/Biro/Lembaga/Fakultas/Prodi (drop-down pilihan) -->
      <div>
        <label class="block font-semibold mb-1" for="unit">Unit/Biro/Lembaga/Fakultas/Prodi</label>
        <select id="unit" name="id_unit" class="w-full border border-gray-300 rounded px-3 py-2">
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
      <!-- 11. Unit/Biro/Lembaga Lain (isian manual jika tidak ada di daftar) -->
      <div>
        <label class="block font-semibold mb-1" for="unitLain">Unit/Biro/Lembaga Lain <span class="text-sm font-normal text-gray-600"> (*jika tidak ada di daftar) </span></label>
        <input type="text" id="unitLain" name="unitLain"
          class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Isi jika unit tidak ada di daftar" />
      </div>
      <!-- 12. Perbaikan (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Perbaikan</label>
        <div id="perbaikanList" class="space-y-2">
          <input type="text" name="perbaikan[]" required
            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan perbaikan" />
        </div>
        <button type="button" id="addPerbaikan" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Perbaikan
        </button>
      </div>
      <!-- 13. Petugas (drop-down daftar teknisi UPT Komputer) -->
        <label class="block font-semibold mb-1" for="unit">Petugas</label>
      <select id="petugas" name="id_petugas" required
  class="w-full border border-gray-300 rounded px-3 py-2">
  <option value="" disabled selected>Pilih petugas</option>
  <?php while ($row = mysqli_fetch_assoc($result_petugas)): ?>
    <option value="<?= $row['id_petugas']; ?>">
      <?= htmlspecialchars($row['nama_petugas']); ?>
    </option>
  <?php endwhile; ?>
</select>

      <!-- 14. Status -->
      <div>
        <label class="block font-semibold mb-1" for="status">Status</label>
        <select id="status" name="status" required
          class="w-full border border-gray-300 rounded px-3 py-2">
          <option value="Antrian">Antrian</option>
          <option value="Diproses">Diproses</option>
            <option value="Tertunda">Tertunda</option>
            <option value="Selesai">Selesai</option>
            <option value="Proses Dibelikan Perangkat oleh BAU">Proses Dibelikan Perangkat oleh BAU</option>
            <option value="Perangkat Sudah Dibelikan oleh BAU">Perangkat Sudah Dibelikan oleh BAU</option>
            <option value="Proses Dibelikan Perangkat oleh UPT">Proses Dibelikan Perangkat oleh UPT</option>
            <option value="Perangkat Sudah Dibelikan oleh UPT">Perangkat Sudah Dibelikan oleh UPT</option>
        </select>
      </div>
      <div class="pt-4">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
          Simpan Data
        </button>
      </div>
    </form>
  </div>
  <script>
    // Fungsi untuk menambah input multi
    function addInput(buttonId, containerId, name, placeholder) {
      const btn = document.getElementById(buttonId);
      const container = document.getElementById(containerId);
      btn.addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'text';
        input.name = name + '[]';
        input.placeholder = placeholder;
        input.required = true;
        input.className = 'w-full border border-gray-300 rounded px-3 py-2';
        container.appendChild(input);
      });
    }
    addInput('addPerangkat', 'perangkatList', 'perangkat', 'Masukkan perangkat');
    addInput('addKeluhan', 'keluhanList', 'keluhan', 'Masukkan keluhan');
    addInput('addHasil', 'hasilList', 'hasilPengecekan', 'Masukkan hasil pengecekan');
    addInput('addPerbaikan', 'perbaikanList', 'perbaikan', 'Masukkan perbaikan');
  </script>
    <br><br><br><br><br><br>
</body>

</html>