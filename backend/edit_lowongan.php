<?php
require 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $id = $_POST['id'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $email = $_POST['email'];
    $judul_pekerjaan = $_POST['judul_pekerjaan'];
    $lokasi = $_POST['lokasi'];
    $wilayah = $_POST['wilayah'];
    $tipe_pekerjaan = $_POST['tipe_pekerjaan'];
    $deskripsi = $_POST['deskripsi'];

    // Check if new image is uploaded
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $path = "uploads/" . $gambar;

        // Delete old image
        $sql_get_old_image = "SELECT gambar FROM lowongan_kerja WHERE id = '$id'";
        $result = $conn->query($sql_get_old_image);
        $old_image = $result->fetch_assoc();
        if ($old_image['gambar'] != 'no-image.jpg') {
            unlink("uploads/" . $old_image['gambar']);
        }

        // Upload new image
        if (move_uploaded_file($tmp, $path)) {
            $sql = "UPDATE lowongan_kerja SET 
                    nama_perusahaan = '$nama_perusahaan',
                    gambar = '$gambar',
                    email = '$email',
                    judul_pekerjaan = '$judul_pekerjaan',
                    lokasi = '$lokasi',
                    wilayah = '$wilayah',
                    tipe_pekerjaan = '$tipe_pekerjaan',
                    deskripsi = '$deskripsi'
                    WHERE id = '$id'";
        } else {
            echo "Gagal upload gambar.";
            exit();
        }
    } else {
        // If no new image is uploaded, keep the old image
        $sql = "UPDATE lowongan_kerja SET 
                nama_perusahaan = '$nama_perusahaan',
                email = '$email',
                judul_pekerjaan = '$judul_pekerjaan',
                lokasi = '$lokasi',
                wilayah = '$wilayah',
                tipe_pekerjaan = '$tipe_pekerjaan',
                deskripsi = '$deskripsi'
                WHERE id = '$id'";
    }

    // Prepare and execute query
    if ($conn->query($sql) === TRUE) {
        header('Location: edit-success.php');
        exit();
    } else {
        echo "Gagal update data: " . $conn->error;
    }

    $conn->close();
} else {
    // If form not submitted, redirect back
    header('Location: edit-job.php');
    exit();
}
?>