<?php
// Koneksi ke database dan session start
include 'backend/koneksi.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: formlogin.php");
    exit;
}
if ($_SESSION['user']['Level'] != 'admin') {
    // Jika bukan admin, redirect ke halaman lain atau tampilkan error
    header("Location: unauthorized.php"); // atau index.php
    exit;
}

// Ambil data user dari session
$nama = $_SESSION['user']['nama_user'];
$level = $_SESSION['user']['Level'];
$userid = $_SESSION['user']['UserID'];

// Query untuk mengambil data laporan dari database
$query = "SELECT 
            lm.*, 
            u.nama_unit AS nama_unit_laporan,
            GROUP_CONCAT(DISTINCT p.nama_unit SEPARATOR ', ') AS perangkat,
            GROUP_CONCAT(DISTINCT k.deskripsi_keluhan SEPARATOR ', ') AS keluhan,
            GROUP_CONCAT(DISTINCT pb.deskripsi_perbaikan SEPARATOR ', ') AS perbaikan,
            GROUP_CONCAT(DISTINCT pt.nama_petugas SEPARATOR ', ') AS nama_petugas
          FROM laporan_mr lm 
          LEFT JOIN unit u ON lm.id_unit = u.id_unit 
          LEFT JOIN perangkat p ON lm.id_mr = p.id_mr 
          LEFT JOIN keluhan k ON lm.id_mr = k.id_mr
          LEFT JOIN perbaikan pb ON lm.id_mr = pb.id_mr
          LEFT JOIN petugas pt ON pb.id_petugas = pt.id_petugas
          GROUP BY lm.id_mr
          ORDER BY lm.tanggal_laporan DESC";



$result = mysqli_query($koneksi, $query);

// Handle filter jika ada
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_unit = isset($_GET['unit']) ? $_GET['unit'] : '';

if ($filter_status || $filter_unit) {
    $query = "SELECT 
                lm.*, 
                u.nama_unit as nama_unit_laporan,
                GROUP_CONCAT(DISTINCT p.nama_unit SEPARATOR ', ') as perangkat,
                GROUP_CONCAT(DISTINCT k.deskripsi_keluhan SEPARATOR ', ') as keluhan,
                pt.nama_petugas
              FROM laporan_mr lm 
              LEFT JOIN unit u ON lm.id_unit = u.id_unit 
              LEFT JOIN perangkat p ON lm.id_unit = p.id_unit
              LEFT JOIN keluhan k ON lm.id_mr = k.id_mr
              LEFT JOIN perbaikan pb ON lm.id_mr = pb.id_mr
              LEFT JOIN petugas pt ON pb.id_petugas = pt.id_petugas
              WHERE 1=1";

    if ($filter_status) {
        $query .= " AND lm.status = '$filter_status'";
    }

    if ($filter_unit) {
        $query .= " AND lm.id_unit = $filter_unit";
    }

    $query .= " GROUP BY lm.id_mr ORDER BY lm.tanggal_laporan DESC";
    $result = mysqli_query($koneksi, $query);
}

// Query untuk dropdown unit
$unit_query = "SELECT * FROM unit";
$unit_result = mysqli_query($koneksi, $unit_query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/1.png" />
    <title>Sistem MR - Manajemen Laporan</title>
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

    <!-- Header -->
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2 pt-12">Manajemen Laporan MR</h1>
            <p class="text-gray-600">Kelola semua laporan Maintenance dan Repair perangkat komputer</p>

            <!-- Filter dan Pencarian -->
            <div class="flex flex-col md:flex-row items-center justify-between mt-6 gap-6 mb-8">
                <form method="GET" action="" class="flex flex-wrap items-center gap-4">
                    <!-- Dropdown Status -->
                    <div class="relative">
                        <select name="status"
                            class="block w-48 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="Menunggu" <?= $filter_status == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="Diproses" <?= $filter_status == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Selesai" <?= $filter_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>

                    <!-- Dropdown Unit -->
                    <div class="relative">
                        <select name="unit"
                            class="block w-48 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Unit</option>
                            <?php while ($unit = mysqli_fetch_assoc($unit_result)): ?>
                                <option value="<?= $unit['id_unit'] ?>" <?= $filter_unit == $unit['id_unit'] ? 'selected' : '' ?>>
                                    <?= $unit['nama_unit'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Tombol Filter & Reset -->
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    <a href="manajemen-laporan.php"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </form>
            </div>


            <!-- Tabel Laporan -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
<table class="min-w-full text-sm divide-y divide-gray-200">
    <thead class="bg-gray-50 text-xs">
        <tr>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">No. MR</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Keperluan</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Unit</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Perangkat</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Perbaikan</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Petugas</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Status</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
        </tr>
    </thead>
    <tbody class="bg-white text-xs divide-y divide-gray-200">
        <!-- Tr dan td isi konten tetap sama, hanya kurangi padding -->
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr class="hover:bg-gray-100">
            <td class="px-4 py-2"><?= $row['nomor_mr']; ?><br><span class="text-gray-500 text-[10px]">No. Surat: <?= $row['nomor_surat']; ?></span></td>
            <td class="px-4 py-2"><?= $row['keperluan_surat']; ?></td>
            <td class="px-4 py-2"><?= date('d M Y', strtotime($row['tanggal_laporan'])); ?><br><span class="text-gray-500 text-[10px]">Check: <?= date('d M Y', strtotime($row['tanggal_pengecekan'])); ?></span></td>
            <td class="px-4 py-2"><?= $row['nama_unit_laporan']; ?></td>
            <td class="px-4 py-2"><?= $row['perangkat']; ?><br><span class="text-gray-500 text-[10px]"><?= $row['keluhan']; ?></span></td>
            <td class="px-4 py-2"><?= $row['perbaikan']; ?></td>
            <td class="px-4 py-2">
                <?= $row['nama_petugas'] ? $row['nama_petugas'] : '<span class="text-gray-400">Belum ada</span>'; ?>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
    <?php
        if ($row['status'] == 'Selesai') {
            $status_class = 'bg-green-100 text-green-700';
            $icon = 'fa-check-circle';
        } elseif ($row['status'] == 'Diproses') {
            $status_class = 'bg-blue-100 text-blue-700';
            $icon = 'fa-tools';
        } else {
            $status_class = 'bg-yellow-100 text-yellow-700';
            $icon = 'fa-clock';
        }
    ?>
    <span class="inline-flex items-center px-2 py-[2px] text-[11px] font-medium rounded-full <?= $status_class ?>">
        <i class="fas <?= $icon ?> mr-1 text-[11px]"></i>
        <?= $row['status'] ?>
    </span>
</td>

            <td class="px-4 py-2 flex gap-2">
    <a href="detail-laporan.php?id=<?= $row['id_mr']; ?>" 
       class="text-blue-600 hover:text-blue-900 text-xs flex items-center gap-1">
        <i class="fas fa-eye text-[12px]"></i>
        Detail
    </a>

    <a href="edit-laporan.php?id=<?= $row['id_mr']; ?>" 
       class="text-yellow-600 hover:text-yellow-900 text-xs flex items-center gap-1">
        <i class="fas fa-edit text-[12px]"></i>
        Edit
    </a>

    <button onclick="confirmDelete(<?= $row['id_mr']; ?>)" 
            class="text-red-600 hover:text-red-900 text-xs flex items-center gap-1">
        <i class="fas fa-trash text-[12px]"></i>
        Hapus
    </button>
</td>

        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

                </div>
            </div>
        </div>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 mt-2 text-center">Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="mt-6 flex justify-center space-x-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="button" onclick="proceedDelete()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedId = null;

        function confirmDelete(id) {
            selectedId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function proceedDelete() {
            if (selectedId) {
                // Redirect to delete script with the selected ID
                window.location.href = `backend/hapus-laporan.php?id=${selectedId}`;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>