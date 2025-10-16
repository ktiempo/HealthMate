<?php
session_start();
include('../db/config.php');

if (isset($_GET['patient_id'])) {
  $id = intval($_GET['patient_id']);

  $sql = "UPDATE patients SET status = 'Inactive' WHERE patient_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Patient Archived!',
      'text' => 'This patient has been marked as inactive.'
    ];
  } else {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'Error!',
      'text' => 'Failed to update patient status.'
    ];
  }

  $stmt->close();
  $conn->close();
}

header("Location: ../view/doctor/patients.php");
exit;
?>
