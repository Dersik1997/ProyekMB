<?php
session_start();
include "koneksi.php";

// Cek apakah ada ID petani yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: mitra_tani.php");
    exit();
}

$id_petani = (int)$_GET['id'];
$query_petani = mysqli_query($conn, "SELECT * FROM mitra_petani WHERE id = $id_petani");

if (mysqli_num_rows($query_petani) == 0) {
    header("Location: mitra_tani.php");
    exit();
}

$petani = mysqli_fetch_assoc($query_petani);
$nama_petani = $petani['nama_petani'];
$foto_profil = (!empty($petani['foto']) && file_exists("uploads/".$petani['foto'])) ? "uploads/".$petani['foto'] : "https://ui-avatars.com/api/?name=".urlencode($nama_petani)."&background=10b981&color=fff";

// Hitung jumlah postingan aktif milik petani ini
$query_hitung = mysqli_query($conn, "SELECT COUNT(id) as total FROM komoditas WHERE petani = '$nama_petani'");
$data_hitung = mysqli_fetch_assoc($query_hitung);
$total_produk = $data_hitung['total'] ?? 0;
?>
<!doctype html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Etalase Toko <?= htmlspecialchars($nama_petani) ?> | Si Tangkulak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script>
      tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] }, colors: { brand: { 50: "#ecfdf5", 100: "#d1fae5", 500: "#10b981", 600: "#059669", 900: "#064e3b" } } } } };
    </script>
    <style>
      .fade-in { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); } 
      @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>
  </head>
  <body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
    
    <nav class="fixed w-full z-50 backdrop-blur-md bg-white/90 border-b border-gray-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
          <a href="index.php" class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
            <div class="w-10 h-10 bg-brand-500 text-white rounded-xl flex items-center justify-center text-xl shadow-lg shadow-brand-500/30"><i class="fa-solid fa-leaf"></i></div>
            <span class="font-bold text-2xl tracking-tight text-gray-900">Si <span class="text-brand-600">Tangkulak</span></span>
          </a>
          <div class="hidden md:flex space-x-8">
            <a href="index.php" class="text-gray-500 hover:text-brand-600 transition font-medium">Beranda</a>
            <a href="pasar.php" class="text-gray-500 hover:text-brand-600 transition font-medium">Beli Sayur (Pasar)</a>
            <a href="mitra_tani.php" class="text-brand-600 font-semibold border-b-2 border-brand-500 pb-1 transition">Temukan Petani</a>
          </div>
          <div class="hidden md:flex items-center space-x-4">
              <a href="mitra_tani.php" class="text-sm font-bold text-gray-500 hover:text-gray-900 transition flex items-center gap-1.5 bg-gray-100 px-4 py-2 rounded-xl"><i class="fa-solid fa-arrow-left text-xs"></i> Kembali</a>
          </div>
        </div>
      </div>
    </nav>

    <main class="flex-1 pt-20 fade-in">
        
      <div class="bg-white border-b border-gray-200/80 shadow-xs relative">
          <div class="h-52 md:h-72 bg-gradient-to-r from-emerald-800 via-emerald-600 to-teal-500 w-full relative overflow-hidden">
             <div class="absolute inset-0 bg-black/10"></div>
             <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-15"></div>
             <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/10 rounded-full blur-xl"></div>
          </div>

          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative pb-8">
              <div class="flex flex-col md:flex-row items-center md:items-end gap-6 -mt-20 md:-mt-24 relative z-10">
                  
                  <div class="w-36 h-32 md:w-44 md:h-44 bg-white rounded-3xl overflow-hidden shadow-2xl border-4 border-white flex-shrink-0 relative">
                      <img src="<?= $foto_profil ?>" class="w-full h-full object-cover">
                      <div class="absolute bottom-2 right-2 bg-brand-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] border-2 border-white shadow shadow-black/20"><i class="fa-solid fa-check"></i></div>
                  </div>

                  <div class="text-center md:text-left flex-1">
                      <div class="flex flex-col md:flex-row items-center gap-2 md:gap-3 mb-2">
                          <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight"><?= htmlspecialchars($nama_petani) ?></h1>
                          <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md border border-blue-100">
                              <i class="fa-solid fa-certificate"></i> Official Seller
                          </span>
                      </div>
                      
                      <div class="flex flex-wrap justify-center md:justify-start gap-2 text-xs md:text-sm font-medium text-gray-600 mb-4">
                          <span class="bg-gray-100/80 border border-gray-200/60 px-3 py-1 rounded-xl flex items-center gap-1.5"><i class="fa-solid fa-map-location-dot text-brand-500"></i> Lahan: <?= htmlspecialchars($petani['lokasi']) ?></span>
                          <span class="bg-gray-100/80 border border-gray-200/60 px-3 py-1 rounded-xl flex items-center gap-1.5"><i class="fa-solid fa-boxes-stacked text-brand-500"></i> <b><?= $total_produk ?></b> Produk Aktif</span>
                      </div>
                      
                      <p class="text-gray-500 max-w-3xl italic text-sm md:text-md leading-relaxed">
                          <i class="fa-solid fa-quote-left text-gray-300 mr-1"></i>
                          <?= htmlspecialchars($petani['bio'] ?? 'Selamat datang di toko kami! Kami menyediakan hasil bumi terbaik yang dipanen segar setiap hari langsung dari kebun sendiri untuk menjamin kesegaran maksimal sampai ke tangan Anda.') ?>
                          <i class="fa-solid fa-quote-right text-gray-300 ml-1"></i>
                      </p>
                  </div>

                  <div class="flex-shrink-0 mt-4 md:mt-0 w-full md:w-auto">
                      <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $petani['no_telepon']) ?>" target="_blank" class="w-full md:w-auto bg-green-500 hover:bg-green-600 text-white px-6 py-3.5 rounded-2xl font-bold shadow-lg shadow-green-500/20 flex items-center justify-center gap-2 transition transform hover:-translate-y-0.5 active:scale-95">
                          <i class="fa-brands fa-whatsapp text-xl"></i> Hubungi via WhatsApp
                      </a>
                  </div>

              </div>
          </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <div class="flex items-center gap-3 mb-8 border-b border-gray-200 pb-4">
            <div class="w-2 h-8 bg-brand-500 rounded-full"></div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Semua Hasil Panen Toko Ini</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <?php
          // Mengambil semua komoditas yang nama petaninya SAMA dengan pemilik toko ini
          $query_komoditas = mysqli_query($conn, "SELECT * FROM komoditas WHERE petani = '$nama_petani' ORDER BY id DESC");
          
          if(mysqli_num_rows($query_komoditas) > 0) {
              while($row = mysqli_fetch_assoc($query_komoditas)) {
                  $fotos = json_decode($row['foto'], true);
                  $img_src = (is_array($fotos) && count($fotos) > 0) ? 'uploads/' . $fotos[0] : 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=500&q=80';
                  $harga_asli = $row['harga'];
                  $harga_coret = $harga_asli + ($harga_asli * 0.30);
          ?>
          <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col transform hover:-translate-y-1">
            
            <div class="relative h-52 overflow-hidden bg-gray-50 flex-shrink-0">
              <?php if(stripos($row['nama_komoditas'], 'organik') !== false): ?>
                <div class="absolute top-3 left-3 z-10 bg-brand-500/90 backdrop-blur-xs text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">Organik</div>
              <?php elseif(stripos($row['nama_komoditas'], 'super') !== false): ?>
                <div class="absolute top-3 left-3 z-10 bg-red-500/90 backdrop-blur-xs text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">Super</div>
              <?php else: ?>
                <div class="absolute top-3 left-3 z-10 bg-emerald-600/90 backdrop-blur-xs text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">Segar</div>
              <?php endif; ?>
              
              <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($row['nama_komoditas']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
            </div>
            
            <div class="p-5 flex flex-col flex-1 bg-white">
              <h3 class="font-bold text-md text-gray-900 mb-1 line-clamp-2 h-12 leading-snug group-hover:text-brand-600 transition-colors" title="<?= htmlspecialchars($row['nama_komoditas']) ?>">
                <?= htmlspecialchars($row['nama_komoditas']) ?>
              </h3>
              
              <div class="text-xs text-gray-400 font-medium mb-4 flex items-center gap-1">
                  <i class="fa-solid fa-circle-nodes text-brand-500 text-[10px]"></i> Stok Tersedia: <span class="font-bold text-gray-700"><?= htmlspecialchars($row['volume']) ?></span>
              </div>
              
              <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-100">
                <div>
                  <span class="text-xs text-gray-400 line-through block mb-0.5">Rp <?= number_format($harga_coret, 0, ',', '.') ?></span>
                  <span class="font-black text-lg text-gray-900 tracking-tight">
                    Rp <?= number_format($harga_asli, 0, ',', '.') ?><span class="text-xs text-gray-400 font-normal"> /kg</span>
                  </span>
                </div>
                <a href="pesan.php?id=<?= $row['id'] ?>" class="w-11 h-11 rounded-full bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white flex items-center justify-center transition-all shadow-xs flex-shrink-0 text-md active:scale-90">
                  <i class="fa-solid fa-cart-plus"></i>
                </a>
              </div>
            </div>

          </div>
          <?php } } else { ?>
             <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                 <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-4xl shadow-inner"><i class="fa-solid fa-store-slash"></i></div>
                 <p class="text-gray-500 font-bold text-lg">Etalase Masih Kosong</p>
                 <p class="text-gray-400 text-sm mt-1">Petani ini belum mengunggah produk jualan untuk hari ini.</p>
             </div>
          <?php } ?>
        </div>

      </div>
    </main>

    <footer class="bg-gray-900 text-white pt-16 pb-8 border-t-4 border-brand-500 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
          
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-brand-500 text-white rounded-lg flex items-center justify-center"><i class="fa-solid fa-leaf"></i></div>
              <span class="font-bold text-2xl tracking-tight">Si <span class="text-brand-500">Tangkulak</span></span>
            </div>
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
        <div class="pt-8 border-t border-gray-800 text-center text-gray-500 text-sm">
          <p>&copy; 2026 Si Tangkulak. Ekosistem Pertanian Digital.</p>
        </div>
      </div>
    </footer>

  </body>
</html>