<?php
require_once 'backend/db.php';
session_start();
  // Check if job ID is provided
  if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
      header("Location: berandapelamar.php");
      exit();
  }

  $job_id = $_GET['id'];
  
  $user_id = $_SESSION['user_id'];

  // Prepare and execute query to get job details
  $stmt = $conn->prepare("SELECT * FROM lowongan_kerja WHERE id = ?");
  $stmt->bind_param("i", $job_id);
  $stmt->execute();
  $result = $stmt->get_result();

  // Check if job exists
  if ($result->num_rows === 0) {
      header("Location: berandapelamar.php");
      exit();
  }

  $job = $result->fetch_assoc();

  // Get related jobs (excluding current job)
  $related_stmt = $conn->prepare("SELECT * FROM lowongan_kerja WHERE id != ? ORDER BY created_at DESC LIMIT 3");
  $related_stmt->bind_param("i", $job_id);
  $related_stmt->execute();
  $related_jobs = $related_stmt->get_result();

  $sudah_lamar = false;
  $stmt = $conn->prepare("SELECT id FROM lamaran WHERE pelamar_id = ? AND lowongan_id = ?");
  $stmt->bind_param("ii", $user_id, $job_id);
  $stmt->execute();
  if ($stmt->get_result()->num_rows > 0) {
      $sudah_lamar = true;
  }

  // Cek apakah lowongan sudah disimpan
  $sudah_disimpan = false;
  $stmt = $conn->prepare("SELECT id FROM simpan_lamaran WHERE pelamar_id = ? AND lowongan_id = ?");
  $stmt->bind_param("ii", $user_id, $job_id);
  $stmt->execute();
  if ($stmt->get_result()->num_rows > 0) {
      $sudah_disimpan = true;
  }


$conn->close();
?>
<!doctype html>
<html lang="en">
  <head>
    <title>LokerNow | <?= htmlspecialchars($job['judul_pekerjaan']) ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link rel="stylesheet" href="css/custom-bs.css">
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="fonts/line-icons/style.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/animate.min.css">
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


    <!-- HERO SECTION -->
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg');" id="home-section">
      <div class="container">
        <div class="row">
          <div class="col-md-7">
            <h1 class="text-white font-weight-bold"><?= htmlspecialchars($job['judul_pekerjaan']) ?></h1>
          </div>
        </div>
      </div>
    </section>

    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success">
          <?= $_SESSION['success_message'] ?>
          <?php unset($_SESSION['success_message']); ?>
      </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger">
          <?= $_SESSION['error_message'] ?>
          <?php unset($_SESSION['error_message']); ?>
      </div>
  <?php endif; ?>

    <!-- JOB DETAILS -->
    <section class="site-section">
      <div class="container">
        <div class="row align-items-center mb-5">
          <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="d-flex align-items-center">
              <div class="border p-2 d-inline-block mr-3 rounded">
              <img src="backend/uploads/<?= htmlspecialchars($job['gambar']) ?>" alt="<?= htmlspecialchars($job['nama_perusahaan']) ?> Logo" style="width: 100px; height: auto;">
              </div>
              <div>
                <h2><?= htmlspecialchars($job['judul_pekerjaan']) ?></h2>
                <div>
                  <span class="ml-0 mr-2 mb-2"><span class="icon-briefcase mr-2"></span><?= htmlspecialchars($job['id']) ?></span>
                  <span class="ml-0 mr-2 mb-2"><span class="icon-briefcase mr-2"></span><?= htmlspecialchars($job['nama_perusahaan']) ?></span>
                  <span class="m-2"><span class="icon-room mr-2"></span><?= htmlspecialchars($job['lokasi']) ?></span>
                  <span class="m-2"><span class="icon-clock-o mr-2"></span><span class="text-primary"><?= htmlspecialchars($job['tipe_pekerjaan']) ?></span></span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
              <div class="row">
                  <div class="col-6">
                  <form method="post" action="backend/simpan.php">
                  <input type="hidden" name="id" value="<?= $job_id ?>">
                      <?php if ($sudah_disimpan): ?>
                          <button type="submit" name="simpan" class="btn btn-block btn-danger btn-md">
                              <span class="icon-heart mr-2"></span>Disimpan
                          </button>
                      <?php else: ?>
                          <button type="submit" name="simpan" class="btn btn-block btn-light btn-md">
                              <span class="icon-heart-o mr-2 text-danger"></span>Simpan
                          </button>
                      <?php endif; ?>
                  </form>

                  </div>
                  <div class="col-6">
                      <?php if ($sudah_lamar): ?>
                          <button class="btn btn-block btn-success btn-md" disabled>
                              <span class="icon-check mr-2"></span>Sudah Dilamar
                          </button>
                      <?php else: ?>
                          <button type="button" class="btn btn-block btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#lamarcepat">
                              Lamar Cepat
                          </button>
                      <?php endif; ?>

                      <!-- APPLICATION MODAL -->
                      <div data-bs-backdrop="false" class="modal fade" id="lamarcepat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                              <div class="modal-content">
                                  <form method="post" action="backend/lamar.php?id=<?= $job_id ?>">
                                  <input type="hidden" name="id" value="<?= $job_id ?>">
                                      <div class="modal-header">
                                          <h1 class="modal-title fs-5" id="exampleModalLabel">Lamar Cepat</h1>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                          Kirim lamaran anda sekarang untuk posisi <?= htmlspecialchars($job['judul_pekerjaan']) ?> di <?= htmlspecialchars($job['nama_perusahaan']) ?>?
                                          <div class="form-group mt-3">
                                              <label for="surat_lamaran">Surat Lamaran (Opsional)</label>
                                              <textarea class="form-control" id="surat_lamaran" name="surat_lamaran" rows="3"></textarea>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                          <button type="submit" name="lamar" class="btn btn-primary">Kirim Lamaran</button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-lg-8">
            <div class="mb-5">
              <?php if (!empty($job['gambar'])): ?>
              <figure class="mb-5">
                <img src="backend/uploads/<?= htmlspecialchars($job['gambar']) ?>" alt="<?= htmlspecialchars($job['judul_pekerjaan']) ?>" class="img-fluid rounded">
              </figure>
              <?php endif; ?>
              
              <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                <span class="icon-align-left mr-3"></span>Deskripsi
              </h3>
              <p><?= nl2br(htmlspecialchars($job['deskripsi'])) ?></p>
              <p><?= nl2br(htmlspecialchars($sudah_disimpan)) ?></p>
            </div>
            
            <?php if (!empty($job['tanggung_jawab'])): ?>
            <div class="mb-5">
              <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                <span class="icon-rocket mr-3"></span>Tanggung Jawab
              </h3>
              <ul class="list-unstyled m-0 p-0">
                <?php 
                $responsibilities = explode("\n", $job['tanggung_jawab']);
                foreach ($responsibilities as $item): 
                  if (!empty(trim($item))):
                ?>
                <li class="d-flex align-items-start mb-2">
                  <span class="icon-check_circle mr-2 text-muted"></span>
                  <span><?= htmlspecialchars(trim($item)) ?></span>
                </li>
                <?php 
                  endif;
                endforeach; 
                ?>
              </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($job['kualifikasi'])): ?>
            <div class="mb-5">
              <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                <span class="icon-book mr-3"></span>Kualifikasi
              </h3>
              <ul class="list-unstyled m-0 p-0">
                <?php 
                $qualifications = explode("\n", $job['kualifikasi']);
                foreach ($qualifications as $item): 
                  if (!empty(trim($item))):
                ?>
                <li class="d-flex align-items-start mb-2">
                  <span class="icon-check_circle mr-2 text-muted"></span>
                  <span><?= htmlspecialchars(trim($item)) ?></span>
                </li>
                <?php 
                  endif;
                endforeach; 
                ?>
              </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($job['manfaat'])): ?>
            <div class="mb-5">
              <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                <span class="icon-turned_in mr-3"></span>Manfaat dan Keuntungan
              </h3>
              <ul class="list-unstyled m-0 p-0">
                <?php 
                $benefits = explode("\n", $job['manfaat']);
                foreach ($benefits as $item): 
                  if (!empty(trim($item))):
                ?>
                <li class="d-flex align-items-start mb-2">
                  <span class="icon-check_circle mr-2 text-muted"></span>
                  <span><?= htmlspecialchars(trim($item)) ?></span>
                </li>
                <?php 
                  endif;
                endforeach; 
                ?>
              </ul>
            </div>
            <?php endif; ?>
          </div>
          
          <div class="col-lg-4">
            <div class="bg-light p-3 border rounded mb-4">
              <h3 class="text-primary mt-3 h5 pl-3 mb-3">Ringkasan pekerjaan</h3>
              <ul class="list-unstyled pl-3 mb-0">
                <li class="mb-2">
                  <strong class="text-black">Dipublis:</strong> <?= date('d F Y', strtotime($job['created_at'])) ?>
                </li>
                <li class="mb-2">
                  <strong class="text-black">Status:</strong> <?= htmlspecialchars($job['tipe_pekerjaan']) ?>
                </li>
                <li class="mb-2">
                  <strong class="text-black">Lokasi:</strong> <?= htmlspecialchars($job['lokasi']) ?>
                </li>
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

    <!-- RELATED JOBS -->
    <section class="site-section" id="next">
      <div class="container">
        <div class="row mb-5 justify-content-center">
          <div class="col-md-7 text-center">
            <h2 class="section-title mb-2">Lowongan Terkait</h2>
          </div>
        </div>
        
        <ul class="job-listings mb-5">
          <?php if ($related_jobs->num_rows > 0): ?>
            <?php while($related = $related_jobs->fetch_assoc()): ?>
            <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
              <a href="job-single.php?id=<?= $related['id'] ?>"></a>
              <div class="job-listing-logo">
                <img src="backend/uploads/<?= htmlspecialchars($related['gambar']) ?>" alt="<?= htmlspecialchars($related['nama_perusahaan']) ?>" class="img-fluid">
              </div>

              <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                  <h2><?= htmlspecialchars($related['judul_pekerjaan']) ?></h2>
                  <strong><?= htmlspecialchars($related['nama_perusahaan']) ?></strong>
                </div>
                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                  <span class="icon-room"></span> <?= htmlspecialchars($related['lokasi']) ?>
                </div>
                <div class="job-listing-meta">
                  <span class="badge badge-<?= $related['tipe_pekerjaan'] == 'Full Time' ? 'success' : 'danger' ?>"><?= htmlspecialchars($related['tipe_pekerjaan']) ?></span>
                </div>
              </div>
            </li>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="alert alert-info">Tidak ada lowongan terkait saat ini.</div>
          <?php endif; ?>
        </ul>
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
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>