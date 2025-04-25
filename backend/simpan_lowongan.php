<?php
session_start();
require 'db.php';
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_perusahaan = $_SESSION['user_id'];
$nama_perusahaan = $_POST['nama_perusahaan'];
$email = $_POST['email'];
$judul_pekerjaan = $_POST['judul_pekerjaan'];
$lokasi = $_POST['lokasi'];
$wilayah = $_POST['wilayah'];
$tipe_pekerjaan = $_POST['tipe_pekerjaan'];
$deskripsi = $_POST['deskripsi'];

// Upload gambar
$gambar = $_FILES['gambar']['name'];
$tmp = $_FILES['gambar']['tmp_name'];
$path = "uploads/" . $gambar;

if (move_uploaded_file($tmp, $path)) {
    $sql = "INSERT INTO lowongan_kerja (nama_perusahaan, gambar, email, judul_pekerjaan, lokasi, wilayah, tipe_pekerjaan, deskripsi, id_perusahaan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn ->prepare($sql);
    $stmt->bind_param("ssssssssi", $nama_perusahaan, $gambar, $email, $judul_pekerjaan, $lokasi, $wilayah, $tipe_pekerjaan, $deskripsi , $id_perusahaan);

    if ($stmt->execute()) {
        header("Location: http://localhost/lokernow/aktivitasperusahaan.php?success=Job+berhasil+ditambahkan");
        exit(); // Sangat disarankan untuk menghentikan eksekusi setelah redirect
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
    

    $stmt->close();
} else {
    echo "Gagal upload gambar.";
}

$conn->close();
?>
