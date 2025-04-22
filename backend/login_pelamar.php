<?php
require 'db.php';

session_start();

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "Semua field wajib diisi.";
        exit;
    }

    $stmt = $conn->prepare("SELECT id, password, is_verified FROM pelamar WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: ../berandapelamar.php");
                exit;
            } else {
                echo "Akun belum diverifikasi. Silakan cek email Anda untuk link verifikasi.";
            }
        } else {
            echo "Password salah.";
        }
    } else {
        echo "Email tidak terdaftar.";
    }
    
    $stmt->close();
}

$conn->close();
?>