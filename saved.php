<?php
require_once 'backend/db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get saved jobs for the current user
$sql = "SELECT l.* FROM lowongan_kerja l 
        JOIN simpan_lamaran s ON l.id = s.lowongan_id 
        WHERE s.pelamar_id = ? 
        ORDER BY s.tanggal_simpan DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saved_jobs = $stmt->get_result();

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM simpan_lamaran WHERE pelamar_id = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$total_jobs = $count_stmt->get_result()->fetch_assoc()['total'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>LokerNow | Lowongan yang disimpan</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

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
              <h1 class="text-white font-weight-bold">Loker yang anda simpan</h1>
            </div>
          </div>
        </div>
      </section>

      <section class="site-section">
        <div class="container">
          <?php if ($saved_jobs->num_rows > 0): ?>
            <ul class="job-listings mb-5">
              <?php while($job = $saved_jobs->fetch_assoc()): ?>
              <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                <a href="job-single.php?id=<?= $job['id'] ?>"></a>
                <div class="job-listing-logo">
                  <img src="backend/uploads/<?= htmlspecialchars($job['gambar']) ?>" alt="<?= htmlspecialchars($job['nama_perusahaan']) ?>" class="img-fluid">
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
                    <span class="fa-solid fa-heart text-danger"></span> Disimpan
                  </div>
                </div>
              </li>
              <?php endwhile; ?>
            </ul>
          <?php else: ?>
            <div class="alert alert-info">
              Anda belum menyimpan lowongan pekerjaan.
            </div>
          <?php endif; ?>

          <div class="row pagination-wrap">
            <div class="col-md-6 text-center text-md-left mb-4 mb-md-0">
              <span>Showing <?= min(1, $total_jobs) ?>-<?= min(7, $total_jobs) ?> Of <?= $total_jobs ?> Jobs</span>
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
        <!-- [FOOTER CONTENT REMAINS THE SAME] -->
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
    <script>
      // Add click handler for unsave action
      document.querySelectorAll('.job-listing').forEach(job => {
        job.addEventListener('click', function(e) {
          if (e.target.classList.contains('fa-heart')) {
            e.preventDefault();
            e.stopPropagation();
            
            const jobId = this.querySelector('a').href.split('id=')[1];
            fetch('backend/unsave.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `lowongan_id=${jobId}`
            })
            .then(response => response.json())
            .then(data => {
              if (data.status === 'success') {
                this.remove();
                // Update job count
                const countText = document.querySelector('.pagination-wrap span');
                const currentCount = parseInt(countText.textContent.match(/Of (\d+)/)[1]);
                countText.textContent = countText.textContent.replace(/Of \d+/, `Of ${currentCount - 1}`);
              }
            });
          }
        });
      });
    </script>
  </body>
</html>