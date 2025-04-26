<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "lokernow"; // ganti sesuai nama database kamu

$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // pastikan id adalah angka

    // Siapkan query hapus
    $stmt = $conn->prepare("DELETE FROM lowongan_kerja WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Eksekusi query
    if ($stmt->execute()) {
        header("Location: aktivitasperusahaan.php?success=Lowongan+berhasil+dihapus");
        exit();
    } else {
        echo "Gagal menghapus data: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID tidak ditemukan.";
}

$conn->close();
?>
