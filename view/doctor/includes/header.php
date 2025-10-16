<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>HealthMate Doctor</title>
  <meta content="HealthMate Doctor Dashboard" name="description">
  <meta content="healthmate, doctor, clinic, appointment" name="keywords">

  <!-- Favicons -->
  <link href="../../assets/img/healthmate-logo.png" rel="icon">
  <link href="../../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../../assets/css/style.css" rel="stylesheet">

  <style>
    :root {
      --main-color: #0088a9;
      --secondary-color: #004b63;
      --light-bg: #f7f8fa;
    }

    html,
    body {
      height: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-bg);
    }

    main.main {
      flex: 1;
    }

    .header {
      background: var(--main-color);
      color: white;
    }

    .sidebar {
      background: var(--secondary-color);
    }

    .sidebar a {
      color: white;
    }

    .sidebar a.active,
    .sidebar a:hover {
      background: var(--main-color);
      color: #fff;
    }

    footer.footer {
      background: var(--secondary-color);
      color: white;
      position: relative;
      width: 100%;
      margin-top: auto;
      z-index: 1;
    }

    /* When modal is open, fix footer in place */
    body.modal-open footer.footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 1040; /* under Bootstrap modal (z-index 1050) */
    }
  </style>
</head>

<body>
