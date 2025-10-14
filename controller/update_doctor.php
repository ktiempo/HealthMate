<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include(__DIR__ . '/../db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $doctor_id = intval($_POST['doctor_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $specialization = trim($_POST['specialization']);
    $credentials = trim($_POST['credentials']);
    $password = trim($_POST['password']);

    // ✅ Update doctor info
    $stmt = $conn->prepare("UPDATE doctors SET name=?, email=?, specialization=?, credentials=?, password=? WHERE doctor_id=?");
    $stmt->bind_param("sssssi", $name, $email, $specialization, $credentials, $password, $doctor_id);
    $stmt->execute();
    $stmt->close();

    // ✅ Clear old schedules
    $conn->query("DELETE FROM doctor_slots WHERE doctor_id = $doctor_id");

    // ✅ Insert new slots
    if (!empty($_POST['day'])) {
        $ins = $conn->prepare("INSERT INTO doctor_slots (doctor_id, day_of_week, start_time, end_time, total_slots) VALUES (?, ?, ?, ?, ?)");
        foreach ($_POST['day'] as $i => $day) {
            $day_name = $_POST['day'][$i];
            $start = $_POST['start_time'][$i];
            $end = $_POST['end_time'][$i];
            $slots = intval($_POST['slots'][$i]);
            if (!empty($day_name) && !empty($start) && !empty($end)) {
                $ins->bind_param("isssi", $doctor_id, $day_name, $start, $end, $slots);
                $ins->execute();
            }
        }
        $ins->close();
    }

    $conn->close();

    // ✅ Proper overlay popup, not replacing background
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
      <meta charset='UTF-8'>
      <title>Doctor Updated</title>
      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      <style>
        html, body {
          margin: 0;
          height: 100%;
          background-color: transparent;
        }
      </style>
    </head>
    <body>
      <script>
        Swal.fire({
          title: '✅ Doctor Updated!',
          text: 'The doctor information and schedule have been saved successfully.',
          icon: 'success',
          confirmButtonText: 'Return to Manage Doctors',
          confirmButtonColor: '#0088a9',
          background: '#ffffff',
          color: '#004b63',
          backdrop: 'rgba(0,0,0,0.4)',
          customClass: {
            popup: 'rounded-4 shadow-lg'
          }
        }).then(() => {
          window.location.href = '../view/admin/manage_doctors.php';
        });
      </script>
    </body>
    </html>";
    exit;
} else {
    header('Location: ../view/admin/manage_doctors.php');
    exit;
}
?>
