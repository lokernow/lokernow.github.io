<?php
session_start();
require_once 'backend/db.php';

// Check if user is logged in as company
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if job ID is provided
if (!isset($_GET['id'])) {
    header("Location: aktivitasperusahaan.php");
    exit();
}

$job_id = $_GET['id'];
$id_perusahaan = $_SESSION['user_id'];

// Get job details
$job = [];
$query = "SELECT * FROM lowongan_kerja WHERE id = ? AND id_perusahaan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $job_id, $id_perusahaan);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

if (!$job) {
    header("Location: aktivitasperusahaan.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $data = [
        'judul_pekerjaan' => $_POST['job-title'],
        'lokasi' => $_POST['job-location'],
        'wilayah' => $_POST['job-region'],
        'tipe_pekerjaan' => $_POST['job-type'],
        'deskripsi' => $_POST['job-description'],
        'id' => $job_id,
        'id_perusahaan' => $id_perusahaan
    ];

    // Handle file upload
    if (!empty($_FILES['job-image']['name'])) {
        $uploadDir = __DIR__ . '/backend/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $extension = pathinfo($_FILES['job-image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        // Validate and move uploaded file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['job-image']['type'], $allowedTypes) && 
            move_uploaded_file($_FILES['job-image']['tmp_name'], $targetPath)) {
            
            // Delete old image if exists
            if (!empty($job['gambar']) && file_exists($uploadDir . $job['gambar'])) {
                unlink($uploadDir . $job['gambar']);
            }
            
            $data['gambar'] = $filename;
        }
    }

    // Update job in database
    $query = "UPDATE lowongan_kerja SET 
              judul_pekerjaan = ?, 
              lokasi = ?, 
              wilayah = ?, 
              tipe_pekerjaan = ?, 
              deskripsi = ?" . 
              (isset($data['gambar']) ? ", gambar = ?" : "") . 
              " WHERE id = ? AND id_perusahaan = ?";
    
    $stmt = $conn->prepare($query);
    
    // Bind parameters
    if (isset($data['gambar'])) {
        $stmt->bind_param(
            "ssssssii", 
            $data['judul_pekerjaan'],
            $data['lokasi'],
            $data['wilayah'],
            $data['tipe_pekerjaan'],
            $data['deskripsi'],
            $data['gambar'],
            $data['id'],
            $data['id_perusahaan']
        );
    } else {
        $stmt->bind_param(
            "sssssii", 
            $data['judul_pekerjaan'],
            $data['lokasi'],
            $data['wilayah'],
            $data['tipe_pekerjaan'],
            $data['deskripsi'],
            $data['id'],
            $data['id_perusahaan']
        );
    }
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Lowongan berhasil diperbarui";
        $_SESSION['message_type'] = "success";
        header("Location: aktivitasperusahaan.php");
        exit();
    } else {
        $_SESSION['message'] = "Gagal memperbarui lowongan";
        $_SESSION['message_type'] = "danger";
    }
    
    $stmt->close();
}

$conn->close();
?>
<!doctype html>
<html lang="en">
  <head>
    <title>LokerNow | Edit Lowongan</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="css/custom-bs.css">
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="fonts/line-icons/style.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/quill.snow.css">
    
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="css/style.css">    
  </head>
  <body id="top">

  <div id="overlayer"></div>
  <div class="loader">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
    
<div class="site-wrap">
    <!-- NAVBAR -->
    <header class="site-navbar mt-3">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="site-logo col-6"><img src="images/logo.png" alt="" width="80vw"></div>
          <nav class="mx-auto site-navigation">
            <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
              <li><a href="berandaperusahaan.php">Beranda</a></li>
              <li><a href="aktivitasperusahaan.php">Aktivitas</a></li>
              <li><a href="profilperusahaan.php">Profil</a></li>
              <li class="d-lg-none"><a href="post-job.php"><span class="mr-2">+</span> Tambah Loker</a></li>
              <li class="d-lg-none"><a href="logout.php">Log Out</a></li>
            </ul>
          </nav>
          <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
            <div class="ml-auto">
              <a href="post-job.php" class="btn btn-outline-white border-width-2 d-none d-lg-inline-block"><span class="mr-2 icon-add"></span>Tambah Loker</a>
              <a href="logout.php" class="btn btn-primary border-width-2 d-none d-lg-inline-block"><span class="mr-2 icon-lock_outline"></span>Log Out</a>
            </div>
            <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3"><span class="icon-menu h3 m-0 p-0 mt-2"></span></a>
          </div>
        </div>
      </div>
    </header>

    <!-- HOME -->
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg');" id="home-section">
      <div class="container">
        <div class="row">
          <div class="col-md-7">
            <h1 class="text-white font-weight-bold">Edit Pekerjaan</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="site-section">
      <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
            <?php 
              echo $_SESSION['message']; 
              unset($_SESSION['message']);
              unset($_SESSION['message_type']);
            ?>
          </div>
        <?php endif; ?>

        <div class="row align-items-center mb-5">
          <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="d-flex align-items-center">
              <div>
                <h2>Edit Pekerjaan</h2>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="row">
              <div class="col-6">
                <a href="job-single.php?id=<?php echo $job_id; ?>" class="btn btn-block btn-light btn-md">
                  <span class="icon-open_in_new mr-2"></span>Tinjau
                </a>
              </div>
              <div class="col-6">
                <button type="submit" form="edit-job-form" class="btn btn-block btn-primary btn-md">Simpan</button>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-5">
          <div class="col-lg-12">
            <form id="edit-job-form" class="p-4 p-md-5 border rounded" method="post" enctype="multipart/form-data">
              <h3 class="text-black mb-5 border-bottom pb-2">Detail Pekerjaan</h3>
              
              <div class="form-group">
                <label for="job-image" class="d-block">Unggah Gambar</label> <br>
                <?php if (!empty($job['gambar'])): ?>
                  <img src="backend/uploads/<?php echo htmlspecialchars($job['gambar']); ?>" class="img-fluid mb-2" style="max-height: 200px;"><br>
                <?php endif; ?>
                <label class="btn btn-primary btn-md btn-file">
                  Telusuri File<input type="file" name="job-image" id="job-image" hidden>
                </label>
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($job['email'] ?? ''); ?>" 
                       placeholder="you@yourdomain.com">
              </div>
              <div class="form-group">
                <label for="job-title">Judul Pekerjaan</label>
                <input type="text" class="form-control" id="job-title" name="job-title" 
                       value="<?php echo htmlspecialchars($job['judul_pekerjaan']); ?>" 
                       placeholder="Product Designer" required>
              </div>
              <div class="form-group">
                <label for="job-location">Lokasi</label>
                <input type="text" class="form-control" id="job-location" name="job-location" 
                       value="<?php echo htmlspecialchars($job['lokasi']); ?>" 
                       placeholder="e.g. New York" required>
              </div>

              <div class="form-group">
                <label for="job-region">Wilayah Pekerjaan</label>
                <select class="selectpicker border rounded" id="job-region" name="job-region" 
                        data-style="btn-black" data-width="100%" data-live-search="true" title="Select Region" required>
                  <option value="Dimana Saja" <?php echo ($job['wilayah'] == 'Dimana Saja') ? 'selected' : ''; ?>>Dimana Saja</option>
                  <option value="Jakarta" <?php echo ($job['wilayah'] == 'Jakarta') ? 'selected' : ''; ?>>Jakarta</option>
                  <option value="Surabaya" <?php echo ($job['wilayah'] == 'Surabaya') ? 'selected' : ''; ?>>Surabaya</option>
                  <option value="Bogor" <?php echo ($job['wilayah'] == 'Bogor') ? 'selected' : ''; ?>>Bogor</option>
                  <option value="Bandung" <?php echo ($job['wilayah'] == 'Bandung') ? 'selected' : ''; ?>>Bandung</option>
                  <option value="Medan" <?php echo ($job['wilayah'] == 'Medan') ? 'selected' : ''; ?>>Medan</option>
                  <option value="Batam" <?php echo ($job['wilayah'] == 'Batam') ? 'selected' : ''; ?>>Batam</option>
                  <option value="Yogyakarta" <?php echo ($job['wilayah'] == 'Yogyakarta') ? 'selected' : ''; ?>>Yogyakarta</option>
                  <option value="Semarang" <?php echo ($job['wilayah'] == 'Semarang') ? 'selected' : ''; ?>>Semarang</option>
                </select>
              </div>

              <div class="form-group">
                <label for="job-type">Tipe Pekerjaan</label>
                <select class="selectpicker border rounded" id="job-type" name="job-type" 
                        data-style="btn-black" data-width="100%" data-live-search="true" title="Select Job Type" required>
                  <option value="Part Time" <?php echo ($job['tipe_pekerjaan'] == 'Part Time') ? 'selected' : ''; ?>>Part Time</option>
                  <option value="Full Time" <?php echo ($job['tipe_pekerjaan'] == 'Full Time') ? 'selected' : ''; ?>>Full Time</option>
                </select>
              </div>

              <div class="form-group">
                <label for="job-description">Deskripsi Pekerjaan</label>
                <textarea class="form-control" id="job-description" name="job-description" rows="10" required><?php echo htmlspecialchars($job['deskripsi']); ?></textarea>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <footer class="site-footer">
      <a href="#top" class="smoothscroll scroll-top">
        <span class="icon-keyboard_arrow_up"></span>
      </a>

      <div class="container">
        <div class="row mb-5">
          <div class="col-6 col-md-3 mb-4 mb-md-0">
            <h3>Pencarian Populer</h3>
            <ul class="list-unstyled">
              <li><a href="#">Web Design</a></li>
              <li><a href="#">Graphic Design</a></li>
              <li><a href="#">Web Developers</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-3 mb-4 mb-md-0">
            <h3>Perusahaan</h3>
            <ul class="list-unstyled">
              <li><a href="#">Tentang Kami</a></li>
              <li><a href="#">Karir</a></li>
              <li><a href="#">Blog</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-3 mb-4 mb-md-0">
            <h3>Dukungan</h3>
            <ul class="list-unstyled">
              <li><a href="#">Bantuan</a></li>
              <li><a href="#">Privasi</a></li>
              <li><a href="#">Syarat & Ketentuan</a></li>
            </ul>
          </div>
          <div class="col-6 col-md-3 mb-4 mb-md-0">
            <h3>Hubungi Kami</h3>
            <div class="footer-social">
              <a href="#"><span class="icon-facebook"></span></a>
              <a href="#"><span class="icon-twitter"></span></a>
              <a href="#"><span class="icon-instagram"></span></a>
              <a href="#"><span class="icon-linkedin"></span></a>
            </div>
          </div>
        </div>

        <div class="row text-center">
          <div class="col-12">
            <p class="copyright"><small>
              Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | LokerNow
            </small></p>
          </div>
        </div>
      </div>
    </footer>
  </div>

  <!-- SCRIPTS -->
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
</body>
</html>