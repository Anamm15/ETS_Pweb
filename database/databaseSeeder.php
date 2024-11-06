<?php
    $password_siswa = hash('sha256', data: 'siswa123');
    $password_guru = hash('sha256', data: 'anam123');
    $query_seeder = "
        INSERT INTO mapel (nama) VALUES
        ('Matematika'),
        ('Bahasa Indonesia'),
        ('Bahasa Inggris'),
        ('Fisika'),
        ('Kimia'),
        ('Biologi'),
        ('Sejarah'),
        ('Geografi'),
        ('Ekonomi'),
        ('Sosiologi'),
        ('Agama'),
        ('Seni Budaya'),
        ('Pendidikan Jasmani'),
        ('Komputer');

        INSERT INTO kelas (nama) VALUES
        ('Ilmu Pengetahuan Alam 1'),
        ('Ilmu Pengetahuan Alam 2'),
        ('Ilmu Pengetahuan Alam 3'),
        ('Ilmu Pengetahuan Alam 4'),
        ('Ilmu Pengetahuan Alam 5'),
        ('Ilmu Pengetahuan Sosial 1'),
        ('Ilmu Pengetahuan Sosial 2'),
        ('Ilmu Pengetahuan Sosial 3'),
        ('Ilmu Pengetahuan Sosial 4'),
        ('Ilmu Pengetahuan Sosial 5');

        INSERT INTO kelas_mapel (kelas_id, mapel_id) VALUES
        (1, 1),
        (1, 2),
        (1, 3),
        (1, 4),
        (1, 5),
        (2, 1),
        (2, 2),
        (2, 3),
        (2, 4),
        (3, 1),
        (3, 3),
        (3, 5),
        (4, 1),
        (4, 2),
        (4, 4),
        (5, 1),
        (5, 2),
        (5, 6),
        (6, 1),
        (6, 2),
        (6, 3),
        (6, 8),
        (6, 9),
        (7, 1),
        (7, 8),
        (7, 9),
        (8, 1),
        (8, 9),
        (9, 1),
        (10, 10);


        INSERT INTO users (nama, nis, alamat, password, role, kelas_id) VALUES 
        ('Siswa 1', '11601', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Siswa 2', '11602', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Siswa 3', '11603', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Siswa 4', '11604', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Siswa 5', '11605', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Siswa 6', '11606', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Siswa 7', '11607', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Siswa 8', '11608', 'Pasuruan', '$password_siswa', 'siswa', 1),
        ('Choirul Anam', '22601', 'Surabaya', '$password_guru', 'guru', NULL);
    "

?>