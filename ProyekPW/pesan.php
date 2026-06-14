<?php
session_start();
include "koneksi.php";

// Cek apakah ada ID produk yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_produk = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM komoditas WHERE id = $id_produk");

// Jika produk tidak ditemukan, kembalikan ke index
if (mysqli_num_rows($query) == 0) {
    header("Location: index.php");
    exit();
}

$produk = mysqli_fetch_assoc($query);
$harga_asli = $produk['harga'];

// MENGAMBIL SEMUA FOTO DARI DATABASE
$fotos = json_decode($produk['foto'], true);
// Menetapkan foto pertama sebagai foto utama secara default
$img_src_default = (is_array($fotos) && count($fotos) > 0) ? 'uploads/' . $fotos[0] : 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=500&q=80';

// Logika ketika form pesanan disubmit
if (isset($_POST['buat_pesanan'])) {
    $nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $qty = (int)$_POST['qty'];
    
    $komoditas = mysqli_real_escape_string($conn, $produk['nama_komoditas']);
    $total_harga = $qty * $harga_asli;

    $insert = "INSERT INTO pesanan (nama_pembeli, kontak_pembeli, alamat_pengiriman, komoditas_dipesan, qty, total_harga, status) 
               VALUES ('$nama_pembeli', '$kontak', '$alamat', '$komoditas', '$qty', '$total_harga', 'Menunggu Konfirmasi')";
    
    if (mysqli_query($conn, $insert)) {
        $_SESSION['pesan_sukses'] = "Pesanan berhasil dibuat! Petani akan segera menghubungi WhatsApp Anda.";
        header("Location: index.php");
        exit();
    }
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Selesaikan Pesanan | Si Tangkulak</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] }, colors: { brand: { 50: "#ecfdf5", 500: "#10b981", 600: "#059669", 900: "#064e3b" } } } } };</script>
  <style>
    /* Sembunyikan scrollbar untuk galeri foto agar rapi */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
  
  <!-- NAVBAR -->
  <nav class="fixed w-full z-50 backdrop-blur-md bg-white/90 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between h-20 items-center">
      <a href="index.php" class="flex-shrink-0 flex items-center gap-2">
        <div class="w-10 h-10 bg-brand-500 text-white rounded-xl flex items-center justify-center text-xl shadow-lg"><i class="fa-solid fa-leaf"></i></div>
        <span class="font-bold text-2xl text-gray-900">Si <span class="text-brand-600">Tangkulak</span></span>
      </a>
      <a href="pasar.php" class="text-gray-500 hover:text-brand-600 font-medium transition flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Batal & Kembali</a>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="flex-1 pt-32 pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Selesaikan Pesanan Anda 🛒</h1>
          <p class="text-gray-500 mt-2">Lengkapi detail pengiriman agar petani bisa langsung memprosesnya.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          <!-- KIRI: Detail Produk + GALERI FOTO -->
          <div class="lg:col-span-5 space-y-4">
              <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 sticky top-28">
                  
                  <!-- Foto Utama Produk -->
                  <div class="aspect-square rounded-2xl overflow-hidden mb-4 bg-gray-100 relative shadow-inner">
                      <img id="mainProductImg" src="<?= $img_src_default ?>" alt="Produk" class="w-full h-full object-cover transition-all duration-300">
                  </div>

                  <!-- FITUR GALERI (Hanya Muncul Jika Foto Lebih Dari 1) -->
                  <?php if(is_array($fotos) && count($fotos) > 1): ?>
                  <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
                      <?php foreach($fotos as $index => $foto): ?>
                          <img src="uploads/<?= $foto ?>" 
                               onclick="changePreviewImg('uploads/<?= $foto ?>', this)" 
                               class="w-16 h-16 rounded-xl object-cover border-2 <?= $index === 0 ? 'border-brand-500 bg-brand-50' : 'border-gray-200' ?> cursor-pointer hover:border-brand-500 transition-all thumb-img" 
                               alt="thumbnail">
                      <?php endforeach; ?>
                  </div>
                  <?php endif; ?>

                  <div class="inline-flex bg-brand-50 text-brand-600 text-xs font-bold px-3 py-1 rounded-full my-3 border border-brand-100">
                      <i class="fa-solid fa-check-circle mr-1"></i> Stok Siap: <?= htmlspecialchars($produk['volume']) ?>
                  </div>
                  
                  <h2 class="text-2xl font-bold text-gray-900 mb-2 leading-tight"><?= htmlspecialchars($produk['nama_komoditas']) ?></h2>
                  <div class="text-3xl font-black text-brand-600 mb-6">Rp <?= number_format($harga_asli, 0, ',', '.') ?><span class="text-sm text-gray-500 font-medium"> /kg</span></div>
                  
                  <div class="border-t border-gray-100 pt-5 space-y-3">
                      <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-user-tractor"></i></div>
                          <div><p class="text-xs text-gray-500">Dijual Oleh</p><p class="font-bold text-gray-900"><?= htmlspecialchars($produk['petani']) ?></p></div>
                      </div>
                      <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center"><i class="fa-solid fa-location-dot"></i></div>
                          <div><p class="text-xs text-gray-500">Dikirim Dari</p><p class="font-bold text-gray-900"><?= htmlspecialchars($produk['lokasi']) ?></p></div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- KANAN: Form Checkout -->
          <div class="lg:col-span-7">
              <form method="POST" action="pesan.php?id=<?= $id_produk ?>" class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
                  <h3 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Informasi Pengiriman & Kontak</h3>
                  
                  <div class="space-y-5">
                      <div>
                          <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap Penerima</label>
                          <input type="text" name="nama_pembeli" required class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition" placeholder="Contoh: Budi Santoso">
                      </div>
                      
                      <div>
                          <label class="block text-sm font-bold text-gray-700 mb-2">No. WhatsApp Aktif</label>
                          <input type="number" name="kontak" required class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition" placeholder="Contoh: 081234567890">
                      </div>
                      
                      <div>
                          <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap Pengiriman</label>
                          <textarea name="alamat" required rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition" placeholder="Tuliskan nama jalan, blok, RT/RW, dan patokan rumah..."></textarea>
                      </div>

                      <div>
                          <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Beli (Kg)</label>
                          <div class="relative">
                              <input type="number" id="qty" name="qty" required min="1" value="1" oninput="hitungTotal()" class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-brand-500 outline-none transition text-xl font-black text-brand-700 text-center">
                              <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">Kilogram</span>
                          </div>
                      </div>
                  </div>

                  <div class="mt-8 bg-gray-900 rounded-2xl p-6 text-white flex justify-between items-center shadow-md">
                      <div>
                          <p class="text-gray-400 text-sm mb-1">Total Pembayaran</p>
                          <h4 class="text-3xl font-bold" id="tampil_total">Rp <?= number_format($harga_asli, 0, ',', '.') ?></h4>
                      </div>
                      <button type="submit" name="buat_pesanan" class="bg-brand-500 hover:bg-brand-400 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-lg transition-transform transform hover:-translate-y-1 active:scale-95">
                          Pesan Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                      </button>
                  </div>
                  <p class="text-xs text-gray-400 mt-5 text-center leading-relaxed"><i class="fa-solid fa-shield-halved text-brand-500"></i> Aman & Terpercaya. Pembayaran dilakukan dengan COD atau Transfer setelah dihubungi petani.</p>
              </form>
          </div>

      </div>
    </div>
  </main>

  <script>
      const hargaPerKg = <?= $harga_asli ?>;
      
      // SCRIPT INTERAKTIF KHUSUS GANTI GAMBAR UTAMA SAAT THUMBNAIL DIKLIK
      function changePreviewImg(src, element) {
          document.getElementById('mainProductImg').src = src;
          
          // Reset border dari semua thumbnail
          document.querySelectorAll('.thumb-img').forEach(img => {
              img.classList.remove('border-brand-500', 'bg-brand-50');
              img.classList.add('border-gray-200');
          });
          
          // Set border hijau ke gambar yang sedang diklik
          element.classList.remove('border-gray-200');
          element.classList.add('border-brand-500', 'bg-brand-50');
      }

      // SCRIPT HITUNG TOTAL HARGA
      function hitungTotal() {
          let qtyInput = document.getElementById('qty');
          let qty = parseInt(qtyInput.value);
          if(isNaN(qty) || qty < 1) qty = 1;
          
          let total = hargaPerKg * qty;
          document.getElementById('tampil_total').innerText = 'Rp ' + total.toLocaleString('id-ID');
      }
  </script>
</body>
</html>