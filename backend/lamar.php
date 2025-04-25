<?php
require_once 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Anda harus login terlebih dahulu");
}

// Validasi input
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header("HTTP/1.1 400 Bad Request");
    exit("ID Lowongan tidak valid");
}

$user_id = $_SESSION['user_id'];
$lowongan_id = $_POST['id'];
$surat_lamaran = $_POST['surat_lamaran'] ?? '';

// Cek apakah sudah melamar
$stmt = $conn->prepare("SELECT id FROM lamaran WHERE pelamar_id = ? AND lowongan_id = ?");
$stmt->bind_param("ii", $user_id, $lowongan_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Anda+sudah+melamar");
    exit;
}

// Dapatkan resume pelamar
$stmt = $conn->prepare("SELECT resume FROM profile_pelamar WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc()['resume'];

if (empty($resume)) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Resume+belum+diupload");
    exit;
}

// Buat lamaran
$stmt = $conn->prepare("INSERT INTO lamaran (pelamar_id, lowongan_id, resume, surat_lamaran, status) VALUES (?, ?, ?, ?, 'dikirim')");
$stmt->bind_param("iiss", $user_id, $lowongan_id, $resume, $surat_lamaran);

if ($stmt->execute()) {
    header("Location: ../applied.php?success=Lamaran+berhasil+dikirim");

    exit;
} else {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=Gagal+mengirim+lamaran");
    exit;
}

$conn->close();
?>
