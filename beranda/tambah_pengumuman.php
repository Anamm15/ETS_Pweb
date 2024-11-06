<?php 
session_start();
include '../database/database.php';

$message = '';
if (isset($_POST['pengumuman'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tanggal = $_POST['tanggal'];
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $imageName = $_FILES['gambar']['name'];
        $imageTmpPath = $_FILES['gambar']['tmp_name'];
        
        $targetDir = "../assets/storages/";
        $targetFilePath = $targetDir . basename($imageName);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($imageTmpPath, $targetFilePath)) {
            $stmt = $connection->prepare("INSERT INTO pengumuman (judul, isi, tanggal, gambar) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $judul, $isi, $tanggal, $targetFilePath);
            
            if ($stmt->execute()) {
                $message = 'Pengumuman berhasil ditambahkan!';
            } else {
                $message = 'Gagal menambahkan pengumuman: ' . $connection->error;
            }
            $stmt->close();
        } else {
            $message = 'Gagal mengunggah gambar.';
        }
    } else {
        $message = 'Gambar tidak diupload atau terdapat kesalahan.';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/beranda.css">
    <title>SMAN 1 BANGIL</title>
</head>
<body>
    <?php include '../components/navbar.php'; ?>

    <div class="wrapper">
        <div class="login__container">
            <h2>Silahkan Masukkan Rincian Pengumuman</h2>
            <form action="tambah_pengumuman.php" method="post" enctype="multipart/form-data">
                <div class="input__group">
                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" placeholder="Masukkan Judul pengumuman" required>
                </div>
                <div class="input__group">
                    <label for="isi">Deskripsi/Isi</label>
                    <textarea id="isi" name="isi" placeholder="Masukkan isi pengumuman" required></textarea>
                </div>
                <div class="input__group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required>
                </div>
                <div class="input__group">
                    <label for="gambar">Gambar</label>
                    <input type="file" id="gambar" name="gambar" accept="image/*" required>
                </div>
                <button type="submit" class="pengumuman" name="pengumuman">Tambah</button>
                <?php if ($message): ?>
                    <p class="pengumuman__message"><?php echo $message; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
