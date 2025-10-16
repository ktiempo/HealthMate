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
$filter = $_GET['filter'] ?? 'All';
$search = $_GET['search'] ?? '';

// Fetch today's confirmed appointments
$today = date('Y-m-d');
$stmt_today = $conn->prepare("
  SELECT a.*, p.name AS patient_name, p.mobile_number
  FROM appointments a
  JOIN patients p ON a.patient_id = p.patient_id
  WHERE a.doctor_id = ? AND a.appointment_date = ? AND a.status = 'Confirmed'
  ORDER BY a.appointment_time ASC
");
$stmt_today->bind_param("is", $doctor_id, $today);
$stmt_today->execute();
$result_today = $stmt_today->get_result();
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

  <!-- ======= Today's Appointments ======= -->
  <section class="section mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header text-white" style="background-color:#0088a9;">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Today's Confirmed Appointments</h5>
      </div>
      <div class="card-body">
        <?php if ($result_today->num_rows > 0): ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Patient</th>
                  <th>Time</th>
                  <th>Reason</th>
                  <th>Contact</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; while ($t = $result_today->fetch_assoc()): ?>
                  <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($t['patient_name']); ?></td>
                    <td><?= date("h:i A", strtotime($t['appointment_time'])); ?></td>
                    <td><?= htmlspecialchars($t['reason']); ?></td>
                    <td><?= htmlspecialchars($t['mobile_number']); ?></td>
                    <td>
                      <button class="btn btn-primary btn-sm" onclick="updateStatus(<?= $t['appointment_id']; ?>, 'Completed')">
                        <i class="bi bi-clipboard-check"></i> Mark as Done
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="text-muted mb-0 text-center">No confirmed appointments for today.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ======= All Appointments ======= -->
  <section class="section">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
          <h5 class="card-title mb-0">All Appointments</h5>

          <form class="d-flex" method="GET" action="">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm me-2" placeholder="Search by patient">
            <select name="filter" class="form-select form-select-sm me-2">
              <?php
              $statuses = ['All', 'Pending', 'Confirmed', 'Completed', 'Cancelled'];
              foreach ($statuses as $status) {
                $selected = ($filter == $status) ? 'selected' : '';
                echo "<option $selected>$status</option>";
              }
              ?>
            </select>
            <button class="btn btn-primary btn-sm" style="background-color:#0088a9;border:none;"><i class="bi bi-funnel"></i></button>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $query = "SELECT a.*, p.name AS patient_name, p.mobile_number
                        FROM appointments a
                        JOIN patients p ON a.patient_id = p.patient_id
                        WHERE a.doctor_id = ?";

              if ($filter !== 'All') {
                $query .= " AND a.status = ?";
              }

              if (!empty($search)) {
                $query .= " AND p.name LIKE ?";
              }

              $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
              $stmt = $conn->prepare($query);

              if ($filter !== 'All' && !empty($search)) {
                $like = "%$search%";
                $stmt->bind_param("iss", $doctor_id, $filter, $like);
              } elseif ($filter !== 'All') {
                $stmt->bind_param("is", $doctor_id, $filter);
              } elseif (!empty($search)) {
                $like = "%$search%";
                $stmt->bind_param("is", $doctor_id, $like);
              } else {
                $stmt->bind_param("i", $doctor_id);
              }

              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                  $badge = match ($row['status']) {
                    'Pending' => 'bg-warning text-dark',
                    'Confirmed' => 'bg-success',
                    'Completed' => 'bg-primary',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                  };
                  $id = $row['appointment_id'];
              ?>
                  <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row['patient_name']); ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?= date("h:i A", strtotime($row['appointment_time'])); ?></td>
                    <td><?= htmlspecialchars($row['reason']); ?></td>
                    <td><span class="badge <?= $badge; ?>"><?= htmlspecialchars($row['status']); ?></span></td>
                    <td>
                      <?php if ($row['status'] == 'Pending'): ?>
                        <button class="btn btn-success btn-sm" onclick="updateStatus(<?= $id; ?>, 'Confirmed')"><i class="bi bi-check-circle"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="updateStatus(<?= $id; ?>, 'Cancelled')"><i class="bi bi-x-circle"></i></button>
                      <?php elseif ($row['status'] == 'Confirmed'): ?>
                        <button class="btn btn-primary btn-sm" onclick="updateStatus(<?= $id; ?>, 'Completed')"><i class="bi bi-clipboard-check"></i></button>
                      <?php endif; ?>
                      <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#view<?= $id; ?>"><i class="bi bi-eye"></i></button>
                    </td>
                  </tr>

                  <!-- View Modal -->
                  <div class="modal fade" id="view<?= $id; ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header" style="background-color:#0088a9; color:white;">
                          <h5 class="modal-title"><i class="bi bi-calendar-event"></i> Appointment Details</h5>
                          <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-start">
                          <p><strong>Patient Name:</strong> <?= htmlspecialchars($row['patient_name']); ?></p>
                          <p><strong>Contact:</strong> <?= htmlspecialchars($row['mobile_number']); ?></p>
                          <p><strong>Date:</strong> <?= htmlspecialchars($row['appointment_date']); ?></p>
                          <p><strong>Time:</strong> <?= date("h:i A", strtotime($row['appointment_time'])); ?></p>
                          <p><strong>Reason:</strong> <?= htmlspecialchars($row['reason']); ?></p>
                          <p><strong>Status:</strong> <span class="badge <?= $badge; ?>"><?= htmlspecialchars($row['status']); ?></span></p>
                        </div>
                        <div class="modal-footer">
                          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
              <?php
                }
              } else {
                echo "<tr><td colspan='7' class='text-muted'>No appointments found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include('includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateStatus(id, status) {
  Swal.fire({
    title: 'Update Status?',
    text: `Mark this appointment as "${status}"?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#0088a9',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, update'
  }).then((res) => {
    if (res.isConfirmed) {
      window.location = `../../controller/update_appointment_status.php?id=${id}&status=${status}`;
    }
  });
}
</script>

<style>
.table-dark { background-color: #004b63 !important; }
.btn-info:hover { background-color: #007c96 !important; }
.card-header h5 { font-weight: 600; }
form.d-flex input, form.d-flex select {
  border-radius: 5px;
  border: 1px solid #ccc;
}
form.d-flex button {
  background-color: #0088a9;
  color: white;
}
form.d-flex button:hover {
  background-color: #006d87;
}
</style>
