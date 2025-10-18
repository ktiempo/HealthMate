<?php
session_start();
include('../db/config.php');

$doctor_id = $_POST['doctor_id'] ?? null;
$mobile = trim($_POST['mobile_number'] ?? '');
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (!$doctor_id || !$mobile || !$date || !$time || !$reason) {
  echo json_encode(["status" => "error", "message" => "Missing required fields."]);
  exit;
}

// 🧾 Validate mobile
if (!preg_match('/^[0-9]{11}$/', $mobile)) {
  echo json_encode(["status" => "error", "message" => "Please enter a valid 11-digit mobile number."]);
  exit;
}

// 🧍‍♀️ Check if patient exists and is registered under the doctor
$stmt = $conn->prepare("SELECT patient_id FROM patients WHERE doctor_id=? AND mobile_number=? LIMIT 1");
$stmt->bind_param("is", $doctor_id, $mobile);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if (!$patient) {
  echo json_encode([
    "status" => "error",
    "message" => "You are not registered under this doctor. Please contact the clinic to register first."
  ]);
  exit;
}
$patient_id = $patient['patient_id'];

// 🚫 Check if doctor is unavailable on that date
$unavail = $conn->prepare("SELECT 1 FROM doctor_unavailable_dates WHERE doctor_id=? AND unavailable_date=? LIMIT 1");
$unavail->bind_param("is", $doctor_id, $date);
$unavail->execute();
if ($unavail->get_result()->num_rows > 0) {
  echo json_encode(["status" => "error", "message" => "Doctor is unavailable on this date."]);
  exit;
}

// 🔒 Check slot availability (max 5)
$slot_label = "$time";
$count = $conn->prepare("
  SELECT COUNT(*) AS booked 
  FROM appointments 
  WHERE doctor_id=? AND appointment_date=? AND appointment_time=? 
  AND status IN ('Pending','Confirmed')
");
$count->bind_param("iss", $doctor_id, $date, $slot_label);
$count->execute();
$booked = $count->get_result()->fetch_assoc()['booked'] ?? 0;

if ($booked >= 5) {
  echo json_encode(["status" => "error", "message" => "This time slot is fully booked."]);
  exit;
}

// 🚫 Prevent duplicate booking
$dup = $conn->prepare("
  SELECT appointment_id FROM appointments 
  WHERE patient_id=? AND doctor_id=? AND appointment_date=? AND appointment_time=? 
  AND status IN ('Pending','Confirmed') LIMIT 1
");
$dup->bind_param("iiis", $patient_id, $doctor_id, $date, $slot_label);
$dup->execute();
if ($dup->get_result()->num_rows > 0) {
  echo json_encode(["status" => "error", "message" => "You already have an appointment in this slot."]);
  exit;
}

// ✅ Insert new appointment
$insert = $conn->prepare("
  INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status)
  VALUES (?, ?, ?, ?, ?, 'Pending')
");
$insert->bind_param("iisss", $patient_id, $doctor_id, $date, $slot_label, $reason);

if ($insert->execute()) {
  echo json_encode(["status" => "success", "message" => "Your appointment has been booked successfully!"]);
} else {
  echo json_encode(["status" => "error", "message" => "An error occurred. Please try again later."]);
}
?>
