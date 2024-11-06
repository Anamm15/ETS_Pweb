<?php 
session_start();
include '../database/database.php';
$kelas = NULL;

if (isset($_SESSION['nis'])) {
    $stmt = $connection->prepare("SELECT * FROM users WHERE nis = ?");
    $stmt->bind_param("s", $_SESSION['nis']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $kelas = $row['kelas_id'];
    }
    $stmt->close();
}

$sql = "
    SELECT m.*, km.id AS kelas_mapel_id FROM mapel m
    JOIN kelas_mapel km ON km.mapel_id = m.id
    JOIN kelas k ON k.id = km.kelas_id
    JOIN users u ON u.kelas_id = k.id
    WHERE u.nis = ?;
";

$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $_SESSION['nis']);
$stmt->execute();
$res = $stmt->get_result();
$mapel = [];

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $mapel[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/kelas.css">
    <title>SMAN 1 BANGIL</title>
</head>
<body>
    <?php include '../components/navbar.php' ?>
    
    <?php if ($kelas == NULL): ?>
        <div class="message__info">Anda belum terdaftar di kelas apapun, silahkan hubungi guru</div>
    <?php endif; ?>

    <div class="kelas__container">
        <div class="kelas__title">
            Daftar Kelas
        </div>
        <table class="kelas__tabel">
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
            </tr>
            <?php foreach ($mapel as $index => $row): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <a href="detail_presensi.php?id=<?php echo $row['kelas_mapel_id']; ?>"><?php echo $row['nama']; ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
</body>
</html>