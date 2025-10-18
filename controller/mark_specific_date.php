<?php
session_start();
include('../db/config.php');

if (!isset($_SESSION['doctor_id'])) {
  http_response_code(403);
  echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
  exit;
}

$doctor_id = $_SESSION['doctor_id'];
$data = $_POST['unavailable_date'] ?? '';
if (!$data) exit(json_encode(["status"=>"error","message"=>"No date provided"]));

$dates = json_decode($data, true);
if (!is_array($dates)) $dates = [$data];

foreach ($dates as $date) {
  $stmt = $conn->prepare("INSERT IGNORE INTO doctor_unavailable_dates (doctor_id, unavailable_date) VALUES (?, ?)");
  $stmt->bind_param("is", $doctor_id, $date);
  $stmt->execute();
}

echo json_encode(["status"=>"success","message"=>"Selected date(s) marked as unavailable."]);
?>
