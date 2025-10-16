<?php
session_start();
include('../db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['patient_id'];

  $stmt = $conn->prepare("UPDATE patients SET 
    name=?, nickname=?, birthday=?, gender=?, age=?, address=?, mobile_number=?,
    mother_name=?, mother_nickname=?, mother_age=?, mother_occupation=?,
    father_name=?, father_nickname=?, father_age=?, father_occupation=?
    WHERE patient_id=?");

  $stmt->bind_param(
    "ssssissssisssssi",
    $_POST['name'], $_POST['nickname'], $_POST['birthday'], $_POST['gender'], $_POST['age'], $_POST['address'], $_POST['mobile_number'],
    $_POST['mother_name'], $_POST['mother_nickname'], $_POST['mother_age'], $_POST['mother_occupation'],
    $_POST['father_name'], $_POST['father_nickname'], $_POST['father_age'], $_POST['father_occupation'],
    $id
  );

  if ($stmt->execute()) {
    $_SESSION['alert'] = ['type'=>'success','title'=>'Updated!','text'=>'Patient information updated successfully.'];
  } else {
    $_SESSION['alert'] = ['type'=>'error','title'=>'Error!','text'=>'Failed to update patient info.'];
  }

  header("Location: ../view/doctor/patients.php");
  exit;
}
?>
