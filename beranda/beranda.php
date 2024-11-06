<?php 
session_start();
include '../database/database.php';

$pengumuman = [];
$sql = "SELECT * FROM pengumuman";
$res = $connection->query($sql);
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $row['judul'] = substr($row['judul'], 0, 50) . '...';
        $row['isi'] = substr($row['isi'], 0, 200) . '...';
        $pengumuman[] = $row;
    }
}
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
    
    <div class="beranda__container">
        <h1 class="title__beranda">Pengumuman Sekolah</h1>
        
        <div class="content__beranda__container">
            <?php foreach ($pengumuman as $item): ?>
                <div class="content__beranda">
                    <a href="berita.php?id=<?php echo $item['id']; ?>">
                        <img src="<?php echo $item['gambar']; ?>" alt="Pengumuman" class="news__image">
                        <h3 class="news__item__title"><?php echo htmlspecialchars($item['judul']); ?></h3>
                        <p class="news__item__content"><?php echo htmlspecialchars($item['isi']); ?></p>
                        <i class="news__item__date"><?php echo htmlspecialchars($item['tanggal']); ?></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if ($_SESSION['role'] == 'guru'): ?>
        <button class="tambah__pengumuman">
            <a href="tambah_pengumuman.php">Tambah Pengumuman</a>
        </button>
    <?php endif; ?>
    <script src="../assets/js/beranda.js"></script>
</body>
</html>
