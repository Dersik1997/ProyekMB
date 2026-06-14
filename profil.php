<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

// Ambil data profil petani (Asumsi ID 1 untuk prototype ini)
$query_profil = mysqli_query($conn, "SELECT * FROM mitra_petani LIMIT 1");
$profil = mysqli_fetch_assoc($query_profil);

// Jika tombol simpan ditekan
if(isset($_POST['simpan_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_petani']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $wa = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    
    // Proses Upload Foto Profil
    $nama_foto = $profil['foto'] ?? ''; 
    if(isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != "") {
        $tmp = $_FILES['foto']['tmp_name'];
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_foto = "profil_" . time() . "." . $ext;
        move_uploaded_file($tmp, "uploads/" . $nama_foto);
    }

    if(mysqli_num_rows($query_profil) > 0) {
        // Update jika profil sudah ada
        $id_profil = $profil['id'];
        mysqli_query($conn, "UPDATE mitra_petani SET nama_petani='$nama', lokasi='$lokasi', no_telepon='$wa', bio='$bio', foto='$nama_foto' WHERE id=$id_profil");
    } else {
        // Insert jika profil belum ada
        mysqli_query($conn, "INSERT INTO mitra_petani (nama_petani, lokasi, no_telepon, bio, foto, status) VALUES ('$nama', '$lokasi', '$wa', '$bio', '$nama_foto', 'Aktif')");
    }
    
    $_SESSION['pesan'] = "Profil berhasil diperbarui!";
    header("Location: profil.php");
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil Saya | Aplikasi Petani</title>
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
      <a href="dashboard.php" class="flex items-center gap-3 text-emerald-200 hover:text-white hover:bg-emerald-800 px-4 py-3 rounded-xl font-medium transition"><i class="fa-solid fa-store"></i> Etalase Panen</a>
      <a href="pesanan.php" class="flex items-center justify-between text-emerald-200 hover:text-white hover:bg-emerald-800 px-4 py-3 rounded-xl font-medium transition group">
          <div class="flex items-center gap-3"><i class="fa-solid fa-bell"></i> Pesanan Masuk</div>
      </a>
      <a href="saldo.php" class="flex items-center gap-3 text-emerald-200 hover:text-white hover:bg-emerald-800 px-4 py-3 rounded-xl font-medium transition"><i class="fa-solid fa-wallet"></i> Saldo & Pendapatan</a>
      <a href="profil.php" class="flex items-center gap-3 bg-emerald-700 text-white px-4 py-3 rounded-xl font-medium transition shadow-md"><i class="fa-solid fa-user-check"></i> Profil Saya</a>
    </nav>

    <div class="mt-auto">
      <a href="logout.php" class="flex items-center gap-3 text-red-300 hover:text-white hover:bg-red-500 px-4 py-3 rounded-xl font-medium transition mb-4">
        <i class="fa-solid fa-power-off"></i> Keluar
      </a>
    </div>
  </aside>

  <main class="flex-1 md:ml-64 min-h-screen p-6 md:p-10 w-full relative overflow-x-hidden">
    <div class="max-w-4xl mx-auto w-full slide-up">
        
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Profil Petani</h1>
          <p class="text-sm text-gray-500">Informasi ini akan ditampilkan ke publik agar pembeli semakin percaya.</p>
        </div>
      </header>

      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
          <form method="POST" action="" enctype="multipart/form-data">
              
              <div class="flex flex-col md:flex-row items-center gap-8 mb-8 border-b border-gray-100 pb-8">
                  <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-emerald-100 shadow-md flex-shrink-0 group bg-gray-50">
                      <?php if(!empty($profil['foto']) && file_exists("uploads/".$profil['foto'])): ?>
                          <img id="previewFoto" src="uploads/<?= $profil['foto'] ?>" class="w-full h-full object-cover">
                      <?php else: ?>
                          <img id="previewFoto" src="https://ui-avatars.com/api/?name=<?= urlencode($profil['nama_petani'] ?? 'Petani') ?>&background=10b981&color=fff" class="w-full h-full object-cover">
                      <?php endif; ?>
                      
                      <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center cursor-pointer transition-all" onclick="document.getElementById('inputFoto').click()">
                          <i class="fa-solid fa-camera text-white text-2xl"></i>
                      </div>
                      <input type="file" id="inputFoto" name="foto" accept="image/*" class="hidden" onchange="loadPreview(this)">
                  </div>
                  <div>
                      <h3 class="text-xl font-bold text-gray-900 mb-1">Foto Profil Anda</h3>
                      <p class="text-sm text-gray-500 mb-3">Gunakan foto asli Anda atau foto kebun agar pembeli lebih yakin.</p>
                      <button type="button" onclick="document.getElementById('inputFoto').click()" class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl text-sm font-bold border border-emerald-200 hover:bg-emerald-600 hover:text-white transition">Pilih Foto</button>
                  </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                      <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap / Nama Kebun</label>
                      <input type="text" name="nama_petani" value="<?= htmlspecialchars($profil['nama_petani'] ?? '') ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition">
                  </div>
                  <div>
                      <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Utama Kebun</label>
                      <input type="text" name="lokasi" value="<?= htmlspecialchars($profil['lokasi'] ?? '') ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition">
                  </div>
                  <div class="md:col-span-2">
                      <label class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp Aktif</label>
                      <input type="number" name="no_telepon" value="<?= htmlspecialchars($profil['no_telepon'] ?? '') ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition">
                  </div>
                  <div class="md:col-span-2">
                      <label class="block text-sm font-bold text-gray-700 mb-2">Bio / Cerita Singkat (Ditampilkan ke Pembeli)</label>
                      <textarea name="bio" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition" placeholder="Contoh: Kami adalah kelompok tani yang fokus menanam sayuran organik tanpa pestisida kimia..."><?= htmlspecialchars($profil['bio'] ?? '') ?></textarea>
                  </div>
              </div>

              <div class="mt-8 flex justify-end">
                  <button type="submit" name="simpan_profil" class="bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3.5 rounded-xl font-bold text-lg shadow-lg shadow-emerald-600/30 transition-transform transform hover:-translate-y-1">
                      Simpan Profil <i class="fa-solid fa-floppy-disk ml-2"></i>
                  </button>
              </div>
          </form>
      </div>
    </div>
  </main>

  <script>
    <?php if(isset($_SESSION['pesan'])): ?>
        Swal.fire({ title: 'Berhasil!', text: '<?= $_SESSION['pesan'] ?>', icon: 'success', confirmButtonColor: '#059669', timer: 3000 });
        <?php unset($_SESSION['pesan']); ?>
    <?php endif; ?>

    // Script ganti foto preview
    function loadPreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
  </script>
</body>
</html>