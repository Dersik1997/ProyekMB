<?php
session_start();
include "koneksi.php";
?>
<!doctype html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Si Tangkulak | Beli Panen Langsung dari Petani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] }, colors: { brand: { 50: "#ecfdf5", 100: "#d1fae5", 500: "#10b981", 600: "#059669", 900: "#064e3b" } } } } };
    </script>
    <style>
      .fade-in { animation: fadeIn 1s ease-in-out; } 
      @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
      .no-scrollbar::-webkit-scrollbar { display: none; }
      .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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
            <a href="index.php" class="text-brand-600 font-semibold border-b-2 border-brand-500 pb-1 transition">Beranda</a>
            <a href="pasar.php" class="text-gray-500 hover:text-brand-600 transition font-medium">Beli Sayur (Pasar)</a>
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

    <main class="flex-1 pt-28 pb-10 lg:pt-36 lg:pb-16 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          
          <div class="fade-in z-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 text-brand-600 font-medium text-sm mb-6 border border-brand-100">
              <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-500 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-brand-600"></span></span>
              Langsung dari Kebun
            </div>
            <h1 class="text-4xl lg:text-6xl font-bold leading-tight text-gray-900 mb-6">Pesan Sayur Segar, <br /><span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-teal-400">Semudah Pesan Ojek.</span></h1>
            <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg">Pilih petani terdekat, lihat hasil panen hari ini, dan pesan langsung tanpa lewat tengkulak. Harga lebih murah untuk konsumen, untung lebih besar untuk petani.</p>
            
            <div class="flex flex-col sm:flex-row gap-4">
              <a href="pasar.php" class="bg-brand-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-brand-500 transition-all shadow-xl shadow-brand-500/30 flex items-center justify-center gap-2 transform hover:-translate-y-1"><i class="fa-solid fa-basket-shopping"></i> Mulai Belanja</a>
              <a href="mitra_tani.php" class="bg-white text-gray-800 border border-gray-200 px-8 py-4 rounded-full font-semibold text-lg hover:bg-gray-50 transition-all flex items-center justify-center gap-2"><i class="fa-solid fa-location-crosshairs text-brand-600"></i> Cari Petani Terdekat</a>
            </div>
          </div>
          
       <div class="relative fade-in hidden lg:block group">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-gradient-to-tr from-brand-200 to-teal-100 rounded-full blur-3xl opacity-50 -z-10"></div>
    
    <div class="relative rounded-3xl overflow-hidden shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500 border-8 border-white h-[500px]">
      
      <div id="hero-slider" class="flex w-full h-full transition-transform duration-500 ease-in-out">

          <div class="min-w-full h-full flex-shrink-0 relative bg-black">
              <video class="w-full h-full object-contain" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80">
                  <source src="uploads/1.mp4" type="video/mp4">
              </video>
          </div>

      </div>
      
      <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 text-brand-600 w-10 h-10 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"><i class="fa-solid fa-chevron-left"></i></button>
      <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 text-brand-600 w-10 h-10 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
</div>

        </div>
      </div>
    </main>

    <section id="rekomendasi" class="py-16 bg-white border-t border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Rekomendasi Panen Hari Ini <span class="text-xl">🌟</span></h2>
            <p class="text-gray-500">Postingan etalase terbaru yang baru saja ditambahkan oleh petani mitra.</p>
          </div>
          <a href="pasar.php" class="hidden sm:inline-flex items-center gap-2 text-brand-600 font-semibold hover:text-brand-700 transition">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <?php
          $query = mysqli_query($conn, "SELECT * FROM komoditas ORDER BY id DESC LIMIT 8");
          if(mysqli_num_rows($query) > 0) {
              while($row = mysqli_fetch_assoc($query)) {
                  $fotos = json_decode($row['foto'], true);
                  $img_src = (is_array($fotos) && count($fotos) > 0) ? 'uploads/' . $fotos[0] : 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=500&q=80';
                  $harga_asli = $row['harga'];
                  $harga_coret = $harga_asli + ($harga_asli * 0.30);
          ?>
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-shadow overflow-hidden group flex flex-col">
            <div class="relative h-48 overflow-hidden bg-gray-100 flex-shrink-0">
              <?php if(stripos($row['nama_komoditas'], 'organik') !== false): ?>
                <div class="absolute top-3 left-3 z-10 bg-brand-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Organik</div>
              <?php elseif(stripos($row['nama_komoditas'], 'super') !== false): ?>
                <div class="absolute top-3 left-3 z-10 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Unggulan</div>
              <?php else: ?>
                <div class="absolute top-3 left-3 z-10 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Segar</div>
              <?php endif; ?>
              <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($row['nama_komoditas']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
            </div>
            <div class="p-5 flex flex-col flex-1">
              <div class="text-xs text-brand-600 font-semibold mb-1 flex items-center gap-1 truncate"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['lokasi']) ?></div>
              <h3 class="font-bold text-lg text-gray-900 mb-1 truncate" title="<?= htmlspecialchars($row['nama_komoditas']) ?>"><?= htmlspecialchars($row['nama_komoditas']) ?></h3>
              <p class="text-sm text-gray-500 mb-3 truncate"><i class="fa-solid fa-user-check text-gray-400"></i> Petani: <span class="font-medium text-gray-700"><?= htmlspecialchars($row['petani']) ?></span></p>
              <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-100">
                <div>
                  <span class="text-xs text-gray-400 line-through block">Rp <?= number_format($harga_coret, 0, ',', '.') ?></span>
                  <span class="font-bold text-xl text-gray-900">Rp <?= number_format($harga_asli, 0, ',', '.') ?><span class="text-sm text-gray-500 font-normal">/<?= htmlspecialchars($row['volume']) ?></span></span>
                </div>
                <a href="pesan.php?id=<?= $row['id'] ?>" class="w-12 h-12 rounded-full bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white flex items-center justify-center transition-colors shadow-sm flex-shrink-0 text-lg">
                  <i class="fa-solid fa-cart-plus"></i>
                </a>
              </div>
            </div>
          </div>
          <?php } } else {
              echo "<div class='col-span-1 sm:col-span-2 lg:col-span-4 text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-300'>";
              echo "<i class='fa-solid fa-basket-shopping text-4xl text-gray-300 mb-3'></i><p class='text-gray-500 font-medium'>Belum ada postingan hasil panen hari ini.</p></div>";
          } ?>
        </div>
      </div>
    </section>

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
                title: 'Pesanan Berhasil!',
                text: '<?= $_SESSION['pesan_sukses'] ?>',
                icon: 'success',
                confirmButtonColor: '#059669',
                timer: 4000
            });
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>

        let currentSlide = 0;
        const totalSlides = 2; 
        const sliderDiv = document.getElementById('hero-slider');
        
        function updateSlider() {
            sliderDiv.style.transform = `translateX(-${currentSlide * 100}%)`;
            document.getElementById('dot-0').className = (currentSlide === 0) ? "w-2.5 h-2.5 rounded-full bg-white transition-all scale-125" : "w-2.5 h-2.5 rounded-full bg-white/50 transition-all";
            document.getElementById('dot-1').className = (currentSlide === 1) ? "w-2.5 h-2.5 rounded-full bg-white transition-all scale-125" : "w-2.5 h-2.5 rounded-full bg-white/50 transition-all";
        }

        function nextSlide() { currentSlide = (currentSlide + 1) % totalSlides; updateSlider(); }
        function prevSlide() { currentSlide = (currentSlide - 1 + totalSlides) % totalSlides; updateSlider(); }
        function goToSlide(index) { currentSlide = index; updateSlider(); }
    </script>
  </body>
</html>