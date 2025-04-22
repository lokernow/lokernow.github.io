<?php
session_start();
require 'db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Simpan pelamar
    $stmt = $conn->prepare("INSERT INTO pelamar (email, password, is_verified) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $user_id = $stmt->insert_id;

    // Generate token & simpan ke verification_tokens
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 day')); // token 1 hari

    $stmt2 = $conn->prepare("INSERT INTO verification_tokens (user_id, token, expiry_date) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $user_id, $token, $expiry);
    $stmt2->execute();

    // Kirim email verifikasi
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Ganti sesuai layanan SMTP kamu
        $mail->SMTPAuth   = true;
        $mail->Username   = 'uchihasizui9@gmail.com'; // Email pengirim
        $mail->Password   = 'qkcppieiuqpsmghg';   // App password Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('uchihasizui9@gmail.com', 'Lokernow'); // Nama pengirim
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Email Anda';
        $mail->Body    = "
            <h3>Halo</h3>
            <p>Terima kasih sudah mendaftar. Silakan klik tombol di bawah ini untuk memverifikasi email Anda:</p>
            <p><a href='http://localhost/lokernow/backend/verify.php?token=$token' style='padding:10px 20px; background:#28a745; color:white; text-decoration:none;'>Verifikasi Email</a></p>
            <p>Atau salin link ini: <br> http://localhost/lokernow/backend/verify.php?token=$token</p>
        ";

        $mail->send();
        header("Location: register_success.php");
        exit;
        
    } catch (Exception $e) {
        echo "Gagal mengirim email verifikasi. Mailer Error: {$mail->ErrorInfo}";
    }

    $stmt->close();
    $stmt2->close();
}

$conn->close();
?>
