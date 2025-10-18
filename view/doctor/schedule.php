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

        <!-- Weekly Schedule -->
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
                <th>Remaining Slots</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM doctor_slots WHERE doctor_id = ? 
                      ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("i", $doctor_id);
              $stmt->execute();
              $res = $stmt->get_result();

              if ($res->num_rows > 0) {
                $i = 1;
                while ($row = $res->fetch_assoc()) {
                  $slot_id = $row['slot_id'];
                  $time = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));

                  $countQuery = $conn->prepare("
                    SELECT COUNT(*) AS booked FROM appointments 
                    WHERE doctor_id=? AND appointment_time=? 
                    AND status IN ('Pending','Confirmed')
                  ");
                  $countQuery->bind_param("is", $doctor_id, $time);
                  $countQuery->execute();
                  $booked = $countQuery->get_result()->fetch_assoc()['booked'] ?? 0;
                  $remaining = max($row['total_slots'] - $booked, 0);

                  echo "
                  <tr>
                    <td>{$i}</td>
                    <td>{$row['day_of_week']}</td>
                    <td>{$time}</td>
                    <td>{$row['total_slots']}</td>
                    <td><span class='badge bg-info text-dark'>{$remaining}</span></td>
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
                              <select name='day_of_week' class='form-select' required>";
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
                  </div>";
                  $i++;
                }
              } else {
                echo "<tr><td colspan='6' class='text-muted'>No schedules added yet.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- 💙 Clinic Unavailability -->
        <div class="mt-5 p-4 rounded-4 shadow-sm" style="background:#f8fcfd;border:1px solid #d3ebf0;">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;background:#0088a933;">
              <i class="bi bi-calendar-x text-primary fs-5"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-semibold text-primary">Clinic Unavailability</h5>
              <small class="text-muted">Select one or multiple days (or a range) to mark your clinic as closed.</small>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <div class="input-group" style="max-width:350px;">
              <span class="input-group-text bg-white">
                <i class="bi bi-calendar-event-fill text-primary"></i>
              </span>
              <input type="text" id="unavailable_date" class="form-control" placeholder="Select date(s)" readonly>
            </div>
            <button class="btn btn-danger" id="markUnavailableBtn">
              <i class="bi bi-x-octagon"></i> Mark as Unavailable
            </button>
          </div>

          <div class="p-3 rounded-3" style="background:#ffffff;border:1px solid #dbeef2;">
            <h6 class="fw-semibold text-primary mb-3"><i class="bi bi-ban"></i> Unavailable Dates</h6>
            <ul class="list-group small" id="unavailableList">
              <?php
              $dates = $conn->query("SELECT unavailable_date FROM doctor_unavailable_dates WHERE doctor_id = $doctor_id ORDER BY unavailable_date ASC");
              $unavailable_dates = [];
              if ($dates->num_rows > 0) {
                while ($d = $dates->fetch_assoc()) {
                  $dateVal = $d['unavailable_date'];
                  $unavailable_dates[] = $dateVal;
                  $date = date("F j, Y", strtotime($dateVal));
                  echo "
                  <li class='list-group-item d-flex justify-content-between align-items-center border-0 border-bottom'>
                    <div><i class='bi bi-calendar-event text-primary me-2'></i>{$date}</div>
                    <button class='btn btn-sm btn-outline-danger remove-date' data-date='{$dateVal}'>
                      <i class='bi bi-trash'></i> Remove
                    </button>
                  </li>";
                }
              } else {
                echo "<li class='list-group-item text-muted border-0'>No unavailable dates set.</li>";
              }
              ?>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

<?php include('includes/footer.php'); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const disabledDates = <?= json_encode($unavailable_dates); ?>;
  let pickedDates = [];

  // 🗓️ Flatpickr (multi or range)
  const picker = flatpickr("#unavailable_date", {
    mode: "multiple", // allow selecting multiple or range of dates
    altInput: true,
    altFormat: "F j, Y",
    dateFormat: "Y-m-d",
    minDate: "today",
    disable: disabledDates,
    disableMobile: true,
    onChange: (selectedDates, dateStr) => pickedDates = dateStr.split(", ")
  });

  // Delete schedule
  window.deleteSchedule = function(id){
    Swal.fire({
      title:'Delete this schedule?',
      text:'This cannot be undone.',
      icon:'warning',
      showCancelButton:true,
      confirmButtonColor:'#d33',
      confirmButtonText:'Yes, delete'
    }).then(res=>{
      if(res.isConfirmed){
        window.location='../../controller/delete_schedule.php?id='+id;
      }
    });
  }

  // Mark unavailable (multi-date support)
  document.getElementById('markUnavailableBtn').addEventListener('click', ()=>{
    if (pickedDates.length === 0)
      return Swal.fire('Error','Please select at least one date.','error');

    fetch('../../controller/mark_specific_date.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({ unavailable_date: JSON.stringify(pickedDates) })
    })
    .then(res => res.json())
    .then(data => {
      Swal.fire('Done!', data.message || 'Marked unavailable.', 'success')
        .then(()=>location.reload());
    });
  });

  // Remove unavailable
  document.querySelectorAll('.remove-date').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const date=btn.dataset.date;
      Swal.fire({
        title:`Remove ${date}?`,
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        confirmButtonText:'Yes, remove'
      }).then(res=>{
        if(res.isConfirmed){
          fetch('../../controller/remove_unavailable_date.php',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:new URLSearchParams({unavailable_date:date})
          }).then(()=>location.reload());
        }
      });
    });
  });
});
</script>

<style>
.table-dark{background-color:#004b63!important}
.list-group-item{transition:all .2s ease}
.list-group-item:hover{background:#f1faff}
.btn-outline-danger:hover{background:#dc3545;color:white}
.flatpickr-calendar{z-index:2000!important}
.flatpickr-day.disabled,
.flatpickr-day.disabled:hover{
  background:#f3f3f3!important;
  color:#bbb!important;
  cursor:not-allowed!important;
}
.flatpickr-input[readonly]{background-color:white!important;cursor:pointer;}
</style>
