<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link" href="dashboard.php">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <!-- Doctors Dropdown -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#doctor-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-badge"></i><span>Doctors</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="doctor-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="add_doctor.php" class="doctor-submenu">
            <i class="bi bi-circle"></i><span>Add Doctor</span>
          </a>
        </li>
        <li>
          <a href="manage_doctors.php" class="doctor-submenu">
            <i class="bi bi-circle"></i><span>Manage Doctors</span>
          </a>
        </li>
      </ul>
    </li><!-- End Doctors Nav -->

    <!-- Patients -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="patients.php">
        <i class="bi bi-people"></i>
        <span>Patients</span>
      </a>
    </li><!-- End Patients Nav -->

    <!-- Appointments -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="appointments.php">
        <i class="bi bi-calendar-check"></i>
        <span>Appointments</span>
      </a>
    </li><!-- End Appointments Nav -->

  </ul>

</aside><!-- End Sidebar -->

<!-- Sidebar Custom Styles -->
<style>
  /* Sidebar background */
  .sidebar {
    background-color: #004b63;
  }

  /* All sidebar main items */
  .sidebar a,
  .sidebar span,
  .sidebar i {
    color: #002e3b; /* dark bluish text for main links */
  }

  /* White font ONLY for Add/Manage Doctors */
  .sidebar .nav-content a.doctor-submenu {
    color: #ffffff !important;
    font-weight: 500;
    padding-left: 45px;
  }

  /* Hover effect for dropdown items */
  .sidebar .nav-content a.doctor-submenu:hover {
    background-color: #0088a9;
    border-radius: 8px;
    color: #ffffff !important;
  }

  /* Chevron color (for dropdown arrow) */
  .sidebar .bi-chevron-down {
    color: #002e3b;
  }
</style>
