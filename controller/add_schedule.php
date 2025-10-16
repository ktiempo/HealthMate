<?php
session_start();
include('../db/config.php');

$doctor_id = $_SESSION['doctor_id'];
$day = $_POST['day_of_week'];
$start = $_POST['start_time'];
$end = $_POST['end_time'];
$slots = $_POST['total_slots'];

$stmt = $conn->prepare("INSERT INTO doctor_slots (doctor_id, day_of_week, start_time, end_time, total_slots) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $doctor_id, $day, $start, $end, $slots);

if ($stmt->execute()) {
  $_SESSION['alert'] = ['type'=>'success','title'=>'Added!','text'=>'Schedule added successfully.'];
} else {
  $_SESSION['alert'] = ['type'=>'error','title'=>'Error!','text'=>'Failed to add schedule.'];
}

header("Location: ../view/doctor/schedule.php");
exit;
?>
