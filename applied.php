<?php
require_once 'backend/db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$pelamar_id = $_SESSION['user_id'];

// Query to get all applications for this user with job details
$query = "SELECT l.*, lo.judul_pekerjaan AS lowongan_judul, lo.lokasi, pe.nama_perusahaan, pe.logo
          FROM lamaran l
          JOIN lowongan_kerja lo ON l.lowongan_id = lo.id
          JOIN company_profiles pe ON lo.id_perusahaan = pe.id
          WHERE l.pelamar_id = ?
          ORDER BY l.tanggal_lamaran DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pelamar_id);
$stmt->execute();
$result = $stmt->get_result();
$lamaran = $result->fetch_all(MYSQLI_ASSOC);
$total_lamaran = count($lamaran);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>LokerNow | Lamaran</title>
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
      <!-- NAVBAR (same as before) -->
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
                  <a class="nav-link active">Aktivitas</a>
                  <ul class="dropdown">
                    <li><a href="applied.php">Lamaran</a></li>
                    <li><a href="saved.php">Disimpan</a></li>
                  </ul>
                </li>
                <li><a href="profilpelamar.php">Profil</a></li>
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

      <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg')" id="home-section">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <h1 class="text-white font-weight-bold">Loker yang sudah anda lamar</h1>
              <div class="custom-breadcrumbs">
                <span class="text-white"><strong><?= $total_lamaran ?> lamaran</strong></span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="site-section">
        <div class="container">
          <?php if ($total_lamaran > 0): ?>
            <ul class="job-listings mb-5">
              <?php foreach ($lamaran as $l): ?>
                <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                  <a href="applieddetail.php?id=<?= $l['id'] ?>"></a>
                  <div class="job-listing-logo">
                    <img src="<?= !empty($l['logo']) ? 'backend/uploads/logos/'.$l['logo'] : 'images/job_logo_1.jpg' ?>" 
                         alt="<?= $l['nama_perusahaan'] ?>" class="img-fluid" />
                  </div>

                  <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                    <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                      <h2><?= htmlspecialchars($l['lowongan_judul']) ?></h2>
                      <strong><?= htmlspecialchars($l['nama_perusahaan']) ?></strong>
                    </div>
                    <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                      <span class="icon-room"></span> <?= htmlspecialchars($l['lokasi']) ?>
                    </div>
                    <div class="job-listing-meta">
                      <?php 
                        $badge_class = '';
                        switch($l['status']) {
                          case 'dikirim': $badge_class = 'badge-info'; break;
                          case 'diproses': $badge_class = 'badge-warning'; break;
                          case 'diterima': $badge_class = 'badge-success'; break;
                          case 'ditolak': $badge_class = 'badge-danger'; break;
                          default: $badge_class = 'badge-secondary';
                        }
                      ?>
                      <span class="badge <?= $badge_class ?>">
                        <?= ucfirst($l['status']) ?>
                      </span>
                      <small class="text-muted d-block mt-1">
                        <?= date('d M Y', strtotime($l['tanggal_lamaran'])) ?>
                      </small>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>

            <div class="row pagination-wrap">
              <div class="col-md-6 text-center text-md-left mb-4 mb-md-0">
                <span>Showing 1-<?= $total_lamaran ?> of <?= $total_lamaran ?> Jobs</span>
              </div>
              <div class="col-md-6 text-center text-md-right">
                <div class="custom-pagination ml-auto">
                  <a href="#" class="prev disabled">Prev</a>
                  <div class="d-inline-block">
                    <a href="#" class="active">1</a>
                  </div>
                  <a href="#" class="next disabled">Next</a>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="alert alert-info">
              <p>Anda belum melamar pekerjaan apapun. <a href="rekomendasipelamar.html">Cari lowongan</a> untuk melamar.</p>
            </div>
          <?php endif; ?>
        </div>
      </section>
      <!-- AKHIR CARD PEKERJAAN -->

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
              <p class="copyright">
                <small>
                  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                  Copyright &copy;
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  All rights reserved | This template is made with
                  <i class="icon-heart text-danger" aria-hidden="true"></i> by
                  <a href="https://colorlib.com" target="_blank">Colorlib</a>
                  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></small
                >
              </p>
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
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
