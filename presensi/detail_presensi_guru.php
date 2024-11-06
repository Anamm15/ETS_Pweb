<?php
session_start();
include '../database/database.php';
include '../components/modal.php';
include '../functions/generateKodeAbsen.php';

$kelas_mapel_id = intval($_GET['id']);
$pertemuan = [];
$users = [];
$kelas_mapel = null;
$kode_presensi = null;

$sql = "SELECT * FROM pertemuan WHERE kelas_mapel_id = $kelas_mapel_id";
$res = $connection->query($sql);
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $pertemuan[] = $row;
    }
}

$sql = "SELECT kelas.id AS kelas_id, kelas.nama AS kelas, mapel.nama AS mapel FROM kelas_mapel
    JOIN kelas ON kelas_mapel.kelas_id = kelas.id
    JOIN mapel ON kelas_mapel.mapel_id = mapel.id
    WHERE kelas_mapel.id = $kelas_mapel_id";
$res = $connection->query($sql);

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $kelas_mapel = $row;
}

$sql = "SELECT * FROM users WHERE kelas_id = " . $kelas_mapel['kelas_id'];
$res = $connection->query($sql);
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
}

if (isset($_POST['pertemuan'])) {
    $kode_presensi = generateKodeAbsen();
    $sql = "SELECT * FROM pertemuan WHERE kelas_mapel_id = $kelas_mapel_id AND kode_presensi = '$kode_presensi'";
    $res = $connection->query($sql);

    while ($res->num_rows > 0) {
        $kode_presensi = generateKodeAbsen();
        $res = $connection->query($sql);
    }


    $batas_waktu = date('H:i:s', strtotime('+2 hours'));
    $tanggal = date('Y-m-d');
    $sql = "INSERT INTO pertemuan (kelas_mapel_id, kode_presensi, batas_waktu, tanggal) VALUES ($kelas_mapel_id, '$kode_presensi', '$batas_waktu', '$tanggal')";
    $res = $connection->query($sql);
    $pertemuan_id = null;

    if ($res) {
        $pertemuan_id = $connection->insert_id;
    }

    foreach ($users as $user) {
        $sql = "INSERT INTO presensi (pertemuan_id, user_id) VALUES ($pertemuan_id, " . $user['id'] . ")";
        $res = $connection->query($sql);
    }

    unset($_POST['pertemuan']);
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $kelas_mapel_id);
    exit;
}

$stmt = $connection->prepare("SELECT 
    pt.id AS pertemuan_id,
    SUM(CASE WHEN p.status = 'hadir' THEN 1 ELSE 0 END) AS hadir,
    SUM(CASE WHEN p.status = 'izin' THEN 1 ELSE 0 END) AS izin,
    SUM(CASE WHEN p.status = 'sakit' THEN 1 ELSE 0 END) AS sakit,
    SUM(CASE WHEN p.status = 'alpa' THEN 1 ELSE 0 END) AS alpa
FROM presensi p
JOIN pertemuan pt ON pt.id = p.pertemuan_id
WHERE pt.kelas_mapel_id = ?
GROUP BY pt.id
ORDER BY pt.id");

$stmt->bind_param("i", $kelas_mapel_id);
$stmt->execute();
$stmt->bind_result($pertemuan_id, $hadir, $izin, $sakit, $alpa);

$status = [];
while ($stmt->fetch()) {
    $status[$pertemuan_id] = [
        'hadir' => $hadir,
        'izin' => $izin,
        'sakit' => $sakit,
        'alpa' => $alpa
    ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/presensi.css">
    <title>SMAN 1 BANGIL</title>
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <?php
        renderModal("modal1", "Tambahkan Pertemuan", '
            <div class="input__group">
                <label for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" required>
            </div>
        ');
    ?>
    <div class="presensi__container">
        <div class="presensi__content">
            <div class="presensi__detail">
                <h3><?php echo $kelas_mapel['mapel']; ?></h3>
                <h3><?php echo $_SESSION['nama'] . " - " . $kelas_mapel['kelas']; ?></h3>
                <p>Jumlah Siswa: <?php echo count($users); ?></p>
                <form action="detail_presensi_guru.php?id=<?php echo $kelas_mapel_id ?>" method="post">
                    <button type="submit" name="pertemuan" class="open-modal-btn" onclick="openModal('modal1')">Tambah Pertemuan</button>
                </form>
            </div>
            <div class="presensi__main">
                <div class="presensi__pertemuan">
                <table cellspacing="70" cellpadding="0">
                    <tr>
                        <th>Pertemuan</th>
                        <th>Tanggal</th>   
                        <th>Batas Waktu</th>
                        <th>Kode Absen</th>     
                        <th>Keterangan</th>                      
                    </tr>
                    <?php foreach ($pertemuan as $index => $pertemuan_item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $pertemuan_item['tanggal']; ?></td>
                            <td><?php echo $pertemuan_item['batas_waktu']; ?></td>
                            <td><?php echo $pertemuan_item['kode_presensi']; ?></td>
                            <td class="presensi__keterangan">
                                <div style="background-color: green;"><?php echo isset($status[$pertemuan_item['id']]) ? $status[$pertemuan_item['id']]['hadir'] : 0; ?></div>
                                <div style="background-color: blue;"><?php echo isset($status[$pertemuan_item['id']]) ? $status[$pertemuan_item['id']]['izin'] : 0; ?></div>
                                <div style="background-color: orange;"><?php echo isset($status[$pertemuan_item['id']]) ? $status[$pertemuan_item['id']]['sakit'] : 0; ?></div>
                                <div style="background-color: red;"><?php echo isset($status[$pertemuan_item['id']]) ? $status[$pertemuan_item['id']]['alpa'] : 0; ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
