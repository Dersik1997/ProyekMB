<?php
// Wajib ditaruh paling atas
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    // Mengambil data dari input form (sekarang pakai email sesuai desainmu)
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Gunakan Prepared Statement untuk mencegah SQL Injection
    // Asumsi: kolom di database bernama 'email'. Jika namanya 'username', ubah query di bawah.
    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $data['password'])) {
            // Jika sukses, set session
            $_SESSION['status'] = "login";
            $_SESSION['email'] = $email; 
            
            // Pindahkan ke dashboard utama
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Email atau kata sandi salah!";
        }
    } else {
        $error = "Email atau kata sandi salah!";
    }
    
    mysqli_stmt_close($stmt);
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Masuk - Si Tangkulak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] },
          },
        },
      };
    </script>
  </head>
  <body class="bg-gray-50 font-sans antialiased min-h-screen flex">
    <div class="flex flex-1 w-full">
      <div
        class="hidden lg:flex flex-1 bg-emerald-950 text-white p-12 flex-col justify-between relative overflow-hidden"
      >
        <div
          class="absolute -top-20 -left-20 w-80 h-80 bg-emerald-800 rounded-full blur-3xl opacity-40"
        ></div>
        <div
          class="absolute bottom-0 right-0 w-96 h-96 bg-teal-700 rounded-full blur-3xl opacity-30"
        ></div>

        <div class="flex items-center gap-2 z-10">
          <div
            class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-xl shadow-lg"
          >
            <i class="fa-solid fa-leaf text-white"></i>
          </div>
          <span class="font-bold text-2xl tracking-tight"
            >Si <span class="text-emerald-400">Tangkulak</span></span
          >
        </div>

        <div class="z-10 max-w-md my-auto">
          <h1 class="text-4xl font-bold leading-tight mb-4">
            Membantu Petani Sejahtera, Menjaga Pasokan Konsumen.
          </h1>
          <p class="text-emerald-200/80 leading-relaxed">
            Masuk ke sistem pengelolaan terpadu untuk memantau harga pasar,
            mengelola hasil panen, dan menandatangani kontrak digital.
          </p>
        </div>

        <p class="text-sm text-emerald-300/60 z-10">
          &copy; 2026 Si Tangkulak Ekosistem.
        </p>
      </div>

      <div
        class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24 bg-white"
      >
        <div class="mx-auto w-full max-w-sm">
          <div class="mb-8">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900">
              Selamat Datang
            </h2>
            <p class="mt-2 text-sm text-gray-500">
              Silakan masuk menggunakan akun mitra Anda.
            </p>
          </div>

          <?php if(isset($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm flex items-center gap-2" role="alert">
              <i class="fa-solid fa-circle-exclamation"></i>
              <span><?= $error ?></span>
            </div>
          <?php endif; ?>

          <form class="space-y-6" action="" method="POST">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700"
                >Alamat Email</label
              >
              <div class="mt-1 relative rounded-md shadow-sm">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"
                >
                  <i class="fa-regular fa-envelope"></i>
                </div>
                <input
                  id="email"
                  name="email"
                  type="email"
                  required
                  class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-sm"
                  placeholder="nama@petani.com"
                />
              </div>
            </div>

            <div>
              <div class="flex justify-between items-center">
                <label
                  for="password"
                  class="block text-sm font-medium text-gray-700"
                  >Kata Sandi</label
                >
                <a
                  href="#"
                  class="text-xs font-semibold text-emerald-600 hover:text-emerald-500"
                  >Lupa sandi?</a
                >
              </div>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"
                >
                  <i class="fa-solid fa-lock"></i>
                </div>
                <input
                  id="password"
                  name="password"
                  type="password"
                  required
                  class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-sm"
                  placeholder="••••••••"
                />
                <div
                  class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600"
                >
                  <i class="fa-regular fa-eye"></i>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input
                  id="remember-me"
                  name="remember-me"
                  type="checkbox"
                  class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded-md"
                />
                <label
                  for="remember-me"
                  class="ml-2 block text-sm text-gray-700"
                  >Ingat saya</label
                >
              </div>
            </div>

            <div>
              <button
                type="submit"
                name="login"
                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-emerald-600/20 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all transform hover:-translate-y-0.5"
              >
                Masuk ke Dashboard
              </button>
            </div>
          </form>

          <p class="mt-8 text-center text-sm text-gray-600">
            Belum punya akun mitra?
            <a
              href="#"
              class="font-semibold text-emerald-600 hover:text-emerald-500"
              >Daftar sekarang</a
            >
          </p>
        </div>
      </div>
    </div>
  </body>
</html>