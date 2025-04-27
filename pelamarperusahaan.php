<?php
session_start();
require_once 'backend/db.php';

// Check if job ID is provided
if (!isset($_GET['id'])) {
    header("Location: aktivitasperusahaan.php");
    exit();
}

$job_id = $_GET['id'];
$id_perusahaan = $_SESSION['user_id'] ?? null;

// Get job details
$job = [];
$applicants = [];
$companyProfile = [];

try {
    // Get job details
    $query = "SELECT l.*, c.nama_perusahaan, c.logo 
              FROM lowongan_kerja l
              JOIN company_profiles c ON l.id_perusahaan = c.id_perusahaan
              WHERE l.id = ? AND l.id_perusahaan = ?";
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

   // Get applicants for this job
   $stmt = $conn->prepare("SELECT l.*, p.nama_lengkap, p.foto_profil 
                        FROM lamaran l
                        JOIN profile_pelamar p ON l.pelamar_id = p.user_id
                        WHERE l.lowongan_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$applicants = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

    


    // Get company profile
    $query = "SELECT * FROM company_profiles WHERE id_perusahaan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_perusahaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $companyProfile = $result->fetch_assoc();
    $stmt->close();

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Terjadi kesalahan saat mengambil data";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>LokerNow | Detail Lowongan</title>
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
                <li><a class="nav-link active" href="aktivitasperusahaan.php">Aktivitas</a></li>
                <li><a href="profilperusahaan.php">Profil</a></li>
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
      <!-- HOME -->
      <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg')" id="home-section">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <h1 class="text-white font-weight-bold"><?php echo htmlspecialchars($job['judul_pekerjaan']); ?></h1>
            </div>
          </div>
        </div>
      </section>

      

      <section class="site-section">
        <div class="container">
          <div class="row align-items-center mb-5">
            <div class="col-lg-8 mb-4 mb-lg-0">
              <div class="d-flex align-items-center">
                <div class="border p-2 d-inline-block mr-3 rounded">
                  <img
                    src="<?php echo !empty($companyProfile['logo']) ? 'backend/uploads/logos/'.$companyProfile['logo'] : 'images/pixell-design-logo-perusahaan-pt-cahayabakti.jpg'; ?>"
                    alt="Logo Perusahaan"
                    width="150vw"
                  />
                </div>
                <div>
                  <h2><?php echo htmlspecialchars($job['judul_pekerjaan']); ?></h2>
                  <div>
                    <span class="ml-0 mr-2 mb-2">
                      <span class="icon-briefcase mr-2"></span>
                      <?php echo htmlspecialchars($job['nama_perusahaan']); ?>
                    </span>
                    <span class="m-2">
                      <span class="icon-room mr-2"></span>
                      <?php echo htmlspecialchars($job['lokasi']); ?>
                    </span>
                    <span class="m-2">
                      <span class="icon-clock-o mr-2"></span>
                      <span class="text-primary"><?php echo htmlspecialchars($job['tipe_pekerjaan']); ?></span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="row">
                <div class="col-6">
                  <a href="edit-job.php?id=<?php echo $job['id']; ?>" class="btn btn-block btn-primary btn-md">Edit</a>
                  <!-- Button trigger modal -->
                  <button type="button" class="btn btn-block btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#hapus">
                    Hapus
                  </button>

                  <!-- Modal -->
                  <div data-bs-backdrop="false" class="modal fade" id="hapus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="exampleModalLabel">Hapus Loker</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Anda yakin ingin menghapus lowongan pekerjaan ini?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <a href="delete-job.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">Hapus</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Akhir Modal -->
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-8">
              <div class="mb-5">
                <?php if (!empty($job['gambar'])): ?>
                <figure class="mb-5">
                <img src="<?php echo !empty($job['gambar']) ? 'backend/uploads/' . htmlspecialchars($job['gambar']) : 'images/pixell-design-logo-perusahaan-pt-cahayabakti.jpg'; ?>" alt="<?php echo htmlspecialchars($job['nama_perusahaan']); ?>" class="img-fluid">
                </figure>
                <?php endif; ?>
                <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                  <span class="icon-align-left mr-3"></span>Deskripsi
                </h3>
                <p>
                  <?php echo nl2br(htmlspecialchars($job['deskripsi'])); ?>
                </p>
              </div>
              
              <!-- You can add more sections here for responsibilities, qualifications, etc. -->
              <!-- These would need additional fields in your database table -->

            </div>
            <div class="col-lg-4">
              <div class="bg-light p-3 border rounded mb-4">
                <h3 class="text-primary mt-3 h5 pl-3 mb-3">
                  Ringkasan pekerjaan
                </h3>
                <ul class="list-unstyled pl-3 mb-0">
                  <li class="mb-2">
                    <strong class="text-black">Dipublis:</strong> 
                    <?php echo date('d F Y', strtotime($job['created_at'])); ?>
                  </li>
                  <li class="mb-2">
                    <strong class="text-black">Tipe Pekerjaan:</strong>
                    <?php echo htmlspecialchars($job['tipe_pekerjaan']); ?>
                  </li>
                  <li class="mb-2">
                    <strong class="text-black">Lokasi:</strong> 
                    <?php echo htmlspecialchars($job['lokasi']); ?>
                  </li>
                  <!-- Add more job details as needed -->
                </ul>
              </div>

              <div class="bg-light p-3 border rounded">
                <h3 class="text-primary mt-3 h5 pl-3 mb-3">Share</h3>
                <div class="px-3">
                  <a href="#" class="pt-3 pb-3 pr-3 pl-0"><span class="icon-facebook"></span></a>
                  <a href="#" class="pt-3 pb-3 pr-3 pl-0"><span class="icon-twitter"></span></a>
                  <a href="#" class="pt-3 pb-3 pr-3 pl-0"><span class="icon-linkedin"></span></a>
                  <a href="#" class="pt-3 pb-3 pr-3 pl-0"><span class="icon-pinterest"></span></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <?php if (!empty($applicants)): ?>
        
      <section class="site-section" id="next">
        <div class="container">
          <div class="row mb-5 justify-content-center">
            <div class="col-md-7 text-center">
              <h2 class="section-title mb-2">Profil Pelamar</h2>
            </div>
          </div>

          <ul class="job-listings mb-5">
            <?php foreach ($applicants as $applicant): ?>
            <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
              <a href="profillamaran.php?id=<?php echo $applicant['id']; ?>"></a>
              <div class="job-listing-logo">
                <img src="<?php echo !empty($applicant['foto_profil']) ? htmlspecialchars($applicant['foto_profil']) : 'images/jay.jpg'; ?>" class="rounded-circle p-1" alt="" width="80vw"/>
              </div>

              <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                  <h2><?php echo htmlspecialchars($job['judul_pekerjaan']); ?></h2>
                  <strong><?php echo htmlspecialchars($applicant['nama_lengkap']); ?></strong>
                </div>
                <div class="job-listing-meta">
                    <span class="badge badge-success">see detail<a href="profillamaran.php?id=<?php echo $applicant['id']; ?>">  </a></span>
                </div>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>

          <div class="row pagination-wrap">
            <div class="col-md-6 text-center text-md-left mb-4 mb-md-0">
              <span>Showing 1-<?php echo min(7, count($applicants)); ?> Of <?php echo count($applicants); ?> Applicants</span>
            </div>
            <div class="col-md-6 text-center text-md-right">
              <div class="custom-pagination ml-auto">
                <a href="#" class="prev">Prev</a>
                <div class="d-inline-block">
                  <a href="#" class="active">1</a>
                  <?php if (count($applicants) > 7): ?>
                  <a href="#">2</a>
                  <a href="#">3</a>
                  <a href="#">4</a>
                  <?php endif; ?>
                </div>
                <a href="#" class="next">Next</a>
              </div>
            </div>
          </div>
        </div>
      </section>
      <?php endif; ?>

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
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>