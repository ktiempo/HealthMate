<?php
session_start();
include('../db/config.php');

$id = $_POST['slot_id'];
$day = $_POST['day_of_week'];
$start = $_POST['start_time'];
$end = $_POST['end_time'];
$slots = $_POST['total_slots'];

$stmt = $conn->prepare("UPDATE doctor_slots SET day_of_week=?, start_time=?, end_time=?, total_slots=? WHERE slot_id=?");
$stmt->bind_param("sssii", $day, $start, $end, $slots, $id);

if ($stmt->execute()) {
  $_SESSION['alert'] = ['type'=>'success','title'=>'Updated!','text'=>'Schedule updated successfully.'];
} else {
  $_SESSION['alert'] = ['type'=>'error','title'=>'Error!','text'=>'Failed to update schedule.'];
}

header("Location: ../view/doctor/schedule.php");
exit;
?>
