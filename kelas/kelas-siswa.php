<?php
    session_start();
    include '../database/database.php';

    $kelas = [];
    $user = [];

    $stmt = $connection->prepare("SELECT * FROM users WHERE nis = ?");
    $stmt->bind_param("s", $_SESSION['nis']);
    if ($stmt->execute()) {
        $user = $stmt->get_result();
        $stmt->close();
    }

    $stmt = $connection->prepare("SELECT mapel.nama AS mapel, kelas.nama AS kelas, kelas_mapel.id AS kelas_mapel_id FROM kelas_mapel
        JOIN kelas ON kelas_mapel.kelas_id = kelas.id
        JOIN mapel ON kelas_mapel.mapel_id = mapel.id
        JOIN users ON users.kelas_id = kelas.id
        WHERE users.nis = ?
        ");
    $stmt->bind_param("s", $_SESSION['nis']);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $kelas[] = $row;
            }
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
            Daftar Kelas <?php echo $_SESSION['nama']; ?>
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
                        <a href="kelas-materi-siswa.php?id=<?php echo $row['kelas_mapel_id']; ?>"><?php echo $row['mapel']; ?></a>
                    </td>
                    <td>
                        <a href="kelas-materi-siswa.php?id=<?php echo $row['kelas_mapel_id']; ?>"><?php echo $row['kelas']; ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>