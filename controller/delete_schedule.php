<?php
session_start();
include('../db/config.php');

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM doctor_slots WHERE slot_id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  $_SESSION['alert'] = ['type'=>'success','title'=>'Deleted!','text'=>'Schedule removed successfully.'];
} else {
  $_SESSION['alert'] = ['type'=>'error','title'=>'Error!','text'=>'Failed to delete schedule.'];
}

header("Location: ../view/doctor/schedule.php");
exit;
?>
