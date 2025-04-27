<?php
require_once 'backend/db.php';

// Fetch recommended jobs from database
// You can modify this query to implement your recommendation logic
// For now, we'll just get the most recent jobs
$sql = "SELECT * FROM lowongan_kerja ORDER BY created_at DESC LIMIT 7";
$result = $conn->query($sql);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM lowongan_kerja";
$count_result = $conn->query($count_sql);
$total_jobs = $count_result->fetch_assoc()['total'];

$conn->close();
?>
<!doctype html>
<html lang="en">
  <head>
    <title>LokerNow | Rekomendasi</title>
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
    <link rel="stylesheet" href="css/style.css">

    <style>
      .fixed-logo {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px; /* Opsional, buat sudut rounded */
      }

    </style>
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
            <div class="site-logo col-6">
              <img src="images/logo.png" alt="" width="80vw" />
            </div>

            <nav class="mx-auto site-navigation">
              <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                <li><a href="berandapelamar.php">Beranda</a></li>
                <li><a class="nav-link active" href="rekomendasipelamar.php">Rekomendasi</a></li>
                <li class="has-children">
                  <a>Aktivitas</a>
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


    <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg');" id="home-section">
      <div class="container">
        <div class="row">
          <div class="col-md-7">
            <h1 class="text-white font-weight-bold">Ada Rekomendasi Loker nih Untukmu!</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="site-section">
      <div class="container">
        <?php if ($result->num_rows > 0): ?>
          <ul class="job-listings mb-5">
            <?php while($job = $result->fetch_assoc()): ?>
            <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
              <a href="job-single.php?id=<?= $job['id'] ?>"></a>
              <div class="job-listing-logo">
                <img src="backend/uploads/<?= htmlspecialchars($job['gambar']) ?>" alt="<?= htmlspecialchars($job['nama_perusahaan']) ?>" class="img-fluid fixed-logo">
              </div>
              <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                  <h2><?= htmlspecialchars($job['judul_pekerjaan']) ?></h2>
                  <strong><?= htmlspecialchars($job['nama_perusahaan']) ?></strong>
                </div>
                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                  <span class="icon-room"></span> <?= htmlspecialchars($job['lokasi']) ?>
                </div>
                <div class="job-listing-meta">
                  <span class="badge badge-<?= $job['tipe_pekerjaan'] == 'Full Time' ? 'success' : 'danger' ?>">
                    <?= htmlspecialchars($job['tipe_pekerjaan']) ?>
                  </span>
                </div>
              </div>
            </li>
            <?php endwhile; ?>
          </ul>
        <?php else: ?>
          <div class="alert alert-info">Belum ada lowongan rekomendasi saat ini.</div>
        <?php endif; ?>

        <div class="row pagination-wrap">
          <div class="col-md-6 text-center text-md-left mb-4 mb-md-0">
            <span>Showing 1-<?= $result->num_rows ?> Of <?= $total_jobs ?> Jobs</span>
          </div>
          <div class="col-md-6 text-center text-md-right">
            <div class="custom-pagination ml-auto">
              <a href="#" class="prev">Prev</a>
              <div class="d-inline-block">
                <a href="#" class="active">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">4</a>
              </div>
              <a href="#" class="next">Next</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <footer class="site-footer">
      <a href="#top" class="smoothscroll scroll-top">
        <span class="icon-keyboard_arrow_up"></span>
      </a>

      <div class="container">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>