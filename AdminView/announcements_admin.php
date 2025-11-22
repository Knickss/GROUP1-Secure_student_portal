<?php
include("../includes/auth_session.php");
include("../includes/auth_admin.php");
include("../config/db_connect.php");
include("../includes/logging.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? "Administrator";

/* ================= FETCH ADMIN PROFILE PIC ================= */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = !empty($profile_pic)
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfileImg.png";

/* ================= ALLOWED AUDIENCES ================= */
$validAudiences = ['all', 'students', 'teachers', 'class'];

/* ================= CREATE ANNOUNCEMENT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {

    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $audience = trim($_POST['audience'] ?? '');

    if ($title !== '' && $content !== '' && in_array($audience, $validAudiences, true)) {

        $stmt = $conn->prepare("
            INSERT INTO announcements (title, content, author_id, audience, date_posted)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssis", $title, $content, $user_id, $audience);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();

        log_activity(
            $conn,
            $user_id,
            "Created Announcement",
            "Created announcement '{$title}' (ID {$newId}).",
            "success"
        );
    }

    header("Location: announcements_admin.php");
    exit;
}

/* ================= EDIT ANNOUNCEMENT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {

    $id       = (int)($_POST['announcement_id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $audience = trim($_POST['audience'] ?? '');

    if ($id > 0 && $title !== '' && $content !== '' && in_array($audience, $validAudiences, true)) {

        $stmt = $conn->prepare("
            UPDATE announcements
            SET title = ?, content = ?, audience = ?, date_posted = NOW()
            WHERE announcement_id = ?
        ");
        $stmt->bind_param("sssi", $title, $content, $audience, $id);
        $stmt->execute();
        $stmt->close();

        log_activity(
            $conn,
            $user_id,
            "Edited Announcement",
            "Edited announcement '{$title}' (ID {$id}).",
            "success"
        );
    }

    header("Location: announcements_admin.php");
    exit;
}

/* ================= DELETE ANNOUNCEMENT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['announcement_id'] ?? 0);

    if ($id > 0) {
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

        log_activity(
            $conn,
            $user_id,
            "Deleted Announcement",
            "Deleted announcement '{$delTitle}' (ID {$id}).",
            "success"
        );
    }

    header("Location: announcements_admin.php");
    exit;
}

/* ================= FETCH ALL ================= */
$stmt = $conn->prepare("
  SELECT 
      a.announcement_id,
      a.title,
      a.content,
      a.audience,
      a.course_id,
      a.date_posted,
      u.full_name AS author_name,
      c.course_code
  FROM announcements a
  LEFT JOIN users   u ON u.user_id  = a.author_id
  LEFT JOIN courses c ON c.course_id = a.course_id
  ORDER BY a.date_posted DESC
");
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();

/* ================= CLEAN AUDIENCE LABEL ================= */
function format_audience($aud) {
    return match ($aud) {
        'students' => 'Students',
        'teachers' => 'Teachers',
        'all'      => 'All Users',
        'class'    => 'Class Announcement',
        default    => 'Unknown'
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

<style>
/* lock body scroll when modal open */
body.modal-open {
    overflow: hidden;
}

/* modal scrolling + layout */
.modal {
    display: none;
    align-items: center;
    justify-content: center;
}

.modal-content {
    max-height: 80vh !important;
    overflow-y: auto !important;
    word-wrap: break-word;
}

#viewBody {
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.6;
}

/* truncated preview in card */
.announce-preview {
    max-height: 120px;
    overflow: hidden;
}

/* equal size buttons in delete modal */
.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 15px;
}

.modal-buttons .delete-btn,
.modal-buttons .cancel-btn {
    min-width: 110px;
    padding: 8px 18px;
    text-align: center;
}
</style>

</head>

<body>
<div class="portal-layout">

<?php include('sidebar_admin.php'); ?>

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
<p class="semester-text">Post, review, or manage announcements across the platform.</p>

<!-- CREATE FORM -->
<div class="announcement-form">
  <h3><i class="fa-solid fa-bullhorn"></i> Create New Announcement</h3>

  <form method="POST">
    <input type="hidden" name="action" value="create">

    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Message:</label>
    <textarea name="content" rows="5" required></textarea>

    <label>Target Audience:</label>
    <select name="audience" required>
      <option value="">Select an audience</option>
      <option value="all">All Users</option>
      <option value="students">Students</option>
      <option value="teachers">Teachers</option>
    </select>

    <button class="save-btn" type="submit">Post Announcement</button>
  </form>
</div>

<!-- LIST -->
<section class="announcements-section">
  <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

  <?php if ($announcements->num_rows > 0): ?>
    <?php while ($a = $announcements->fetch_assoc()): ?>
      <?php
        $aid        = $a['announcement_id'];
        $title      = $a['title'];
        $content    = $a['content'];

        // truncate preview
        $preview = mb_strlen($content) > 300
            ? mb_substr($content, 0, 300) . "…"
            : $content;

        $aud        = $a['audience'];
        $author     = $a['author_name'] ?? 'Unknown';
        $courseCode = $a['course_code'] ?? '';

        $targetLabel = ($aud === 'class')
            ? ($courseCode ?: 'Class Announcement')
            : format_audience($aud);
      ?>

      <div class="announcement-card"
        data-id="<?= $aid ?>"
        data-title="<?= htmlspecialchars($title, ENT_QUOTES) ?>"
        data-content="<?= htmlspecialchars($content, ENT_QUOTES) ?>"
        data-audience="<?= $aud ?>"
        data-audiencelabel="<?= htmlspecialchars($targetLabel, ENT_QUOTES) ?>"
        data-author="<?= htmlspecialchars($author, ENT_QUOTES) ?>"
      >

        <h3><?= htmlspecialchars($title) ?></h3>

        <p class="announce-date">
          Posted: <?= date("F j, Y", strtotime($a['date_posted'])) ?>
          | Target: <?= htmlspecialchars($targetLabel) ?>
          | By: <?= htmlspecialchars($author) ?>
        </p>

        <p class="announce-preview"><?= nl2br(htmlspecialchars($preview)) ?></p>

        <div class="card-actions">
          <button class="details-btn" onclick="openView(this)"><i class="fa-solid fa-eye"></i> View</button>
          <button class="edit-btn" onclick="openEdit(this)"><i class="fa-solid fa-pen"></i> Edit</button>
          <button class="delete-btn" onclick="openDelete(this)"><i class="fa-solid fa-trash"></i> Delete</button>
        </div>
      </div>

    <?php endwhile; ?>
  <?php else: ?>
    <p class="empty-state">No announcements yet.</p>
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
    <p id="viewMeta"></p>
    <div id="viewBody"></div>
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
      <input type="text" name="title" id="edit_title" required>

      <label>Message:</label>
      <textarea name="content" id="edit_content" rows="5" required></textarea>

      <label>Audience:</label>
      <select name="audience" id="edit_audience" required>
         <option value="all">All Users</option>
         <option value="students">Students</option>
         <option value="teachers">Teachers</option>
         <option value="class">Course</option>
      </select>

      <button class="save-btn" type="submit">Save</button>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal">
  <div class="modal-content" style="width:380px; text-align:center;">
    <span class="close" onclick="closeDelete()">&times;</span>
    <h2>Delete Announcement?</h2>
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
// helpers
function lockBody() {
  document.body.classList.add('modal-open');
}
function unlockBody() {
  document.body.classList.remove('modal-open');
}

/* VIEW */
function openView(btn) {
  const c = btn.closest(".announcement-card");
  viewTitle.textContent = c.dataset.title;
  viewMeta.textContent  = "Target: " + c.dataset.audiencelabel + " | By: " + c.dataset.author;
  viewBody.innerHTML    = (c.dataset.content || "").replace(/\n/g, "<br>");

  viewModal.style.display = "flex";
  lockBody();
}
function closeView() {
  viewModal.style.display = "none";
  unlockBody();
}

/* EDIT */
function openEdit(btn) {
  const c = btn.closest(".announcement-card");
  edit_id.value       = c.dataset.id;
  edit_title.value    = c.dataset.title;
  edit_content.value  = c.dataset.content;
  edit_audience.value = c.dataset.audience;

  editModal.style.display = "flex";
  lockBody();
}
function closeEdit() {
  editModal.style.display = "none";
  unlockBody();
}

/* DELETE */
function openDelete(btn){
  const c = btn.closest(".announcement-card");
  delete_id.value = c.dataset.id;

  deleteModal.style.display = "flex";
  lockBody();
}
function closeDelete() {
  deleteModal.style.display = "none";
  unlockBody();
}

/* click outside to close */
window.addEventListener('click', (e) => {
  if (e.target === viewModal)  closeView();
  if (e.target === editModal)  closeEdit();
  if (e.target === deleteModal) closeDelete();
});
</script>

</body>
</html>
