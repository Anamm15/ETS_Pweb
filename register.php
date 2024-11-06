<?php 
include 'database/database.php';

$message = '';
$sql = "SELECT * FROM kelas";
$res = $connection->query($sql);
$data = []; 

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
}

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $nis = $_POST['nis'];
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];
    $kelas = $_POST['kelas'];
    $password = $_POST['password'];
    $hashed_password = hash('sha256', $password);

    $stmt = $connection->prepare("INSERT INTO users (nis, nama, alamat, role, password, kelas_id) VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("issssi", $nis, $nama, $alamat, $role, $hashed_password, $kelas); 

    if ($stmt->execute()) {
        $message = 'Registrasi Berhasil!';
    } else {
        $message = 'Registrasi Gagal: ' . $stmt->error;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMAN 1 BANGIL</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <div class="login__container">
            <h2>Silahkan Login Dahulu</h2>
            <form action="register.php" method="post">
                <div class="input__group">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                <div class="input__group">
                    <label for="nis">NIS</label>
                    <input type="text" id="nis" name="nis" required>
                </div>
                <div class="input__group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" required>
                </div>
                <div class="input__group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="guru">Guru</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
                <div class="input__group" id="kelasInput" style="display: none;">
                    <label for="kelas">Kelas</label>
                    <select id="kelas" name="kelas" required> 
                        <?php foreach ($data as $row): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input__group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="register" name="register">Register</button>
                <?php if ($message): ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script src="./assets/js/script.js"></script>
</body>
</html>
