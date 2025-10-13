<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
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
  </div><!-- End Page Title -->

  <section class="section">
    <div class="container-fluid">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <h5 class="card-title">Doctor List</h5>
            <a href="add_doctor.php" class="btn btn-primary" style="background-color:#0088a9; border:none;">
              <i class="bi bi-person-plus"></i> Add Doctor
            </a>
          </div>

          <!-- Doctor Table -->
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead class="table-dark text-center">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Specialization</th>
                  <th>Schedule</th>
                  <th>Credentials</th>
                  <th>Password</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php
                $query = "SELECT * FROM doctors ORDER BY doctor_id DESC";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                  $count = 1;
                  while ($row = $result->fetch_assoc()) {
                    // Split multiple schedules for neat display
                    $schedules = explode(',', $row['schedule']);
                    $formattedSchedule = "<ul class='list-unstyled m-0'>";
                    foreach ($schedules as $sched) {
                      $formattedSchedule .= "<li>🩺 " . htmlspecialchars(trim($sched)) . "</li>";
                    }
                    $formattedSchedule .= "</ul>";

                    echo "
                      <tr>
                        <td>{$count}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['specialization']}</td>
                        <td>{$formattedSchedule}</td>
                        <td>{$row['credentials']}</td>
                        <td>{$row['password']}</td>
                        <td>
                          <a href='edit_doctor.php?id={$row['doctor_id']}' class='btn btn-sm btn-warning'>
                            <i class='bi bi-pencil-square'></i>
                          </a>
                          <a href='../../controller/delete_doctor.php?id={$row['doctor_id']}'
                             class='btn btn-sm btn-danger'
                             onclick='return confirm(\"Are you sure you want to delete this doctor?\")'>
                            <i class='bi bi-trash'></i>
                          </a>
                        </td>
                      </tr>
                    ";
                    $count++;
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-muted text-center'>No doctors found.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div><!-- End Table -->

        </div>
      </div>
    </div>
  </section>

</main>

<?php include('includes/footer.php'); ?>

<style>
  .table {
    border-radius: 10px;
    overflow: hidden;
  }
  .table th {
    background-color: #004b63 !important;
    color: #ffffff !important;
  }
  .table td {
    vertical-align: middle;
  }
  .table ul {
    text-align: left;
    padding-left: 0.8rem;
  }
  .table ul li {
    margin-bottom: 4px;
    color: #004b63;
  }
</style>
