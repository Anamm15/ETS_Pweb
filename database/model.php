<?php 
    include 'database.php';

    $model = "
    CREATE TABLE IF NOT EXISTS kelas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(50) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        nis VARCHAR(50) UNIQUE NOT NULL,
        alamat VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('guru', 'siswa') NOT NULL,
        cookie VARCHAR(255) DEFAULT NULL,
        kelas_id INT DEFAULT NULL,
        FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE IF NOT EXISTS mapel (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS kelas_mapel (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kelas_id INT NOT NULL,
        mapel_id INT NOT NULL,
        FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE IF NOT EXISTS pengumuman (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        isi TEXT NOT NULL,
        tanggal DATE NOT NULL,
        gambar varchar(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS pertemuan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kelas_mapel_id INT NOT NULL,
        tanggal DATE NOT NULL,
        batas_waktu TIME NOT NULL,
        kode_presensi VARCHAR(10) NOT NULL,
        FOREIGN KEY (kelas_mapel_id) REFERENCES kelas_mapel(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE IF NOT EXISTS presensi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pertemuan_id INT NOT NULL,
        user_id INT NOT NULL,
        status ENUM('HADIR', 'IZIN', 'SAKIT', 'ALPA') NOT NULL DEFAULT 'ALPA',
        waktu_presensi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        catatan VARCHAR(255) DEFAULT NULL,
        FOREIGN KEY (pertemuan_id) REFERENCES pertemuan(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE IF NOT EXISTS materi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        kelas_mapel_id INT NOT NULL,
        nama_materi VARCHAR(255) NOT NULL,
        keterangan VARCHAR(255) DEFAULT NULL,
        FOREIGN KEY (kelas_mapel_id) REFERENCES kelas_mapel(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE IF NOT EXISTS kelas_materi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        materi VARCHAR(255),
        materi_id INT NOT NULL,
        FOREIGN KEY (materi_id) REFERENCES materi(id) ON DELETE CASCADE ON UPDATE CASCADE
    )
    ";
?>
