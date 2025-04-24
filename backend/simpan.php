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

// Cek apakah lowongan sudah disimpan
$stmt = $conn->prepare("SELECT id FROM simpan_lamaran WHERE pelamar_id = ? AND lowongan_id = ?");
$stmt->bind_param("ii", $user_id, $lowongan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM simpan_lamaran WHERE pelamar_id = ? AND lowongan_id = ?");
    $stmt->bind_param("ii", $user_id, $lowongan_id);
    if ($stmt->execute()) {
        header("Location: ../saved.php?success=Lamaran+berhasil+dikirim");

    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus dari simpanan']);
    }
} else {
    $stmt = $conn->prepare("INSERT INTO simpan_lamaran (pelamar_id, lowongan_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $lowongan_id);
    if ($stmt->execute()) {
        header("Location: ../saved.php?success=Lamaran+berhasil+dikirim");

    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan lowongan']);
    }
}

$conn->close();
?>