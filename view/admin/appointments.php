<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

// Search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "SELECT 
          a.appointment_id,
          p.name AS patient_name,
          p.mobile_number,
          d.name AS doctor_name,
          a.appointment_date,
          a.appointment_time,
          a.reason,
          a.status,
          a.created_at
        FROM appointments a
        LEFT JOIN patients p ON a.patient_id = p.patient_id
        LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
        WHERE 1=1";

if ($search !== '') {
  $sql .= " AND (p.name LIKE '%$search%' OR d.name LIKE '%$search%')";
}
if ($statusFilter !== '') {
  $sql .= " AND a.status = '$statusFilter'";
}
$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time ASC";
$result = $conn->query($sql);
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Appointments</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Appointments</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
          <h5 class="card-title mb-0">All Appointments</h5>
          <form method="GET" class="d-flex align-items-center" style="gap: 5px;">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search doctor/patient..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="status" class="form-select form-select-sm">
              <option value="">All</option>
              <option value="Pending" <?php echo ($statusFilter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
              <option value="Confirmed" <?php echo ($statusFilter == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
              <option value="Completed" <?php echo ($statusFilter == 'Completed') ? 'selected' : ''; ?>>Completed</option>
              <option value="Cancelled" <?php echo ($statusFilter == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-sm" style="background-color:#0088a9;color:white;"><i class="bi bi-search"></i></button>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-striped text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                  $badgeClass = match ($row['status']) {
                    'Pending' => 'bg-warning text-dark',
                    'Confirmed' => 'bg-success',
                    'Completed' => 'bg-primary',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                  };

                  echo "
                  <tr>
                    <td>{$i}</td>
                    <td>{$row['patient_name']}</td>
                    <td>{$row['doctor_name']}</td>
                    <td>" . date('M d, Y', strtotime($row['appointment_date'])) . "</td>
                    <td>" . date('h:i A', strtotime($row['appointment_time'])) . "</td>
                    <td>{$row['reason']}</td>
                    <td><span class='badge {$badgeClass}'>{$row['status']}</span></td>
                    <td>
                      <button class='btn btn-info btn-sm view-appointment' 
                              data-bs-toggle='modal' 
                              data-bs-target='#viewAppointmentModal'
                              data-patient='{$row['patient_name']}'
                              data-doctor='{$row['doctor_name']}'
                              data-date='" . date('M d, Y', strtotime($row['appointment_date'])) . "'
                              data-time='" . date('h:i A', strtotime($row['appointment_time'])) . "'
                              data-reason='{$row['reason']}'
                              data-status='{$row['status']}'
                              data-mobile='{$row['mobile_number']}'
                              data-created='" . date('M d, Y h:i A', strtotime($row['created_at'])) . "'>
                        <i class='bi bi-eye'></i> View
                      </button>
                    </td>
                  </tr>";
                  $i++;
                }
              } else {
                echo "<tr><td colspan='8' class='text-muted text-center'>No appointments found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- 🩺 Modal: Appointment Details -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#0088a9;color:white;">
        <h5 class="modal-title"><i class="bi bi-calendar-week"></i> Appointment Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="appointmentDetails"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
// Handle View Appointment Button
document.addEventListener('click', e => {
  if (e.target.closest('.view-appointment')) {
    const btn = e.target.closest('.view-appointment');
    const html = `
      <h6 class='fw-bold mb-2'>🩺 Appointment Information</h6>
      <p>
        <strong>Patient:</strong> ${btn.dataset.patient}<br>
        <strong>Doctor:</strong> ${btn.dataset.doctor}<br>
        <strong>Date:</strong> ${btn.dataset.date}<br>
        <strong>Time:</strong> ${btn.dataset.time}<br>
        <strong>Reason:</strong> ${btn.dataset.reason || '—'}<br>
        <strong>Status:</strong> ${btn.dataset.status}<br>
      </p>
      <hr>
      <h6 class='fw-bold mb-2'>📱 Contact Info</h6>
      <p>
        <strong>Patient Mobile:</strong> ${btn.dataset.mobile || '—'}<br>
        <strong>Created On:</strong> ${btn.dataset.created}
      </p>
    `;
    document.getElementById('appointmentDetails').innerHTML = html;
  }
});
</script>

<style>
.table-dark { background-color:#004b63!important; color:white; }
.btn-info { background-color:#0088a9; border:none; }
.btn-info:hover { background-color:#006f8b; }
.modal-header { border-bottom:none!important; }
.modal-footer { border-top:none!important; }
.badge { font-size: 0.85rem; padding: 6px 10px; }
</style>
