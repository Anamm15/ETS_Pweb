<?php 
include 'database/model.php';
session_start();

$message = '';
$messageSeeder = '';

if ($connection->multi_query($model)) {
    do {
        if ($result = $connection->store_result()) {
            $result->free();
        }
    } while ($connection->more_results() && $connection->next_result());
} else {
    die("Database error: " . $connection->error);
}

if (isset($_COOKIE['user_session'])) {
    $stmt = $connection->prepare("SELECT * FROM users WHERE cookie = ?");
    $stmt->bind_param("s", $_COOKIE['user_session']);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $_SESSION['nis'] = $user['nis'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        header("Location: ./beranda/beranda.php");
        exit();
    }
    $stmt->close();
}

if (isset($_POST['login'])) {
    $nis = $_POST['nis'];
    $password = $_POST['password'];
    $hashed_password = hash('sha256', $password);

    $stmt = $connection->prepare("SELECT * FROM users WHERE nis = ? AND password = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $nis, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['nis'] = $row['nis'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['role'];

            $cookie_value = hash('sha256', $row['nis']);
            setcookie('user_session', $cookie_value, time() + 3600 * 24, "/");

            $stmt = $connection->prepare("UPDATE users SET cookie = ? WHERE nis = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $cookie_value, $nis);
                $res = $stmt->execute();

                if ($res) {
                    header("Location: ./beranda/beranda.php");
                    exit();
                } else {
                    $message = 'Server Error';
                }
                $stmt->close();
            } else {
                echo "Error preparing update statement: " . $connection->error;
            }
        } else {
            $message = 'Username atau password salah!';
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $connection->error;
    }
}

if (isset($_POST['migrate'])) {
    include './database/databaseSeeder.php';
    if ($connection->multi_query(query: $query_seeder)) {
        do {
            if ($result = $connection->store_result()) {
                $result->free();
            }
        } while ($connection->more_results() && $connection->next_result());
        $messageSeeder = "Database seeder berhasil dijalankan!";
    } else {
        $messageSeeder = "Database seeder gagal dijalankan: " . $connection->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="style.css?v=1.0">
    <title>SMAN 1 BANGIL</title>
</head>
<body>
    <div class="wrapper">
        <div class="login__container">
            <h2>Silahkan Login Dahulu</h2>
            <form action="index.php" method="post">
                <div class="input__group">
                    <label for="nis">NIS</label>
                    <input type="text" id="nis" name="nis" placeholder="Masukkan NIS Anda" required>
                </div>
                <div class="input__group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                </div>
                <button type="submit" class="login" name="login">Login</button>
                <?php if ($message): ?>
                    <p class="login__message"><?php echo $message; ?></p>
                    <?php endif; ?>
            </form>
            <form action="index.php" method="post">
                <button type="submit" class="migrate" name="migrate">database seeder! klik jika memulai dari awal</button>
            </form>
            <p><?php echo $messageSeeder; ?></p>
        </div>
    </div>
</body>
</html>
