<?php
session_start();
include('../../db/config.php');
?>

<?php include('includes/header.php'); ?>
<?php include('includes/topbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="pagetitle mt-4">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->
    <div class="col-lg-8">
      <div class="row">

        <!-- Total Doctors Card -->
        <div class="col-xxl-4 col-md-6">
          <div class="card info-card sales-card shadow-sm border-0">
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
                      $row = $result->fetch_assoc();
                      echo $row['total'];
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Total</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Doctors Card -->

        <!-- Total Patients Card -->
        <div class="col-xxl-4 col-md-6">
          <div class="card info-card revenue-card shadow-sm border-0">
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
                      $row = $result->fetch_assoc();
                      echo $row['total'];
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Total</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Patients Card -->

        <!-- Total Appointments Card -->
        <div class="col-xxl-4 col-xl-12">
          <div class="card info-card customers-card shadow-sm border-0">
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
                      $row = $result->fetch_assoc();
                      echo $row['total'];
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Total</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Appointments Card -->

      </div>
    </div><!-- End Left side columns -->

  </div>
</section>

<style>
  .pagetitle h1 {
    color: #004b63;
    font-weight: 600;
  }
  .breadcrumb a {
    color: #0088a9;
    text-decoration: none;
  }
  .info-card {
    transition: transform 0.2s ease-in-out;
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
  }
</style>

<?php include('includes/footer.php'); ?>
