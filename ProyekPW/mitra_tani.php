<?php
session_start();
include "koneksi.php";
?>
<!doctype html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Temukan Petani | Si Tangkulak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script>
      tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] }, colors: { brand: { 50: "#ecfdf5", 100: "#d1fae5", 500: "#10b981", 600: "#059669", 900: "#064e3b" } } } } };
    </script>
    <style>.fade-in { animation: fadeIn 0.8s ease-in-out; } @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }</style>
  </head>
  <body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
    
    <!-- NAVBAR -->
    <nav class="fixed w-full z-50 backdrop-blur-md bg-white/90 border-b border-gray-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-20 items-center">
        <a href="index.php" class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
          <div class="w-10 h-10 bg-brand-500 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-brand-500/30"><i class="fa-solid fa-leaf"></i></div>
          <span class="font-bold text-2xl text-gray-900">Si <span class="text-brand-600">Tangkulak</span></span>
        </a>
        <div class="hidden md:flex space-x-8">
          <a href="index.php" class="text-gray-500 hover:text-brand-600 transition font-medium">Beranda</a>
          <a href="pasar.php" class="text-gray-500 hover:text-brand-600 transition font-medium">Beli Sayur (Pasar)</a>
          <a href="mitra_tani.php" class="text-brand-600 font-semibold border-b-2 border-brand-500 pb-1 transition">Temukan Petani</a>
        </div>
        <div class="hidden md:flex items-center space-x-4">
            <?php if(isset($_SESSION['status']) && $_SESSION['status'] == "login"): ?>
                <a href="dashboard.php" class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-medium transition shadow-md">Aplikasi Petani</a>
            <?php else: ?>
                <a href="login.php" class="text-gray-600 hover:text-brand-600 font-medium transition"><i class="fa-solid fa-tractor mr-1"></i> Login Petani</a>
            <?php endif; ?>
        </div>
      </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="flex-1 pt-32 pb-16 fade-in">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
          <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-4 tracking-tight">Pahlawan Pangan Kami 👨‍🌾</h1>
          <p class="text-gray-500 max-w-2xl mx-auto text-lg">Kenali para petani hebat yang mendedikasikan hidupnya untuk menanam hasil panen terbaik langsung untuk Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php
          $query = mysqli_query($conn, "SELECT * FROM mitra_petani WHERE status='Aktif' ORDER BY id DESC");
          if(mysqli_num_rows($query) > 0) {
              while($row = mysqli_fetch_assoc($query)) {
                  $foto_profil = (!empty($row['foto']) && file_exists("uploads/".$row['foto'])) ? "uploads/".$row['foto'] : "https://ui-avatars.com/api/?name=".urlencode($row['nama_petani'])."&background=10b981&color=fff";
          ?>
          <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col group transform hover:-translate-y-2">
            <div class="h-28 bg-gradient-to-br from-brand-600 to-teal-400 w-full relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20"></div>
            </div>
            
            <div class="px-6 pb-8 pt-0 relative flex-1 flex flex-col items-center text-center">
                <div class="w-24 h-24 bg-white rounded-full overflow-hidden shadow-lg border-4 border-white mx-auto -mt-12 mb-4 z-10 relative bg-gray-100">
                    <img src="<?= $foto_profil ?>" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 right-0 bg-blue-500 w-6 h-6 rounded-full border-2 border-white flex items-center justify-center text-white text-[10px]"><i class="fa-solid fa-check"></i></div>
                </div>
                
                <h3 class="font-bold text-2xl text-gray-900 mb-1"><?= htmlspecialchars($row['nama_petani']) ?></h3>
                <p class="text-xs text-brand-600 font-bold mb-4 bg-brand-50 px-3 py-1 rounded-full"><i class="fa-solid fa-map-location-dot"></i> <?= htmlspecialchars($row['lokasi']) ?></p>
                
                <p class="text-sm text-gray-500 mb-6 italic line-clamp-3 leading-relaxed px-4">
                    "<?= htmlspecialchars($row['bio'] ?? 'Petani tangguh yang selalu memprioritaskan kualitas hasil panen segar.') ?>"
                </p>
                
                <div class="w-full mt-auto pt-5 border-t border-gray-100">
                    <!-- Link Mengarah ke Etalase Khusus Petani -->
                    <a href="etalase_petani.php?id=<?= $row['id'] ?>" class="w-full flex items-center justify-center gap-2 bg-gray-50 border border-gray-200 text-gray-700 font-bold py-3 rounded-xl text-sm hover:bg-brand-500 hover:border-brand-500 hover:text-white transition-colors shadow-sm">
                        <i class="fa-solid fa-store"></i> Kunjungi Etalase
                    </a>
                </div>
            </div>
          </div>
          <?php } } else { 
              echo "<div class='col-span-1 md:col-span-2 lg:col-span-3 text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200'>";
              echo "<i class='fa-solid fa-users text-6xl text-gray-300 mb-4 block'></i>";
              echo "<p class='text-gray-500 font-medium text-lg'>Belum ada petani yang memperbarui profilnya.</p></div>";
          } ?>
        </div>
      </div>
    </main>

    <!-- FOOTER LENGKAP -->
    <footer class="bg-gray-900 text-white pt-16 pb-8 border-t-4 border-brand-500 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center gap-2 mb-4"><div class="w-8 h-8 bg-brand-500 text-white rounded-lg flex items-center justify-center"><i class="fa-solid fa-leaf"></i></div><span class="font-bold text-2xl tracking-tight">Si <span class="text-brand-500">Tangkulak</span></span></div>
            <p class="text-gray-400 max-w-sm mb-6 leading-relaxed">Membangun ekosistem pertanian yang adil. Menghubungkan keringat petani langsung ke meja konsumen tanpa perantara ganda.</p>
            <div class="flex space-x-4">
              <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-facebook-f"></i></a>
              <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-twitter"></i></a>
            </div>
          </div>
          <div>
            <h4 class="text-lg font-bold mb-4">Eksplorasi</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="index.php" class="hover:text-brand-400 transition-colors">Beranda</a></li>
              <li><a href="pasar.php" class="hover:text-brand-400 transition-colors">Beli Sayur (Pasar)</a></li>
              <li><a href="mitra_tani.php" class="hover:text-brand-400 transition-colors">Temukan Petani</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-lg font-bold mb-4">Bantuan</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-brand-400 transition-colors">Syarat & Ketentuan</a></li>
              <li><a href="#" class="hover:text-brand-400 transition-colors">Kebijakan Privasi</a></li>
              <li><a href="#" class="hover:text-brand-400 transition-colors">Hubungi Kami</a></li>
            </ul>
          </div>
        </div>
        <div class="pt-8 border-t border-gray-800 text-center text-gray-500 text-sm"><p>&copy; 2026 Si Tangkulak. Ekosistem Pertanian Digital.</p></div>
      </div>
    </footer>
  </body>
</html>