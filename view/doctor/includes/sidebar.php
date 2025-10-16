<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link active" href="dashboard.php">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Patients -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="patients.php">
        <i class="bi bi-people"></i>
        <span>Patients</span>
      </a>
    </li>

    <!-- Appointments -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="appointments.php">
        <i class="bi bi-calendar-check"></i>
        <span>Appointments</span>
      </a>
    </li>

    <!-- Schedule -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="schedule.php">
        <i class="bi bi-clock-history"></i>
        <span>My Schedule</span>
      </a>
    </li>

  </ul>

</aside><!-- End Sidebar -->

<style>
  /* Sidebar background */
  .sidebar {
    background-color: #004b63;
  }

  /* Default link style */
  .sidebar .nav-link {
    color: #e0e0e0;
    font-weight: 500;
    border-radius: 8px;
    margin-bottom: 6px;
    padding: 10px 15px;
    transition: all 0.3s ease;
  }

  /* Default icon color */
  .sidebar .nav-link i {
    color: #e0e0e0;
    font-size: 1.2rem;
    margin-right: 8px;
  }

  /* Hover effect */
  .sidebar .nav-link:hover {
    background-color: #0088a9;
    color: #ffffff;
  }

  /* Hover also changes icon */
  .sidebar .nav-link:hover i {
    color: #ffffff;
  }

  /* Active menu item */
  .sidebar .nav-link.active {
    background-color: #e8f5f8;
    color: #004b63;
    font-weight: 600;
  }

  /* Active icon */
  .sidebar .nav-link.active i {
    color: #004b63;
  }

  /* Remove bullet spacing */
  .sidebar ul {
    padding-left: 0;
  }
</style>
