<?php
include 'backend/koneksi.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: formlogin.php");
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
    <h1 class="text-2xl font-bold mb-6">Form Input Laporan Kerusakan</h1>
    <form id="mrForm" class="space-y-6" enctype="multipart/form-data" action="./backend/proses-laporan-user.php" method="POST">

      <!-- 1. Nomor Surat -->
      <div>
        <label class="block font-semibold mb-1" for="nomorSurat">Nomor Surat</label>
        <input type="text" id="nomorSurat" name="nomorSurat" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>

      <!-- 2. Keperluan Surat -->
      <div>
        <label class="block font-semibold mb-1" for="keperluanSurat">Keperluan Surat</label>
        <input type="text" id="keperluanSurat" name="keperluanSurat" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>

      <!-- 3. Lampiran (upload dokumen opsional) -->
      <div>
        <label class="block font-semibold mb-1" for="lampiran">Lampiran (opsional)</label>
        <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
          class="w-full" />
        <p class="text-sm text-gray-500 mt-1">Upload surat resmi, bukti foto, dll.</p>
      </div>

      <!-- 4. Tanggal Laporan -->
      <div>
        <label class="block font-semibold mb-1" for="tanggalLaporan">Tanggal Laporan</label>
        <input type="date" id="tanggalLaporan" name="tanggalLaporan" required
          class="w-full border border-gray-300 rounded px-3 py-2" value="<?= date('Y-m-d'); ?>" />
      </div>

      <!-- 5. Perangkat (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Perangkat yang Bermasalah</label>
        <div id="perangkatList" class="space-y-2">
          <input type="text" name="perangkat[]" required
            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan perangkat" />
        </div>
        <button type="button" id="addPerangkat" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Perangkat
        </button>
      </div>

      <!-- 6. Keluhan (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Keluhan / Masalah</label>
        <div id="keluhanList" class="space-y-2">
          <input type="text" name="keluhan[]" required
            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan keluhan" />
        </div>
        <button type="button" id="addKeluhan" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Keluhan
        </button>
      </div>

      <!-- 7. Unit/Biro/Lembaga/Fakultas/Prodi (drop-down pilihan) -->
      <div>
        <label class="block font-semibold mb-1" for="unit">Unit/Biro/Lembaga/Fakultas/Prodi</label>
        <select id="unit" name="id_unit" class="w-full border border-gray-300 rounded px-3 py-2" required>
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

      <!-- 8. Unit/Biro/Lembaga Lain (isian manual jika tidak ada di daftar) -->
      <div id="unitLainContainer" style="display: none;">
        <label class="block font-semibold mb-1" for="unitLain">Unit/Biro/Lembaga Lain</label>
        <input type="text" id="unitLain" name="unitLain"
          class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Isi nama unit baru" />
      </div>

      <!-- HILANGKAN: Hasil Pengecekan, Perbaikan, Petugas, dan Status -->

      <div class="pt-4">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
          Kirim Laporan
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

    // Fungsi show/hide unit lain
    document.addEventListener('DOMContentLoaded', function() {
      const unitSelect = document.getElementById('unit');
      const unitLainContainer = document.getElementById('unitLainContainer');
      
      function toggleUnitLain() {
        if (unitSelect.value === '99') {
          unitLainContainer.style.display = 'block';
          // Tambah required jika unit lain ditampilkan
          document.getElementById('unitLain').required = true;
        } else {
          unitLainContainer.style.display = 'none';
          document.getElementById('unitLain').required = false;
        }
      }
      
      // Initial state
      toggleUnitLain();
      
      // Listen for changes
      unitSelect.addEventListener('change', toggleUnitLain);
    });
  </script>
  <br><br><br><br><br><br>
</body>

</html>