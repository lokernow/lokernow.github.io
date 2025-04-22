<?php
require 'db.php';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $conn->prepare("SELECT user_id, expiry_date FROM verification_tokens WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $expiry_date = $row['expiry_date'];
        
        if (strtotime($expiry_date) > time()) {
            $update = $conn->prepare("UPDATE pelamar SET is_verified = 1 WHERE id = ?");
            $update->bind_param("i", $user_id);
            $update->execute();
            
            $delete = $conn->prepare("DELETE FROM verification_tokens WHERE token = ?");
            $delete->bind_param("s", $token);
            $delete->execute();
            
            header("Location: verify_success.php");
        } else {
            echo "<h2>Link Kadaluarsa</h2>";
            echo "<p>Link verifikasi sudah kadaluarsa. Silakan daftar ulang.</p>";
        }
    } else {
        echo "<h2>Token Tidak Valid</h2>";
        echo "<p>Token verifikasi tidak valid.</p>";
    }
    
    $stmt->close();
} else {
    echo "<h2>Token Tidak Ditemukan</h2>";
    echo "<p>Token verifikasi tidak ditemukan.</p>";
}

$conn->close();
?>