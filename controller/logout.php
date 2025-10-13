<?php
session_start();
session_destroy();
header("Location: ../view/public_pages/login.html");
exit;
?>
