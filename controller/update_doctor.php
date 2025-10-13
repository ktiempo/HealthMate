<?php
include('../db/config.php');

if (isset($_POST['update'])) {
  $id = intval($_POST['doctor_id']);
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $specialization = trim($_POST['specialization']);
  $credentials = trim($_POST['credentials']);
  $password = trim($_POST['password']);

  // Combine multiple schedule entries into one string
  $schedules = $_POST['schedule'];
  $schedule = implode(", ", array_filter($schedules));

  // Update doctor record
  $sql = "UPDATE doctors SET 
            name = '$name',
            email = '$email',
            specialization = '$specialization',
            schedule = '$schedule',
            credentials = '$credentials',
            password = '$password'
          WHERE doctor_id = $id";

  if ($conn->query($sql)) {
    echo "<script>
            alert('✅ Doctor information updated successfully!');
            window.location.href = '../view/admin/manage_doctors.php';
          </script>";
  } else {
    echo "<script>
            alert('❌ Error updating doctor information.');
            window.history.back();
          </script>";
  }
}
?>
