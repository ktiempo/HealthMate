<?php
include('../db/config.php');

if (!isset($_GET['doctor_id'])) {
  echo json_encode(["slots" => [], "unavailable_dates" => []]);
  exit;
}

$doctor_id = intval($_GET['doctor_id']);
$slots = [];
$unavailable = [];

// 🩵 Fetch unavailable dates (from doctor_unavailable_dates)
$res = $conn->query("SELECT unavailable_date FROM doctor_unavailable_dates WHERE doctor_id = $doctor_id");
while ($r = $res->fetch_assoc()) {
  $unavailable[] = $r['unavailable_date'];
}

// 🧠 Fetch doctor’s base weekly schedule
$sql = "SELECT day_of_week, start_time, end_time 
        FROM doctor_slots 
        WHERE doctor_id = ? 
        ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $day = $row['day_of_week'];
  $start = strtotime($row['start_time']);
  $end   = strtotime($row['end_time']);

  // break schedule into hourly slots
  while ($start < $end) {
    $slot_start = date("H:i:s", $start);
    $slot_end   = date("H:i:s", strtotime('+1 hour', $start));

    // check if that slot is open or closed
    $check = $conn->prepare("
      SELECT is_open FROM doctor_slot_status 
      WHERE doctor_id=? AND day_of_week=? 
      AND start_time=? AND end_time=? LIMIT 1
    ");
    $check->bind_param("isss", $doctor_id, $day, $slot_start, $slot_end);
    $check->execute();
    $slot_status = $check->get_result()->fetch_assoc();
    $is_open = $slot_status ? intval($slot_status['is_open']) : 1;

    if ($is_open) {
      // count booked appointments for this hour
      $slot_label = "$slot_start-$slot_end";
      $booked_sql = "SELECT COUNT(*) AS booked 
                     FROM appointments 
                     WHERE doctor_id = ? AND appointment_time = ? 
                     AND status IN ('Pending','Confirmed')";
      $booked_stmt = $conn->prepare($booked_sql);
      $booked_stmt->bind_param("is", $doctor_id, $slot_label);
      $booked_stmt->execute();
      $booked = $booked_stmt->get_result()->fetch_assoc()['booked'] ?? 0;

      $remaining = max(5 - $booked, 0); // max 5 per slot

      if ($remaining > 0) {
        $slots[] = [
          "day_of_week" => $day,
          "start_time" => date("g:i A", strtotime($slot_start)),
          "end_time" => date("g:i A", strtotime($slot_end)),
          "remaining_slots" => $remaining
        ];
      }
    }
    $start = strtotime('+1 hour', $start);
  }
}

header('Content-Type: application/json');
echo json_encode(["slots" => $slots, "unavailable_dates" => $unavailable]);
?>
