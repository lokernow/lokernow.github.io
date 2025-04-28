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

    // Simpan perusahaan
    $stmt = $conn->prepare("INSERT INTO perusahaan (email, password, is_verified) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        // Ambil user_id yang valid setelah insert berhasil
        $user_id = $stmt->insert_id;

        // Generate token dan simpan ke verification_tokens
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 day')); // token 1 hari

        $stmt2 = $conn->prepare("INSERT INTO verification_tokens (company_id, token, expiry_date) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $user_id, $token, $expiry);

        if ($stmt2->execute()) {
            // Kirim email verifikasi
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Ganti sesuai layanan SMTP kamu
                $mail->SMTPAuth   = true;
                $mail->Username   = 'wijayaangelina0@gmail.com'; // Email pengirim
                $mail->Password   = 'seasyrweotbvbkbt';   // App password Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('wijayaangelina0@gmail.com', 'Lokernow'); // Nama pengirim
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Verifikasi Email Anda';
                $mail->Body    = "
                    <h3>Halo</h3>
                    <p>Terima kasih sudah mendaftar. Silakan klik tombol di bawah ini untuk memverifikasi email Anda:</p>
                    <p><a href='http://localhost/23si3/PROJECT/backend/verify-perusahaan.php?token=$token' style='padding:10px 20px; background:#28a745; color:white; text-decoration:none;'>Verifikasi Email</a></p>
                    <p>Atau salin link ini: <br> http://localhost/23si3/PROJECT/backend/verify-perusahaan.php?token=$token</p>
                ";

                if ($mail->send()) {
                    header("Location: register_success.php");
                    exit;
                } else {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                echo "Gagal mengirim email verifikasi. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Gagal menyimpan token verifikasi: " . $stmt2->error;
        }
    } else {
        echo "Gagal membuat akun perusahaan: " . $stmt->error;
    }

    // Menutup statement
    $stmt->close();
    $stmt2->close();
}

// Menutup koneksi ke database
$conn->close();
?>
