<?php
session_start();
if (isset($_GET['pesan']) && $_GET['pesan'] == "belum_login") {
  echo '
    <div class="fixed top-5 left-5">
      <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg shadow-lg flex items-center gap-2">
        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
        </svg>
        <span class="font-semibold">Anda harus login dulu!</span>
      </div>
    </div>

    <script>
      setTimeout(() => {
        const alertBox = document.querySelector(".fixed.top-5.right-5");
        if (alertBox) {
          alertBox.style.transition = "opacity 0.5s";
          alertBox.style.opacity = "0";
          setTimeout(() => alertBox.remove(), 500);
        }
      }, 3000);
    </script>
    ';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/img" href="assets/1.png" />
  <title>Form Login</title>
  <link rel="stylesheet" href="./assets/css/output.css">
</head>

<body class="bg-gradient-to-r from-blue-800 to-sky-600 min-h-screen flex items-center justify-center font-sans">

  <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full">
    <!-- Judul -->
    <div class="flex justify-center">
    <img src="assets/logo.png" 
         alt="Logo UNIPMA" 
         style="width: 50px; height: 50px; object-fit: contain;">
</div>
<h2 class="text-3xl font-bold text-center text-blue-800 mb-2">
    Selamat Datang di UPT KOMPUTER
</h2>
<p class="text-center font-semibold text-gray-600 mb-8">
    Silahkan login terlebih dahulu
</p>



    <!-- Form -->
    <form action="./backend/login.php" method="post" class="space-y-5">
      <!-- Username -->
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" name="username"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          placeholder="Masukkan Username" required>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          placeholder="Masukkan Password" required>
      </div>
        
  <?php if (!empty($_SESSION['error'])) { ?>
  <div class="text-red-600" role="alert">
    <div class="flex items-center">
      
      <div class="py-1">
        <svg class="w-6 h-6 text-red-600 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
        </svg>
      </div>
      <div>
        <p class="text-sm">
          <?= htmlspecialchars($_SESSION['error']); ?>
        </p>
      </div>
    </div>
  </div>
	<?php unset($_SESSION['error']);?>
<?php } ?>
        
    <?php if (!empty($_SESSION['login_error'])) { ?>
  <div class="text-red-600" role="alert">
    <div class="flex items-center">
      
      <div class="py-1">
        <svg class="w-6 h-6 text-red-600 mr-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
        </svg>
      </div>
      <div>
        <p class="text-sm">
          <?= htmlspecialchars($_SESSION['login_error']); ?>
        </p>
      </div>
    </div>
  </div>
	<?php unset($_SESSION['login_error']);?>
<?php } ?>

        <br><br>
      <!-- Tombol -->
      <button type="submit"
        class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition">
        Login
      </button>
    </form>

    <div class="text-center text-sm text-gray-600 mt-6">
      <p>Belum punya akun?</p>
      <a href="register.php" class="text-blue-600 font-medium hover:underline">Daftar</a>
    </div>
  </div>

</body>

</html>