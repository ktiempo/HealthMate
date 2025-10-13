<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

if (!isset($_GET['id'])) {
  header("Location: manage_doctors.php");
  exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM doctors WHERE doctor_id = $id");
if ($result->num_rows === 0) {
  echo "<script>alert('❌ Doctor not found!'); window.location.href='manage_doctors.php';</script>";
  exit();
}
$doctor = $result->fetch_assoc();
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edit Doctor</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item"><a href="manage_doctors.php">Doctors</a></li>
        <li class="breadcrumb-item active">Edit Doctor</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Update Doctor Information</h5>

              <!-- Edit Doctor Form -->
              <form method="POST" action="../../controller/update_doctor.php">
                <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Full Name</label>
                  <div class="col-sm-9">
                    <input type="text" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Email</label>
                  <div class="col-sm-9">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Specialization</label>
                  <div class="col-sm-9">
                    <input type="text" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" class="form-control" required>
                  </div>
                </div>

                <!-- Dynamic Schedule Fields -->
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Schedule</label>
                  <div class="col-sm-9">
                    <div id="scheduleContainer">
                      <?php
                      $schedules = explode(',', $doctor['schedule']);
                      foreach ($schedules as $index => $sched) {
                        $sched = trim($sched);
                        $btn = $index === 0
                          ? '<button type="button" class="btn btn-success add-sched-btn"><i class="bi bi-plus-lg"></i></button>'
                          : '<button type="button" class="btn btn-danger remove-sched-btn"><i class="bi bi-dash-lg"></i></button>';
                        echo "
                          <div class='input-group mb-2'>
                            <input type='text' name='schedule[]' class='form-control' value='".htmlspecialchars($sched)."' placeholder='e.g. Mon–Fri 9AM–5PM'>
                            $btn
                          </div>
                        ";
                      }
                      ?>
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Credentials</label>
                  <div class="col-sm-9">
                    <textarea name="credentials" class="form-control" rows="3"><?php echo htmlspecialchars($doctor['credentials']); ?></textarea>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Password</label>
                  <div class="col-sm-9">
                    <input type="text" name="password" value="<?php echo htmlspecialchars($doctor['password']); ?>" class="form-control" required>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" name="update" class="btn btn-primary" style="background-color:#0088a9; border:none;">Update</button>
                  <a href="manage_doctors.php" class="btn btn-secondary">Cancel</a>
                </div>
              </form><!-- End form -->

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include('includes/footer.php'); ?>

<!-- ✅ Dynamic Schedule JS -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("scheduleContainer");

  container.addEventListener("click", function (e) {
    if (e.target.classList.contains("add-sched-btn") || e.target.closest(".add-sched-btn")) {
      const newRow = document.createElement("div");
      newRow.classList.add("input-group", "mb-2");
      newRow.innerHTML = `
        <input type="text" name="schedule[]" class="form-control" placeholder="e.g. Sat 8AM–12PM">
        <button type="button" class="btn btn-danger remove-sched-btn"><i class="bi bi-dash-lg"></i></button>
      `;
      container.appendChild(newRow);
    }

    if (e.target.classList.contains("remove-sched-btn") || e.target.closest(".remove-sched-btn")) {
      e.target.closest(".input-group").remove();
    }
  });
});
</script>

<style>
  .add-sched-btn {
    background-color: #0088a9 !important;
    border: none !important;
    color: #fff;
  }
  .remove-sched-btn {
    background-color: #dc3545 !important;
    border: none !important;
    color: #fff;
  }
  .add-sched-btn:hover,
  .remove-sched-btn:hover {
    opacity: 0.9;
  }
  .card {
    width: 100%;
  }
</style>
