<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Fetch announcements (newest first)
$stmt = $conn->prepare("
  SELECT 
    a.announcement_id,
    a.title,
    a.content,
    a.date_posted,
    a.audience,
    u.full_name AS author_name
  FROM announcements a
  LEFT JOIN users u ON a.author_id = u.user_id
  WHERE a.audience IN ('all', 'student', 'class')
  ORDER BY a.date_posted DESC
");
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Announcements</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="portal-layout">

    <!-- Sidebar -->
    <?php include('sidebar_st.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search announcements..." class="search-bar" id="announceSearch">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfilePic.png" alt="User Avatar" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <!-- Announcements Section -->
      <section class="dashboard-body">
        <h1>Announcements</h1>
        <p class="semester-text">Stay updated with the latest campus news and reminders</p>

        <div class="announcement-grid">
          <?php if ($announcements->num_rows > 0): ?>
            <?php while ($row = $announcements->fetch_assoc()): ?>
              <div class="announcement-card">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="announce-date">
                  Posted by <?php echo htmlspecialchars($row['author_name'] ?? 'System'); ?> • 
                  <?php echo date('M d, Y', strtotime($row['date_posted'])); ?>
                </p>
                <p class="announce-preview">
                  <?php
                    $preview = mb_strimwidth(strip_tags($row['content']), 0, 150, "...");
                    echo htmlspecialchars($preview);
                  ?>
                </p>
                <button class="details-btn" onclick="openModal('modal<?php echo $row['announcement_id']; ?>')">View More</button>
              </div>

              <!-- Modal for each announcement -->
              <div id="modal<?php echo $row['announcement_id']; ?>" class="modal">
                <div class="modal-content">
                  <span class="close" onclick="closeModal('modal<?php echo $row['announcement_id']; ?>')">&times;</span>
                  <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                  <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p style="text-align:center; color:#555; font-style:italic;">No announcements available.</p>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Modal control
    function openModal(id) {
      document.getElementById(id).style.display = "flex";
    }
    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }
    window.onclick = function(event) {
      const modals = document.querySelectorAll(".modal");
      modals.forEach(m => {
        if (event.target === m) m.style.display = "none";
      });
    };

    // Search filter
    document.getElementById("announceSearch").addEventListener("keyup", function() {
      const query = this.value.toLowerCase();
      document.querySelectorAll(".announcement-card").forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(query) ? "block" : "none";
      });
    });
  </script>
</body>
</html>
