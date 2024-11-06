<?php
session_start();
include '../database/database.php';

if (!isset($_SESSION['nama'])) {
    header("Location: login.php");
    exit;
}

$kelas_mapel_id = intval($_GET['id']);

if (isset($_POST['section'])) {
    $materi = $_POST['materi'];
    $keterangan = $_POST['keterangan'];
    
    $stmt = $connection->prepare("INSERT INTO materi (kelas_mapel_id, nama_materi, keterangan) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $kelas_mapel_id, $materi, $keterangan);
    $stmt->execute();
    $stmt->close();
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $kelas_mapel_id);
    exit;
}

$kelas_mapel = null;
$users = [];
$materi = [];

$sql = "SELECT kelas.id AS kelas_id, kelas.nama AS kelas, mapel.nama AS mapel FROM kelas_mapel
        JOIN kelas ON kelas_mapel.kelas_id = kelas.id
        JOIN mapel ON kelas_mapel.mapel_id = mapel.id
        WHERE kelas_mapel.id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $kelas_mapel_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $kelas_mapel = $res->fetch_assoc();
}
$stmt->close();

if ($kelas_mapel) {
    $sql = "SELECT * FROM users WHERE kelas_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $kelas_mapel['kelas_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
}

$sql = "SELECT * FROM materi WHERE kelas_mapel_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $kelas_mapel_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $materi[] = $row;
}
$stmt->close();

if (isset($_POST['materi']) && isset($_FILES['file'])) {
    $kelas_mapel_id = intval($_GET['id']);
    $section_id = intval($_GET['section_id']);

    $targetDir = "../assets/storages/";
    $fileName = basename($_FILES["file"]["name"]);
    $fileTmpPath = $_FILES["file"]["tmp_name"];
    $targetFilePath = $targetDir . $fileName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
        $stmt = $connection->prepare("INSERT INTO kelas_materi (materi_id, materi) VALUES (?, ?)");
        $stmt->bind_param("is", $section_id, $fileName);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $kelas_mapel_id);
            exit;
        }
        $stmt->close();
    }
}

if (isset($_POST['delete'])) {
    $material_id = $_GET['material_id'];
    $materi = $_GET['materi'];
    $file_path = "../assets/storages/" . $materi;

    $stmt = $connection->prepare("DELETE FROM kelas_materi WHERE id = ?");
    $stmt->bind_param("i", $material_id);
    if ($stmt->execute()) {
        $stmt->close();
        unlink($file_path);
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $kelas_mapel_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/kelas.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
    <title>SMAN 1 BANGIL</title>
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <?php include '../components/modal.php'; ?>
    <?php
    renderModal("modal_section", "Tambahkan Pertemuan", '
        <form method="post" action="kelas-materi-guru.php?id=' . htmlspecialchars($kelas_mapel_id) . '" >
            <div class="input__group">
                <label for="materi">Judul Materi</label>
                <input type="text" id="materi" name="materi" required placeholder="Masukkan judul materi">
            </div>
            <div class="input__group">
                <label for="keterangan">Deskripsi</label>
                <textarea id="keterangan" name="keterangan" required placeholder="Masukkan deskripsi materi"></textarea>
            </div>
            <button type="submit" class="section" name="section">Tambah</button>
        </form>
    ');
    ?>

    <div class="kelas__materi__container">
        <div class="kelas__materi__header">
            <h3><?php echo htmlspecialchars($kelas_mapel['mapel']); ?></h3>
            <h3><?php echo htmlspecialchars($_SESSION['nama']) . " - " . htmlspecialchars($kelas_mapel['kelas']); ?></h3>
            <p>Jumlah Siswa: <?php echo count($users); ?></p>
            <button type="button" class="open-modal-btn" onclick="openModal('modal_section')">Tambah Section</button>
        </div>

        <?php foreach ($materi as $index => $row): ?>
            <div class="kelas__materi__body">
                <?php
                    $stmt = $connection->prepare("SELECT * FROM kelas_materi WHERE materi_id = ?");
                    $stmt->bind_param("i", $row['id']);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $material = [];

                    while ($data = $res->fetch_assoc()) {
                        $material[] = $data;
                    }
                    $stmt->close();
                ?>
                <h3>Section <?php echo $index + 1 . " - " . htmlspecialchars($row['nama_materi']); ?></h3>
                <p><?php echo htmlspecialchars($row['keterangan']); ?></p>
                <?php foreach ($material as $index => $material_row): ?>
                    <div class="material">
                        <a href="../assets/storages/<?php echo $material_row['materi']; ?>" download><?php echo $material_row['materi']; ?></a>
                        <form action="kelas-materi-guru.php?id=<?php echo $kelas_mapel_id ?>&material_id=<?php echo $material_row['id'] ?>&materi=<?php echo $material_row['materi'] ?>" method="post">
                            <button id="button__delete" type="submit" name="delete">&times;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <form action="kelas-materi-guru.php?id=<?php echo $kelas_mapel_id ?>&section_id=<?php echo $row['id'] ?>" method="post" enctype="multipart/form-data">
                    <div class="input__group">
                        <label for="file">Masukkan materi baru: </label>
                        <input type="file" id="file" name="file" required>
                    </div>
                    <button type="submit" name="materi" class="open-modal-btn">Tambah Materi</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
