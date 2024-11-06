<?php
    session_start();
    include '../database/database.php';
    $pengumuman_id = intval($_GET['id']);

    $sql = "SELECT * FROM pengumuman WHERE id = $pengumuman_id";
    $res = $connection->query($sql);
    $pengumuman = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/beranda.css">
    <title>SMAN 1 BANGIL</title>
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <div class="berita__container">
        <h1 class="judul"><?php echo htmlspecialchars($pengumuman['judul']); ?></h1>
        <div class="gambar">
            <img src="<?php echo $pengumuman['gambar']; ?>" alt="Pengumuman" class="detail__image">
        </div>
        <p class="isi"><?php echo nl2br(htmlspecialchars($pengumuman['isi'])); ?></p>
        <p class="tanggal">Tanggal: <?php echo htmlspecialchars($pengumuman['tanggal']); ?></p>
        <a href="beranda.php" class="kembali">Kembali</a>
    </div>
</body>
</html>