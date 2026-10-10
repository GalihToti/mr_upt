<?php
// Koneksi ke database dan session start
include 'backend/koneksi.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: formlogin.php");
    exit;
}

// Ambil data user dari session
$nama = $_SESSION['user']['nama_user'];
$level = $_SESSION['user']['Level'];
$userid = $_SESSION['user']['UserID'];

// Query untuk mengambil data laporan dari database
$query = "SELECT 
            lm.*, 
            u.nama_unit as nama_unit_laporan,
            GROUP_CONCAT(DISTINCT p.nama_unit SEPARATOR ', ') as perangkat,
            GROUP_CONCAT(DISTINCT k.deskripsi_keluhan SEPARATOR ', ') as keluhan,
            GROUP_CONCAT(DISTINCT pt.nama_petugas SEPARATOR ', ') AS nama_petugas
          FROM laporan_mr lm 
          LEFT JOIN unit u ON lm.id_unit = u.id_unit 
          LEFT JOIN perangkat p ON lm.id_unit = p.id_unit
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
    <title>Sistem MR - Manajemen Laporan</title>
    <link rel="stylesheet" href="./assets/css/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-menunggu {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-diproses {
            background-color: #bfdbfe;
            color: #1e40af;
        }

        .status-selesai {
            background-color: #bbf7d0;
            color: #166534;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            margin: 0 5px;
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #3b82f6;
            text-decoration: none;
        }

        .pagination a:hover {
            background-color: #3b82f6;
            color: white;
        }

        .pagination .active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg fixed top-0 left-0 w-full z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-tools text-2xl mr-2 text-yellow-300"></i>
                <span class="font-bold text-lg">UPT Komputer UNIPMA</span>
            </div>
            <div class="space-x-2 flex items-center">
                <a href="./index.php" class="hover:bg-blue-700 rounded-md px-4 py-2">Dashboard</a>
                <?php if ($level == 'admin'): ?>
                    <a href="./input-laporan.php" class="hover:bg-blue-700 rounded-md px-4 py-2">Input Laporan</a>
                    <a href="./manajemen-laporan.php" class="hover:bg-blue-700 rounded-md px-4 py-2">Manajemen Laporan</a>
                <?php endif; ?>
                <a href="./laporan.php" class="hover:bg-blue-700 rounded-md px-4 py-2">Laporan</a>
                <!-- Tombol Logout -->
                <button onclick="openModal()" class="px-4 py-2 rounded-md font-medium bg-red-500 text-white hover:bg-red-700">Logout</button>
            </div>
        </div>
    </nav>

    <!-- Modal Logout -->
    <div id="logoutModal"
        class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm text-center">
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar?</p>
            <div class="flex justify-center gap-3">
                <a id="confirmLogout" href="./backend/logout.php"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-300">
                    Keluar
                </a>
                <button onclick="closeModal()"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-2 focus:ring-gray-400">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('hidden');
            document.getElementById("confirmLogout").focus();
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
    </script>

    <!-- Header -->
    <div class="container mx-auto px-4 py-6 pt-20">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Manajemen Laporan MR</h1>
            <p class="text-gray-600">Kelola semua laporan Maintenance dan Repair perangkat komputer</p>

            <!-- Filter dan Pencarian -->
            <div class="flex flex-wrap items-center justify-between mt-6 gap-4">
                <form method="GET" action="" class="flex items-center gap-4">
                    <div class="relative">
                        <select name="status" class="block w-full px-4 py-2 pr-8 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="Menunggu" <?= $filter_status == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="Diproses" <?= $filter_status == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Selesai" <?= $filter_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div class="relative">
                        <select name="unit" class="block w-full px-4 py-2 pr-8 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Unit</option>
                            <?php while ($unit = mysqli_fetch_assoc($unit_result)): ?>
                                <option value="<?= $unit['id_unit'] ?>" <?= $filter_unit == $unit['id_unit'] ? 'selected' : '' ?>>
                                    <?= $unit['nama_unit'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>

                    <a href="manajemen-laporan.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                </form>

                <div class="relative">
                    <input type="text" placeholder="Cari laporan..." class="w-64 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button class="absolute right-0 top-0 h-full px-3 text-gray-400">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. MR</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perangkat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= $row['nomor_mr'] ?></div>
                                        <div class="text-sm text-gray-500">No. Surat: <?= $row['nomor_surat'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($row['tanggal_laporan'])) ?></div>
                                        <div class="text-sm text-gray-500"><?= date('d M Y', strtotime($row['tanggal_pengecekan'])) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= $row['nama_unit_laporan'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $row['keperluan_surat'] ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?= $row['perangkat'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $row['keluhan'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status_class = '';
                                        if ($row['status'] == 'Selesai') {
                                            $status_class = 'status-selesai';
                                            $icon = 'fa-check-circle';
                                        } elseif ($row['status'] == 'Diproses') {
                                            $status_class = 'status-diproses';
                                            $icon = 'fa-tools';
                                        } else {
                                            $status_class = 'status-menunggu';
                                            $icon = 'fa-clock';
                                        }
                                        ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $status_class ?>">
                                            <i class="fas <?= $icon ?> mr-1"></i> <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $row['nama_petugas'] ? $row['nama_petugas'] : 'Belum ditugaskan' ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="detail-laporan.php?id=<?= $row['id_mr'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data laporan yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination bg-gray-50 px-4 py-3">
                <a href="#" class="disabled"><i class="fas fa-chevron-left"></i></a>
                <a href="#" class="active">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <span>...</span>
                <a href="#">10</a>
                <a href="#"><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
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