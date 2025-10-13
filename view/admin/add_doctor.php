<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

$popup = ""; // placeholder for SweetAlert script

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $specialization = trim($_POST['specialization']);
    $credentials = trim($_POST['credentials']);
    $password = trim($_POST['password']);
    $schedules = $_POST['schedule'];
    $schedule = implode(", ", array_filter($schedules));

    // Check for duplicate email
    $check = $conn->query("SELECT * FROM doctors WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $popup = "
        <script>
        Swal.fire({
          icon: 'error',
          title: 'Duplicate Email',
          text: 'That email already exists. Please use another.',
          background: '#fdfdfd',
          backdrop: 'rgba(0,0,0,0.4)',
          confirmButtonColor: '#0088a9'
        });
        </script>";
    } else {
        $sql = "INSERT INTO doctors (name, email, specialization, schedule, credentials, password)
                VALUES ('$name', '$email', '$specialization', '$schedule', '$credentials', '$password')";

        if ($conn->query($sql)) {
            $popup = "
            <script>
            Swal.fire({
              icon: 'success',
              title: 'Doctor Added!',
              text: 'The doctor has been successfully registered.',
              background: '#fdfdfd',
              backdrop: 'rgba(0,0,0,0.4)',
              confirmButtonColor: '#0088a9',
              showClass: { popup: 'animate__animated animate__fadeInDown' },
              hideClass: { popup: 'animate__animated animate__fadeOutUp' }
            }).then(() => {
              window.location.href = 'manage_doctors.php';
            });
            </script>";
        } else {
            $popup = "
            <script>
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Something went wrong while saving the doctor.',
              background: '#fdfdfd',
              backdrop: 'rgba(0,0,0,0.4)',
              confirmButtonColor: '#0088a9'
            });
            </script>";
        }
    }
}
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Add Doctor</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Add Doctor</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Doctor Information</h5>

              <!-- Add Doctor Form -->
              <form method="POST" action="">

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Full Name</label>
                  <div class="col-sm-9">
                    <input type="text" name="name" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Email</label>
                  <div class="col-sm-9">
                    <input type="email" name="email" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Specialization</label>
                  <div class="col-sm-9">
                    <input type="text" name="specialization" class="form-control" required>
                  </div>
                </div>

                <!-- Dynamic Schedule Fields -->
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Schedule</label>
                  <div class="col-sm-9">
                    <div id="scheduleContainer">
                      <div class="input-group mb-2">
                        <input type="text" name="schedule[]" class="form-control" placeholder="e.g. Mon–Fri 9AM–5PM">
                        <button type="button" class="btn btn-success add-sched-btn"><i class="bi bi-plus-lg"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Credentials</label>
                  <div class="col-sm-9">
                    <textarea name="credentials" class="form-control" rows="3" placeholder="PRC License, etc."></textarea>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Password</label>
                  <div class="col-sm-9">
                    <input type="text" name="password" class="form-control" required>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" name="submit" class="btn btn-primary" style="background-color:#0088a9; border:none;">Save Doctor</button>
                  <button type="reset" class="btn btn-secondary">Clear</button>
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

<!-- ✅ SweetAlert and Animate CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

<!-- ✅ Display SweetAlert -->
<?php echo $popup; ?>
