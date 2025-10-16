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
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>My Patients</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Patients</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
          <h5 class="card-title mb-0">Patient List</h5>
          <button class="btn btn-primary btn-sm" style="background-color:#0088a9;border:none;"
            data-bs-toggle="modal" data-bs-target="#addPatientModal">
            <i class="bi bi-person-plus"></i> Add Patient
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Patient Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Mobile</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $doctor_id = $_SESSION['doctor_id'];
              $stmt = $conn->prepare("SELECT * FROM patients WHERE doctor_id = ? AND status = 'Active' ORDER BY created_at DESC");
              $stmt->bind_param("i", $doctor_id);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                  $pid = $row['patient_id'];
              ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['gender']); ?></td>
                <td><?= htmlspecialchars($row['age']); ?></td>
                <td><?= htmlspecialchars($row['mobile_number']); ?></td>
                <td>
                  <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                    data-bs-target="#view<?= $pid; ?>"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                    data-bs-target="#edit<?= $pid; ?>"><i class="bi bi-pencil-square"></i></button>
                  <button class="btn btn-secondary btn-sm" onclick="deactivatePatient(<?= $pid; ?>)">
                    <i class="bi bi-person-dash"></i>
                  </button>
                </td>
              </tr>

              <!-- View Modal -->
              <div class="modal fade" id="view<?= $pid; ?>" tabindex="-1" aria-labelledby="viewLabel<?= $pid; ?>"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header" style="background-color:#0088a9;color:white;">
                      <h5 class="modal-title"><i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($row['name']); ?>'s Information</h5>
                      <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-6"><strong>Full Name:</strong> <?= htmlspecialchars($row['name']); ?></div>
                        <div class="col-md-6"><strong>Nickname:</strong> <?= htmlspecialchars($row['nickname']); ?></div>
                        <div class="col-md-4"><strong>Birthday:</strong> <?= htmlspecialchars($row['birthday']); ?></div>
                        <div class="col-md-4"><strong>Gender:</strong> <?= htmlspecialchars($row['gender']); ?></div>
                        <div class="col-md-4"><strong>Age:</strong> <?= htmlspecialchars($row['age']); ?></div>
                        <div class="col-md-12"><strong>Address:</strong> <?= htmlspecialchars($row['address']); ?></div>
                        <div class="col-md-6"><strong>Cell Number:</strong> <?= htmlspecialchars($row['mobile_number']); ?></div>

                        <h6 class="mt-4 fw-bold text-primary">Mother's Information</h6>
                        <div class="col-md-6"><strong>Name:</strong> <?= htmlspecialchars($row['mother_name']); ?></div>
                        <div class="col-md-3"><strong>Nickname:</strong> <?= htmlspecialchars($row['mother_nickname']); ?></div>
                        <div class="col-md-3"><strong>Age:</strong> <?= htmlspecialchars($row['mother_age']); ?></div>
                        <div class="col-md-6"><strong>Occupation:</strong> <?= htmlspecialchars($row['mother_occupation']); ?></div>

                        <h6 class="mt-4 fw-bold text-primary">Father's Information</h6>
                        <div class="col-md-6"><strong>Name:</strong> <?= htmlspecialchars($row['father_name']); ?></div>
                        <div class="col-md-3"><strong>Nickname:</strong> <?= htmlspecialchars($row['father_nickname']); ?></div>
                        <div class="col-md-3"><strong>Age:</strong> <?= htmlspecialchars($row['father_age']); ?></div>
                        <div class="col-md-6"><strong>Occupation:</strong> <?= htmlspecialchars($row['father_occupation']); ?></div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Edit Modal -->
              <div class="modal fade" id="edit<?= $pid; ?>" tabindex="-1" aria-labelledby="editLabel<?= $pid; ?>"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header" style="background-color:#0088a9;color:white;">
                      <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Patient</h5>
                      <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <form method="POST" action="../../controller/update_patient.php">
                        <input type="hidden" name="patient_id" value="<?= $pid; ?>">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($row['name']); ?>" class="form-control" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Nickname</label>
                            <input type="text" name="nickname" value="<?= htmlspecialchars($row['nickname']); ?>" class="form-control">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Birthday</label>
                            <input type="date" name="birthday" value="<?= htmlspecialchars($row['birthday']); ?>" class="form-control birthday-edit" data-age-id="age<?= $pid; ?>">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                              <option value="Male" <?= $row['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                              <option value="Female" <?= $row['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" id="age<?= $pid; ?>" value="<?= htmlspecialchars($row['age']); ?>" class="form-control">
                          </div>
                          <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($row['address']); ?>" class="form-control">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile_number" value="<?= htmlspecialchars($row['mobile_number']); ?>" class="form-control">
                          </div>

                          <h6 class="mt-3 fw-bold text-primary">Mother's Info</h6>
                          <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="mother_name" value="<?= htmlspecialchars($row['mother_name']); ?>" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Nickname</label>
                            <input type="text" name="mother_nickname" value="<?= htmlspecialchars($row['mother_nickname']); ?>" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="mother_age" value="<?= htmlspecialchars($row['mother_age']); ?>" class="form-control">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="mother_occupation" value="<?= htmlspecialchars($row['mother_occupation']); ?>" class="form-control">
                          </div>

                          <h6 class="mt-3 fw-bold text-primary">Father's Info</h6>
                          <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="father_name" value="<?= htmlspecialchars($row['father_name']); ?>" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Nickname</label>
                            <input type="text" name="father_nickname" value="<?= htmlspecialchars($row['father_nickname']); ?>" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="father_age" value="<?= htmlspecialchars($row['father_age']); ?>" class="form-control">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="father_occupation" value="<?= htmlspecialchars($row['father_occupation']); ?>" class="form-control">
                          </div>

                          <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary"
                              style="background-color:#0088a9;border:none;">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <?php
                }
              } else {
                echo "<tr><td colspan='6' class='text-muted'>No patients found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Add Patient Modal -->
<div class="modal fade" id="addPatientModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#0088a9;color:white;">
        <h5 class="modal-title"><i class="bi bi-person-plus"></i> Add New Patient</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../../controller/add_patient.php">
          <div class="row g-3">
            <h6 class="fw-bold text-primary">Patient Information</h6>
            <div class="col-md-6">
              <label class="form-label">Patient Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nickname</label>
              <input type="text" name="nickname" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Birthday</label>
              <input type="date" name="birthday" id="birthday" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-select" required>
                <option value="" selected disabled>Select</option>
                <option>Male</option>
                <option>Female</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Age</label>
              <input type="number" name="age" id="age" class="form-control" readonly>
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cell Number</label>
              <input type="text" name="mobile_number" class="form-control" required>
            </div>

            <h6 class="mt-4 mb-2 fw-bold text-primary">Mother's Info</h6>
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" name="mother_name" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Nickname</label>
              <input type="text" name="mother_nickname" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Age</label>
              <input type="number" name="mother_age" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Occupation</label>
              <input type="text" name="mother_occupation" class="form-control">
            </div>

            <h6 class="mt-4 mb-2 fw-bold text-primary">Father's Info</h6>
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" name="father_name" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Nickname</label>
              <input type="text" name="father_nickname" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Age</label>
              <input type="number" name="father_age" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Occupation</label>
              <input type="text" name="father_occupation" class="form-control">
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary"
                style="background-color:#0088a9;border:none;">Save Patient</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Auto compute age
document.getElementById('birthday').addEventListener('change', function() {
  const bday = new Date(this.value);
  const today = new Date();
  let age = today.getFullYear() - bday.getFullYear();
  const m = today.getMonth() - bday.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < bday.getDate())) age--;
  document.getElementById('age').value = age;
});

// Edit birthday auto-age
document.addEventListener("change", e => {
  if (e.target.matches(".birthday-edit")) {
    const id = e.target.dataset.ageId;
    const bday = new Date(e.target.value);
    const today = new Date();
    let age = today.getFullYear() - bday.getFullYear();
    const m = today.getMonth() - bday.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < bday.getDate())) age--;
    document.getElementById(id).value = age;
  }
});

// Delete patient
function deactivatePatient(id){
  Swal.fire({
    title:'Deactivate this patient?',
    text:'The patient will be marked as inactive but not removed from records.',
    icon:'warning',
    showCancelButton:true,
    confirmButtonColor:'#6c757d',
    cancelButtonColor:'#aaa',
    confirmButtonText:'Yes, deactivate'
  }).then(res=>{
    if(res.isConfirmed){
      window.location='../../controller/delete_patient.php?patient_id='+id;
    }
  });
}
</script>

<?php if (isset($_SESSION['alert'])): ?>
<script>
Swal.fire({
  icon: '<?= $_SESSION['alert']['type']; ?>',
  title: '<?= $_SESSION['alert']['title']; ?>',
  text: '<?= $_SESSION['alert']['text']; ?>',
  confirmButtonColor: '#0088a9'
});
</script>
<?php unset($_SESSION['alert']); endif; ?>

<style>
.table-dark{background-color:#004b63!important}
.modal-header{border-bottom:none!important}
.btn-primary:hover{background-color:#006f8c!important}
</style>
