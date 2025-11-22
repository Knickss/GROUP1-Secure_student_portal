<?php
include("../includes/auth_session.php");
include("../includes/auth_student.php");
include("../config/db_connect.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Student';

/* ===================== FETCH PROFILE PIC ===================== */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = !empty($profile_pic)
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfilePic.png";

/* ===================== FETCH ANNOUNCEMENTS ===================== */
$stmt = $conn->prepare("
  SELECT 
    a.announcement_id,
    a.title,
    a.content,
    a.date_posted,
    a.audience,
    a.course_id,
    u.full_name AS author_name,
    c.course_code
  FROM announcements a
  LEFT JOIN users u ON a.author_id = u.user_id
  LEFT JOIN courses c ON c.course_id = a.course_id
  WHERE 
      a.audience = 'all'
      OR a.audience = 'students'
      OR (a.audience = 'class' AND a.course_id IN (
            SELECT course_id FROM enrollments WHERE student_id = ?
         ))
  ORDER BY a.date_posted DESC
");
$stmt->bind_param("i", $user_id);
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

<style>
/* ================== MODAL PROPERTIES (MATCHES ADMIN + PROF) ================== */

/* Lock background scroll */
body.modal-open {
  overflow: hidden;
}

/* Modal wrapper */
.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  z-index: 999;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* Modal body scrolls independently */
.modal-content {
  max-height: 80vh;
  overflow-y: auto;
  background: white;
  padding: 25px;
  border-radius: 12px;
  position: relative;
  max-width: 550px;
  width: 100%;
}

/* Close button */
.modal .close {
  position: absolute;
  right: 18px;
  top: 12px;
  cursor: pointer;
  font-size: 22px;
  font-weight: bold;
  color: #b21e8f;
}

/* Title */
.modal-content h2 {
  color: #b21e8f;
  font-size: 22px;
  margin-bottom: 15px;
}

/* Wrapped modal content */
#viewBody,
.modal-content p {
  white-space: pre-wrap;
  word-wrap: break-word;
  line-height: 1.6;
}

/* Truncated preview in card */
.announce-preview {
  max-height: 120px;
  overflow: hidden;
}
</style>

</head>

<body>
<div class="portal-layout">

  <?php include('sidebar_st.php'); ?>

  <main class="main-content">

    <header class="topbar">
      <div></div>
      <div class="profile-section">
        <img src="<?= $avatar ?>" class="avatar">
        <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
      </div>
    </header>

    <section class="dashboard-body">
      <h1>Announcements</h1>
      <p class="semester-text">Stay updated with the latest campus news and reminders</p>

      <div class="announcement-grid">

        <?php while ($a = $announcements->fetch_assoc()): ?>

          <?php
            if ($a['audience'] === 'all')          $target = "All Users";
            elseif ($a['audience'] === 'students') $target = "All Students";
            elseif ($a['audience'] === 'class')    $target = $a['course_code'] ?? "Your Class";
            else                                    $target = "Unknown";

            $content = $a['content'] ?? '';

            // truncate preview to match admin/prof behavior
            if (mb_strlen($content) > 300) {
                $preview = mb_substr($content, 0, 300) . "…";
            } else {
                $preview = $content;
            }

          ?>

          <div class="announcement-card"
            data-title="<?= htmlspecialchars($a['title'], ENT_QUOTES) ?>"
            data-content="<?= htmlspecialchars($content, ENT_QUOTES) ?>"
            data-target="<?= htmlspecialchars($target, ENT_QUOTES) ?>"
            data-author="<?= htmlspecialchars($a['author_name'] ?? 'System', ENT_QUOTES) ?>"
            data-id="m<?= $a['announcement_id'] ?>"
          >

            <h3><?= htmlspecialchars($a['title']) ?></h3>

            <p class="announce-date">
              Posted by <?= htmlspecialchars($a['author_name'] ?? "System") ?>
              • <?= date("M d, Y", strtotime($a['date_posted'])) ?>
              • Target: <?= htmlspecialchars($target) ?>
            </p>

            <p class="announce-preview"><?= nl2br(htmlspecialchars($preview)) ?></p>

            <button class="details-btn" onclick="openModal('m<?= $a['announcement_id'] ?>')">
              <i class="fa-solid fa-eye"></i> View Details
            </button>
          </div>

          <!-- MODAL -->
          <div id="m<?= $a['announcement_id'] ?>" class="modal">
            <div class="modal-content">
              <span class="close" onclick="closeModal('m<?= $a['announcement_id'] ?>')">&times;</span>
              <h2><?= htmlspecialchars($a['title']) ?></h2>
              <p><?= nl2br(htmlspecialchars($content)) ?></p>
            </div>
          </div>

        <?php endwhile; ?>

      </div>

    </section>
  </main>
</div>

<script>
// Lock/unlock background
function lockBody(){ document.body.classList.add("modal-open"); }
function unlockBody(){ document.body.classList.remove("modal-open"); }

function openModal(id){
  document.getElementById(id).style.display = "flex";
  lockBody();
}

function closeModal(id){
  document.getElementById(id).style.display = "none";
  unlockBody();
}

// Close when clicking outside modal
window.onclick = function(e){
  document.querySelectorAll(".modal").forEach(m => {
    if (e.target === m){
      m.style.display = "none";
      unlockBody();
    }
  });
};
</script>

</body>
</html>
