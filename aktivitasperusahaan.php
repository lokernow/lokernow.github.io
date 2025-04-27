<?php
session_start();
require_once 'backend/db.php';

// Check if company is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.html");
    exit();
}

$id_perusahaan = $_SESSION['user_id'];

// Get company profile data
$companyProfile = [];
$query = "SELECT * FROM company_profiles WHERE id_perusahaan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_perusahaan);
$stmt->execute();
$result = $stmt->get_result();
$companyProfile = $result->fetch_assoc();
$stmt->close();

// Get job listings data
$jobs = [];
$query = "SELECT * FROM lowongan_kerja WHERE id_perusahaan = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_perusahaan);
$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);
$totalJobs = count($jobs);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>LokerNow | Aktivitas Perusahaan</title>
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
          <div class="site-logo col-6"><img src="images/logo.png" alt="" width="80vw"></div>
          <nav class="mx-auto site-navigation">
            <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
              <li><a href="berandaperusahaan.php">Beranda</a></li>
              <li><a class="nav-link active" href="aktivitasperusahaan.php">Aktivitas</a></li>
              <li><a href="profilperusahaan.php">Profil</a></li>
              <li class="d-lg-none"><a href="post-job.php"><span class="mr-2">+</span> Tambah Loker</a></li>
              <li class="d-lg-none"><a href="backend/logout.php">Log Out</a></li>
            </ul>
          </nav>
          <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
            <div class="ml-auto">
              <a href="post-job.php" class="btn btn-outline-white border-width-2 d-none d-lg-inline-block"><span class="mr-2 icon-add"></span>Tambah Loker</a>
              <a href="backend/logout.php" class="btn btn-primary border-width-2 d-none d-lg-inline-block"><span class="mr-2 icon-lock_outline"></span>Log Out</a>
            </div>
            <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3"><span class="icon-menu h3 m-0 p-0 mt-2"></span></a>
          </div>
        </div>
      </div>
    </header>

      <!-- HOME -->
      <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg')" id="home-section">
      <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-<?php echo $_SESSION['message_type']; ?> text-center">
        <?php echo $_SESSION['message']; ?>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
      </div>
    <?php endif; ?>
    
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-3 text-center">
              <img src="<?php echo !empty($companyProfile['logo']) ? 'backend/uploads/logos/'.$companyProfile['logo'] : 'images/pixell-design-logo-perusahaan-pt-cahayabakti.jpg'; ?>" alt="Logo Perusahaan" width="150vw" />
            </div>
            <div class="col-md-9">
              <p style="margin: 0;font-size: 24px; font-weight: bold; color: white">
                <?php echo htmlspecialchars($companyProfile['nama_perusahaan'] ?? 'PT. Cahaya Bakti Sentosoraya'); ?>
              </p>
              <p style="color: white">
                <?php echo htmlspecialchars($companyProfile['deskripsi'] ?? 'Deskripsi perusahaan belum ditambahkan'); ?>
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="site-section">
        <div class="container">
          <div class="row mb-5 justify-content-center">
            <div class="col-md-7 text-center">
              <h2 class="section-title mb-2">Lowongan Yang Ditampilkan</h2>
            </div>
          </div>

          <?php if ($totalJobs > 0): ?>
          <ul class="job-listings mb-5">
            <?php foreach ($jobs as $job): ?>
            <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
              <a href="pelamarperusahaan.php?id=<?php echo $job['id']; ?>"></a>
              <div class="job-listing-logo">
              <img src="<?php echo !empty($job['gambar']) ? 'backend/uploads/' . htmlspecialchars($job['gambar']) : 'images/pixell-design-logo-perusahaan-pt-cahayabakti.jpg'; ?>" alt="<?php echo htmlspecialchars($job['nama_perusahaan']); ?>" class="img-fluid">
              </div>

              <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                  <h2><?php echo htmlspecialchars($job['judul_pekerjaan']); ?></h2>
                  <strong><?php echo htmlspecialchars($job['nama_perusahaan']); ?></strong>
                </div>
                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                  <span class="icon-room"></span> <?php echo htmlspecialchars($job['lokasi']); ?>
                </div>
                <div class="job-listing-meta">
                  <span class="badge badge-success">Edit<a href="edit-job.php?id=<?php echo $job['id']; ?>"></a></span>
                </div>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>

          <div class="row pagination-wrap">
            <div class="col-md-6 text-center text-md-left mb-4 mb-md-0">
              <span>Menampilkan 1-<?php echo min(7, $totalJobs); ?> dari <?php echo $totalJobs; ?> Lowongan</span>
            </div>
            <div class="col-md-6 text-center text-md-right">
              <div class="custom-pagination ml-auto">
                <a href="#" class="prev">Sebelumnya</a>
                <div class="d-inline-block">
                  <a href="#" class="active">1</a>
                  <?php if ($totalJobs > 7): ?>
                  <a href="#">2</a>
                  <a href="#">3</a>
                  <a href="#">4</a>
                  <?php endif; ?>
                </div>
                <a href="#" class="next">Selanjutnya</a>
              </div>
            </div>
          </div>
          <?php else: ?>
          <div class="alert alert-info">
            Belum ada lowongan yang ditambahkan. <a href="post-job.php" class="alert-link">Tambahkan lowongan pertama Anda</a>.
          </div>
          <?php endif; ?>
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