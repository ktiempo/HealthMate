<?php
include('../db/config.php');

if (isset($_POST['submit'])) {
    // Collect form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $specialization = trim($_POST['specialization']);
    $credentials = trim($_POST['credentials']);
    $password = trim($_POST['password']);
    $schedules = $_POST['schedule'];
    $schedule = implode(", ", array_filter($schedules));

    // Check duplicate email
    $check = $conn->query("SELECT * FROM doctors WHERE email = '$email'");
    if ($check->num_rows > 0) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Duplicate Email',
            text: 'That email already exists. Please use another.',
            background: '#fdfdfd',
            backdrop: 'rgba(0,0,0,0.4)',
            confirmButtonColor: '#0088a9'
        }).then(() => {
            window.history.back();
        });
        </script>";
        exit;
    }

    // Insert into DB
    $sql = "INSERT INTO doctors (name, email, specialization, schedule, credentials, password)
            VALUES ('$name', '$email', '$specialization', '$schedule', '$credentials', '$password')";

    if ($conn->query($sql)) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Doctor Added!',
            text: 'The doctor has been successfully registered.',
            background: '#fdfdfd',
            backdrop: 'rgba(0,0,0,0.4)',
            confirmButtonColor: '#0088a9',
            showClass: { popup: 'animate__animated animate__fadeInDown' },
            hideClass: { popup: 'animate__animated animate__fadeOutUp' }
        }).then(() => {
            window.location.href = '../view/admin/manage_doctors.php';
        });
        </script>";
    } else {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong while saving the doctor.',
            background: '#fdfdfd',
            backdrop: 'rgba(0,0,0,0.4)',
            confirmButtonColor: '#0088a9'
        }).then(() => {
            window.history.back();
        });
        </script>";
    }
}
?>
