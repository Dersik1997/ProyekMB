<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

// Logika Penarikan Saldo
if(isset($_POST['tarik_saldo'])) {
    $nominal = (int)$_POST['nominal'];
    $keterangan = "Penarikan ke Rekening Bank " . mysqli_real_escape_string($conn, $_POST['bank']);
    
    // Insert ke tabel riwayat_saldo dengan jenis 'Penarikan' status 'Pending'
    mysqli_query($conn, "INSERT INTO riwayat_saldo (jenis_transaksi, nominal, keterangan, status) VALUES ('Penarikan', '$nominal', '$keterangan', 'Pending')");
    
    $_SESSION['pesan'] = "Permintaan penarikan Rp " . number_format($nominal, 0, ',', '.') . " sedang diproses oleh admin.";
    header("Location: saldo.php");
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Saldo Dompet | Aplikasi Petani</title>
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
  
  <!-- SIDEBAR (Aplikasi Petani) -->
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

  <!-- MAIN CONTENT -->
  <main class="flex-1 md:ml-64 min-h-screen p-6 md:p-10 w-full relative overflow-x-hidden">
    <div class="max-w-7xl mx-auto w-full slide-up">
        
      <!-- DOMPET HEADER -->
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-emerald-600 p-8 rounded-3xl shadow-lg border border-emerald-500 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
        <div class="relative z-10">
          <p class="text-emerald-100 font-semibold mb-1 uppercase tracking-widest text-xs"><i class="fa-solid fa-wallet"></i> Saldo Tersedia Anda</p>
          <?php
            // Hitung Total Pemasukan
            // Hitung Total Pemasukan
            $query_pemasukan = @mysqli_query($conn, "SELECT SUM(nominal) as masuk FROM riwayat_saldo WHERE jenis_transaksi='Pendapatan' AND status='Berhasil'");
            $row_pemasukan = mysqli_fetch_assoc($query_pemasukan);
            $pemasukan = $row_pemasukan['masuk'] ? (int)$row_pemasukan['masuk'] : 0;

            // Hitung Total Penarikan
            $query_penarikan = @mysqli_query($conn, "SELECT SUM(nominal) as keluar FROM riwayat_saldo WHERE jenis_transaksi='Penarikan'");
            $row_penarikan = mysqli_fetch_assoc($query_penarikan);
            $penarikan = $row_penarikan['keluar'] ? (int)$row_penarikan['keluar'] : 0;

            $saldo_aktif = $pemasukan - $penarikan;
            if($saldo_aktif < 0) $saldo_aktif = 0; // Prevent minus display
          ?>
          <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-2">Rp <?= number_format($saldo_aktif, 0, ',', '.') ?></h1>
          <p class="text-sm text-emerald-200">Hasil penjualan dari Pasar Tani langsung masuk ke dompet ini.</p>
        </div>
        <button onclick="toggleModal('modalTarikSaldo')" class="relative z-10 bg-white text-emerald-700 hover:bg-emerald-50 px-8 py-4 rounded-xl font-bold shadow-xl flex items-center gap-3 transition-all transform hover:-translate-y-1">
          <i class="fa-solid fa-money-bill-transfer text-xl"></i> Tarik Ke Bank
        </button>
      </header>

      <!-- STATISTIK SINGKAT -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5">
              <div class="w-14 h-14 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl"><i class="fa-solid fa-arrow-trend-up"></i></div>
              <div>
                  <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Pendapatan (Kotor)</p>
                  <h3 class="text-2xl font-bold text-gray-900">Rp <?= number_format($pemasukan, 0, ',', '.') ?></h3>
              </div>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5">
              <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-2xl"><i class="fa-solid fa-money-bill-wave"></i></div>
              <div>
                  <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Sudah Ditarik</p>
                  <h3 class="text-2xl font-bold text-gray-900">Rp <?= number_format($penarikan, 0, ',', '.') ?></h3>
              </div>
          </div>
      </div>

      <!-- RIWAYAT TRANSAKSI -->
      <section class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-700"><i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto p-2">
          <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
              <tr class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                <th class="py-4 px-6">ID Transaksi</th>
                <th class="py-4 px-6">Keterangan</th>
                <th class="py-4 px-6">Tanggal</th>
                <th class="py-4 px-6">Nominal</th>
                <th class="py-4 px-6">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
              <?php
              $result = @mysqli_query($conn, "SELECT * FROM riwayat_saldo ORDER BY id DESC LIMIT 15");
              if($result && mysqli_num_rows($result) > 0) {
                  while($row = mysqli_fetch_assoc($result)) {
                      $is_masuk = ($row['jenis_transaksi'] == 'Pendapatan');
                      $nominal_color = $is_masuk ? "text-green-600" : "text-gray-900";
                      $icon = $is_masuk ? "<i class='fa-solid fa-arrow-down text-green-500 bg-green-100 p-2 rounded-full mr-2'></i>" : "<i class='fa-solid fa-arrow-up text-orange-500 bg-orange-100 p-2 rounded-full mr-2'></i>";
                      
                      $badge = "bg-gray-100 text-gray-700";
                      if($row['status'] == 'Berhasil') $badge = "bg-green-100 text-green-700 border-green-200";
                      if($row['status'] == 'Pending') $badge = "bg-yellow-100 text-yellow-700 border-yellow-200";
                      if($row['status'] == 'Gagal') $badge = "bg-red-100 text-red-700 border-red-200";
              ?>
              <tr class="hover:bg-gray-50/50">
                <td class="py-4 px-6 text-gray-500 text-xs">TRX-<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT) ?></td>
                <td class="py-4 px-6 font-semibold flex items-center"><?= $icon ?> <?= htmlspecialchars($row['keterangan']) ?></td>
                <td class="py-4 px-6 text-gray-500"><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                <td class="py-4 px-6 font-bold <?= $nominal_color ?>"><?= $is_masuk ? '+' : '-' ?> Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                <td class="py-4 px-6"><span class="px-2 py-1 rounded text-xs font-bold border <?= $badge ?>"><?= $row['status'] ?></span></td>
              </tr>
              <?php } } else { ?>
                <tr>
                    <td colspan="5" class="py-16 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300 text-2xl shadow-inner"><i class="fa-solid fa-money-bill"></i></div>
                        <p class="text-gray-500 font-medium">Belum ada aktivitas transaksi.</p>
                    </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <!-- MODAL TARIK SALDO -->
  <div id="modalTarikSaldo" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900/60 backdrop-blur-sm p-4 transition-opacity">
      <form method="POST" action="" class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden flex flex-col slide-up">
          <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
              <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2"><i class="fa-solid fa-building-columns text-emerald-600"></i> Tarik Ke Rekening Bank</h3>
              <button type="button" onclick="toggleModal('modalTarikSaldo')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
          </div>
          <div class="p-6 space-y-5 bg-white">
              <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 flex justify-between items-center">
                  <span class="text-sm font-bold text-emerald-800">Saldo Maksimal:</span>
                  <span class="text-lg font-black text-emerald-600">Rp <?= number_format($saldo_aktif, 0, ',', '.') ?></span>
              </div>
              
              <div>
                  <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">Nominal Penarikan (Rp)</label>
                  <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 font-bold">Rp</span>
                      <input type="number" name="nominal" max="<?= $saldo_aktif ?>" required class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl font-bold text-gray-900 text-lg focus:ring-2 focus:ring-emerald-500 outline-none shadow-inner" placeholder="0">
                  </div>
              </div>
              
              <div>
                  <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">Pilih Bank Tujuan</label>
                  <select name="bank" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 bg-white shadow-sm font-medium">
                      <option value="BRI">Bank BRI</option>
                      <option value="BCA">Bank BCA</option>
                      <option value="Mandiri">Bank Mandiri</option>
                      <option value="BSI">Bank BSI</option>
                  </select>
              </div>

              <div>
                  <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">Nomor Rekening</label>
                  <input type="text" name="no_rek" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none shadow-sm" placeholder="Contoh: 1234567890">
              </div>
          </div>
          <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
              <button type="button" onclick="toggleModal('modalTarikSaldo')" class="px-5 py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl text-sm hover:bg-gray-100 transition">Batal</button>
              <button type="submit" name="tarik_saldo" class="px-5 py-3 bg-emerald-600 text-white font-bold rounded-xl text-sm hover:bg-emerald-500 shadow-lg shadow-emerald-600/30 transition">Ajukan Penarikan</button>
          </div>
      </form>
  </div>

  <script>
    <?php if(isset($_SESSION['pesan'])): ?>
        Swal.fire({ title: 'Berhasil!', text: '<?= $_SESSION['pesan'] ?>', icon: 'success', confirmButtonColor: '#059669', timer: 4000 });
        <?php unset($_SESSION['pesan']); ?>
    <?php endif; ?>

    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle("hidden");
    }
  </script>
</body>
</html>