<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
  header("Location: doctor_login.php");
  exit;
}

include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
?>

<main id="main" class="main">

  <!-- Page Title -->
  <div class="pagetitle">
    <h1>Doctor Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->

  <section class="section dashboard">
    <div class="row">

      <!-- Welcome Message -->
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center py-4">
            <h5 class="card-title mb-1">Welcome, Dr. <?php echo htmlspecialchars($_SESSION['doctor_name']); ?> 👨‍⚕️</h5>
            <p class="text-muted mb-0">Manage your patients, view appointments, and check your schedule here.</p>
          </div>
        </div>
      </div>

      <!-- Total Patients -->
      <div class="col-md-4">
        <div class="card info-card patients-card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="card-title text-muted text-center">Total Patients</h6>
            <?php
            $doctor_id = $_SESSION['doctor_id'];
            $patients = $conn->query("SELECT COUNT(*) AS total FROM patients WHERE doctor_id = $doctor_id");
            $p = $patients->fetch_assoc();
            ?>
            <div class="d-flex align-items-center justify-content-center mt-2">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-25">
                <i class="bi bi-people fs-4 text-primary"></i>
              </div>
              <h3 class="ms-3 mb-0 text-primary fw-bold"><?php echo $p['total']; ?></h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Appointments -->
      <div class="col-md-4">
        <div class="card info-card appointments-card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="card-title text-muted text-center">Upcoming Appointments</h6>
            <?php
            $appointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = $doctor_id AND status IN ('Pending','Confirmed')");
            $a = $appointments->fetch_assoc();
            ?>
            <div class="d-flex align-items-center justify-content-center mt-2">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-25">
                <i class="bi bi-calendar-check fs-4 text-success"></i>
              </div>
              <h3 class="ms-3 mb-0 text-success fw-bold"><?php echo $a['total']; ?></h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Completed Appointments -->
      <div class="col-md-4">
        <div class="card info-card completed-card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="card-title text-muted text-center">Completed Appointments</h6>
            <?php
            $completed = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = $doctor_id AND status = 'Completed'");
            $c = $completed->fetch_assoc();
            ?>
            <div class="d-flex align-items-center justify-content-center mt-2">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-25">
                <i class="bi bi-check-circle fs-4 text-info"></i>
              </div>
              <h3 class="ms-3 mb-0 text-info fw-bold"><?php echo $c['total']; ?></h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Schedule -->
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title"><i class="bi bi-clock-history text-primary me-1"></i> Upcoming Schedule</h5>
            <?php
            $sched = $conn->query("SELECT day_of_week, start_time, end_time FROM doctor_slots WHERE doctor_id = $doctor_id ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
            if ($sched->num_rows > 0): ?>
              <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                  <thead class="table-primary">
                    <tr>
                      <th>Day</th>
                      <th>Start Time</th>
                      <th>End Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $sched->fetch_assoc()): ?>
                      <tr>
                        <td><strong><?php echo $row['day_of_week']; ?></strong></td>
                        <td><?php echo date('h:i A', strtotime($row['start_time'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['end_time'])); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted text-center mb-0">No schedule added yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </section>

</main><!-- End #main -->

<?php include('includes/footer.php'); ?>

<style>
  .pagetitle h1 {
    color: #004b63;
    font-weight: 600;
  }

  .info-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    background-color: #ffffff;
  }

  .info-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }

  .card-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
  }

  .table-primary th {
    background-color: #0088a9 !important;
    color: #fff;
  }

  .text-primary {
    color: #0088a9 !important;
  }

  .text-success {
    color: #4CAF50 !important;
  }

  .text-info {
    color: #00BCD4 !important;
  }

  .card-title {
    color: #004b63;
    font-weight: 600;
  }
</style>
