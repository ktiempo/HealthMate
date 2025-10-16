<?php
session_start();
include('../db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $doctor_id = $_SESSION['doctor_id'];
  $name = $_POST['name'];
  $nickname = $_POST['nickname'];
  $birthday = $_POST['birthday'];
  $address = $_POST['address'];
  $gender = $_POST['gender'];
  $age = $_POST['age'];
  $mother_name = $_POST['mother_name'];
  $mother_nickname = $_POST['mother_nickname'];
  $mother_occupation = $_POST['mother_occupation'];
  $mother_age = $_POST['mother_age'];
  $father_name = $_POST['father_name'];
  $father_nickname = $_POST['father_nickname'];
  $father_occupation = $_POST['father_occupation'];
  $father_age = $_POST['father_age'];
  $mobile_number = $_POST['mobile_number'];

  $stmt = $conn->prepare("INSERT INTO patients (
      doctor_id, name, nickname, birthday, address, gender, age,
      mother_name, mother_nickname, mother_occupation, mother_age,
      father_name, father_nickname, father_occupation, father_age,
      mobile_number
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

  $stmt->bind_param(
    "isssssssssssssis",
    $doctor_id,
    $name,
    $nickname,
    $birthday,
    $address,
    $gender,
    $age,
    $mother_name,
    $mother_nickname,
    $mother_occupation,
    $mother_age,
    $father_name,
    $father_nickname,
    $father_occupation,
    $father_age,
    $mobile_number
  );

  if ($stmt->execute()) {
    $_SESSION['alert'] = [
      'type' => 'success',
      'title' => 'Patient Added!',
      'text' => 'The new patient has been successfully saved.'
    ];
  } else {
    $_SESSION['alert'] = [
      'type' => 'error',
      'title' => 'Error!',
      'text' => 'Something went wrong while saving the patient.'
    ];
  }

  $stmt->close();
  $conn->close();

  header("Location: ../view/doctor/patients.php");
  exit;
}
?>
