<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

if (!isset($_GET['id'])) {
  header("Location: manage_doctors.php");
  exit;
}

$doctor_id = intval($_GET['id']);
$doctor = $conn->query("SELECT * FROM doctors WHERE doctor_id = $doctor_id")->fetch_assoc();
$schedules = $conn->query("SELECT * FROM doctor_slots WHERE doctor_id = $doctor_id ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Edit Doctor</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item"><a href="manage_doctors.php">Doctors</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mt-3">Update Doctor Information</h5>

        <form method="POST" action="../../controller/update_doctor.php">
          <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Full Name</label>
            <div class="col-sm-9">
              <input type="text" name="name" class="form-control" value="<?php echo $doctor['name']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Email</label>
            <div class="col-sm-9">
              <input type="email" name="email" class="form-control" value="<?php echo $doctor['email']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Specialization</label>
            <div class="col-sm-9">
              <input type="text" name="specialization" class="form-control" value="<?php echo $doctor['specialization']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Credentials</label>
            <div class="col-sm-9">
              <input type="text" name="credentials" class="form-control" value="<?php echo $doctor['credentials']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Password</label>
            <div class="col-sm-9">
              <input type="text" name="password" class="form-control" value="<?php echo $doctor['password']; ?>" required>
            </div>
          </div>

          <hr>
          <h5 class="card-title mt-4">Doctor Schedule</h5>

          <div id="schedule-container">
            <?php if ($schedules->num_rows > 0): ?>
              <?php while ($row = $schedules->fetch_assoc()): ?>
                <div class="row mb-2 schedule-item align-items-center">
                  <div class="col-md-3">
                    <select name="day[]" class="form-select" required>
                      <?php
                      $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                      foreach ($days as $day) {
                        $selected = ($row['day_of_week'] == $day) ? 'selected' : '';
                        echo "<option value='$day' $selected>$day</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <input type="time" name="start_time[]" class="form-control" value="<?php echo $row['start_time']; ?>" required>
                  </div>
                  <div class="col-md-3">
                    <input type="time" name="end_time[]" class="form-control" value="<?php echo $row['end_time']; ?>" required>
                  </div>
                  <div class="col-md-2">
                    <input type="number" name="slots[]" class="form-control" value="<?php echo $row['total_slots']; ?>" min="1" required>
                  </div>
                  <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-slot"><i class="bi bi-trash"></i></button>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="row mb-2 schedule-item align-items-center">
                <div class="col-md-3">
                  <select name="day[]" class="form-select" required>
                    <option value="">Select Day</option>
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
                  <input type="number" name="slots[]" class="form-control" min="1" required>
                </div>
                <div class="col-md-1 text-center">
                  <button type="button" class="btn btn-danger btn-sm remove-slot"><i class="bi bi-trash"></i></button>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="text-end mb-3">
            <button type="button" class="btn btn-outline-success btn-sm" id="add-slot"><i class="bi bi-plus-circle"></i> Add Slot</button>
          </div>

          <div class="text-center">
            <button type="submit" name="update" class="btn btn-primary" style="background-color:#0088a9;border:none;">Save Changes</button>
            <a href="manage_doctors.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include('includes/footer.php'); ?>

<script>
// Add new schedule slot
document.getElementById('add-slot').addEventListener('click', function() {
  let container = document.getElementById('schedule-container');
  let slot = document.createElement('div');
  slot.classList.add('row', 'mb-2', 'schedule-item', 'align-items-center');
  slot.innerHTML = `
    <div class="col-md-3">
      <select name="day[]" class="form-select" required>
        <option value="">Select Day</option>
        <option>Monday</option><option>Tuesday</option><option>Wednesday</option>
        <option>Thursday</option><option>Friday</option><option>Saturday</option><option>Sunday</option>
      </select>
    </div>
    <div class="col-md-3"><input type="time" name="start_time[]" class="form-control" required></div>
    <div class="col-md-3"><input type="time" name="end_time[]" class="form-control" required></div>
    <div class="col-md-2"><input type="number" name="slots[]" class="form-control" min="1" required></div>
    <div class="col-md-1 text-center"><button type="button" class="btn btn-danger btn-sm remove-slot"><i class="bi bi-trash"></i></button></div>
  `;
  container.appendChild(slot);
});

// Remove slot
document.addEventListener('click', function(e) {
  if (e.target.closest('.remove-slot')) {
    e.target.closest('.schedule-item').remove();
  }
});
</script>

<style>
.schedule-item select, .schedule-item input { font-size: 0.9rem; }
.btn-outline-success { border-color: #0088a9; color: #0088a9; }
.btn-outline-success:hover { background-color: #0088a9; color: white; }
</style>
