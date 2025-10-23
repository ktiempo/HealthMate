<?php
// 🔧 Add these first lines at the very top:
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

// then include your config
include('../db/config.php');

$doctor_id = intval($_GET['doctor_id'] ?? 0);

if (!$doctor_id) {
  echo json_encode(['error' => 'Invalid doctor ID']);
  exit;
}

$slots = [];
$stmt = $conn->prepare("SELECT day_of_week, start_time, end_time, total_slots FROM doctor_slots WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $timeLabel = date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time']));
  $count = $conn->prepare("SELECT COUNT(*) as booked FROM appointments WHERE doctor_id = ? AND appointment_time = ? AND status IN ('Pending','Confirmed')");
  $count->bind_param("is", $doctor_id, $timeLabel);
  $count->execute();
  $booked = $count->get_result()->fetch_assoc()['booked'] ?? 0;
  $remaining = max($row['total_slots'] - $booked, 0);

  $slots[] = [
    'day_of_week' => $row['day_of_week'],
    'start_time' => date("h:i A", strtotime($row['start_time'])),
    'end_time' => date("h:i A", strtotime($row['end_time'])),
    'remaining_slots' => $remaining
  ];
}

// 🚫 Fetch unavailable dates
$unavailable = [];
$q = $conn->prepare("SELECT unavailable_date FROM doctor_unavailable_dates WHERE doctor_id = ?");
$q->bind_param("i", $doctor_id);
$q->execute();
$res = $q->get_result();
while ($row = $res->fetch_assoc()) {
  $unavailable[] = date("Y-m-d", strtotime($row['unavailable_date']));
}

// ✅ Return clean JSON
echo json_encode([
  'slots' => $slots,
  'unavailable_dates' => $unavailable
]);
?>
