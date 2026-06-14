<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

// Logika Update Status Pesanan (Terima / Proses / Selesaikan / Tolak)
if(isset($_POST['update_status'])) {
    $id_pesanan = (int)$_POST['id_pesanan'];
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);
    
    // 1. Update status pesanan
    mysqli_query($conn, "UPDATE pesanan SET status='$status_baru' WHERE id=$id_pesanan");
    
    // 2. LOGIKA BARU: Jika statusnya "Selesai", otomatis tambahkan uang ke Saldo!
    if($status_baru == 'Selesai') {
        // Ambil data total harga dari pesanan yang baru diselesaikan
        $query_get_order = mysqli_query($conn, "SELECT komoditas_dipesan, total_harga FROM pesanan WHERE id=$id_pesanan");
        if($order_data = mysqli_fetch_assoc($query_get_order)) {
            $nominal = $order_data['total_harga'];
            $nama_barang = $order_data['komoditas_dipesan'];
            $keterangan = "Hasil Penjualan " . $nama_barang . " (Order #ORD-" . str_pad($id_pesanan, 4, '0', STR_PAD_LEFT) . ")";
            
            // Masukkan uangnya ke tabel riwayat_saldo
            mysqli_query($conn, "INSERT INTO riwayat_saldo (jenis_transaksi, nominal, keterangan, status) VALUES ('Pendapatan', '$nominal', '$keterangan', 'Berhasil')");
        }
    }

    $_SESSION['pesan'] = "Status pesanan diubah menjadi: " . $status_baru;
    header("Location: pesanan.php");
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pesanan Masuk | Aplikasi Petani</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] } } } }; </script>
  <style>
    .slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>

<body class="bg-gray-50 font-sans antialiased text-gray-800 flex">
  
 <aside class="fixed inset-y-0 left-0 w-64 bg-emerald-900 text-white h-screen p-6 hidden md:flex flex-col z-20 shadow-2xl">
    <div class="flex items-center gap-2 px-2 mb-8 border-b border-emerald-800 pb-6">
      <div class="w-10 h-10 bg-white text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-md"><i class="fa-solid fa-tractor"></i></div>
      <div>
          <span class="font-bold text-lg block leading-none text-emerald-300">Si Tangkulak</span>
          <span class="text-xs text-white opacity-80">Aplikasi Petani</span>
      </div>
    </div>
    
    <nav class="space-y-2 flex-1">
      <a href="dashboard.php" class="flex items-center gap-3 bg-emerald-700 text-white px-4 py-3 rounded-xl font-medium transition shadow-md"><i class="fa-solid fa-store"></i> Etalase Panen</a>
      
      <!-- MENU BARU: Pesanan Masuk -->
      <a href="pesanan.php" class="flex items-center justify-between text-emerald-200 hover:text-white hover:bg-emerald-800 px-4 py-3 rounded-xl font-medium transition group">
          <div class="flex items-center gap-3"><i class="fa-solid fa-bell"></i> Pesanan Masuk</div>
          <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm group-hover:scale-110 transition-transform">3</span>
      </a>
      
      <a href="saldo.php" class="flex items-center gap-3 text-emerald-200 hover:text-white hover:bg-emerald-800 px-4 py-3 rounded-xl font-medium transition"><i class="fa-solid fa-wallet"></i> Saldo & Pendapatan</a>
      <a href="profil.php" class="flex items-center gap-3 text-emerald-200 hover:text-white hover:bg-emerald-800 px-4 py-3 rounded-xl font-medium transition"><i class="fa-solid fa-user-check"></i> Profil Saya</a>
    </nav>

    <div class="mt-auto">
      <a href="logout.php" class="flex items-center gap-3 text-red-300 hover:text-white hover:bg-red-500 px-4 py-3 rounded-xl font-medium transition mb-4">
        <i class="fa-solid fa-power-off"></i> Keluar
      </a>
      <div class="border-t border-emerald-800 pt-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center"><i class="fa-solid fa-user"></i></div>
        <div class="truncate">
          <h4 class="text-sm font-semibold truncate">Mitra Petani</h4>
          <p class="text-xs text-emerald-300 truncate"><?= $_SESSION['email'] ?? 'Akun Terverifikasi' ?></p>
        </div>
      </div>
    </div>
  </aside>

  <main class="flex-1 md:ml-64 min-h-screen p-6 md:p-10 w-full relative overflow-x-hidden">
    <div class="max-w-7xl mx-auto w-full slide-up">
        
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Daftar Pesanan Konsumen</h1>
          <p class="text-sm text-gray-500">Pantau dan kelola orderan sayur/buah yang masuk ke toko Anda.</p>
        </div>
      </header>

      <section class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto p-2">
          <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
              <tr class="bg-emerald-50/50 text-xs font-bold uppercase tracking-wider text-emerald-800 border-b border-gray-100">
                <th class="py-4 px-6">ID Order</th>
                <th class="py-4 px-6">Pembeli & Kontak</th>
                <th class="py-4 px-6">Komoditas Dipilih</th>
                <th class="py-4 px-6">Total Tagihan</th>
                <th class="py-4 px-6">Status Pesanan</th>
                <th class="py-4 px-6 text-center">Tindakan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
              <?php
              $result = @mysqli_query($conn, "SELECT * FROM pesanan ORDER BY id DESC");
              
              if($result && mysqli_num_rows($result) > 0) {
                  while($row = mysqli_fetch_assoc($result)) {
                      $badge = "bg-gray-100 text-gray-700";
                      if($row['status'] == 'Menunggu Konfirmasi') $badge = "bg-yellow-100 text-yellow-700 border border-yellow-200";
                      if($row['status'] == 'Diproses') $badge = "bg-blue-100 text-blue-700 border border-blue-200";
                      if($row['status'] == 'Dikirim') $badge = "bg-indigo-100 text-indigo-700 border border-indigo-200";
                      if($row['status'] == 'Selesai') $badge = "bg-green-100 text-green-700 border border-green-200";
                      if($row['status'] == 'Dibatalkan') $badge = "bg-red-100 text-red-700 border border-red-200";
              ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="py-4 px-6 font-bold text-gray-500">#ORD-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                <td class="py-4 px-6">
                  <div class="font-bold text-gray-900"><?= htmlspecialchars($row['nama_pembeli']) ?></div>
                  <div class="text-xs text-gray-500"><i class="fa-brands fa-whatsapp text-emerald-500"></i> <?= htmlspecialchars($row['kontak_pembeli']) ?></div>
                  <div class="text-xs text-gray-400 mt-1 truncate max-w-[200px]" title="<?= htmlspecialchars($row['alamat_pengiriman']) ?>"><i class="fa-solid fa-truck"></i> <?= htmlspecialchars($row['alamat_pengiriman']) ?></div>
                </td>
                <td class="py-4 px-6 font-semibold text-gray-800">
                  <?= htmlspecialchars($row['komoditas_dipesan']) ?> <br>
                  <span class="text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">Qty: <?= $row['qty'] ?> Kg</span>
                </td>
                <td class="py-4 px-6 font-bold text-emerald-600 text-lg">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                <td class="py-4 px-6">
                  <span class="px-2.5 py-1 text-xs font-bold rounded-full <?= $badge ?> shadow-sm"><?= htmlspecialchars($row['status']) ?></span>
                  <div class="text-[10px] text-gray-400 mt-1"><?= date('d M Y, H:i', strtotime($row['tanggal_pesan'])) ?></div>
                </td>
                <td class="py-4 px-6 text-center">
                    <?php if($row['status'] == 'Menunggu Konfirmasi'): ?>
                        <div class="flex items-center justify-center gap-2">
                            <form method="POST" action="">
                                <input type="hidden" name="id_pesanan" value="<?= $row['id'] ?>">
                                <input type="hidden" name="status_baru" value="Diproses">
                                <button type="submit" name="update_status" class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-600 hover:text-white transition shadow-sm"><i class="fa-solid fa-check"></i> Terima</button>
                            </form>
                            <form method="POST" action="" onsubmit="return confirm('Yakin ingin menolak pesanan ini?');">
                                <input type="hidden" name="id_pesanan" value="<?= $row['id'] ?>">
                                <input type="hidden" name="status_baru" value="Dibatalkan">
                                <button type="submit" name="update_status" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition shadow-sm"><i class="fa-solid fa-xmark"></i> Tolak</button>
                            </form>
                        </div>
                    <?php elseif($row['status'] == 'Diproses'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="id_pesanan" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status_baru" value="Dikirim">
                            <button type="submit" name="update_status" class="bg-blue-50 text-blue-700 px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fa-solid fa-truck-fast"></i> Kirim Orderan</button>
                        </form>
                    <?php elseif($row['status'] == 'Dikirim'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="id_pesanan" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status_baru" value="Selesai">
                            <button type="submit" name="update_status" class="bg-green-500 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-green-600 transition shadow-md"><i class="fa-solid fa-check-double"></i> Selesaikan</button>
                        </form>
                    <?php else: ?>
                        <span class="text-xs text-gray-400 font-medium italic"><i class="fa-solid fa-lock text-gray-300"></i> Terkunci</span>
                    <?php endif; ?>
                </td>
              </tr>
              <?php } } else { ?>
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-4xl shadow-inner"><i class="fa-solid fa-receipt"></i></div>
                        <p class="text-gray-500 font-bold text-lg">Belum ada pesanan masuk.</p>
                        <p class="text-gray-400 text-sm mt-1">Pastikan stok di etalase Anda selalu tersedia dan menarik.</p>
                    </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <script>
    <?php if(isset($_SESSION['pesan'])): ?>
        Swal.fire({ title: 'Berhasil!', text: '<?= $_SESSION['pesan'] ?>', icon: 'success', confirmButtonColor: '#059669', timer: 3000 });
        <?php unset($_SESSION['pesan']); ?>
    <?php endif; ?>
  </script>
</body>
</html>