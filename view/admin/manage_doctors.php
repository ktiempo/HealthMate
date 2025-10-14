<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

// delete doctor
if (isset($_GET['delete_id'])) {
  $id = intval($_GET['delete_id']);
  $conn->query("DELETE FROM doctors WHERE doctor_id=$id");
  echo "<script>
      Swal.fire({icon:'success',title:'Deleted',text:'Doctor deleted successfully',confirmButtonColor:'#0088a9'})
      .then(()=>window.location='manage_doctors.php');
    </script>";
}

// fetch doctors + schedules
$sql = "SELECT d.*, 
        GROUP_CONCAT(
          CONCAT(ds.day_of_week,'|',
                 TIME_FORMAT(ds.start_time,'%h:%i %p'),' - ',
                 TIME_FORMAT(ds.end_time,'%h:%i %p'),'|',
                 ds.total_slots)
          ORDER BY FIELD(ds.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
          SEPARATOR ','
        ) AS sched
        FROM doctors d
        LEFT JOIN doctor_slots ds ON d.doctor_id = ds.doctor_id
        GROUP BY d.doctor_id
        ORDER BY d.name";
$res = $conn->query($sql);
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Manage Doctors</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Doctors</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
          <h5 class="card-title mb-0">Doctor List</h5>
          <a href="add_doctor.php" class="btn btn-primary btn-sm" style="background-color:#0088a9;border:none;">
            <i class="bi bi-person-plus"></i> Add Doctor
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Credentials</th>
                <th>Password</th>
                <th>Schedule</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $modals = ''; // collect modals separately
              if ($res->num_rows > 0) {
                $i = 1;
                while ($r = $res->fetch_assoc()) {
                  $scheds = !empty($r['sched']) ? explode(',', $r['sched']) : [];
                  $doctor_id = $r['doctor_id'];

                  echo "
                  <tr>
                    <td>{$i}</td>
                    <td>{$r['name']}</td>
                    <td>{$r['email']}</td>
                    <td>{$r['specialization']}</td>
                    <td>{$r['credentials']}</td>
                    <td>{$r['password']}</td>
                    <td>
                      <button class='btn btn-outline-info btn-sm' data-bs-toggle='modal' data-bs-target='#modal{$doctor_id}'>
                        <i class='bi bi-calendar-week'></i> View Schedule
                      </button>
                    </td>
                    <td>
                      <a href='edit_doctor.php?id={$doctor_id}' class='btn btn-warning btn-sm me-1'>
                        <i class='bi bi-pencil'></i>
                      </a>
                      <button class='btn btn-danger btn-sm' onclick='confirmDelete({$doctor_id})'>
                        <i class='bi bi-trash'></i>
                      </button>
                    </td>
                  </tr>";

                  // build modal HTML and store in variable (printed later)
                  $modalBody = "";
                  if (empty($scheds)) {
                    $modalBody = "<p class='text-center text-muted mb-0'>No schedule available.</p>";
                  } else {
                    $rows = "";
                    foreach ($scheds as $s) {
                      list($day, $time, $slots) = explode('|', $s);
                      $rows .= "<tr><td><b>$day</b></td><td>$time</td><td>$slots</td></tr>";
                    }
                    $modalBody = "
                    <div class='table-responsive'>
                      <table class='table table-bordered text-center align-middle'>
                        <thead class='table-primary'>
                          <tr><th>Day</th><th>Time</th><th>Slots</th></tr>
                        </thead>
                        <tbody>$rows</tbody>
                      </table>
                    </div>";
                  }

                  $modals .= "
                  <div class='modal fade' id='modal{$doctor_id}' tabindex='-1'>
                    <div class='modal-dialog modal-dialog-centered modal-md'>
                      <div class='modal-content'>
                        <div class='modal-header' style='background-color:#0088a9;color:white;'>
                          <h5 class='modal-title'><i class=\"bi bi-calendar-week\"></i> {$r['name']}'s Schedule</h5>
                          <button class='btn-close' data-bs-dismiss='modal'></button>
                        </div>
                        <div class='modal-body'>$modalBody</div>
                        <div class='modal-footer'>
                          <button class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                        </div>
                      </div>
                    </div>
                  </div>";
                  $i++;
                }
              } else {
                echo "<tr><td colspan='8' class='text-center text-muted'>No doctors found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- print all modals here (outside table) -->
        <?php echo $modals; ?>
      </div>
    </div>
  </section>
</main>

<?php include('includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id){
  Swal.fire({
    title:'Delete this doctor?',
    text:'This cannot be undone.',
    icon:'warning',
    showCancelButton:true,
    confirmButtonColor:'#d33',
    cancelButtonColor:'#6c757d',
    confirmButtonText:'Yes, delete'
  }).then(res=>{
    if(res.isConfirmed) window.location='manage_doctors.php?delete_id='+id;
  });
}
</script>

<style>
.table-dark{background-color:#004b63!important}
.btn-outline-info{border-color:#0088a9!important;color:#0088a9!important}
.btn-outline-info:hover{background-color:#0088a9!important;color:white!important}
.modal-header{border-bottom:none!important}
.modal-footer{border-top:none!important}
.table-primary th{background-color:#0088a9!important;color:white!important}
</style>
