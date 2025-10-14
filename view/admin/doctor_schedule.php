<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

$popup = "";

// Handle form submit
if (isset($_POST['submit'])) {
  $doctor_id = $_POST['doctor_id'];
  $slot_date = $_POST['slot_date'];
  $start_time = $_POST['start_time'];
  $end_time = $_POST['end_time'];
  $total_slots = $_POST['total_slots'];

  $sql = "INSERT INTO doctor_slots (doctor_id, slot_date, start_time, end_time, total_slots)
          VALUES ('$doctor_id', '$slot_date', '$start_time', '$end_time', '$total_slots')";
  if ($conn->query($sql)) {
    $popup = "
    <script>
    Swal.fire({
      icon: 'success',
      title: 'Slot Added!',
      text: 'New time slot added successfully.',
      confirmButtonColor: '#0088a9'
    });
    </script>";
  } else {
    $popup = "
    <script>
    Swal.fire({
      icon: 'error',
      title: 'Error!',
      text: 'Something went wrong while adding the slot.',
      confirmButtonColor: '#0088a9'
    });
    </script>";
  }
}
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Doctor Schedule</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Doctor Schedule</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Add Time Slot</h5>

              <form method="POST" action="">
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Select Doctor</label>
                  <div class="col-sm-9">
                    <select name="doctor_id" class="form-select" required>
                      <option value="" disabled selected>Choose a doctor</option>
                      <?php
                      $result = $conn->query("SELECT doctor_id, name FROM doctors ORDER BY name ASC");
                      while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['doctor_id']}'>{$row['name']}</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Date</label>
                  <div class="col-sm-9">
                    <input type="date" name="slot_date" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Start Time</label>
                  <div class="col-sm-9">
                    <input type="time" name="start_time" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">End Time</label>
                  <div class="col-sm-9">
                    <input type="time" name="end_time" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Total Slots</label>
                  <div class="col-sm-9">
                    <input type="number" name="total_slots" class="form-control" value="5" min="1" required>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" name="submit" class="btn btn-primary" style="background-color:#0088a9; border:none;">Save Slot</button>
                </div>
              </form>

              <hr>
              <h5 class="card-title mt-4">Existing Time Slots</h5>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>Doctor</th>
                      <th>Date</th>
                      <th>Start</th>
                      <th>End</th>
                      <th>Total</th>
                      <th>Booked</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $res = $conn->query("SELECT ds.*, d.name FROM doctor_slots ds
                                         JOIN doctors d ON ds.doctor_id = d.doctor_id
                                         ORDER BY ds.slot_date ASC, ds.start_time ASC");
                    $i = 1;
                    while ($r = $res->fetch_assoc()) {
                      echo "<tr>
                              <td>{$i}</td>
                              <td>{$r['name']}</td>
                              <td>{$r['slot_date']}</td>
                              <td>{$r['start_time']}</td>
                              <td>{$r['end_time']}</td>
                              <td>{$r['total_slots']}</td>
                              <td>{$r['booked_slots']}</td>
                            </tr>";
                      $i++;
                    }
                    ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include('includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php echo $popup; ?>
