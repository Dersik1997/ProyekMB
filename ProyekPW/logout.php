<?php
// 1. Panggil session
session_start();

// 2. Hancurkan semua data session (buang tiket login)
session_unset();
session_destroy();

// 3. Tendang kembali ke halaman login
header("Location: index.php");
exit;
?>