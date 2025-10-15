<?php
session_start();
include('../../db/config.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

// Fetch patients and their assigned doctor
$sql = "SELECT p.*, d.name AS doctor_name
        FROM patients p
        LEFT JOIN doctors d ON p.doctor_id = d.doctor_id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Patients</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Patients</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
          <h5 class="card-title mb-0">Registered Patients</h5>
          <form method="GET" class="d-flex" style="gap: 5px;">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search patient..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-sm" style="background-color:#0088a9;color:white;"><i class="bi bi-search"></i></button>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-striped text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Patient Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Mobile</th>
                <th>Doctor</th>
                <th>Date Registered</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1;
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo "
                  <tr>
                    <td>{$i}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['gender']}</td>
                    <td>{$row['age']}</td>
                    <td>{$row['mobile_number']}</td>
                    <td>{$row['doctor_name']}</td>
                    <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                    <td>
                      <button class='btn btn-info btn-sm view-patient' 
                              data-bs-toggle='modal' 
                              data-bs-target='#viewPatientModal'
                              data-name='{$row['name']}'
                              data-nickname='{$row['nickname']}'
                              data-gender='{$row['gender']}'
                              data-birthday='{$row['birthday']}'
                              data-age='{$row['age']}'
                              data-address='{$row['address']}'
                              data-mobile='{$row['mobile_number']}'
                              data-mother='{$row['mother_name']}'
                              data-mothernick='{$row['mother_nickname']}'
                              data-motherocc='{$row['mother_occupation']}'
                              data-motherage='{$row['mother_age']}'
                              data-father='{$row['father_name']}'
                              data-fathernick='{$row['father_nickname']}'
                              data-fatherocc='{$row['father_occupation']}'
                              data-fatherage='{$row['father_age']}'
                              data-doctor='{$row['doctor_name']}'
                              data-registered='" . date('M d, Y', strtotime($row['created_at'])) . "'>
                        <i class='bi bi-eye'></i> View
                      </button>
                    </td>
                  </tr>";
                  $i++;
                }
              } else {
                echo "<tr><td colspan='8' class='text-muted text-center'>No patients found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Modal: View Patient Details -->
<div class="modal fade" id="viewPatientModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#0088a9;color:white;">
        <h5 class="modal-title"><i class="bi bi-person-lines-fill"></i> Patient Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="patientDetails"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
// 🩺 Handle View Patient Button Click
document.addEventListener('click', function(e) {
  if (e.target.closest('.view-patient')) {
    let btn = e.target.closest('.view-patient');

    let html = `
      <h6 class='fw-bold mb-2'>👤 Patient Information</h6>
      <p><strong>Name:</strong> ${btn.dataset.name} (${btn.dataset.nickname || '—'})<br>
      <strong>Gender:</strong> ${btn.dataset.gender || '—'}<br>
      <strong>Birthday:</strong> ${btn.dataset.birthday || '—'} (${btn.dataset.age || '—'} yrs old)<br>
      <strong>Address:</strong> ${btn.dataset.address || '—'}</p>
      <hr>

      <h6 class='fw-bold mb-2'>👩‍👧 Parent / Guardian</h6>
      <p><strong>Mother:</strong> ${btn.dataset.mother || '—'} (${btn.dataset.mothernick || ''}) - ${btn.dataset.motherocc || '—'}, Age ${btn.dataset.motherage || '—'}<br>
      <strong>Father:</strong> ${btn.dataset.father || '—'} (${btn.dataset.fathernick || ''}) - ${btn.dataset.fatherocc || '—'}, Age ${btn.dataset.fatherage || '—'}</p>
      <hr>

      <h6 class='fw-bold mb-2'>📱 Contact Details</h6>
      <p><strong>Mobile:</strong> ${btn.dataset.mobile || '—'}<br>
      <strong>Doctor:</strong> ${btn.dataset.doctor || '—'}<br>
      <strong>Registered:</strong> ${btn.dataset.registered || '—'}</p>
    `;

    document.getElementById('patientDetails').innerHTML = html;
  }
});
</script>

<style>
.table-dark { background-color:#004b63!important; color:white; }
.btn-info { background-color:#0088a9; border:none; }
.btn-info:hover { background-color:#006f8b; }
.modal-header { border-bottom:none!important; }
.modal-footer { border-top:none!important; }
</style>
