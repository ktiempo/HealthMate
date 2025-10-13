<?php
session_start();
include('../db/config.php');

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = $conn->query("SELECT * FROM admins WHERE username='$username' AND password='$password'");

if ($query->num_rows > 0) {
  $_SESSION['admin'] = $username;
  header("Location: ../view/admin/dashboard.php");
  exit;
} else {
  echo "<script>alert('Invalid credentials'); window.location.href='../view/public_pages/login.html';</script>";
}
?>
