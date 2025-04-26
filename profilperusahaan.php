<?php
session_start();
require_once 'backend/db.php';

$profile = [];
$galleries = [];

if (isset($_SESSION['user_id'])) {
    $id_perusahaan = $_SESSION['user_id'];

    // Ambil data profil perusahaan berdasarkan id_perusahaan
    $stmt = $conn->prepare("SELECT * FROM company_profiles WHERE id_perusahaan = ?");
    $stmt->bind_param("i", $id_perusahaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $stmt->close();

    // Ambil dan decode galeri
    $galeri_json = $profile['galeri_perusahaan'] ?? '[]'; // fallback jika null
    $galleries = json_decode($galeri_json, true);

    // Pastikan hasil decode adalah array
    if (!is_array($galleries)) {
        $galleries = [];
    }
}
?>




<!doctype html>
<html lang="en">
  <head>
    <title>LokerNow | Profil Perusahaan</title>
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
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
  </head>
  <body id="top">

  <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-<?php echo $_SESSION['message_type']; ?> text-center">
        <?php echo $_SESSION['message']; ?>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
      </div>
    <?php endif; ?>

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
    </div> <!-- .site-mobile-menu -->
    

    
    <!-- NAVBAR -->
    <header class="site-navbar mt-3">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="site-logo col-6"><img src="images/logo.png" alt="" width="80vw"></div>
          <nav class="mx-auto site-navigation">
            <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
              <li><a class="nav-link active" href="berandaperusahaan.php">Beranda</a></li>
              <li><a href="aktivitasperusahaan.php">Aktivitas</a></li>
              <li><a href="profilperusahaan.php">Profil</a></li>
              <li class="d-lg-none"><a href="post-job.php"><span class="mr-2">+</span> Tambah Loker</a></li>
              <li class="d-lg-none"><a href="backend/logout.php">Log Out</a></li>
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
        <div class="row mb-5">
          <div class="col-lg-12">
            <form class="p-4 p-md-5 border rounded" method="post" action="backend/profil_action.php" enctype="multipart/form-data">
              <h3 class="text-black mb-5 border-bottom pb-2">Detail Profil Anda</h3>
              
              <div class="form-group">
                <?php if (!empty($profile['logo'])): ?>
                  <img src="backend/uploads/logos/<?php echo htmlspecialchars($profile['logo']); ?>" class="rounded-circle" alt="Logo Perusahaan" width="100"><br>
                <?php else: ?>
                  <img src="images/perusahaan.jpg" class="rounded-circle" alt="Logo Default" width="100"><br>
                <?php endif; ?>
                <label class="mt-2 btn btn-primary btn-sm btn-file">
                  Upload Logo Perusahaan<input type="file" name="logo" hidden>
                </label>
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" 
                       value="<?php echo isset($profile['email']) ? htmlspecialchars($profile['email']) : ''; ?>" 
                       placeholder="you@yourdomain.com" required>
              </div>

              <div class="form-group">
                <label for="nomortelepon">Nomor Telepon</label>
                <input type="text" class="form-control" id="nomortelepon" name="nomortelepon"
                       value="<?php echo isset($profile['nomor_telepon']) ? htmlspecialchars($profile['nomor_telepon']) : ''; ?>" 
                       placeholder="08xxx-xxxx-xxxx">
              </div>

              <div class="form-group">
                <label for="namaperusahaan">Nama Perusahaan</label>
                <input type="text" class="form-control" id="namaperusahaan" name="namaperusahaan"
                       value="<?php echo isset($profile['nama_perusahaan']) ? htmlspecialchars($profile['nama_perusahaan']) : ''; ?>" 
                       placeholder="Nama Perusahaan" required>
              </div>

              <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <select class="selectpicker border rounded" id="lokasi" name="lokasi" data-style="btn-black" data-width="100%" data-live-search="true" title="Pilih Lokasimu">
                  <?php
                  $locations = [
                    'Ambon, Maluku', 'Banda Aceh, Aceh', 'Bandung, Jawa Barat', 'Banjarmasin, Kalimantan Selatan',
                    'Batam, Kepulauan Riau', 'Bengkulu, Bengkulu', 'Denpasar, Bali', 'Gorontalo, Gorontalo',
                    'Jakarta, DKI Jakarta', 'Jambi, Jambi', 'Jayapura, Papua', 'Kendari, Sulawesi Tenggara',
                    'Kupang, Nusa Tenggara Timur', 'Makassar, Sulawesi Selatan', 'Manado, Sulawesi Utara',
                    'Manokwari, Papua Barat', 'Mamuju, Sulawesi Barat', 'Mataram, Nusa Tenggara Barat',
                    'Medan, Sumatera Utara', 'Palangka Raya, Kalimantan Tengah', 'Palembang, Sumatera Selatan',
                    'Palu, Sulawesi Tengah', 'Pangkal Pinang, Kepulauan Bangka Belitung', 'Pekanbaru, Riau',
                    'Pontianak, Kalimantan Barat', 'Samarinda, Kalimantan Timur', 'Semarang, Jawa Tengah',
                    'Serang, Banten', 'Surabaya, Jawa Timur', 'Tanjung Pinang, Kepulauan Riau',
                    'Tanjung Selor, Kalimantan Utara', 'Ternate, Maluku Utara', 'Yogyakarta, DI Yogyakarta'
                  ];
                  
                  foreach ($locations as $location) {
                      $selected = (isset($profile['lokasi']) && $profile['lokasi'] == $location) ? 'selected' : '';
                      echo "<option value=\"$location\" $selected>$location</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label for="deskripsiperusahaan">Deskripsi Perusahaan</label>
                <textarea class="form-control" id="deskripsiperusahaan" name="deskripsiperusahaan" 
                          placeholder="Ceritakan tentang perusahaan anda"><?php echo isset($profile['deskripsi']) ? htmlspecialchars($profile['deskripsi']) : ''; ?></textarea>
              </div>

              <div class="form-group">
                <label for="kulturperusahaan">Kultur Perusahaan</label>
                <textarea class="form-control" id="kulturperusahaan" name="kulturperusahaan" 
                          placeholder="Ceritakan tentang kultur atau visi & misi perusahaan anda"><?php echo isset($profile['kultur']) ? htmlspecialchars($profile['kultur']) : ''; ?></textarea>
              </div>

              <div class="form-group">
  <label for="galeriperusahaan">Galeri Perusahaan</label><br>

  <?php if (!empty($galleries)): ?>
    <div class="mb-3">
      <h6>Galeri Saat Ini:</h6>
      <div class="d-flex flex-wrap">
        <?php foreach ($galleries as $gallery): ?>
          <div class="mr-2 mb-2">
            <img src="backend/uploads/galleries/<?php echo htmlspecialchars($gallery); ?>" width="100" class="img-thumbnail">
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <label class="mt-2 btn btn-primary btn-sm btn-file">
    Upload Galeri Perusahaan
    <input type="file" name="galeriperusahaan[]" multiple hidden>
  </label>
  <small class="form-text text-muted">Anda bisa memilih lebih dari satu file</small>
</div>

              
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-dark btn-lg">Simpan</button>
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
              <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart text-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank" >Colorlib</a>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></small></p>
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