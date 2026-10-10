<?php
include 'backend/koneksi.php';
session_start();

$nama  = $_SESSION['user']['nama_user'];
$level = $_SESSION['user']['Level'];

if (!isset($_SESSION['user'])) {
  header("Location: formlogin.php");
  exit;
}

if ($_SESSION['user']['Level'] != 'admin') {
    // Jika bukan admin, redirect ke halaman lain atau tampilkan error
    header("Location: unauthorized.php"); // atau index.php
    exit;
}



if (!isset($_GET['id'])) {
  header("Location: manajemen-laporan.php");
  exit;
}

$id_mr = $_GET['id'];

// Ambil data laporan utama
$query = "SELECT * FROM laporan_mr WHERE id_mr = $id_mr";
$result = mysqli_query($koneksi, $query);
$laporan = mysqli_fetch_assoc($result);

// Ambil data lampiran
$query_lampiran = "SELECT * FROM lampiran WHERE id_mr = $id_mr";
$result_lampiran = mysqli_query($koneksi, $query_lampiran);
$lampiran = mysqli_fetch_assoc($result_lampiran);

// Ambil data keluhan
$query_keluhan = "SELECT * FROM keluhan WHERE id_mr = $id_mr";
$result_keluhan = mysqli_query($koneksi, $query_keluhan);
$keluhans = [];
while ($row = mysqli_fetch_assoc($result_keluhan)) {
  $keluhans[] = $row['deskripsi_keluhan'];
}

// Ambil data perbaikan
$query_perbaikan = "SELECT * FROM perbaikan WHERE id_mr = $id_mr";
$result_perbaikan = mysqli_query($koneksi, $query_perbaikan);
$perbaikans = [];
while ($row = mysqli_fetch_assoc($result_perbaikan)) {
  $perbaikans[] = $row['deskripsi_perbaikan'];
}

// Ambil data perangkat
$query_perangkat = "SELECT * FROM perangkat WHERE id_unit = {$laporan['id_unit']}";
$result_perangkat = mysqli_query($koneksi, $query_perangkat);
$perangkats = [];
while ($row = mysqli_fetch_assoc($result_perangkat)) {
  $perangkats[] = $row['nama_unit'];
}

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil data dari form
  $nomor = $_POST['nomorSurat'];
  $keperluan = $_POST['keperluanSurat'];
  $tgl_lap = $_POST['tanggalLaporan'];
  $tgl_cek = $_POST['tanggalPengecekan'];
  $status = $_POST['status'];
  $id_unit = $_POST['id_unit'];
  $id_petugas = $_POST['id_petugas'];
  
  // Ambil array multi input
  $perangkat = $_POST['perangkat'] ?? [];
  $keluhan = $_POST['keluhan'] ?? [];
  $hasilPengecekan = $_POST['hasilPengecekan'] ?? [];
  $perbaikan = $_POST['perbaikan'] ?? [];
  
  // Gabungkan hasil pengecekan jadi string
  $hasil = implode(", ", $hasilPengecekan);
  
  // Mulai transaction
  $koneksi->begin_transaction();
  
  try {
    // Update tabel laporan_mr
    $update_stmt = $koneksi->prepare("UPDATE laporan_mr 
      SET nomor_surat = ?, keperluan_surat = ?, tanggal_laporan = ?, 
          tanggal_pengecekan = ?, hasil_pengecekan = ?, status = ?, id_unit = ?
      WHERE id_mr = ?");
    
    $update_stmt->bind_param(
      "ssssssii",
      $nomor,
      $keperluan,  
      $tgl_lap,
      $tgl_cek,
      $hasil,
      $status,
      $id_unit,
      $id_mr
    );
    
    if (!$update_stmt->execute()) {
      throw new Exception("Gagal mengupdate laporan: " . $update_stmt->error);
    }
    
    $update_stmt->close();
    
    // Hapus data lama dan insert data baru untuk keluhan
    $delete_keluhan = $koneksi->prepare("DELETE FROM keluhan WHERE id_mr = ?");
    $delete_keluhan->bind_param("i", $id_mr);
    $delete_keluhan->execute();
    $delete_keluhan->close();
    
    foreach ($keluhan as $k) {
      if (!empty(trim($k))) {
        $insert_keluhan = $koneksi->prepare("INSERT INTO keluhan (id_mr, deskripsi_keluhan) VALUES (?, ?)");
        $insert_keluhan->bind_param("is", $id_mr, $k);
        if (!$insert_keluhan->execute()) {
          throw new Exception("Gagal menyimpan keluhan: " . $insert_keluhan->error);
        }
        $insert_keluhan->close();
      }
    }
    
    // Hapus data lama dan insert data baru untuk perbaikan
    $delete_perbaikan = $koneksi->prepare("DELETE FROM perbaikan WHERE id_mr = ?");
    $delete_perbaikan->bind_param("i", $id_mr);
    $delete_perbaikan->execute();
    $delete_perbaikan->close();
    
    foreach ($perbaikan as $p) {
      if (!empty(trim($p))) {
        $insert_perbaikan = $koneksi->prepare("INSERT INTO perbaikan (id_mr, id_petugas, deskripsi_perbaikan) VALUES (?, ?, ?)");
        $insert_perbaikan->bind_param("iis", $id_mr, $id_petugas, $p);
        if (!$insert_perbaikan->execute()) {
          throw new Exception("Gagal menyimpan perbaikan: " . $insert_perbaikan->error);
        }
        $insert_perbaikan->close();
      }
    }
    
    // Hapus data lama dan insert data baru untuk perangkat
    $delete_perangkat = $koneksi->prepare("DELETE FROM perangkat WHERE id_unit = ?");
    $delete_perangkat->bind_param("i", $id_unit);
    $delete_perangkat->execute();
    $delete_perangkat->close();
    
    foreach ($perangkat as $p) {
      if (!empty(trim($p))) {
        $insert_perangkat = $koneksi->prepare("INSERT INTO perangkat (id_unit, nama_unit) VALUES (?, ?)");
        $insert_perangkat->bind_param("is", $id_unit, $p);
        if (!$insert_perangkat->execute()) {
          throw new Exception("Gagal menyimpan perangkat: " . $insert_perangkat->error);
        }
        $insert_perangkat->close();
      }
    }
    
    // Commit transaction
    $koneksi->commit();
    
    header("Location: manajemen-laporan.php?success=Laporan berhasil diupdate");
    exit;
    
  } catch (Exception $e) {
    // Rollback transaction jika ada error
    $koneksi->rollback();
    $error = "Update gagal: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Laporan | MR UPT Komputer</title>
  <link rel="stylesheet" href="./assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans min-h-screen">
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

    <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow mt-8">
    <h1 class="text-2xl font-bold mb-6">Edit Laporan MR: <?php echo $laporan['nomor_mr']; ?></h1>
    
    <?php if (isset($error)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>
    
    <form id="mrForm" class="space-y-6" enctype="multipart/form-data" action="" method="POST">
      <!-- Form fields sama seperti input-laporan.php, tapi dengan nilai yang sudah ada -->
      <!-- 2. Nomor Surat -->
      <div>
        <label class="block font-semibold mb-1" for="nomorSurat">Nomor Surat</label>
        <input type="text" id="nomorSurat" name="nomorSurat" value="<?php echo htmlspecialchars($laporan['nomor_surat']); ?>" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      
      <!-- 3. Keperluan Surat -->
      <div>
        <label class="block font-semibold mb-1" for="keperluanSurat">Keperluan Surat</label>
        <input type="text" id="keperluanSurat" name="keperluanSurat" value="<?php echo htmlspecialchars($laporan['keperluan_surat']); ?>" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      
      <!-- 5. Tanggal Laporan -->
      <div>
        <label class="block font-semibold mb-1" for="tanggalLaporan">Tanggal Laporan</label>
        <input type="date" id="tanggalLaporan" name="tanggalLaporan" value="<?php echo htmlspecialchars($laporan['tanggal_laporan']); ?>" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      
      <!-- 6. Tanggal Pengecekan -->
      <div>
        <label class="block font-semibold mb-1" for="tanggalPengecekan">Tanggal Pengecekan</label>
        <input type="date" id="tanggalPengecekan" name="tanggalPengecekan" value="<?php echo htmlspecialchars($laporan['tanggal_pengecekan']); ?>" required
          class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      
      <!-- 7. Perangkat (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Perangkat</label>
        <div id="perangkatList" class="space-y-2">
          <?php foreach ($perangkats as $index => $p): ?>
            <input type="text" name="perangkat[]" value="<?php echo htmlspecialchars($p); ?>" required
              class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan perangkat" />
          <?php endforeach; ?>
        </div>
        <button type="button" id="addPerangkat" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Perangkat
        </button>
      </div>
      
      <!-- 8. Keluhan (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Keluhan</label>
        <div id="keluhanList" class="space-y-2">
          <?php foreach ($keluhans as $index => $k): ?>
            <input type="text" name="keluhan[]" value="<?php echo htmlspecialchars($k); ?>" required
              class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan keluhan" />
          <?php endforeach; ?>
        </div>
        <button type="button" id="addKeluhan" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Keluhan
        </button>
      </div>
      
      <!-- 9. Hasil Pengecekan (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Hasil Pengecekan</label>
        <div id="hasilList" class="space-y-2">
          <?php 
          $hasilArray = explode(", ", $laporan['hasil_pengecekan']);
          foreach ($hasilArray as $index => $h): 
          ?>
            <input type="text" name="hasilPengecekan[]" value="<?php echo htmlspecialchars($h); ?>" required
              class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan hasil pengecekan" />
          <?php endforeach; ?>
        </div>
        <button type="button" id="addHasil" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Hasil Pengecekan
        </button>
      </div>
      
      <!-- 10. Unit/Biro/Lembaga/Fakultas/Prodi (drop-down pilihan) -->
      <div>
        <label class="block font-semibold mb-1" for="unit">Unit/Biro/Lembaga/Fakultas/Prodi</label>
        <select id="unit" name="id_unit" class="w-full border border-gray-300 rounded px-3 py-2">
          <option value="" disabled>Pilih unit</option>
          <?php
          $unit = mysqli_query($koneksi, "SELECT * FROM unit");
          while ($b = mysqli_fetch_array($unit)) {
            $selected = $b['id_unit'] == $laporan['id_unit'] ? 'selected' : '';
            echo "<option value='{$b['id_unit']}' $selected>{$b['nama_unit']}</option>";
          }
          ?>
        </select>
      </div>
      
      <!-- 12. Perbaikan (multi input) -->
      <div>
        <label class="block font-semibold mb-1">Perbaikan</label>
        <div id="perbaikanList" class="space-y-2">
          <?php foreach ($perbaikans as $index => $p): ?>
            <input type="text" name="perbaikan[]" value="<?php echo htmlspecialchars($p); ?>" required
              class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Masukkan perbaikan" />
          <?php endforeach; ?>
        </div>
        <button type="button" id="addPerbaikan" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
          + Tambah Perbaikan
        </button>
      </div>
      
      <!-- 13. Petugas (drop-down daftar teknisi UPT Komputer) -->
      <div>
        <label class="block font-semibold mb-1" for="petugas">Petugas</label>
        <select id="petugas" name="id_petugas" required
          class="w-full border border-gray-300 rounded px-3 py-2">
          <option value="" disabled>Pilih petugas</option>
          <?php
          $petugas = mysqli_query($koneksi, "SELECT * FROM petugas");
          while ($p = mysqli_fetch_array($petugas)) {
            $selected = $p['id_petugas'] == $id_petugas ? 'selected' : '';
            echo "<option value='{$p['id_petugas']}' $selected>{$p['nama_petugas']}</option>";
          }
          ?>
        </select>
      </div>
      
      <!-- 14. Status -->
      <div>
        <label class="block font-semibold mb-1" for="status">Status</label>
        <select id="status" name="status" required
          class="w-full border border-gray-300 rounded px-3 py-2">
          <option value="Menunggu" <?php echo $laporan['status'] == 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
          <option value="Diproses" <?php echo $laporan['status'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
          <option value="Selesai" <?php echo $laporan['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
        </select>
      </div>
      
      <div class="pt-4 flex justify-between">
        <a href="manajemen-laporan.php" class="bg-gray-500 hover:bg-gray-700 text-white px-6 py-2 rounded">
          Batal
        </a>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
          Update Data
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
</body>
</html>