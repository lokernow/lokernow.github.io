<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM perusahaan WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $perusahaan = $result->fetch_assoc();

        if (password_verify($password, $perusahaan['password'])) {
            $_SESSION['user_id'] = $perusahaan['id'];
            $_SESSION['role'] = 'perusahaan';

            header("Location: ../berandaperusahaan.php");
            exit;
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Akun tidak ditemukan!";
    }
}
?>
