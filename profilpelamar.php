<?php
require_once 'backend/db.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pelamar
$stmt = $conn->prepare("SELECT * FROM profile_pelamar WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pelamar = $stmt->get_result()->fetch_assoc();

// Ambil data pendidikan
$pendidikan = [];
$pengalaman = [];

if (isset($pelamar['id'])) {
    // Ambil data pendidikan
    $stmt = $conn->prepare("SELECT * FROM pendidikan_pelamar WHERE pelamar_id = ? ORDER BY tahun_mulai DESC");
    $stmt->bind_param("i", $pelamar['user_id']);
    $stmt->execute();
    $pendidikan = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Ambil data pengalaman
    $stmt = $conn->prepare("SELECT * FROM pengalaman_pelamar WHERE pelamar_id = ? ORDER BY tanggal_mulai DESC");
    $stmt->bind_param("i", $pelamar['user_id']);
    $stmt->execute();
    $pengalaman = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Proses form jika ada submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $nama_lengkap = isset($_POST['namalengkap']) ? trim($_POST['namalengkap']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nomor_telepon = isset($_POST['nomortelepon']) ? trim($_POST['nomortelepon']) : '';
    $lokasi = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : '';
    $deskripsi_personal = isset($_POST['deskripsipersonal']) ? trim($_POST['deskripsipersonal']) : '';

    // Validasi field wajib
    if (empty($nama_lengkap) || empty($email)) {
        $_SESSION['error_message'] = "Nama lengkap dan email harus diisi";
        header("Location: profilpelamar.php");
        exit();
    }

    // Upload foto profil
    $foto_profil = $pelamar['foto_profil'] ?? null;
    if (isset($_FILES['fotoprofil']) && $_FILES['fotoprofil']['error'] === UPLOAD_ERR_OK) {
        // Validasi file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['fotoprofil']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error_message'] = "Hanya file JPG, PNG, atau GIF yang diperbolehkan";
            header("Location: profilpelamar.php");
            exit();
        }
        
        if ($_FILES['fotoprofil']['size'] > 2000000) { // 2MB
            $_SESSION['error_message'] = "Ukuran file terlalu besar (maksimal 2MB)";
            header("Location: profilpelamar.php");
            exit();
        }

        $target_dir = "backend/uploads/profil/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['fotoprofil']['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['fotoprofil']['tmp_name'], $target_file)) {
            $foto_profil = $target_file;
            // Hapus foto lama jika ada
            if (!empty($pelamar['foto_profil']) && file_exists($pelamar['foto_profil'])) {
                unlink($pelamar['foto_profil']);
            }
        }
    }

    // Upload resume
    $resume = $pelamar['resume'] ?? null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        // Validasi file
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $file_type = $_FILES['resume']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error_message'] = "Hanya file PDF atau DOC/DOCX yang diperbolehkan";
            header("Location: profilpelamar.php");
            exit();
        }
        
        if ($_FILES['resume']['size'] > 5000000) { // 5MB
            $_SESSION['error_message'] = "Ukuran file terlalu besar (maksimal 5MB)";
            header("Location: profilpelamar.php");
            exit();
        }

        $target_dir = "backend/uploads/resume/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $filename = 'resume_' . $user_id . '_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
            $resume = $target_file;
            // Hapus resume lama jika ada
            if (!empty($pelamar['resume']) && file_exists($pelamar['resume'])) {
                unlink($pelamar['resume']);
            }
        }
    }

    try {
        // Cek apakah profil sudah ada
        $stmt = $conn->prepare("SELECT id FROM profile_pelamar WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $profil_ada = $stmt->get_result()->num_rows > 0;

        if ($profil_ada) {
            // Update jika profil sudah ada
            $stmt = $conn->prepare("UPDATE profile_pelamar SET 
                nama_lengkap = ?, 
                email = ?, 
                nomor_telepon = ?, 
                lokasi = ?, 
                deskripsi_personal = ?, 
                foto_profil = ?, 
                resume = ? 
                WHERE user_id = ?");
            $stmt->bind_param("sssssssi", $nama_lengkap, $email, $nomor_telepon, $lokasi, $deskripsi_personal, $foto_profil, $resume, $user_id);
        } else {
            // Insert jika profil belum ada
            $stmt = $conn->prepare("INSERT INTO profile_pelamar 
                (user_id, nama_lengkap, email, nomor_telepon, lokasi, deskripsi_personal, foto_profil, resume) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $user_id, $nama_lengkap, $email, $nomor_telepon, $lokasi, $deskripsi_personal, $foto_profil, $resume);
        }

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Profil berhasil " . ($profil_ada ? 'diperbarui' : 'dibuat') . "!";
            
            // Jika insert baru, ambil ID pelamar untuk pendidikan/pengalaman
            if (!$profil_ada) {
                $pelamar_id = $conn->insert_id;
                $_SESSION['pelamar_id'] = $pelamar_id;
            }
            
            header("Location: profilpelamar.php");
            exit();
        } else {
            throw new Exception("Gagal menyimpan profil: " . $stmt->error);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: profilpelamar.php");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>LokerNow | Profil Pelamar</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="stylesheet" href="css/custom-bs.css" />
    <link rel="stylesheet" href="css/jquery.fancybox.min.css" />
    <link rel="stylesheet" href="css/bootstrap-select.min.css" />
    <link rel="stylesheet" href="fonts/icomoon/style.css" />
    <link rel="stylesheet" href="fonts/line-icons/style.css" />
    <link rel="stylesheet" href="css/owl.carousel.min.css" />
    <link rel="stylesheet" href="css/animate.min.css" />
    <link rel="stylesheet" href="css/quill.snow.css" />

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="css/style.css" />
</head>
<body id="top">

    <!-- NAVBAR SECTION -->
    <header class="site-navbar mt-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="site-logo col-6">
                    <img src="images/logo.png" alt="" width="80vw" />
                </div>

                <nav class="mx-auto site-navigation">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="berandapelamar.php">Beranda</a></li>
                        <li><a href="rekomendasipelamar.php">Rekomendasi</a></li>
                        <li class="has-children">
                            <a>Aktivitas</a>
                            <ul class="dropdown">
                                <li><a href="applied.php">Lamaran</a></li>
                                <li><a href="saved.php">Disimpan</a></li>
                            </ul>
                        </li>
                        <li><a class="nav-link active" href="profilpelamar.php">Profil</a></li>
                    </ul>
                </nav>

                <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
                    <div class="ml-auto">
                        <a href="backend/logout.php" class="btn btn-primary border-width-2 d-none d-lg-inline-block">
                            <span class="mr-2 icon-lock_outline"></span>Log Out
                        </a>
                    </div>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3">
                        <span class="icon-menu h3 m-0 p-0 mt-2"></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-white font-weight-bold">Lengkapi Profilmu!</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section">
        <div class="container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="row mb-5">
                <div class="col-lg-12">
                    <form class="p-4 p-md-5 border rounded" method="post" enctype="multipart/form-data">
                        <h3 class="text-black mb-5 border-bottom pb-2">Detail Profil Anda</h3>
                        
                        <div class="form-group">
                            <?php if (!empty($pelamar['foto_profil'])): ?>
                                <img src="<?= htmlspecialchars($pelamar['foto_profil']) ?>" class="rounded-circle" alt="Foto Profil" width="100vw"><br>
                            <?php else: ?>
                                <img src="images/default-profile.jpg" class="rounded-circle" alt="Foto Profil" width="100vw"><br>
                            <?php endif; ?>
                            <label class="mt-2 btn btn-primary btn-sm btn-file">
                                Upload Pas Foto
                                <input type="file" name="fotoprofil" id="fotoprofil" hidden accept="image/*">
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($pelamar['email'] ?? '') ?>" 
                                   placeholder="you@yourdomain.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nomortelepon">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="nomortelepon" name="nomortelepon" 
                                   value="<?= htmlspecialchars($pelamar['nomor_telepon'] ?? '') ?>" 
                                   placeholder="08xxx-xxxx-xxxx">
                        </div>
                        
                        <div class="form-group">
                            <label for="namalengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" id="namalengkap" name="namalengkap" 
                                   value="<?= htmlspecialchars($pelamar['nama_lengkap'] ?? '') ?>" 
                                   placeholder="Nama Lengkap" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <select class="selectpicker border rounded" id="lokasi" name="lokasi" data-style="btn-black" data-width="100%" data-live-search="true" title="Pilih Lokasimu">
                                <?php
                                $lokasi_list = ["Ambon, Maluku", "Banda Aceh, Aceh", "Bandung, Jawa Barat", "Banjarmasin, Kalimantan Selatan", "Batam, Kepulauan Riau", "Bengkulu, Bengkulu", "Denpasar, Bali", "Gorontalo, Gorontalo", "Jakarta, DKI Jakarta", "Jambi, Jambi", "Jayapura, Papua", "Kendari, Sulawesi Tenggara", "Kupang, Nusa Tenggara Timur", "Makassar, Sulawesi Selatan", "Manado, Sulawesi Utara", "Manokwari, Papua Barat", "Mamuju, Sulawesi Barat", "Mataram, Nusa Tenggara Barat", "Medan, Sumatera Utara", "Palangka Raya, Kalimantan Tengah", "Palembang, Sumatera Selatan", "Palu, Sulawesi Tengah", "Pangkal Pinang, Kepulauan Bangka Belitung", "Pekanbaru, Riau", "Pontianak, Kalimantan Barat", "Samarinda, Kalimantan Timur", "Semarang, Jawa Tengah", "Serang, Banten", "Surabaya, Jawa Timur", "Tanjung Pinang, Kepulauan Riau", "Tanjung Selor, Kalimantan Utara", "Ternate, Maluku Utara", "Yogyakarta, DI Yogyakarta"];
                                
                                foreach ($lokasi_list as $lok) {
                                    $selected = (isset($pelamar['lokasi']) && $pelamar['lokasi'] == $lok) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($lok) . "\" $selected>" . htmlspecialchars($lok) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="deskripsipersonal">Deskripsi Personal</label>
                            <textarea class="form-control" id="deskripsipersonal" name="deskripsipersonal" 
                                      placeholder="Ceritakan tentang anda"><?= htmlspecialchars($pelamar['deskripsi_personal'] ?? '') ?></textarea>
                        </div>

                        <!-- Pendidikan -->
                        <div class="form-group">
                            <label for="pendidikan">Pendidikan</label><br>
                            <?php foreach ($pendidikan as $edu): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($edu['tingkat_pendidikan']) ?> - <?= htmlspecialchars($edu['nama_institusi']) ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($edu['bidang_studi']) ?></h6>
                                    <p class="card-text"><?= date('M Y', strtotime($edu['tahun_mulai'])) ?> - <?= date('M Y', strtotime($edu['tahun_selesai'])) ?></p>
                                    <p class="card-text"><?= htmlspecialchars($edu['deskripsi']) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahpendidikan">
                                Tambah Pendidikan
                            </button>
                        </div>

                        <!-- Pengalaman Kerja -->
                        <div class="form-group">
                            <label for="pengalamankerja">Pengalaman Kerja</label><br>
                            <?php foreach ($pengalaman as $exp): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($exp['nama_pekerjaan']) ?> di <?= htmlspecialchars($exp['nama_perusahaan']) ?></h5>
                                    <p class="card-text"><?= date('M Y', strtotime($exp['tanggal_mulai'])) ?> - <?= date('M Y', strtotime($exp['tanggal_selesai'])) ?></p>
                                    <p class="card-text"><?= htmlspecialchars($exp['deskripsi']) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahpengalamankerja">
                                Tambah Pengalaman Kerja
                            </button>
                        </div>

                        <div class="form-group">
                            <label for="resume">Resume</label><br>
                            <?php if (!empty($pelamar['resume'])): ?>
                                <a href="<?= htmlspecialchars($pelamar['resume']) ?>" target="_blank" class="btn btn-sm btn-success mb-2">
                                    Lihat Resume
                                </a><br>
                            <?php endif; ?>
                            <label class="btn btn-primary btn-sm btn-file">
                                Upload Resume
                                <input type="file" name="resume" id="resume" hidden accept=".pdf,.doc,.docx">
                            </label>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark btn-lg">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Tambah Pendidikan -->
    <div class="modal fade" id="tambahpendidikan" tabindex="-1" aria-labelledby="tambahpendidikanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahpendidikanLabel">Tambah Pendidikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="backend/tambah_pendidikan.php">
                    <input type="hidden" name="pelamar_id" value="<?= $pelamar['id'] ?? ($_SESSION['pelamar_id'] ?? '') ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tingkatpendidikan">Tingkat Pendidikan</label>
                            <select class="form-control" id="tingkatpendidikan" name="tingkatpendidikan" required>
                                <option value="">Pilih Tingkat Pendidikan</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="Diploma (D1 - D4)">Diploma (D1 - D4)</option>
                                <option value="Sarjana (S1)">Sarjana (S1)</option>
                                <option value="Magister (S2)">Magister (S2)</option>
                                <option value="Doktor (S3)">Doktor (S3)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama_institusi">Nama Sekolah/Perguruan Tinggi</label>
                            <input type="text" class="form-control" id="nama_institusi" name="nama_institusi" placeholder="Nama institusi" required>
                        </div>
                        <div class="form-group">
                            <label for="bidang_studi">Bidang Studi</label>
                            <input type="text" class="form-control" id="bidang_studi" name="bidang_studi" placeholder="Bidang studi, jurusan">
                        </div>
                        <div class="form-group">
                            <label for="tahun_mulai">Waktu Mulai</label>
                            <input type="month" class="form-control" id="tahun_mulai" name="tahun_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="tahun_selesai">Waktu Selesai</label>
                            <input type="month" class="form-control" id="tahun_selesai" name="tahun_selesai">
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_pendidikan">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_pendidikan" name="deskripsi_pendidikan" placeholder="Ceritakan tentang pendidikan anda"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_pendidikan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pengalaman Kerja -->
    <div class="modal fade" id="tambahpengalamankerja" tabindex="-1" aria-labelledby="tambahpengalamankerjaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahpengalamankerjaLabel">Tambah Pengalaman Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="backend/tambah_pengalaman.php">
                    <input type="hidden" name="pelamar_id" value="<?= $pelamar['id'] ?? ($_SESSION['pelamar_id'] ?? '') ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_pekerjaan">Nama Pekerjaan</label>
                            <input type="text" class="form-control" id="nama_pekerjaan" name="nama_pekerjaan" placeholder="Nama pekerjaan" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_perusahaan">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" placeholder="Nama perusahaan" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_mulai">Waktu Mulai</label>
                            <input type="month" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_selesai">Waktu Selesai</label>
                            <input type="month" class="form-control" id="tanggal_selesai" name="tanggal_selesai">
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_pengalaman">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_pengalaman" name="deskripsi_pengalaman" placeholder="Ceritakan tentang pekerjaan anda"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_pengalaman" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/stickyfill.min.js"></script>
    <script src="js/jquery.fancybox.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/quill.min.js"></script>
    
    <script src="js/bootstrap-select.min.js"></script>
    
    <script src="js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
     
</body>
</html>