<?php
session_start();
include '../database/database.php';

if (!isset($_SESSION['nama'])) {
    header("Location: login.php");
    exit;
}

$kelas_mapel_id = intval($_GET['id']);

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

    <div class="kelas__materi__container">
        <div class="kelas__materi__header">
            <h3><?php echo htmlspecialchars($kelas_mapel['mapel']); ?></h3>
            <h3><?php echo htmlspecialchars($_SESSION['nama']) . " - " . htmlspecialchars($kelas_mapel['kelas']); ?></h3>
            <p>Jumlah Siswa: <?php echo count($users); ?></p>
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
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
