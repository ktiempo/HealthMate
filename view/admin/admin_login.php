<?php
session_start();
include('../../db/config.php'); // ✅ correct path

if (isset($_POST['login'])) {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? OR email=? LIMIT 1");
  $stmt->bind_param("ss", $username, $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    if ($password === $admin['password']) {
      $_SESSION['admin_id'] = $admin['admin_id'];
      $_SESSION['admin_username'] = $admin['username'];
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Invalid password.";
    }
  } else {
    $error = "Admin not found.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HealthMate Admin Login</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom Style -->
  <style>
    body {
      background: linear-gradient(135deg, #004b63, #0088a9);
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: "Poppins", sans-serif;
      overflow: hidden;
    }

    .bg-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('../../Images/admin_bg.jpg') center/cover no-repeat;
      filter: blur(6px);
      opacity: 0.4;
      z-index: 0;
    }

    .login-card {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 40px 35px;
      width: 380px;
      color: white;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      animation: fadeIn 1s ease;
    }

    .login-card .logo {
      font-size: 40px;
      color: #00d4ff;
      margin-bottom: 15px;
    }

    .login-card h3 {
      font-weight: 700;
      font-size: 22px;
      color: white;
      margin-bottom: 25px;
    }

    .form-control {
      border-radius: 10px;
      padding: 10px 15px;
      border: none;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .btn-login {
      background-color: #00a5c4;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 10px;
      width: 100%;
      transition: all 0.3s;
      font-weight: 600;
    }

    .btn-login:hover {
      background-color: #007b91;
      transform: scale(1.02);
    }

    .text-muted {
      color: #cce7ef !important;
    }

    footer {
      position: absolute;
      bottom: 15px;
      color: white;
      font-size: 13px;
      text-align: center;
      width: 100%;
      opacity: 0.8;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="bg-overlay"></div>

  <div class="login-card">
    <div class="logo">
      <i class="bi bi-heart-pulse-fill"></i>
    </div>
    <h3>HealthMate Admin Login</h3>
    <form method="POST">
      <div class="mb-3 text-start">
        <label class="form-label text-white">Username or Email</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3 text-start">
        <label class="form-label text-white">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" name="login" class="btn-login mt-3">Login</button>
    </form>
  </div>

  <footer>
    © 2025 <strong>HealthMate</strong>. All Rights Reserved
  </footer>

  <?php if (isset($error)): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: '<?php echo $error; ?>',
        confirmButtonColor: '#00a5c4',
        background: '#fefefe',
      });
    </script>
  <?php endif; ?>

</body>
</html>
