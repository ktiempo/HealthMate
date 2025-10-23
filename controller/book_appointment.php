<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

include('../db/config.php');

$data = json_decode(file_get_contents("php://input"), true);

$doctor_id = intval($data['doctor_id']);
$mobile = $data['mobile_number'];
$date = $data['appointment_date'];
$time = $data['appointment_time'];
$reason = $data['reason'];

if (!$doctor_id || !$date || !$time) {
  echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
  exit;
}

// Get total slots for that time
$slotCheck = $conn->prepare("
  SELECT total_slots FROM doctor_slots 
  WHERE doctor_id = ? 
  AND TIME_FORMAT(start_time, '%h:%i %p') = SUBSTRING_INDEX(?, '-', 1)
");
$slotCheck->bind_param("is", $doctor_id, $time);
$slotCheck->execute();
$result = $slotCheck->get_result()->fetch_assoc();

if (!$result) {
  echo json_encode(['status' => 'error', 'message' => 'Slot not found']);
  exit;
}

$totalSlots = intval($result['total_slots']);

// Count booked slots
$count = $conn->prepare("
  SELECT COUNT(*) as booked 
  FROM appointments 
  WHERE doctor_id = ? 
  AND appointment_date = ? 
  AND appointment_time = ? 
  AND status IN ('Pending','Confirmed')
");
$count->bind_param("iss", $doctor_id, $date, $time);
$count->execute();
$booked = intval($count->get_result()->fetch_assoc()['booked'] ?? 0);

if ($booked >= $totalSlots) {
  echo json_encode(['status' => 'full', 'message' => 'Slot already full']);
  exit;
}

// Save appointment
$stmt = $conn->prepare("
  INSERT INTO appointments 
  (doctor_id, appointment_date, appointment_time, mobile_number, reason, status)
  VALUES (?, ?, ?, ?, ?, 'Pending')
");
$stmt->bind_param("issss", $doctor_id, $date, $time, $mobile, $reason);
if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully!']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to save appointment.']);
}
?>
