<?php
require_once 'backend/db.php';

// Initialize search parameters
$keyword = $_GET['keyword'] ?? '';
$lokasi = $_GET['lokasi'] ?? '';
$tipe = $_GET['tipe'] ?? '';

// Prepare base query with parameterized statements
$sql = "SELECT * FROM lowongan_kerja WHERE 1=1";
$params = [];
$types = '';

// Add filters if provided
if (!empty($keyword)) {
    $sql .= " AND (judul_pekerjaan LIKE CONCAT('%', ?, '%') 
                OR nama_perusahaan LIKE CONCAT('%', ?, '%')
                OR lokasi LIKE CONCAT('%', ?, '%'))";
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $types .= 'sss';
}

if (!empty($lokasi)) {
    $sql .= " AND lokasi = ?";
    $params[] = $lokasi;
    $types .= 's';
}

if (!empty($tipe)) {
    $sql .= " AND tipe_pekerjaan = ?";
    $params[] = $tipe;
    $types .= 's';
}

$sql .= " ORDER BY created_at DESC LIMIT 7";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
  <head>
    <title>LokerNow | Beranda</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" href="ftco-32x32.png">
    <link rel="stylesheet" href="css/custom-bs.css">
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="fonts/line-icons/style.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/animate.min.css">
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

    <!-- Navbar -->
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
                <a href="logout.php" class="btn btn-primary border-width-2 d-none d-lg-inline-block">
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



    <!-- Hero Section -->
    <section class="home-section section-hero overlay bg-image" style="background-image: url('images/hero_1.jpg');" id="home-section">
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-12">
            <div class="mb-5 text-center">
              <h1 class="text-white font-weight-bold">Cara Tergampang Untuk Wujudkan Mimpimu</h1>
              <p>LokerNow siap membantumu untuk mencari pekerjaan dimanapun, kapanpun.</p>
            </div>
            <form method="GET" action="berandapelamar.php" class="search-jobs-form">
              <div class="row mb-5">
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                  <input type="text" name="keyword" class="form-control form-control-lg" placeholder="Nama Pekerjaan, Perusahaan..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                  <select name="lokasi" class="selectpicker" data-style="btn-white btn-lg" data-width="100%" data-live-search="true" title="Pilih Lokasi">
                    <option value="">-- Semua Lokasi --</option>
                    <?php
                     $lokasi_list = [
                      "Ambon", "Banda Aceh", "Bandung", "Banjarmasin", "Batam", "Bengkulu", "Denpasar", "Gorontalo",
                      "Jakarta", "Jambi", "Jayapura", "Kendari", "Kupang", "Makassar", "Manado", "Manokwari",
                      "Mamuju", "Mataram", "Medan", "Palangka Raya", "Palembang", "Palu", "Pangkal Pinang",
                      "Pekanbaru", "Pontianak", "Samarinda", "Semarang", "Serang", "Surabaya", "Tanjung Pinang",
                      "Tanjung Selor", "Ternate", "Yogyakarta"
                    ];
                    
                      foreach ($lokasi_list as $lok) {
                        $selected = ($lokasi == $lok) ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($lok) . "\" $selected>" . htmlspecialchars($lok) . "</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                  <select name="tipe" class="selectpicker" data-style="btn-white btn-lg" data-width="100%" data-live-search="true" title="Pilih Tipe Pekerjaan">
                    <option value="">-- Semua Tipe --</option>
                    <option value="Part Time" <?= ($tipe == 'Part Time') ? 'selected' : '' ?>>Part Time</option>
                    <option value="Full Time" <?= ($tipe == 'Full Time') ? 'selected' : '' ?>>Full Time</option>
                  </select>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                  <button type="submit" class="btn btn-primary btn-lg btn-block text-white btn-search">
                    <span class="icon-search icon mr-2"></span>Cari Pekerjaan
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- List Lowongan -->
    <section class="site-section">
      <div class="container">
        <div class="row mb-5 justify-content-center">
          <div class="col-md-7 text-center">
            <h2 class="section-title mb-2">Lowongan Pekerjaan Tersedia</h2>
          </div>
        </div>

        <?php
        if ($result->num_rows > 0) {
          echo "<ul class='job-listings mb-5'>";
          while($row = $result->fetch_assoc()) {
            $job_title = htmlspecialchars($row['judul_pekerjaan']);
            $company = htmlspecialchars($row['nama_perusahaan']);
            $location = htmlspecialchars($row['lokasi']);
            $job_type = htmlspecialchars($row['tipe_pekerjaan']);
            $image = htmlspecialchars($row['gambar']);
            $id = htmlspecialchars($row['id']);
            
            echo "
            <li class='job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center'>
              <a href='job-single.php?id=$id'></a>
              <div class='job-listing-logo'>
                <img src='backend/uploads/$image' class='img-fluid fixed-logo' alt='$company Logo'>
              </div>
              <div class='job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4'>
                <div class='job-listing-position custom-width w-50 mb-3 mb-sm-0'>
                  <h2>$job_title</h2>
                  <strong>$company</strong>
                </div>
                <div class='job-listing-location mb-3 mb-sm-0 custom-width w-25'>
                  <span class='icon-room'></span> $location
                </div>
                <div class='job-listing-meta'>
                  <span class='badge badge-" . ($job_type == 'Full Time' ? 'success' : 'danger') . "'>$job_type</span>
                </div>
              </div>
            </li>";
          }
          echo "</ul>";
        } else {
          echo "<div class='alert alert-info'>Belum ada lowongan tersedia.</div>";
        }
        
        $stmt->close();
        $conn->close();
        ?>
      </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
      <div class="container">
        <div class="row text-center">
          <div class="col-md-12">
            <p>&copy; 2025 LokerNow. All rights reserved.</p>
          </div>
        </div>
      </div>
    </footer>

  </div> <!-- .site-wrap -->

  <!-- Scripts -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.fancybox.min.js"></script>
  <script src="js/bootstrap-select.min.js"></script>
  <script src="js/custom.js"></script>

  </body>
</html>