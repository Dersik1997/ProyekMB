<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

// ==========================================
// 1. EKSPOR EXCEL (CSV)
// ==========================================
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Data_Etalase_Panen_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Nama Komoditas', 'Volume', 'Nama Petani', 'Lokasi', 'Harga/kg', 'Status'));
    $res = mysqli_query($conn, "SELECT * FROM komoditas ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, array($row['id'], $row['nama_komoditas'], $row['volume'], $row['petani'], $row['lokasi'], $row['harga'], $row['status']));
    }
    fclose($output);
    exit();
}

// ==========================================
// 2. HAPUS DATA (DELETE)
// ==========================================
if (isset($_POST['hapus_komoditas'])) {
    $id = (int)$_POST['id_hapus'];
    $res = mysqli_query($conn, "SELECT foto, tanda_tangan FROM komoditas WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $fotos = json_decode($row['foto'], true);
        if (is_array($fotos)) {
            foreach ($fotos as $f) { if (file_exists("uploads/" . $f)) unlink("uploads/" . $f); }
        }
        if (file_exists("uploads/" . $row['tanda_tangan']) && !empty($row['tanda_tangan'])) unlink("uploads/" . $row['tanda_tangan']);
    }
    mysqli_query($conn, "DELETE FROM komoditas WHERE id=$id");
    $_SESSION['pesan'] = "Hasil panen ditarik dari etalase!";
    header("Location: dashboard.php");
    exit;
}

// ==========================================
// 3. EDIT DATA (UPDATE)
// ==========================================
if (isset($_POST['edit_komoditas'])) {
    $id = (int)$_POST['id_edit'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_komoditas']);
    $petani = mysqli_real_escape_string($conn, $_POST['petani']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $volume = mysqli_real_escape_string($conn, $_POST['volume']);
    $harga = (int)$_POST['harga'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE komoditas SET nama_komoditas='$nama', petani='$petani', lokasi='$lokasi', volume='$volume', harga='$harga', status='$status' WHERE id=$id");
    $_SESSION['pesan'] = "Etalase panen berhasil diperbarui!";
    header("Location: dashboard.php");
    exit;
}

// ==========================================
// 4. SIMPAN DATA (CREATE) & MULTIPLE UPLOAD
// ==========================================
if (isset($_POST['simpan_data'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_komoditas']);
    $petani = mysqli_real_escape_string($conn, $_POST['petani']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $volume = mysqli_real_escape_string($conn, $_POST['volume']);
    $harga = (int)$_POST['harga'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $foto_filenames = [];
    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'][0] != "") {
        for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
            $tmp_name = $_FILES['foto']['tmp_name'][$i];
            $file_name = time() . "_" . rand(100, 999) . "_" . $_FILES['foto']['name'][$i];
            if (move_uploaded_file($tmp_name, "uploads/" . $file_name)) { $foto_filenames[] = $file_name; }
        }
    }
    $foto_json = json_encode($foto_filenames);

    $ttd_filename = "";
    if (!empty($_POST['signature_base64'])) {
        $img_data = $_POST['signature_base64'];
        $img_data = str_replace('data:image/png;base64,', '', $img_data);
        $img_data = str_replace(' ', '+', $img_data);
        $data = base64_decode($img_data);
        $ttd_filename = "ttd_" . time() . "_" . rand(100, 999) . ".png";
        file_put_contents("uploads/" . $ttd_filename, $data);
    }

    $query = "INSERT INTO komoditas (nama_komoditas, petani, lokasi, volume, harga, status, foto, tanda_tangan) 
              VALUES ('$nama', '$petani', '$lokasi', '$volume', '$harga', '$status', '$foto_json', '$ttd_filename')";
    mysqli_query($conn, $query);
    $_SESSION['pesan'] = "Hasil panen berhasil di-posting ke Pasar Tani!";
    header("Location: dashboard.php");
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aplikasi Petani | Si Tangkulak</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] } } } }; </script>
  <style>
    .fade-in { animation: fadeIn 0.5s ease-in-out; }
    .slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
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
        
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Etalase Hasil Panen</h1>
          <p class="text-sm text-gray-500">Kelola dan upload komoditas yang siap dijual hari ini.</p>
        </div>
        <button onclick="toggleModal('modalTambah')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3.5 rounded-xl font-semibold shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition-all transform hover:-translate-y-1">
          <i class="fa-solid fa-camera"></i> Posting Panen Baru
        </button>
      </header>

      <!-- ============================================== -->
      <!-- TABEL DATA, PENCARIAN & EXPORT                 -->
      <!-- ============================================== -->
      <section class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden mb-8">
        
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-4 bg-gray-50/50">
          <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 mr-2">Ekspor Data:</span>
            <a href="?export=csv" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 shadow-xs flex items-center gap-1.5 transition"><i class="fa-regular fa-file-excel text-emerald-600"></i> Excel</a>
            <button onclick="exportToPDF()" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 shadow-xs flex items-center gap-1.5 transition"><i class="fa-regular fa-file-pdf text-red-500"></i> PDF</button>
          </div>
          
          <div class="relative w-full md:w-72">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fa-solid fa-magnifying-glass text-sm"></i></span>
            <input type="text" id="searchInput" placeholder="Cari sayur, buah, atau lokasi..." class="w-full pl-9 pr-4 py-2 border border-gray-200 bg-white rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-inner" />
          </div>
        </div>

        <div id="table-export-area" class="overflow-x-auto p-2">
          <div class="hidden print-header text-center mb-6"><h2 class="text-xl font-bold">Laporan Etalase Petani</h2><p class="text-sm text-gray-500">Si Tangkulak - <?= date('d M Y') ?></p><hr class="mt-2 border-gray-300"></div>
          
          <table class="w-full text-left border-collapse whitespace-nowrap" id="dataTable">
            <thead>
              <tr class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                <th class="py-4 px-6">Produk & Stok</th>
                <th class="py-4 px-6">Harga Jual/kg</th>
                <th class="py-4 px-6">Foto Etalase</th>
                <th class="py-4 px-6 text-center">Tanda Tangan</th>
                <th class="py-4 px-6 text-center action-col">Atur Etalase</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
              <?php
              $result = mysqli_query($conn, "SELECT * FROM komoditas ORDER BY id DESC");
              if(mysqli_num_rows($result) > 0) {
                  while($row = mysqli_fetch_assoc($result)) {
              ?>
              <tr class="hover:bg-gray-50/80 transition-colors search-row">
                <td class="py-4 px-6 font-semibold text-gray-900">
                  <div class="search-target"><?= htmlspecialchars($row['nama_komoditas']) ?></div>
                  <span class="inline-block bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-md mt-1 search-target">
                      Stok: <?= htmlspecialchars($row['volume']) ?> | Lahan: <?= htmlspecialchars($row['lokasi']) ?>
                  </span>
                </td>
                
                <td class="py-4 px-6 font-bold text-emerald-600 text-lg">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                
                <td class="py-4 px-6">
                  <div class="flex flex-wrap gap-2">
                  <?php 
                  $fotos = json_decode($row['foto'], true);
                  if(is_array($fotos) && count($fotos) > 0) {
                      foreach(array_slice($fotos, 0, 3) as $foto) {
                          echo "<img src='uploads/".$foto."' onclick='previewImage(\"uploads/".$foto."\")' class='w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-pointer hover:opacity-75 shadow-sm transition-all' title='Perbesar' alt='foto'>";
                      }
                  } else { echo "<span class='text-xs text-gray-400 italic'>Tanpa foto</span>"; }
                  ?>
                  </div>
                </td>

                <td class="py-4 px-6 text-center">
                    <?php if(!empty($row['tanda_tangan']) && file_exists("uploads/" . $row['tanda_tangan'])): ?>
                        <img src="uploads/<?= $row['tanda_tangan'] ?>" onclick="previewImage('uploads/<?= $row['tanda_tangan'] ?>')" class="h-10 w-auto rounded bg-white border cursor-pointer hover:shadow-md mx-auto" alt="TTD">
                    <?php else: ?>
                        <span class="text-xs text-gray-400">Belum TTD</span>
                    <?php endif; ?>
                </td>

                <td class="py-4 px-6 text-center action-col">
                  <div class="flex items-center justify-center gap-2">
                      <button onclick="openEditModal(<?= $row['id'] ?>, '<?= addslashes($row['nama_komoditas']) ?>', '<?= addslashes($row['petani']) ?>', '<?= addslashes($row['lokasi']) ?>', '<?= addslashes($row['volume']) ?>', <?= $row['harga'] ?>, '<?= $row['status'] ?>')" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition"><i class="fa-solid fa-pen mr-1"></i> Edit</button>
                      <form method="POST" action="" class="inline-block" onsubmit="return confirmDelete(this);">
                          <input type="hidden" name="id_hapus" value="<?= $row['id'] ?>">
                          <input type="hidden" name="hapus_komoditas" value="1">
                          <button type="button" onclick="confirmDelete(this.form)" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition"><i class="fa-solid fa-trash mr-1"></i> Tarik</button>
                      </form>
                  </div>
                </td>
              </tr>
              <?php } } else { echo "<tr><td colspan='5' class='py-16 text-center'><div class='w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400 text-2xl'><i class='fa-solid fa-basket-shopping'></i></div><p class='text-gray-500 font-medium'>Belum ada hasil panen yang di-upload ke etalase.</p></td></tr>"; } ?>
            </tbody>
          </table>
          <div id="noResult" class="hidden text-center py-10 text-gray-500"><i class="fa-solid fa-search text-3xl mb-2 text-gray-300"></i><br>Data tidak ditemukan.</div>
        </div>
      </section>
    </div>
  </main>

  <!-- MODAL PREVIEW GAMBAR LIGHBOX -->
  <div id="imagePreviewModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/90 p-4 transition-opacity fade-in" onclick="toggleModal('imagePreviewModal')">
      <div class="relative max-w-4xl max-h-[90vh]">
          <button class="absolute -top-10 right-0 text-white hover:text-gray-300 text-3xl"><i class="fa-solid fa-xmark"></i></button>
          <img id="previewImageSrc" src="" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl bg-white" onclick="event.stopPropagation()">
      </div>
  </div>

  <!-- MODAL POSTING PANEN BARU -->
  <div id="modalTambah" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="toggleModal('modalTambah')"></div>
    <div class="flex min-h-full items-center justify-center p-4 relative z-10">
      <form method="POST" action="" enctype="multipart/form-data" onsubmit="prepareSubmit()" class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col slide-up">
        <div class="p-6 border-b bg-emerald-50/50 flex justify-between items-center">
          <h3 class="text-xl font-bold text-emerald-900 flex items-center gap-2"><i class="fa-solid fa-camera text-emerald-600"></i> Posting Panen Hari Ini</h3>
          <button type="button" onclick="toggleModal('modalTambah')" class="text-gray-400 hover:text-gray-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="p-6 space-y-5 overflow-y-auto max-h-[70vh]">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-bold text-gray-500 mb-1">Sayur / Buah Apa Yang Dijual?</label><input type="text" name="nama_komoditas" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500" placeholder="Cth: Sawi Hijau Segar" /></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Berapa Kg Stoknya?</label><input type="text" name="volume" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500" placeholder="Cth: 50 Kg" /></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Harga Jual per Kg (Rp)</label><input type="number" name="harga" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500" placeholder="Cth: 12000" /></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Nama Anda (Petani)</label><input type="text" name="petani" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500" placeholder="Cth: Kang Jajang" /></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Lokasi Lahan</label><input type="text" name="lokasi" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500" placeholder="Cth: Lembang" /></div>
          </div>
          <input type="hidden" name="status" value="Terverifikasi"> 

          <div>
            <label class="block text-xs font-bold text-gray-500 mb-2">Ambil Foto Hasil Panen (Bisa pilih banyak)</label>
            <div class="border-2 border-dashed border-emerald-200 hover:border-emerald-500 rounded-xl p-6 text-center cursor-pointer bg-emerald-50/30 transition relative group">
              <input type="file" name="foto[]" multiple accept="image/*" onchange="updateFileFeedback(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
              <div class="space-y-2 pointer-events-none">
                <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-emerald-500 mx-auto"><i class="fa-solid fa-camera text-xl"></i></div>
                <p class="text-sm font-bold text-emerald-800">Sentuh untuk buka kamera/galeri</p>
                <div id="fileFeedback" class="hidden mt-3 p-3 bg-white border border-emerald-100 rounded-xl text-xs text-emerald-700 text-left w-full"></div>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">Tanda Tangan Konfirmasi Petani</label>
            <div class="bg-gray-50 rounded-xl overflow-hidden border relative">
              <canvas id="signaturePad" class="w-full h-32 cursor-crosshair block"></canvas>
              <input type="hidden" name="signature_base64" id="signature_base64">
              <button type="button" onclick="clearSignature()" class="absolute bottom-2 right-2 px-3 py-1 bg-white text-red-500 text-xs font-bold rounded-lg border shadow-sm"><i class="fa-solid fa-eraser"></i> Ulang</button>
            </div>
          </div>
        </div>

        <div class="p-6 border-t flex justify-end gap-3 bg-gray-50">
          <button type="button" onclick="toggleModal('modalTambah')" class="px-6 py-3 bg-white border text-gray-700 font-bold rounded-xl text-sm">Batal</button>
          <button type="submit" name="simpan_data" class="px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl text-sm hover:bg-emerald-500 shadow-lg shadow-emerald-600/30">Posting ke Pasar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT -->
  <div id="modalEdit" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="toggleModal('modalEdit')"></div>
    <div class="flex min-h-full items-center justify-center p-4 relative z-10">
      <form method="POST" action="" class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col slide-up">
        <div class="p-6 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-bold text-blue-900 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-blue-600"></i> Edit Informasi Etalase</h3>
          <button type="button" onclick="toggleModal('modalEdit')" class="text-gray-400 hover:text-gray-600 text-xl"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="p-6 space-y-5 overflow-y-auto max-h-[70vh]">
          <input type="hidden" name="id_edit" id="edit_id">
          <input type="hidden" name="status" id="edit_status">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-bold text-gray-500 mb-1">Nama Produk</label><input type="text" name="nama_komoditas" id="edit_nama" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500"/></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Update Stok (Kg)</label><input type="text" name="volume" id="edit_volume" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500"/></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Update Harga (Rp)</label><input type="number" name="harga" id="edit_harga" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500"/></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Nama Petani</label><input type="text" name="petani" id="edit_petani" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500"/></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Lokasi Lahan</label><input type="text" name="lokasi" id="edit_lokasi" required class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500"/></div>
          </div>
          <div class="bg-blue-50 text-blue-700 text-xs p-3 rounded-xl border border-blue-100 flex items-center gap-2">
            <i class="fa-solid fa-circle-info"></i> Ganti foto atau tanda tangan? Silakan tarik data ini lalu buat postingan baru.
          </div>
        </div>

        <div class="p-6 border-t flex justify-end gap-3 bg-gray-50">
          <button type="button" onclick="toggleModal('modalEdit')" class="px-6 py-3 bg-white border text-gray-700 font-bold rounded-xl text-sm">Batal</button>
          <button type="submit" name="edit_komoditas" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl text-sm hover:bg-blue-500 shadow-lg shadow-blue-600/30">Update Etalase</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    <?php if(isset($_SESSION['pesan'])): ?>
        Swal.fire({ title: 'Berhasil!', text: '<?= $_SESSION['pesan'] ?>', icon: 'success', confirmButtonColor: '#059669', timer: 3000, showClass: { popup: 'animate__animated animate__fadeInDown' } });
        <?php unset($_SESSION['pesan']); ?>
    <?php endif; ?>

    // PENCARIAN DATA (DataTables Manual)
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.search-row');
        let hasResult = false;
        rows.forEach(row => {
            let text = row.querySelector('.search-target').innerText.toLowerCase();
            if(text.includes(filter)) { row.style.display = ''; hasResult = true; } 
            else { row.style.display = 'none'; }
        });
        document.getElementById('noResult').style.display = hasResult ? 'none' : 'block';
    });

    function previewImage(src) { document.getElementById('previewImageSrc').src = src; toggleModal('imagePreviewModal'); }

    function updateFileFeedback(input) {
        const feedbackDiv = document.getElementById('fileFeedback');
        if (input.files.length > 0) {
            let fileNames = [];
            for(let i=0; i<input.files.length; i++) fileNames.push("<li class='truncate'>• " + input.files[i].name + "</li>");
            feedbackDiv.innerHTML = "<p class='font-bold mb-1'><i class='fa-solid fa-check-circle'></i> " + input.files.length + " Foto Siap Upload:</p><ul>" + fileNames.join('') + "</ul>";
            feedbackDiv.classList.remove('hidden'); feedbackDiv.classList.add('block');
        } else {
            feedbackDiv.classList.add('hidden'); feedbackDiv.classList.remove('block');
        }
    }

    function confirmDelete(form) {
        Swal.fire({ title: 'Tarik Jualan?', text: "Hasil panen ini akan ditarik dari etalase pasar.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Tarik!', cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) form.submit(); })
    }

    function toggleModal(modalID) {
      document.getElementById(modalID).classList.toggle("hidden");
      if(modalID === 'modalTambah') setTimeout(() => initSignaturePad(), 50);
    }

    function openEditModal(id, nama, petani, lokasi, volume, harga, status) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_petani').value = petani;
        document.getElementById('edit_lokasi').value = lokasi;
        document.getElementById('edit_volume').value = volume;
        document.getElementById('edit_harga').value = harga;
        document.getElementById('edit_status').value = status;
        toggleModal('modalEdit');
    }

    let canvas, ctx, isDrawing = false;
    function initSignaturePad() {
      canvas = document.getElementById("signaturePad"); ctx = canvas.getContext("2d");
      canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight;
      ctx.strokeStyle = "#064e3b"; ctx.lineWidth = 3; ctx.lineCap = "round";
      canvas.addEventListener("mousedown", (e) => { isDrawing = true; draw(e); });
      canvas.addEventListener("mousemove", draw); canvas.addEventListener("mouseup", () => (isDrawing = false));
      canvas.addEventListener("touchstart", (e) => { isDrawing = true; drawTouch(e); });
      canvas.addEventListener("touchmove", drawTouch); canvas.addEventListener("touchend", () => (isDrawing = false));
    }
    function draw(e) {
      if (!isDrawing) return; const rect = canvas.getBoundingClientRect();
      ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top); ctx.stroke();
      ctx.beginPath(); ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }
    function drawTouch(e) {
      if (!isDrawing) return; e.preventDefault(); const rect = canvas.getBoundingClientRect(); const touch = e.touches[0];
      ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top); ctx.stroke();
      ctx.beginPath(); ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
    }
    function clearSignature() { if (ctx && canvas) ctx.clearRect(0, 0, canvas.width, canvas.height); }
    function prepareSubmit() { document.getElementById("signature_base64").value = canvas.toDataURL("image/png"); }

    function exportToPDF() {
        const element = document.getElementById('table-export-area');
        const header = element.querySelector('.print-header');
        const actionCols = document.querySelectorAll('.action-col');
        header.classList.remove('hidden'); actionCols.forEach(col => col.style.display = 'none');
        const opt = { margin: 0.3, filename: 'Laporan_Etalase.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2, useCORS: true }, jsPDF: { unit: 'in', format: 'legal', orientation: 'landscape' } };
        html2pdf().set(opt).from(element).save().then(() => { header.classList.add('hidden'); actionCols.forEach(col => col.style.display = ''); });
    }
  </script>
</body>
</html>