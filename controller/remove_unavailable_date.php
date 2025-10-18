<?php
session_start();
include('../db/config.php');

if (!isset($_SESSION['doctor_id'])) {
  http_response_code(403);
  echo json_encode(["status" => "error", "message" => "Unauthorized"]);
  exit;
}

$doctor_id = $_SESSION['doctor_id'];
$unavailable_date = $_POST['unavailable_date'] ?? null;

if (!$unavailable_date) {
  echo json_encode(["status" => "error", "message" => "No date provided"]);
  exit;
}

// Remove record
$stmt = $conn->prepare("DELETE FROM doctor_unavailable_dates WHERE doctor_id = ? AND unavailable_date = ?");
$stmt->bind_param("is", $doctor_id, $unavailable_date);
$stmt->execute();

echo json_encode(["status" => "success", "message" => "Unavailable date removed"]);
?>
