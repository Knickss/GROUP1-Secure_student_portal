<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Activity Logs</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="CSS/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="portal-layout">
    <!-- Sidebar -->
    <?php include('sidebar_admin.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search logs..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"
             onclick="showPopup('Search filter applied (UI placeholder).')"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfileImg.png" alt="User Avatar" class="avatar">
          <span class="profile-name">Admin Shamir</span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <!-- Body -->
      <section class="dashboard-body logs-wrapper">
        <h1>Activity Logs</h1>
        <p class="semester-text">Monitor system-wide actions and events across Escolink Centra</p>

        <!-- Filters -->
        <div class="logs-filters">
          <div class="filter-group">
            <label for="filterUser">User</label>
            <input type="text" id="filterUser" placeholder="Search by user name or ID">
          </div>

          <div class="filter-group">
            <label for="filterRole">Role</label>
            <select id="filterRole">
              <option value="">All Roles</option>
              <option>Student</option>
              <option>Professor</option>
              <option>Admin</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="filterDate">Date</label>
            <input type="date" id="filterDate">
          </div>

          <button class="apply-filter-btn"
                  onclick="showPopup('Filters applied (demo only).')">
            <i class="fa-solid fa-filter"></i> Apply Filters
          </button>

          <button class="clear-filter-btn"
                  onclick="clearFilters(); showPopup('Filters cleared.');">
            <i class="fa-solid fa-rotate-left"></i>
          </button>
        </div>

        <!-- Logs Table -->
        <div class="table-wrapper logs-table-wrapper">
          <table class="logs-table">
            <thead>
              <tr>
                <th>Date &amp; Time</th>
                <th>User</th>
                <th>Role</th>
                <th>Action</th>
                <th>Details</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2025-11-10 08:23</td>
                <td>2025-001 - Edelgard</td>
                <td>Student</td>
                <td>Logged In</td>
                <td>Accessed Student Dashboard</td>
                <td><button class="view-log-btn" onclick="openLogDetails('log1')">View</button></td>
              </tr>
              <tr>
                <td>2025-11-10 08:45</td>
                <td>Prof. Byleth</td>
                <td>Professor</td>
                <td>Updated Grades</td>
                <td>Saved grades for ITPROG-301 (IT-3A)</td>
                <td><button class="view-log-btn" onclick="openLogDetails('log2')">View</button></td>
              </tr>
              <tr>
                <td>2025-11-10 09:05</td>
                <td>Admin Shamir</td>
                <td>Admin</td>
                <td>Created User</td>
                <td>Added new professor account: Manuela</td>
                <td><button class="view-log-btn" onclick="openLogDetails('log3')">View</button></td>
              </tr>
              <tr>
                <td>2025-11-10 09:18</td>
                <td>Prof. Hanneman</td>
                <td>Professor</td>
                <td>Posted Announcement</td>
                <td>“Final Exams Schedule Released”</td>
                <td><button class="view-log-btn" onclick="openLogDetails('log4')">View</button></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Export Button -->
        <div class="logs-actions">
          <button class="logs-export-btn" onclick="openModal('exportLogsModal')">
            <i class="fa-solid fa-file-export"></i> Export Logs
          </button>
        </div>
      </section>
    </main>
  </div>

  <!-- Log Details Modal -->
  <div id="logDetailsModal" class="modal">
    <div class="modal-content user-modal">
      <span class="close" onclick="closeModal('logDetailsModal')">&times;</span>
      <div class="modal-header">
        <i class="fa-solid fa-clipboard-list"></i>
        <h2>Log Details</h2>
      </div>
      <div id="logDetailsBody" class="log-details-body"></div>
      <div class="button-group">
        <button class="cancel-btn" onclick="closeModal('logDetailsModal')">Close</button>
      </div>
    </div>
  </div>

  <!-- Export Logs Modal -->
  <div id="exportLogsModal" class="logs-modal">
    <div class="logs-modal-content">
      <span class="logs-modal-close" onclick="closeModal('exportLogsModal')">&times;</span>
      <div class="logs-modal-header">
        <i class="fa-solid fa-file-export"></i>
        <h2>Export Logs</h2>
      </div>
      <p class="logs-modal-desc">
        Choose the format and data range for exporting your system logs.
      </p>
      <form class="logs-export-form">
        <label for="format">Select Format</label>
        <select id="format">
          <option value="pdf">PDF</option>
          <option value="csv">CSV</option>
          <option value="xlsx">Excel (.xlsx)</option>
        </select>

        <label for="range">Date Range</label>
        <input type="date" id="range-start"> — <input type="date" id="range-end">

        <div class="logs-button-group">
          <button type="button" class="logs-export-confirm-btn" onclick="closeModal('exportLogsModal'); showPopup('Logs exported successfully!');">
            <i class="fa-solid fa-download"></i> Export
          </button>
          <button type="button" class="logs-export-cancel-btn" onclick="closeModal('exportLogsModal')">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Popup -->
  <div id="successPopup" class="success-popup">
    <i class="fa-solid fa-circle-check"></i>
    <span id="popupText">Action successful.</span>
  </div>

  <!-- JS Section -->
  <script>
    const logData = {
      log1: { title: "Student Login", datetime: "2025-11-10 08:23", user: "2025-001 - Edelgard", role: "Student", action: "Logged In", details: "User logged in and opened dashboard." },
      log2: { title: "Grades Updated", datetime: "2025-11-10 08:45", user: "Prof. Byleth", role: "Professor", action: "Updated Grades", details: "Updated midterm grades for ITPROG-301 (IT-3A)." },
      log3: { title: "New User Created", datetime: "2025-11-10 09:05", user: "Admin Shamir", role: "Admin", action: "Created User", details: "Created account for Manuela." },
      log4: { title: "Announcement Posted", datetime: "2025-11-10 09:18", user: "Prof. Hanneman", role: "Professor", action: "Posted Announcement", details: "Posted 'Final Exams Schedule Released'." }
    };

    function openModal(id) {
      document.getElementById(id).style.display = "flex";
    }

    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }

    function showPopup(msg) {
      const popup = document.getElementById("successPopup");
      const text = document.getElementById("popupText");
      text.innerText = msg;
      popup.style.display = "flex";
      popup.style.opacity = "1";
      setTimeout(() => {
        popup.style.opacity = "0";
        setTimeout(() => popup.style.display = "none", 400);
      }, 2000);
    }

    function openLogDetails(id) {
      const log = logData[id];
      if (!log) return;
      const body = document.getElementById("logDetailsBody");
      body.innerHTML = `
        <p><strong>Title:</strong> ${log.title}</p>
        <p><strong>Date &amp; Time:</strong> ${log.datetime}</p>
        <p><strong>User:</strong> ${log.user}</p>
        <p><strong>Role:</strong> ${log.role}</p>
        <p><strong>Action:</strong> ${log.action}</p>
        <p><strong>Details:</strong> ${log.details}</p>`;
      openModal('logDetailsModal');
    }

    function clearFilters() {
      document.getElementById('filterUser').value = '';
      document.getElementById('filterRole').value = '';
      document.getElementById('filterDate').value = '';
    }

    // Close on outside click
    window.onclick = function(event) {
      document.querySelectorAll(".modal, .logs-modal").forEach(m => {
        if (event.target === m) m.style.display = "none";
      });
    };
  </script>
</body>
</html>
