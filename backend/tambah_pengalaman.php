<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM profile_pelamar WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pelamar = $stmt->get_result()->fetch_assoc();
$pelamar_id = $user_id;

$nama_pekerjaan = $_POST['nama_pekerjaan'] ?? '';
$nama_perusahaan = $_POST['nama_perusahaan'] ?? '';
$tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] . '-01' : null;
$tanggal_selesai = isset($_POST['tanggal_selesai']) && $_POST['tanggal_selesai'] != '' ? $_POST['tanggal_selesai'] . '-01' : null;
$deskripsi = $_POST['deskripsi_pengalaman'] ?? '';

try {
    $stmt = $conn->prepare("INSERT INTO pengalaman_pelamar (pelamar_id, nama_pekerjaan, nama_perusahaan, tanggal_mulai, tanggal_selesai, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $pelamar_id, $nama_pekerjaan, $nama_perusahaan, $tanggal_mulai, $tanggal_selesai, $deskripsi);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Pengalaman kerja berhasil ditambahkan!";
    } else {
        throw new Exception("Gagal menambahkan pengalaman kerja: " . $stmt->error);
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}

header("Location: ../profilpelamar.php");
exit();
