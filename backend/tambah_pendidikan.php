<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../masuk.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM profile_pelamar WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pelamar = $stmt->get_result()->fetch_assoc();
$pelamar_id = $user_id;

$tingkat_pendidikan = $_POST['tingkatpendidikan'] ?? '';
$nama_institusi = $_POST['nama_institusi'] ?? '';
$bidang_studi = $_POST['bidang_studi'] ?? '';
$tahun_mulai = isset($_POST['tahun_mulai']) ? $_POST['tahun_mulai'] . '-01' : null;
$tahun_selesai = isset($_POST['tahun_selesai']) && $_POST['tahun_selesai'] != '' ? $_POST['tahun_selesai'] . '-01' : null;

$deskripsi = $_POST['deskripsi_pendidikan'] ?? '';

try {
    $stmt = $conn->prepare("INSERT INTO pendidikan_pelamar (pelamar_id, tingkat_pendidikan, nama_institusi, bidang_studi, tahun_mulai, tahun_selesai, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $pelamar_id, $tingkat_pendidikan, $nama_institusi, $bidang_studi, $tahun_mulai, $tahun_selesai, $deskripsi);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Pendidikan berhasil ditambahkan!";
    } else {
        throw new Exception("Gagal menambahkan pendidikan: " . $stmt->error);
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}

header("Location: ../profilpelamar.php");
exit();
