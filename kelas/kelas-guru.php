<?php
    session_start();
    include '../database/database.php';

    $kelas = [];
    $sql = "SELECT mapel.nama AS mapel, kelas.nama AS kelas, kelas_mapel.id AS kelas_mapel_id FROM kelas_mapel
        JOIN kelas ON kelas_mapel.kelas_id = kelas.id
        JOIN mapel ON kelas_mapel.mapel_id = mapel.id";
    $res = $connection->query($sql);

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $kelas[] = $row;
        }
    }
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
    <?php include '../components/navbar.php'; ?>

    <div class="kelas__container">
        <div class="kelas__title">
            Daftar Kelas SMA Negeri 1 Bangil
        </div>
        <table class="kelas__tabel" cellpadding="20" cellspacing="50">
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
            </tr>
            <?php foreach ($kelas as $index => $row): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <a href="kelas-materi-guru.php?id=<?php echo $row['kelas_mapel_id']; ?>"><?php echo $row['mapel']; ?></a>
                    </td>
                    <td>
                        <a href="kelas-materi-guru.php?id=<?php echo $row['kelas_mapel_id']; ?>"><?php echo $row['kelas']; ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>