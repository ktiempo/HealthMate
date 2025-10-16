<?php
session_start();
include('../db/config.php');

if (isset($_GET['id']) && isset($_GET['status'])) {
  $id = intval($_GET['id']);
  $status = $_GET['status'];

  $allowed = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
  if (!in_array($status, $allowed)) {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'Invalid Status!',
      'text' => 'Operation not permitted.'
    ];
    header("Location: ../view/doctor/appointments.php");
    exit;
  }

  $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
  $stmt->bind_param("si", $status, $id);
  if ($stmt->execute()) {
    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Updated!',
      'text' => "Appointment marked as $status."
    ];
  } else {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'Error!',
      'text' => 'Failed to update appointment.'
    ];
  }
  $stmt->close();
  header("Location: ../view/doctor/appointments.php");
  exit;
}
?>
