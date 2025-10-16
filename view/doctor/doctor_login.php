<?php
session_start();
include('../../db/config.php');

if (isset($_POST['login'])) {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT * FROM doctors WHERE email=? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();

    if ($password === $doctor['password']) {
      $_SESSION['doctor_id'] = $doctor['doctor_id'];
      $_SESSION['doctor_name'] = $doctor['name'];
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Incorrect password.";
    }
  } else {
    $error = "Doctor not found.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HealthMate Doctor Login</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: "Poppins", sans-serif;
      overflow: hidden;
      background: linear-gradient(135deg, #004b63, #0088a9);
      position: relative;
    }

    /* Background overlay image with blur */
    .bg-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('../../Images/bg1.jpg') center/cover no-repeat;
      filter: blur(8px);
      opacity: 0.4;
      z-index: 0;
    }

    /* Semi-transparent blue overlay */
    .color-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 30, 40, 0.55);
      z-index: 1;
    }

    /* Login Card */
    .login-card {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 40px 35px;
      width: 380px;
      color: white;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
      animation: fadeIn 1s ease;
    }

    .login-card .logo {
      font-size: 50px;
      color: #00d4ff;
      margin-bottom: 10px;
    }

    .login-card h3 {
      font-weight: 700;
      font-size: 24px;
      color: white;
      margin-bottom: 20px;
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
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-login:hover {
      background-color: #0083a0;
      transform: scale(1.02);
    }

    footer {
      position: absolute;
      bottom: 15px;
      text-align: center;
      color: white;
      font-size: 13px;
      opacity: 0.8;
      z-index: 2;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <!-- Background Layers -->
  <div class="bg-overlay"></div>
  <div class="color-overlay"></div>

  <!-- Login Card -->
  <div class="login-card">
    <div class="logo">
      <i class="bi bi-person-badge-fill"></i>
    </div>
    <h3>Doctor Login</h3>

    <form method="POST">
      <div class="mb-3 text-start">
        <label class="form-label text-white">Email</label>
        <input type="email" name="email" class="form-control" required>
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
        background: '#fefefe'
      });
    </script>
  <?php endif; ?>

</body>
</html>
