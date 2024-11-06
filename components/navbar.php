<?php 
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();

        setcookie('user_session', '', time() - 3600, "/", "", true, true);
        
        header("Location: ../index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Sistem Sekolah</title>
    <link rel="stylesheet" href="../assets/css/navbar.css">
</head>
<body>
    <nav class="navbar">
        <div class="title">
            <p>SMAN 1 BANGIL</p>
        </div>
        <ul class="navbar-links">
            <li><a href="../beranda/beranda.php">Beranda</a></li>
            <?php if ($_SESSION['role'] == 'siswa'): ?>
                <li><a href="../presensi/presensi-siswa.php">Presensi</a></li>
                <li><a href="../kelas/kelas-siswa.php">Kelas</a></li>
            <?php else: ?>
                <li><a href="../presensi/presensi-guru.php">Presensi</a></li>
                <li><a href="../kelas/kelas-guru.php">Kelas</a></li>
            <?php endif; ?>
            <?php if ($_SESSION['role'] == 'guru'): ?>
                <li><a href="../register.php">Registrasi</a></li>
            <?php endif; ?>
        </ul>
        <form action="../components/navbar.php" method="post">
            <button name="logout" class="logout__button">Logout</button>
        </form>
    </nav>

</body>
</html>
