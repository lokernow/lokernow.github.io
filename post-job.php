<!doctype html>
<html lang="en">
  <head>
    <title>LokerNow | Tambah Lowongan Kerja</title>
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
  
    <!-- HOME -->
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('images/hero_1.jpg');" id="home-section">
      <div class="container">
        <div class="row">
          <div class="col-md-7">
            <h1 class="text-white font-weight-bold">Tambah Lowongan Kerja</h1>
          </div>
        </div>
      </div>
    </section>

    
    <section class="site-section">
      <div class="container">
        <div class="row align-items-center mb-5">
          <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="d-flex align-items-center">
              <div>
                <h2>Tambah Loker</h2>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="row">
              <div class="col-6">
                <a href="#" class="btn btn-block btn-light btn-md"><span class="icon-open_in_new mr-2"></span>Tinjau</a>
              </div>
              <div class="col-6">
                <button type="submit" form="jobForm" class="btn btn-block btn-primary btn-md">Simpan</button>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-5">
          <div class="col-lg-12">
            <form class="p-4 p-md-5 border rounded" method="post" action="backend/simpan_lowongan.php" enctype="multipart/form-data" id="jobForm">
              <h3 class="text-black mb-5 border-bottom pb-2">Detail Pekerjaan</h3>
              
              <div class="form-group">
                <label for="company-image">Unggah Gambar</label> <br>
                <label class="btn btn-primary btn-md btn-file">
                  Telusuri File<input type="file" name="gambar" id="company-image" hidden>
                </label>
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="you@yourdomain.com" required>
              </div>

              <div class="form-group">
                <label for="company-name">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="form-control" id="company-name" placeholder="Nama Perusahaan Anda" required>
              </div>

              <div class="form-group">
                <label for="job-title">Judul Pekerjaan</label>
                <input type="text" name="judul_pekerjaan" class="form-control" id="job-title" placeholder="Product Designer" required>
              </div>

              <div class="form-group">
                <label for="job-location">Lokasi</label>
                <input type="text" name="lokasi" class="form-control" id="job-location" placeholder="e.g. New York" required>
              </div>

              <div class="form-group">
                <label for="job-region">Wilayah Pekerjaan</label>
                <select class="selectpicker border rounded" id="job-region" name="wilayah" data-style="btn-black" data-width="100%" data-live-search="true" title="Select Region" required>
                  <option>Dimana Saja</option>
                  <option>Jakarta</option>
                  <option>Surabaya</option>
                  <option>Bogor</option>
                  <option>Bandung</option>
                  <option>Medan</option>
                  <option>Batam</option>
                  <option>Yogyakarta</option>
                  <option>Semarang</option>
                </select>
              </div>

              <div class="form-group">
                <label for="job-type">Tipe Perkerjaan</label>
                <select class="selectpicker border rounded" id="job-type" name="tipe_pekerjaan" data-style="btn-black" data-width="100%" data-live-search="true" title="Select Job Type" required>
                  <option value="Part Time">Part Time</option>
                  <option value="Full Time">Full Time</option>
                </select>
              </div>

              <div class="form-group">
                <label for="job-description">Deskripsi Pekerjaan</label>
                <textarea name="deskripsi" id="job-description" class="form-control" rows="10" placeholder="Tulis Deskripsi Pekerjaanmu!" required></textarea>
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