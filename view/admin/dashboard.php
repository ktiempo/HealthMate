<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Admin Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <!-- Dashboard Section -->
  <section class="section dashboard">
    <div class="container-fluid">
      <div class="row g-4">

        <!-- Doctors Card -->
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card info-card h-100 shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Doctors <span>| Registered</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-person-badge" style="color:#0088a9;"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                      $result = $conn->query("SELECT COUNT(*) AS total FROM doctors");
                      echo $result->fetch_assoc()['total'];
                    ?>
                  </h6>
                  <span class="text-muted small pt-2 ps-1">Total Doctors</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Doctors Card -->

        <!-- Patients Card -->
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card info-card h-100 shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Patients <span>| Registered</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people" style="color:#0088a9;"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                      $result = $conn->query("SELECT COUNT(*) AS total FROM patients");
                      echo $result->fetch_assoc()['total'];
                    ?>
                  </h6>
                  <span class="text-muted small pt-2 ps-1">Total Patients</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Patients Card -->

        <!-- Appointments Card -->
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card info-card h-100 shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Appointments <span>| Total</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-calendar-check" style="color:#0088a9;"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                      $result = $conn->query("SELECT COUNT(*) AS total FROM appointments");
                      echo $result->fetch_assoc()['total'];
                    ?>
                  </h6>
                  <span class="text-muted small pt-2 ps-1">Total Appointments</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Appointments Card -->

      </div><!-- End Row -->
    </div><!-- End Container -->
  </section>

</main><!-- End #main -->

<?php include('includes/footer.php'); ?>

<style>
  /* ✅ Fix for topbar overlap */
  body {
    padding-top: 70px;
  }

  /* ✅ Dashboard styling */
  .info-card {
    transition: transform 0.2s ease-in-out;
    background: #fff;
    border-radius: 10px;
  }

  .info-card:hover {
    transform: translateY(-5px);
  }

  .card-title {
    font-weight: 600;
    color: #004b63;
  }

  .card-icon {
    background: #e6f4f7;
    width: 50px;
    height: 50px;
    font-size: 1.5rem;
    border-radius: 50%;
  }

  @media (max-width: 767px) {
    .pagetitle h1 {
      font-size: 1.25rem;
    }
    .info-card {
      text-align: center;
    }
    .card-icon {
      margin: 0 auto 10px;
    }
    .d-flex.align-items-center {
      flex-direction: column;
    }
  }
</style>
