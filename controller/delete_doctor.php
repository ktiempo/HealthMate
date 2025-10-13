<?php
include('../db/config.php');

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $query = "DELETE FROM doctors WHERE doctor_id = $id";

  if ($conn->query($query)) {
    echo "<script>
            alert('✅ Doctor deleted successfully!');
            window.location.href = '../view/admin/manage_doctors.php';
          </script>";
  } else {
    echo "<script>
            alert('❌ Error deleting doctor!');
            window.history.back();
          </script>";
  }
}
?>
