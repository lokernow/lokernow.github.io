<?php
require_once 'backend/db.php';
session_start();

// Check if user is logged in (assuming this is for company view)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Get lamaran ID from URL
$lamaran_id = $_GET['id'] ?? null;
if (!$lamaran_id) {
    header("Location: aktivitasperusahaan.html");
    exit();
}

// Get application details with pelamar and lowongan info
$query = "SELECT l.*, 
                 pp.nama_lengkap, pp.email, pp.nomor_telepon, pp.lokasi, 
                 pp.deskripsi_personal, pp.foto_profil, pp.resume,
                 lo.judul_pekerjaan AS judul_lowongan, pe.nama_perusahaan
          FROM lamaran l
          JOIN profile_pelamar pp ON l.pelamar_id = pp.user_id
          JOIN lowongan_kerja lo ON l.lowongan_id = lo.id
          JOIN company_profiles pe ON lo.id_perusahaan = pe.id
          WHERE l.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lamaran_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: aktivitasperusahaan.html");
    exit();
}

$lamaran = $result->fetch_assoc();

// Get pendidikan data
$query_pendidikan = "SELECT * FROM pendidikan_pelamar WHERE pelamar_id = ? ORDER BY tahun_selesai DESC";
$stmt_pendidikan = $conn->prepare($query_pendidikan);
$stmt_pendidikan->bind_param("i", $lamaran['pelamar_id']);
$stmt_pendidikan->execute();
$pendidikan = $stmt_pendidikan->get_result()->fetch_all(MYSQLI_ASSOC);

// Get pengalaman kerja data
$query_pengalaman = "SELECT * FROM pengalaman_pelamar WHERE pelamar_id = ? ORDER BY tanggal_mulai DESC";
$stmt_pengalaman = $conn->prepare($query_pengalaman);
$stmt_pengalaman->bind_param("i", $lamaran['pelamar_id']);
$stmt_pengalaman->execute();
$pengalaman = $stmt_pengalaman->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $update_query = "UPDATE lamaran SET status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("si", $status, $lamaran_id);
    $stmt_update->execute();
    
    // Update the status in our local variable
    $lamaran['status'] = $status;
    
    // Show success message
    $success_message = "Status lamaran berhasil diperbarui!";
}
?>
<!DOCTYPE html>
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
    <div id="overlayer"></div>
    <div class="loader">
      <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>

    <div class="site-wrap">
      <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
          <div class="site-mobile-menu-close mt-3">
            <span class="icon-close2 js-menu-toggle"></span>
          </div>
        </div>
        <div class="site-mobile-menu-body"></div>
      </div>
      <!-- .site-mobile-menu -->

      <!-- NAVBAR -->
      <header class="site-navbar mt-3">
        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="site-logo col-6">
              <img src="images/logo.png" alt="" width="80vw" />
            </div>
            <nav class="mx-auto site-navigation">
              <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                <li><a href="berandaperusahaan.php">Beranda</a></li>
                <li><a  href="aktivitasperusahaan.php">Aktivitas</a></li>
                <li><a class="nav-link active" href="profilperusahaan.php">Profil</a></li>
                <li class="d-lg-none"><a href="post-job.php"><span class="mr-2">+</span> Tambah Loker</a></li>
                <li class="d-lg-none"><a href="backend/logout.php">Log Out</a></li>
              </ul>
            </nav>
            <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
              <div class="ml-auto">
                <a href="post-job.php" class="btn btn-outline-white border-width-2 d-none d-lg-inline-block">
                  <span class="mr-2 icon-add"></span>Tambah Loker
                </a>
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
              <h1 class="text-white font-weight-bold">Profil Pelamar</h1>
              <div class="custom-breadcrumbs">
                <a href="berandaperusahaan.html">Beranda</a> 
                <span class="mx-2 slash">/</span>
                <a href="aktivitasperusahaan.html">Aktivitas</a>
                <span class="mx-2 slash">/</span>
                <span class="text-white"><strong>Profil Pelamar</strong></span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="site-section">
        <div class="container">
          <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
              <?= $success_message ?>
            </div>
          <?php endif; ?>
          
          <div class="row mb-5">
            <div class="col-lg-12">
              <form class="p-4 p-md-5 border rounded" method="post">
                <h3 class="text-black mb-5 border-bottom pb-2">Detail Profil</h3>
                
                <div class="form-group">
                  <img src="<?= !empty($lamaran['foto_profil']) ? $lamaran['foto_profil'] : 'images/jay.jpg' ?>" 
                       class="rounded-circle" alt="Foto Profil" width="100vw"><br>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <p><?= htmlspecialchars($lamaran['email']) ?></p>
                </div>
                
                <div class="form-group">
                  <label for="nomortelepon">Nomor Telepon</label>
                  <p><?= !empty($lamaran['nomor_telepon']) ? htmlspecialchars($lamaran['nomor_telepon']) : '-' ?></p>
                </div>
                
                <div class="form-group">
                  <label for="namalengkap">Nama Lengkap</label>
                  <p><?= htmlspecialchars($lamaran['nama_lengkap']) ?></p>
                </div>
                
                <div class="form-group">
                  <label for="lokasi">Lokasi</label>
                  <p><?= !empty($lamaran['lokasi']) ? htmlspecialchars($lamaran['lokasi']) : '-' ?></p>
                </div>

                <div class="form-group">
                  <label for="deskripsipersonal">Deskripsi Personal</label>
                  <p><?= !empty($lamaran['deskripsi_personal']) ? nl2br(htmlspecialchars($lamaran['deskripsi_personal'])) : '-' ?></p>
                </div>

                <div class="form-group">
                  <label for="pendidikan">Pendidikan</label><br>
                  <?php if (!empty($pendidikan)): ?>
                    <?php foreach ($pendidikan as $edu): ?>
                      <p>
                        <?= htmlspecialchars($edu['tingkat_pendidikan']) ?> <?= htmlspecialchars($edu['bidang_studi']) ?><br>
                        <?= htmlspecialchars($edu['nama_institusi']) ?> (<?= htmlspecialchars($edu['tahun_selesai']) ?>)
                      </p>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p>-</p>
                  <?php endif; ?>
                </div>       

                <div class="form-group">
                  <label for="pengalamankerja">Pengalaman Kerja</label><br>
                  <?php if (!empty($pengalaman)): ?>
                    <?php foreach ($pengalaman as $exp): ?>
                      <p>
                        <?= htmlspecialchars($exp['nama_pekerjaan']) ?> - <?= htmlspecialchars($exp['nama_perusahaan']) ?><br>
                        <?= date('F Y', strtotime($exp['tanggal_mulai'])) ?> - 
                        <?= $exp['tanggal_selesai'] ? 'Sekarang' : date('F Y', strtotime($exp['tanggal_selesai'])) ?>
                      </p>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p>-</p>
                  <?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="job-location">Resume</label><br>
                  <?php if (!empty($lamaran['resume'])): ?>
                    <a href="<?= $lamaran['resume'] ?>" class="btn btn-primary btn-sm" target="_blank">
                      Download Resume
                    </a>
                  <?php else: ?>
                    <p>Tidak ada resume</p>
                  <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-end mb-3">
                  <select class="form-select form-select-lg" name="status">
                    <option value="dikirim" <?= $lamaran['status'] == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                    <option value="diproses" <?= $lamaran['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="diterima" <?= $lamaran['status'] == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                    <option value="ditolak" <?= $lamaran['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                  </select>
                </div>
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-dark btn-lg">Update Status</button>
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
              <h3>Search Trending</h3>
              <ul class="list-unstyled">
                <li><a href="#">Web Design</a></li>
                <li><a href="#">Graphic Design</a></li>
                <li><a href="#">Web Developers</a></li>
                <li><a href="#">Python</a></li>
                <li><a href="#">HTML5</a></li>
                <li><a href="#">CSS3</a></li>
              </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
              <h3>Company</h3>
              <ul class="list-unstyled">
                <li><a href="#">About Us</a></li>
                <li><a href="#">Career</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Resources</a></li>
              </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
              <h3>Support</h3>
              <ul class="list-unstyled">
                <li><a href="#">Support</a></li>
                <li><a href="#">Privacy</a></li>
                <li><a href="#">Terms of Service</a></li>
              </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
              <h3>Contact Us</h3>
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
                Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart text-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
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