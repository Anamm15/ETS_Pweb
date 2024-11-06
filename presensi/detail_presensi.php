<?php 
session_start();
include '../database/database.php';

$kelas_mapel_id = intval($_GET['id']);
$pertemuan = [];
$kelas_mapel = null;
$kode_absen_message = '';
$status = [
    'HADIR' => 0,
    'IZIN' => 0,
    'SAKIT' => 0,
    'ALPA' => 0
];

$stmt = $connection->prepare("SELECT * FROM users WHERE nis = ?");
$stmt->bind_param("s", $_SESSION['nis']);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$user_id = $user['id'];
$stmt->close();

$stmt = $connection->prepare("
    SELECT pertemuan.*, presensi.status AS status FROM pertemuan 
    JOIN presensi ON pertemuan.id = presensi.pertemuan_id
    WHERE kelas_mapel_id = ? AND presensi.user_id = ?");
$stmt->bind_param("is", $kelas_mapel_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $pertemuan[] = $row;
    }
}
$stmt->close();

$stmt = $connection->prepare("
    SELECT kelas.nama AS kelas, mapel.nama AS mapel FROM kelas_mapel
    JOIN kelas ON kelas_mapel.kelas_id = kelas.id
    JOIN mapel ON kelas_mapel.mapel_id = mapel.id
    WHERE kelas_mapel.id = ?");
$stmt->bind_param("i", $kelas_mapel_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $mapel = $row;
}
$stmt->close();

$stmt = $connection->prepare("
    SELECT p.status, COUNT(*) AS jumlah FROM presensi p
    JOIN pertemuan pt ON pt.id = p.pertemuan_id
    WHERE pt.kelas_mapel_id = ? AND p.user_id = ?
    GROUP BY p.status
    ORDER BY FIELD(p.status, 'HADIR', 'IZIN', 'SAKIT', 'ALPA');
");
$stmt->bind_param("ii", $kelas_mapel_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        if ($row['jumlah'] > 0) {
            $status[$row['status']] = $row['jumlah'];
        }
    }
}
$stmt->close();

if (isset($_POST['submit_kode'])) {
    $kode = $_POST['kode'];

    $stmt = $connection->prepare("SELECT * FROM pertemuan WHERE kelas_mapel_id = ? AND kode_presensi = ?");
    $stmt->bind_param("is", $kelas_mapel_id, $kode);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $pertemuan_id = $row['id'];

        $stmt = $connection->prepare("UPDATE presensi 
            SET status = 'HADIR' 
            WHERE pertemuan_id = ? 
              AND user_id = ? 
              AND EXISTS (
                  SELECT 1 FROM pertemuan 
                  WHERE id = ? AND batas_waktu <= CURTIME()
              )
        ");
        $stmt->bind_param("iii", $pertemuan_id, $user_id, $pertemuan_id);
        if ($stmt->execute()) {
            $stmt->close();
            
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $kelas_mapel_id);
            exit;
        } else {
            $stmt->close();
            $kode_absen_message = 'Kode absen melewati batas waktu!';
        }
    } else {
        $kode_absen_message = 'Kode absen salah!';
    }
    $stmt->close();
}


if (isset($_POST['submit_catatan'])) {
    $catatan = $_POST['catatan'];
    
    $stmt = $connection->prepare("SELECT * FROM pertemuan WHERE kelas_mapel_id = ?");
    $stmt->bind_param("i", $kelas_mapel_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $pertemuan_id = $row['id'];
        
        $stmt = $connection->prepare("UPDATE presensi SET catatan = ? WHERE pertemuan_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $catatan, $pertemuan_id, $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $kelas_mapel_id);
        exit;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/presensi.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
    <title>Detail Mata Pelajaran</title>
</head>
<body>
    <?php include '../components/navbar.php'; ?>
    <?php include '../components/modal.php'; ?>
    <?php
        renderModal("kode", "Masukkan kode absen Anda", '
            <form action="detail_presensi.php?id=' . $kelas_mapel_id . '" method="POST">
                <div class="input__group"> 
                    <label for="kode">Kode Absen</label>
                    <input type="text" name="kode" id="kode" required placeholder="Masukkan kode absen Anda">
                </div>
                <button type="submit" name="submit_kode" class="btn">Submit</button>
            </form>
        ')
    ?>

    <?php
        renderModal("catatan", "Masukkan catatan Anda", '
            <form action="detail_presensi.php?id=' . $kelas_mapel_id . '" method="POST">
                <div class="input__group"> 
                    <label for="catatan">Catatan</label>
                    <textarea type="text" name="catatan" id="catatan" required placeholder="Masukkan catatan Anda"> </textarea>
                </div>
                <button type="submit" name="submit_catatan" class="btn">Submit</button>
            </form>
        ')
    ?>

    <div class="presensi__container">
        <div class="presensi__content">
            <div class="presensi__detail">
                <h3><?php echo $mapel['mapel']; ?></h3>
                <h3><?php echo $_SESSION['nama'] . " - " . $mapel['kelas']; ?></h3>
                <div class="presensi__detail__content">
                    <div class="presensi__status">
                        <p style="color: green">Hadir</p>
                        <p><?php echo $status['HADIR']; ?></p>
                    </div>
                    <div class="presensi__status">
                        <p style="color: blue">Izin</p>
                        <p><?php echo $status['IZIN']; ?></p>
                    </div>
                    <div class="presensi__status">
                        <p style="color: orange">Sakit</p>
                        <p><?php echo $status['SAKIT']; ?></p>
                    </div>
                    <div class="presensi__status">
                        <p style="color: red">Alpa</p>
                        <p><?php echo $status['ALPA']; ?></p>
                    </div>
                </div>
                <p><?php echo $kode_absen_message; ?></p>
            </div>
            <div class="presensi__main">
                <div class="presensi__pertemuan">
                <table cellspacing="40" cellpadding="20">
                    <tr>
                        <th>Pertemuan</th>
                        <th>Tanggal</th>   
                        <th>Batas Waktu</th>         
                        <th>Status</th>            
                        <th>Keterangan</th>            
                    </tr>
                    <?php foreach ($pertemuan as $index => $pertemuan_item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $pertemuan_item['tanggal']; ?></td>
                            <td><?php echo $pertemuan_item['batas_waktu']; ?></td>
                            <td><?php echo $pertemuan_item['status']; ?></td>
                            <td>
                                <button class="button" onclick="openModal('kode')">Kode</button>
                                <button class="button" onclick="openModal('catatan')">Catatan</button>
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
