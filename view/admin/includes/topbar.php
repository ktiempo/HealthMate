<!-- ======= Header / Topbar ======= -->
<header id="header" class="header fixed-top d-flex align-items-center" style="background-color:#0088a9;">

  <div class="d-flex align-items-center justify-content-between">
    <a href="dashboard.php" class="logo d-flex align-items-center" style="text-decoration:none;">
      <i class="bi bi-heart-pulse-fill" style="color:white; font-size:1.8rem; margin-right:8px;"></i>
      <span class="d-none d-lg-block" style="color:white; font-weight:600; font-size:1.4rem;">HealthMate</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn" style="color:white; font-size:1.6rem;"></i>
  </div><!-- End Logo -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <!-- ===== User Profile ===== -->
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="/healthmate/assets/img/profile-img.jpg" alt="Profile" class="rounded-circle" style="width:35px; height:35px;">
          <span class="d-none d-md-block dropdown-toggle ps-2 text-white">Admin</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header text-center">
            <h6>Admin</h6>
            <span>System Administrator</span>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="../../controller/logout.php">
              <i class="bi bi-box-arrow-right"></i>
              <span>Sign Out</span>
            </a>
          </li>
        </ul>
      </li><!-- End Profile -->

    </ul>
  </nav><!-- End Icons Navigation -->

</header><!-- End Header -->
