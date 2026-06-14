<?php
session_start();
include "koneksi.php";
?>
<!doctype html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Beli Sayur & Buah Segar | Si Tangkulak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] }, colors: { brand: { 50: "#ecfdf5", 100: "#d1fae5", 500: "#10b981", 600: "#059669", 900: "#064e3b" } } } } };
    </script>
    <style>
      .fade-in { animation: fadeIn 0.8s ease-in-out; } 
      @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
  </head>
  <body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
    
    <nav class="fixed w-full z-50 transition-all duration-300 backdrop-blur-md bg-white/90 border-b border-gray-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
          <a href="index.php" class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
            <div class="w-10 h-10 bg-brand-500 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-brand-500/30"><i class="fa-solid fa-leaf"></i></div>
            <span class="font-bold text-2xl tracking-tight text-gray-900">Si <span class="text-brand-600">Tangkulak</span></span>
          </a>
          <div class="hidden md:flex space-x-8">
            <a href="index.php" class="text-gray-500 hover:text-brand-600 transition font-medium">Beranda</a>
            <a href="pasar.php" class="text-brand-600 font-semibold border-b-2 border-brand-500 pb-1 transition">Beli Sayur (Pasar)</a>
            <a href="mitra_tani.php" class="text-gray-500 hover:text-brand-600 transition font-medium">Temukan Petani</a>
          </div>
          <div class="hidden md:flex items-center space-x-4">
            <?php if(isset($_SESSION['status']) && $_SESSION['status'] == "login"): ?>
                <a href="dashboard.php" class="bg-brand-600 hover:bg-brand-500 text-white px-6 py-2.5 rounded-full font-medium transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Aplikasi Petani</a>
            <?php else: ?>
                <a href="login.php" class="text-gray-600 hover:text-brand-600 font-medium transition"><i class="fa-solid fa-tractor mr-1"></i> Login Petani</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>

    <main class="flex-1 pt-32 pb-16 fade-in">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
          <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-4 tracking-tight">Pasar Tani Terbuka 🍅</h1>
          <p class="text-gray-500 max-w-2xl mx-auto text-lg">Jelajahi hasil bumi segar yang baru saja dipanen langsung oleh para petani mitra kami dengan harga jujur, tanpa perantara.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <?php
          $query = mysqli_query($conn, "SELECT * FROM komoditas ORDER BY id DESC");
          if(mysqli_num_rows($query) > 0) {
              while($row = mysqli_fetch_assoc($query)) {
                  $fotos = json_decode($row['foto'], true);
                  $img_src = (is_array($fotos) && count($fotos) > 0) ? 'uploads/' . $fotos[0] : 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=500&q=80';
                  
                  $harga_asli = $row['harga'];
                  $harga_coret = $harga_asli + ($harga_asli * 0.30);
          ?>
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col transform hover:-translate-y-1">
            <div class="relative h-56 overflow-hidden bg-gray-100 flex-shrink-0">
              <?php if(stripos($row['nama_komoditas'], 'organik') !== false): ?>
                <div class="absolute top-3 left-3 z-10 bg-brand-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md backdrop-blur-sm bg-brand-500/90">Organik</div>
              <?php elseif(stripos($row['nama_komoditas'], 'super') !== false): ?>
                <div class="absolute top-3 left-3 z-10 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md backdrop-blur-sm bg-red-500/90">Unggulan</div>
              <?php else: ?>
                <div class="absolute top-3 left-3 z-10 bg-blue-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md backdrop-blur-sm bg-blue-500/90">Panen Baru</div>
              <?php endif; ?>
              <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($row['nama_komoditas']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
            </div>
            
            <div class="p-5 flex flex-col flex-1">
              <div class="text-xs text-brand-600 font-semibold mb-2 flex items-center gap-1.5 truncate bg-brand-50 w-fit px-2 py-1 rounded-md">
                  <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['lokasi']) ?>
              </div>
              <h3 class="font-bold text-lg text-gray-900 mb-1 truncate leading-tight" title="<?= htmlspecialchars($row['nama_komoditas']) ?>"><?= htmlspecialchars($row['nama_komoditas']) ?></h3>
              <p class="text-sm text-gray-500 mb-4 truncate"><i class="fa-solid fa-user-check text-gray-400 mr-1"></i> Petani: <?= htmlspecialchars($row['petani']) ?></p>
              
              <div class="flex justify-between items-end mt-auto pt-4 border-t border-gray-100">
                <div>
                  <span class="text-xs text-gray-400 line-through block mb-0.5">Rp <?= number_format($harga_coret, 0, ',', '.') ?></span>
                  <span class="font-black text-xl text-gray-900 tracking-tight">Rp <?= number_format($harga_asli, 0, ',', '.') ?><span class="text-sm text-gray-500 font-medium">/<?= htmlspecialchars($row['volume']) ?></span></span>
                </div>
                <a href="pesan.php?id=<?= $row['id'] ?>" class="w-12 h-12 rounded-full bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white flex items-center justify-center transition-colors shadow-sm flex-shrink-0 text-lg">
                  <i class="fa-solid fa-cart-plus"></i>
                </a>
              </div>
            </div>
          </div>
          <?php } } else {
              echo "<div class='col-span-1 sm:col-span-2 lg:col-span-4 text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200'>";
              echo "<i class='fa-solid fa-basket-shopping text-6xl text-gray-300 mb-4 block'></i>";
              echo "<p class='text-gray-500 font-medium text-lg'>Belum ada hasil panen yang diposting oleh petani hari ini.</p></div>";
          } ?>
        </div>
      </div>
    </main>

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

    <script>
        <?php if(isset($_SESSION['pesan_sukses'])): ?>
            Swal.fire({
                title: 'Pesanan Diterima!',
                text: '<?= $_SESSION['pesan_sukses'] ?>',
                icon: 'success',
                confirmButtonColor: '#059669',
                timer: 4000
            });
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>
    </script>
  </body>
</html>