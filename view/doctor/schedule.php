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

$doctor_id = $_SESSION['doctor_id'];
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>My Schedule</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Schedule</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
          <h5 class="card-title mb-0">Weekly Schedule</h5>
          <button class="btn btn-primary btn-sm" style="background-color:#0088a9;border:none;"
                  data-bs-toggle="modal" data-bs-target="#addScheduleModal">
            <i class="bi bi-calendar-plus"></i> Add Schedule
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Day</th>
                <th>Time</th>
                <th>Total Slots</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM doctor_slots WHERE doctor_id = ? ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("i", $doctor_id);
              $stmt->execute();
              $res = $stmt->get_result();

              if ($res->num_rows > 0) {
                $i = 1;
                while ($row = $res->fetch_assoc()) {
                  $slot_id = $row['slot_id'];
                  $time = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));
                  echo "
                  <tr>
                    <td>{$i}</td>
                    <td>{$row['day_of_week']}</td>
                    <td>{$time}</td>
                    <td>{$row['total_slots']}</td>
                    <td>
                      <button class='btn btn-warning btn-sm text-white' data-bs-toggle='modal' data-bs-target='#edit{$slot_id}'>
                        <i class='bi bi-pencil-square'></i>
                      </button>
                      <button class='btn btn-danger btn-sm' onclick='deleteSchedule({$slot_id})'>
                        <i class='bi bi-x-circle'></i>
                      </button>
                    </td>
                  </tr>

                  <!-- Edit Modal -->
                  <div class='modal fade' id='edit{$slot_id}' tabindex='-1'>
                    <div class='modal-dialog modal-dialog-centered'>
                      <div class='modal-content'>
                        <div class='modal-header' style='background-color:#0088a9;color:white;'>
                          <h5 class='modal-title'><i class='bi bi-pencil'></i> Edit Schedule</h5>
                          <button class='btn-close' data-bs-dismiss='modal'></button>
                        </div>
                        <div class='modal-body'>
                          <form method='POST' action='../../controller/update_schedule.php'>
                            <input type='hidden' name='slot_id' value='{$slot_id}'>
                            <div class='mb-3'>
                              <label class='form-label'>Day of Week</label>
                              <select name='day_of_week' class='form-select' required>
                                ";
                                $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                foreach ($days as $day) {
                                  $selected = ($row['day_of_week'] == $day) ? 'selected' : '';
                                  echo "<option $selected>$day</option>";
                                }
                                echo "
                              </select>
                            </div>
                            <div class='row mb-3'>
                              <div class='col'>
                                <label class='form-label'>Start Time</label>
                                <input type='time' name='start_time' value='{$row['start_time']}' class='form-control' required>
                              </div>
                              <div class='col'>
                                <label class='form-label'>End Time</label>
                                <input type='time' name='end_time' value='{$row['end_time']}' class='form-control' required>
                              </div>
                            </div>
                            <div class='mb-3'>
                              <label class='form-label'>Total Slots</label>
                              <input type='number' name='total_slots' value='{$row['total_slots']}' class='form-control' required>
                            </div>
                            <div class='text-center'>
                              <button type='submit' class='btn btn-primary' style='background-color:#0088a9;border:none;'>Save Changes</button>
                              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  ";
                  $i++;
                }
              } else {
                echo "<tr><td colspan='5' class='text-muted'>No schedules added yet.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#0088a9;color:white;">
        <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Add New Schedule</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../../controller/add_schedule.php">
          <div class="mb-3">
            <label class="form-label">Day of Week</label>
            <select name="day_of_week" class="form-select" required>
              <option disabled selected>Select a day</option>
              <option>Monday</option>
              <option>Tuesday</option>
              <option>Wednesday</option>
              <option>Thursday</option>
              <option>Friday</option>
              <option>Saturday</option>
              <option>Sunday</option>
            </select>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label class="form-label">Start Time</label>
              <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col">
              <label class="form-label">End Time</label>
              <input type="time" name="end_time" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Total Slots</label>
            <input type="number" name="total_slots" class="form-control" placeholder="e.g. 10" required>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary" style="background-color:#0088a9;border:none;">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteSchedule(id){
  Swal.fire({
    title:'Delete this schedule?',
    text:'This cannot be undone.',
    icon:'warning',
    showCancelButton:true,
    confirmButtonColor:'#d33',
    cancelButtonColor:'#6c757d',
    confirmButtonText:'Yes, delete'
  }).then(res=>{
    if(res.isConfirmed){
      window.location='../../controller/delete_schedule.php?id='+id;
    }
  });
}
</script>

<style>
.table-dark{background-color:#004b63!important}
.btn-info:hover{background-color:#007c96!important}
</style>
