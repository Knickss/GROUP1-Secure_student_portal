<?php
include("../includes/auth_session.php");
include("../includes/auth_admin.php");
include("../config/db_connect.php");
include("../includes/logging.php"); // <-- ADDED

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? "Administrator";

/* ---------------- FETCH ADMIN PROFILE PIC ---------------- */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$profile_pic = $profile_pic ?? '';
$avatar = $profile_pic !== ''
    ? "../uploads/" . htmlspecialchars((string)$profile_pic)
    : "images/ProfileImg.png";

/* =========================================================
   CREATE ANNOUNCEMENT (ADMIN)
   LOGGING ADDED
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $audience = trim($_POST['audience'] ?? '');

    $validAudiences = ['all', 'students', 'professors'];

    if ($title !== '' && $content !== '' && in_array($audience, $validAudiences, true)) {

        $stmt = $conn->prepare("
            INSERT INTO announcements (title, content, author_id, audience, date_posted)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssis", $title, $content, $user_id, $audience);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();

        /* ---- LOG CREATE ---- */
        log_activity(
            $conn,
            (int)$user_id,
            "Created Announcement",
            "Created announcement '{$title}' (ID {$newId}), audience='{$audience}'.",
            "success"
        );
    }

    header("Location: announcements_admin.php");
    exit;
}

/* =========================================================
   EDIT ANNOUNCEMENT (ADMIN)
   LOGGING ADDED
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id       = (int)($_POST['announcement_id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $audience = trim($_POST['audience'] ?? '');

    $validAudiences = ['all', 'students', 'professors', 'class'];

    if ($id > 0 && $title !== '' && $content !== '' && in_array($audience, $validAudiences, true)) {

        $stmt = $conn->prepare("
            UPDATE announcements
            SET title = ?, content = ?, audience = ?, date_posted = NOW()
            WHERE announcement_id = ?
        ");
        $stmt->bind_param("sssi", $title, $content, $audience, $id);
        $stmt->execute();
        $stmt->close();

        /* ---- LOG EDIT ---- */
        log_activity(
            $conn,
            (int)$user_id,
            "Edited Announcement",
            "Edited announcement '{$title}' (ID {$id}), new audience='{$audience}'.",
            "success"
        );
    }

    header("Location: announcements_admin.php");
    exit;
}

/* =========================================================
   DELETE ANNOUNCEMENT (ADMIN)
   LOGGING ADDED
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['announcement_id'] ?? 0);

    if ($id > 0) {

        /* Fetch title for logging clarity */
        $stmt = $conn->prepare("SELECT title FROM announcements WHERE announcement_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($delTitle);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM announcements WHERE announcement_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        /* ---- LOG DELETE ---- */
        log_activity(
            $conn,
            (int)$user_id,
            "Deleted Announcement",
            "Deleted announcement '{$delTitle}' (ID {$id}).",
            "success"
        );
    }

    header("Location: announcements_admin.php");
    exit;
}

/* ---------------------------------------------------------
   FETCH ALL ANNOUNCEMENTS (ADMIN SEES EVERYTHING)
--------------------------------------------------------- */
$stmt = $conn->prepare("
  SELECT 
      a.announcement_id,
      a.title,
      a.content,
      a.audience,
      a.course_id,
      a.date_posted,
      u.full_name AS author_name,
      c.course_code,
      c.course_name
  FROM announcements a
  LEFT JOIN users   u ON u.user_id  = a.author_id
  LEFT JOIN courses c ON c.course_id = a.course_id
  ORDER BY a.date_posted DESC
");
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();

/* ---------------- SAFE AUDIENCE FORMATTER ---------------- */
function format_audience($aud) {
    $aud = (string)$aud;
    return match ($aud) {
        'students', 'student'       => 'Students',
        'professors', 'teachers'    => 'Professors',
        'all'                       => 'All Users',
        'class'                     => 'Class Announcement',
        default                     => 'Unknown'
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Admin Announcements</title>

  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="CSS/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<div class="portal-layout">

<?php include('sidebar_admin.php'); ?>

<main class="main-content">

<header class="topbar">
  <div class="topbar-left"></div>
  <div class="profile-section">
    <img src="<?= $avatar ?>" class="avatar" alt="Admin Avatar">
    <span class="profile-name"><?= htmlspecialchars((string)$full_name) ?></span>
  </div>
</header>

<section class="dashboard-body">

<h1>Announcements</h1>
<p class="semester-text">Post, review, or manage announcements across the platform.</p>

<!-- CREATE FORM -->
<div class="announcement-form">
  <h3><i class="fa-solid fa-bullhorn"></i> Create New Announcement</h3>

  <form method="POST">
    <input type="hidden" name="action" value="create">

    <label>Title:</label>
    <input type="text" name="title" placeholder="Enter announcement title..." required>

    <label>Message:</label>
    <textarea name="content" rows="5" placeholder="Write your announcement here..." required></textarea>

    <label>Target Audience:</label>
    <select name="audience" required>
      <option value="">Select an audience</option>
      <option value="all">All Users</option>
      <option value="students">Students</option>
      <option value="professors">Teachers</option>
    </select>

    <button class="save-btn" type="submit">Post Announcement</button>
  </form>
</div>

<!-- ANNOUNCEMENT LIST -->
<section class="announcements-section">
  <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

  <?php if ($announcements && $announcements->num_rows > 0): ?>
    <?php while ($a = $announcements->fetch_assoc()): ?>
      <?php
        $aid        = (int)($a['announcement_id'] ?? 0);
        $title      = (string)($a['title'] ?? '');
        $body       = (string)($a['content'] ?? '');
        $aud        = (string)($a['audience'] ?? '');
        $author     = (string)($a['author_name'] ?? 'Unknown');
        $courseCode = (string)($a['course_code'] ?? '');
        $courseName = (string)($a['course_name'] ?? '');

        $date_raw = $a['date_posted'] ?? '';
        $date_fmt = $date_raw ? date("F j, Y", strtotime($date_raw)) : 'Unknown date';

        // Target label logic (Option A for teacher posts)
        if ($aud === 'class') {
            // Teacher course announcement
            $targetLabel = $courseCode !== '' ? $courseCode : 'Class Announcement';
        } else {
            // Normal audience-based admin/seed announcement
            $targetLabel = format_audience($aud);
        }

        // For modal meta text
        $audLabelForMeta = $targetLabel;
      ?>

      <div class="announcement-card"
        data-id="<?= $aid ?>"
        data-title="<?= htmlspecialchars($title, ENT_QUOTES) ?>"
        data-content="<?= htmlspecialchars($body, ENT_QUOTES) ?>"
        data-audience="<?= htmlspecialchars($aud, ENT_QUOTES) ?>"
        data-audiencelabel="<?= htmlspecialchars($audLabelForMeta, ENT_QUOTES) ?>"
        data-author="<?= htmlspecialchars($author, ENT_QUOTES) ?>"
      >
        <h3><?= htmlspecialchars($title) ?></h3>

        <p class="announce-date">
          Posted: <?= htmlspecialchars($date_fmt) ?>
          | Target: <?= htmlspecialchars($targetLabel) ?>
          | By: <?= htmlspecialchars($author) ?>
        </p>

        <p class="announce-preview"><?= nl2br(htmlspecialchars($body)) ?></p>

        <div class="card-actions">
          <!-- Admin can edit/delete ANY announcement (Option A) -->
          <button class="details-btn" onclick="openView(this)">
            <i class="fa-solid fa-eye"></i> View Details
          </button>
          <button class="edit-btn" onclick="openEdit(this)">
            <i class="fa-solid fa-pen"></i> Edit
          </button>
          <button class="delete-btn" onclick="openDelete(this)">
            <i class="fa-solid fa-trash"></i> Delete
          </button>
        </div>
      </div>

    <?php endwhile; ?>
  <?php else: ?>
    <p style="text-align:center; font-style:italic;">No announcements yet.</p>
  <?php endif; ?>

</section>

</section>
</main>
</div>

<!-- VIEW MODAL -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeView()">&times;</span>
    <h2 id="viewTitle"></h2>
    <p id="viewMeta" style="color:#666;margin-top:-6px;"></p>
    <div id="viewBody" style="white-space:pre-wrap;margin-top:10px;"></div>
  </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEdit()">&times;</span>
    <h2>Edit Announcement</h2>

    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="announcement_id" id="edit_id">

      <label>Title:</label>
      <input type="text" id="edit_title" name="title" required>

      <label>Message:</label>
      <textarea id="edit_content" name="content" rows="5" required></textarea>

      <label>Target Audience:</label>
      <select name="audience" id="edit_audience" required>
        <option value="all">All Users</option>
        <option value="students">Students</option>
        <option value="professors">Teachers</option>
        <option value="class">Course (Teacher Post)</option>
      </select>

      <div class="modal-buttons">
        <button class="save-btn" type="submit">Save Changes</button>
        <button class="cancel-btn" type="button" onclick="closeEdit()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeDelete()">&times;</span>
    <h2>Delete Announcement</h2>
    <p>Are you sure?</p>

    <form method="POST">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="announcement_id" id="delete_id">

      <div class="modal-buttons">
        <button type="submit" class="delete-btn">Delete</button>
        <button type="button" class="cancel-btn" onclick="closeDelete()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function openView(btn) {
  const c = btn.closest('.announcement-card');
  document.getElementById('viewTitle').textContent = c.dataset.title || '';
  const target = c.dataset.audiencelabel || 'Unknown';
  const author = c.dataset.author || 'Unknown';
  document.getElementById('viewMeta').textContent  = "Target: " + target + " | By: " + author;
  document.getElementById('viewBody').textContent  = c.dataset.content || '';
  document.getElementById('viewModal').style.display = "flex";
}
function closeView() { document.getElementById('viewModal').style.display = "none"; }

function openEdit(btn) {
  const c = btn.closest('.announcement-card');
  document.getElementById('edit_id').value       = c.dataset.id || '';
  document.getElementById('edit_title').value    = c.dataset.title || '';
  document.getElementById('edit_content').value  = c.dataset.content || '';
  document.getElementById('edit_audience').value = c.dataset.audience || 'all';
  document.getElementById('editModal').style.display = "flex";
}
function closeEdit() { document.getElementById('editModal').style.display = "none"; }

function openDelete(btn){
  const c = btn.closest('.announcement-card');
  document.getElementById('delete_id').value = c.dataset.id || '';
  document.getElementById('deleteModal').style.display = "flex";
}
function closeDelete() { document.getElementById('deleteModal').style.display = "none"; }

window.addEventListener('click', (e) => {
  ['viewModal','editModal','deleteModal'].forEach(id => {
    const m = document.getElementById(id);
    if (e.target === m) m.style.display = 'none';
  });
});
</script>

</body>
</html>
