<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Anda harus login terlebih dahulu";
    $_SESSION['message_type'] = "danger";
    header("Location: ../masuk.html");
    exit();
}

$id_perusahaan = $_SESSION['user_id'];

function uploadFile($file, $targetDir) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Hanya file JPG, PNG, atau GIF yang diperbolehkan'];
    }

    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'Ukuran file maksimal 2MB'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $targetPath = $targetDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }
}

try {
    $data = [
        'id_perusahaan' => $id_perusahaan,
        'email' => $_POST['email'],
        'nomor_telepon' => $_POST['nomortelepon'],
        'nama_perusahaan' => $_POST['namaperusahaan'],
        'lokasi' => $_POST['lokasi'],
        'deskripsi' => $_POST['deskripsiperusahaan'],
        'kultur' => $_POST['kulturperusahaan']
    ];

    // Upload logo
    if (!empty($_FILES['logo']['name'])) {
        $uploadDir = __DIR__ . '/uploads/logos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadResult = uploadFile($_FILES['logo'], $uploadDir);
        if ($uploadResult['success']) {
            $data['logo'] = $uploadResult['filename'];

            // Hapus logo lama
            $stmt = $conn->prepare("SELECT logo FROM company_profiles WHERE id_perusahaan = ?");
            $stmt->bind_param("i", $id_perusahaan);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $oldLogo = $row['logo'] ?? null;
            $stmt->close();

            if ($oldLogo && file_exists($uploadDir . $oldLogo)) {
                unlink($uploadDir . $oldLogo);
            }
        } else {
            throw new Exception($uploadResult['message']);
        }
    }

    // Upload galeri
    $galeriFilenames = [];
    if (!empty($_FILES['galeriperusahaan']['name'][0])) {
        $galleryDir = __DIR__ . '/uploads/galleries/';
        if (!file_exists($galleryDir)) {
            mkdir($galleryDir, 0777, true);
        }

        foreach ($_FILES['galeriperusahaan']['tmp_name'] as $key => $tmpName) {
            $file = [
                'name' => $_FILES['galeriperusahaan']['name'][$key],
                'type' => $_FILES['galeriperusahaan']['type'][$key],
                'tmp_name' => $tmpName,
                'error' => $_FILES['galeriperusahaan']['error'][$key],
                'size' => $_FILES['galeriperusahaan']['size'][$key]
            ];

            $uploadResult = uploadFile($file, $galleryDir);
            if ($uploadResult['success']) {
                $galeriFilenames[] = $uploadResult['filename'];
            }
        }

        if (!empty($galeriFilenames)) {
            $data['galeri_perusahaan'] = json_encode($galeriFilenames);
        }
    }

    // Cek apakah sudah ada profil
    $stmt = $conn->prepare("SELECT id FROM company_profiles WHERE id_perusahaan = ?");
    $stmt->bind_param("i", $id_perusahaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $profileExists = $result->num_rows > 0;
    $stmt->close();

    if ($profileExists) {
        // Update
        $query = "UPDATE company_profiles SET 
                    email = ?, 
                    nomor_telepon = ?, 
                    nama_perusahaan = ?, 
                    lokasi = ?, 
                    deskripsi = ?, 
                    kultur = ?";
        $types = "ssssss";
        $values = [
            $data['email'],
            $data['nomor_telepon'],
            $data['nama_perusahaan'],
            $data['lokasi'],
            $data['deskripsi'],
            $data['kultur']
        ];

        if (isset($data['logo'])) {
            $query .= ", logo = ?";
            $types .= "s";
            $values[] = $data['logo'];
        }

        if (isset($data['galeri_perusahaan'])) {
            $query .= ", galeri_perusahaan = ?";
            $types .= "s";
            $values[] = $data['galeri_perusahaan'];
        }

        $query .= " WHERE id_perusahaan = ?";
        $types .= "i";
        $values[] = $id_perusahaan;

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$values);
    } else {
        // Insert
        $columns = "id_perusahaan, email, nomor_telepon, nama_perusahaan, lokasi, deskripsi, kultur";
        $placeholders = "?, ?, ?, ?, ?, ?, ?";
        $types = "issssss";
        $values = [
            $data['id_perusahaan'],
            $data['email'],
            $data['nomor_telepon'],
            $data['nama_perusahaan'],
            $data['lokasi'],
            $data['deskripsi'],
            $data['kultur']
        ];

        if (isset($data['logo'])) {
            $columns .= ", logo";
            $placeholders .= ", ?";
            $types .= "s";
            $values[] = $data['logo'];
        }

        if (isset($data['galeri_perusahaan'])) {
            $columns .= ", galeri_perusahaan";
            $placeholders .= ", ?";
            $types .= "s";
            $values[] = $data['galeri_perusahaan'];
        }

        $query = "INSERT INTO company_profiles ($columns) VALUES ($placeholders)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$values);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Profil perusahaan berhasil disimpan!";
        $_SESSION['message_type'] = "success";
    } else {
        throw new Exception("Gagal menyimpan data profil: " . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    $_SESSION['message'] = $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

$conn->close();
header("Location: ../profilperusahaan.php");
exit();
?>
