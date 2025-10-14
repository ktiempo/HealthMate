<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

$popup = "";

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $specialization = trim($_POST['specialization']);
    $credentials = trim($_POST['credentials']);
    $password = trim($_POST['password']);

    // Weekly schedule arrays
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_slots = $_POST['total_slots'];

    // Check if doctor already exists
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
        // Insert doctor info
        $sql = "INSERT INTO doctors (name, email, specialization, credentials, password)
                VALUES ('$name', '$email', '$specialization', '$credentials', '$password')";

        if ($conn->query($sql)) {
            $doctor_id = $conn->insert_id;

            // Insert weekly schedule slots
            for ($i = 0; $i < count($day_of_week); $i++) {
                if (!empty($day_of_week[$i]) && !empty($start_time[$i]) && !empty($end_time[$i])) {
                    $day = $day_of_week[$i];
                    $start = $start_time[$i];
                    $end = $end_time[$i];
                    $slots = $total_slots[$i] ?: 5;
                    $conn->query("INSERT INTO doctor_slots (doctor_id, day_of_week, start_time, end_time, total_slots)
                                  VALUES ('$doctor_id', '$day', '$start', '$end', '$slots')");
                }
            }

            $popup = "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Doctor Added!',
                text: 'Doctor and weekly schedule successfully added.',
                background: '#fdfdfd',
                backdrop: 'rgba(0,0,0,0.4)',
                confirmButtonColor: '#0088a9'
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
        <div class="col-lg-10">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Doctor Information</h5>

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

                <hr>
                <h5 class="card-title">Weekly Schedule</h5>

                <div id="timeSlotContainer">
                  <div class="row mb-3 slot-row align-items-center">
                    <div class="col-md-3">
                      <select name="day_of_week[]" class="form-select" required>
                        <option value="" disabled selected>Day</option>
                        <option>Monday</option>
                        <option>Tuesday</option>
                        <option>Wednesday</option>
                        <option>Thursday</option>
                        <option>Friday</option>
                        <option>Saturday</option>
                        <option>Sunday</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <input type="time" name="start_time[]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                      <input type="time" name="end_time[]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                      <input type="number" name="total_slots[]" class="form-control" min="1" value="5">
                    </div>
                    <div class="col-md-1">
                      <button type="button" class="btn btn-success add-slot-btn"><i class="bi bi-plus-lg"></i></button>
                    </div>
                  </div>
                </div>

                <div class="text-center mt-3">
                  <button type="submit" name="submit" class="btn btn-primary" style="background-color:#0088a9; border:none;">Save Doctor</button>
                  <button type="reset" class="btn btn-secondary">Clear</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include('includes/footer.php'); ?>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Dynamic Weekly Schedule JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const container = document.getElementById("timeSlotContainer");

  container.addEventListener("click", function(e) {
    if (e.target.classList.contains("add-slot-btn") || e.target.closest(".add-slot-btn")) {
      const newRow = document.createElement("div");
      newRow.classList.add("row", "mb-3", "slot-row", "align-items-center");
      newRow.innerHTML = `
        <div class="col-md-3">
          <select name="day_of_week[]" class="form-select" required>
            <option value="" disabled selected>Day</option>
            <option>Monday</option>
            <option>Tuesday</option>
            <option>Wednesday</option>
            <option>Thursday</option>
            <option>Friday</option>
            <option>Saturday</option>
            <option>Sunday</option>
          </select>
        </div>
        <div class="col-md-3">
          <input type="time" name="start_time[]" class="form-control" required>
        </div>
        <div class="col-md-3">
          <input type="time" name="end_time[]" class="form-control" required>
        </div>
        <div class="col-md-2">
          <input type="number" name="total_slots[]" class="form-control" min="1" value="5">
        </div>
        <div class="col-md-1">
          <button type="button" class="btn btn-danger remove-slot-btn"><i class="bi bi-dash-lg"></i></button>
        </div>`;
      container.appendChild(newRow);
    }

    if (e.target.classList.contains("remove-slot-btn") || e.target.closest(".remove-slot-btn")) {
      e.target.closest(".slot-row").remove();
    }
  });
});
</script>

<style>
  .add-slot-btn {
    background-color: #0088a9 !important;
    border: none !important;
    color: #fff;
  }
  .remove-slot-btn {
    background-color: #dc3545 !important;
    border: none !important;
    color: #fff;
  }
  .add-slot-btn:hover,
  .remove-slot-btn:hover {
    opacity: 0.9;
  }
</style>

<?php echo $popup; ?>
