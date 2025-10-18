<?php include('../../db/config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find a Doctor | HealthMate</title>

  <!-- Google Fonts & Vendor -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Outfit:wght@500;600&display=swap" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --main: #0088a9;
      --dark: #004b63;
      --light: #f5f8fa;
      --white: #ffffff;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light);
      overflow-x: hidden;
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(rgba(0, 75, 99, 0.85), rgba(0, 75, 99, 0.85)),
                  url('../../Images/pa1.jpg') center/cover no-repeat;
      height: 60vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      text-align: center;
      padding: 0 20px;
    }

    .hero h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 2.7rem;
      font-weight: 600;
      color: #fff;
      text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
    }

    .hero p {
      font-size: 1.1rem;
      max-width: 650px;
      color: #e8f7fa;
      opacity: 0.95;
    }

    /* Doctor Container */
    .container-section {
      margin-top: -80px;
      background: #fff;
      border-radius: 30px 30px 0 0;
      box-shadow: 0 -2px 15px rgba(0,0,0,0.05);
      padding: 70px 20px 80px;
    }

    .doctor-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.08);
      padding: 25px;
      margin-bottom: 25px;
      border-left: 6px solid var(--main);
      transition: 0.3s;
    }

    .doctor-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,136,169,0.18);
    }

    .doctor-header {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .doctor-avatar {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: #e0f6fb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: var(--main);
    }

    .btn-appointment {
      background: var(--main);
      color: #fff;
      border-radius: 25px;
      border: none;
      padding: 8px 20px;
      transition: 0.3s;
    }

    .btn-appointment:hover {
      background: var(--dark);
      transform: scale(1.05);
    }

    footer {
      background: var(--dark);
      color: #fff;
      text-align: center;
      padding: 18px 0;
      margin-top: 50px;
    }

    /* Slot Styles */
    #slotContainer .slot-btn {
      background: #f0fafd;
      border: 1px solid #0088a9;
      border-radius: 10px;
      padding: 10px;
      color: #004b63;
      transition: 0.3s;
      text-align: center;
      cursor: pointer;
      flex: 1 1 calc(50% - 10px);
    }

    #slotContainer .slot-btn:hover,
    .slot-btn.selected {
      background: #0088a9;
      color: white;
      font-weight: 600;
    }
  </style>
</head>

<body>

  <section class="hero">
    <h1>Your Digital Bridge to Better Health</h1>
    <p>Find your doctor, book your visit — all in one place with HealthMate.</p>
  </section>

  <div class="container container-section">
    <div class="text-center mb-4">
      <h4 class="fw-bold text-dark">Available Doctors</h4>
    </div>

    <div class="search-bar d-flex align-items-center mx-auto mb-4"
         style="max-width:600px;background:#fff;border-radius:30px;box-shadow:0 4px 10px rgba(0,0,0,0.08);padding:10px 20px;">
      <i class="bi bi-search me-2" style="color:#0088a9;"></i>
      <input type="text" id="searchDoctor" class="form-control border-0"
             placeholder="Search doctor name or specialization..." onkeyup="searchDoctor()">
    </div>

    <div id="doctorList">
      <?php
      $sql = "SELECT d.*, 
              GROUP_CONCAT(CONCAT(ds.day_of_week, ' ',
              TIME_FORMAT(ds.start_time, '%h:%i %p'), ' - ',
              TIME_FORMAT(ds.end_time, '%h:%i %p'))
              ORDER BY FIELD(ds.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
              SEPARATOR ',') AS full_schedule
              FROM doctors d
              LEFT JOIN doctor_slots ds ON d.doctor_id = ds.doctor_id
              GROUP BY d.doctor_id ORDER BY d.name";

      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        while ($r = $result->fetch_assoc()) {
          $clinic = $r['clinic_address'] ?? 'No clinic address';
          $contact = $r['contact_number'] ?? 'No contact number';
          $credentials = $r['credentials'] ?? 'No credentials provided';
          $schedules = !empty($r['full_schedule']) ? explode(",", $r['full_schedule']) : [];
          echo "
          <div class='doctor-card doctor-item'>
            <div class='doctor-header'>
              <div class='doctor-avatar'><i class='bi bi-person-badge-fill'></i></div>
              <div><h5>{$r['name']}</h5><small>{$r['specialization']}</small></div>
            </div>
            <p><i class='bi bi-award'></i> <b>Credentials:</b> {$credentials}</p>
            <p><i class='bi bi-telephone'></i> <b>Contact:</b> {$contact}</p>
            <p><i class='bi bi-geo-alt'></i> <b>Clinic:</b> {$clinic}</p>
            <p><i class='bi bi-clock'></i> <b>Schedule:</b></p>
            <ul>";
            foreach ($schedules as $s) echo "<li>$s</li>";
          echo "</ul>
            <div class='text-end mt-3'>
              <button class='btn btn-appointment'
                      onclick=\"openBookingModal('{$r['doctor_id']}', '{$r['name']}', '{$r['specialization']}')\">
                <i class='bi bi-calendar-check'></i> Book Appointment
              </button>
            </div>
          </div>";
        }
      } else {
        echo "<p class='text-center text-muted'>No doctors found.</p>";
      }
      ?>
    </div>
  </div>

  <!-- Booking Modal -->
  <div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#0088a9;color:white;">
          <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Book Appointment</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="appointmentForm">
            <input type="hidden" id="doctor_id">
            <div class="mb-3"><label>Doctor</label><input type="text" id="doctor_name" class="form-control" readonly></div>
            <div class="mb-3"><label>Specialization</label><input type="text" id="specialization" class="form-control" readonly></div>
            <div class="mb-3"><label>Mobile Number</label><input type="text" id="mobile_number" class="form-control" required></div>

            <div class="mb-3">
              <label>Date</label>
              <div class="input-group">
                <span class="input-group-text" id="calendarIcon"><i class="bi bi-calendar-event"></i></span>
                <input type="text" id="appointment_date" class="form-control" placeholder="Select date" required readonly>
              </div>
            </div>

            <div class="mb-3">
              <label>Available Slots</label>
              <div id="slotContainer" class="d-flex flex-wrap gap-2 mt-2">
                <small class="text-muted">Select a date to see slots</small>
              </div>
              <input type="hidden" id="appointment_time" required>
            </div>

            <div class="mb-3">
              <label>Reason</label>
              <textarea id="reason" class="form-control" rows="3" required></textarea>
            </div>

            <div class="text-end">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary" style="background:#0088a9;border:none;">Book Now</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer>© 2025 <b>HealthMate</b>. All Rights Reserved</footer>

  <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <script>
let allSlots = [], unavailable = [], enabledDays = [], calendar;

// ✅ FIXED: initialize Flatpickr only after modal fully opens
document.getElementById("bookingModal").addEventListener("shown.bs.modal", function() {
  if (calendar) { calendar.destroy(); } // reset flatpickr each open
  calendar = flatpickr("#appointment_date", {
    altInput: true,
    altFormat: "F j, Y",
    dateFormat: "Y-m-d",
    minDate: "today",
    disableMobile: true
  });

  document.getElementById("calendarIcon").addEventListener("click", () => calendar.open());
});

function searchDoctor() {
  const input = document.getElementById("searchDoctor").value.toLowerCase();
  document.querySelectorAll(".doctor-item").forEach(d => {
    d.style.display = d.innerText.toLowerCase().includes(input) ? "" : "none";
  });
}

function openBookingModal(id, name, spec) {
  document.getElementById("doctor_id").value = id;
  document.getElementById("doctor_name").value = name;
  document.getElementById("specialization").value = spec;
  const slotContainer = document.getElementById("slotContainer");
  slotContainer.innerHTML = "<div class='text-muted small'>Loading...</div>";

  fetch(`../../controller/fetch_slots.php?doctor_id=${id}`)
    .then(res => res.json())
    .then(data => {
      allSlots = data.slots;
      unavailable = data.unavailable_dates;

      const workingDays = [...new Set(allSlots.map(s => s.day_of_week))];
      const dayMap = { Sunday:0, Monday:1, Tuesday:2, Wednesday:3, Thursday:4, Friday:5, Saturday:6 };
      const enabledDays = workingDays.map(d => dayMap[d]);

      if (calendar) {
        calendar.set({
          enable: [date => enabledDays.includes(date.getDay()) && !unavailable.includes(date.toISOString().split('T')[0])]
        });
      }

      slotContainer.innerHTML = "<div class='text-muted small'>Select a date to view available slots.</div>";
    });

  new bootstrap.Modal(document.getElementById("bookingModal")).show();
}

document.getElementById("appointment_date").addEventListener("change", function() {
  const date = new Date(this.value);
  const selectedDay = date.toLocaleDateString('en-US', { weekday: 'long' });
  const slotContainer = document.getElementById("slotContainer");
  slotContainer.innerHTML = "";
  const filtered = allSlots.filter(s => s.day_of_week === selectedDay && s.remaining_slots > 0);

  if (!filtered.length) {
    slotContainer.innerHTML = "<div class='text-muted small'>No slots for this day.</div>";
    return;
  }

  filtered.forEach(slot => {
    const btn = document.createElement("div");
    btn.className = "slot-btn";
    btn.textContent = `${slot.start_time} - ${slot.end_time} (${slot.remaining_slots} slots)`;
    btn.onclick = () => {
      document.querySelectorAll(".slot-btn").forEach(b => b.classList.remove("selected"));
      btn.classList.add("selected");
      document.getElementById("appointment_time").value = `${slot.start_time}-${slot.end_time}`;
    };
    slotContainer.appendChild(btn);
  });
});
  </script>
</body>
</html>
