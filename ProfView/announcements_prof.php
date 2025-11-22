<?php
include("../includes/auth_session.php");
include("../includes/auth_teacher.php");
include("../config/db_connect.php");
include("../includes/logging.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? "Professor";

/* ---------------------------------------------------------
   FETCH PROFILE PICTURE
--------------------------------------------------------- */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = (!empty($profile_pic))
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfileImg.png";

/* ---------------------------------------------------------
   FETCH TEACHER'S COURSES (USED FOR LOGGING)
--------------------------------------------------------- */
$courseMap = [];
$stmt = $conn->prepare("SELECT course_id, course_code, course_name FROM courses WHERE teacher_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $courseMap[$row['course_id']] = $row['course_code'] . " - " . $row['course_name'];
}
$stmt->close();

/* ---------------------------------------------------------
   CREATE ANNOUNCEMENT
   LOGGING ADDED
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $title     = trim($_POST['title'] ?? '');
    $content   = trim($_POST['content'] ?? '');
    $course_id = (int)($_POST['course_id'] ?? 0);

    if ($title !== '' && $content !== '' && $course_id > 0) {

        $stmt = $conn->prepare("
          INSERT INTO announcements (title, content, author_id, audience, course_id, date_posted)
          VALUES (?, ?, ?, 'class', ?, NOW())
        ");
        $stmt->bind_param("ssii", $title, $content, $user_id, $course_id);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();

        /* ------ LOG: CREATE ------ */
        $cLabel = $courseMap[$course_id] ?? "Course ID {$course_id}";
        log_activity(
            $conn,
            (int)$user_id,
            "Posted Class Announcement",
            "Created announcement '{$title}' (ID {$newId}) for {$cLabel}.",
            "success"
        );
    }

    header("Location: announcements_prof.php");
    exit;
}

/* ---------------------------------------------------------
   EDIT ANNOUNCEMENT
   LOGGING ADDED
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id        = (int)($_POST['announcement_id'] ?? 0);
    $title     = trim($_POST['title'] ?? '');
    $content   = trim($_POST['content'] ?? '');
    $course_id = (int)($_POST['course_id'] ?? 0);

    if ($id > 0 && $title !== '' && $content !== '' && $course_id > 0) {

        $stmt = $conn->prepare("
          UPDATE announcements
          SET title = ?, content = ?, course_id = ?, date_posted = NOW()
          WHERE announcement_id = ? AND author_id = ?
        ");
        $stmt->bind_param("ssiii", $title, $content, $course_id, $id, $user_id);
        $stmt->execute();
        $stmt->close();

        /* ------ LOG: EDIT ------ */
        $cLabel = $courseMap[$course_id] ?? "Course ID {$course_id}";
        log_activity(
            $conn,
            (int)$user_id,
            "Edited Class Announcement",
            "Updated announcement '{$title}' (ID {$id}) for {$cLabel}.",
            "success"
        );
    }

    header("Location: announcements_prof.php");
    exit;
}

/* ---------------------------------------------------------
   DELETE ANNOUNCEMENT
   LOGGING ADDED
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['announcement_id'] ?? 0);

    if ($id > 0) {

        // Fetch announcement title & course for logging
        $stmt = $conn->prepare("
          SELECT title, course_id 
          FROM announcements 
          WHERE announcement_id = ? AND author_id = ?
        ");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->bind_result($delTitle, $delCourse);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM announcements WHERE announcement_id = ? AND author_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();

        /* ------ LOG: DELETE ------ */
        $cLabel = $courseMap[$delCourse] ?? "Course ID {$delCourse}";
        log_activity(
            $conn,
            (int)$user_id,
            "Deleted Class Announcement",
            "Deleted announcement '{$delTitle}' (ID {$id}) from {$cLabel}.",
            "success"
        );
    }

    header("Location: announcements_prof.php");
    exit;
}

/* ---------------------------------------------------------
   FETCH TEACHER COURSES AGAIN (for dropdown)
--------------------------------------------------------- */
$courses = [];
$stmt = $conn->prepare("SELECT course_id, course_code, course_name FROM courses WHERE teacher_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $courses[] = $row;
$stmt->close();

/* ---------------------------------------------------------
   FETCH ANNOUNCEMENTS VISIBLE TO TEACHER
--------------------------------------------------------- */
$stmt = $conn->prepare("
  SELECT 
      a.announcement_id,
      a.title,
      a.content,
      a.date_posted,
      a.audience,
      a.course_id,
      c.course_code,
      c.course_name,
      u.full_name AS author_name
  FROM announcements a
  LEFT JOIN courses c ON c.course_id = a.course_id
  LEFT JOIN users  u ON u.user_id  = a.author_id
  WHERE 
      a.audience = 'all'
      OR a.audience = 'teachers'
      OR (a.audience = 'class' AND c.teacher_id = ?)
      OR a.author_id = ?
  ORDER BY a.date_posted DESC
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Professor Announcements</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    /* lock body scroll when modal open */
    body.modal-open {
      overflow: hidden;
    }

    /* center modals (view / edit / delete) */
    .modal {
      display: none;
      align-items: center;
      justify-content: center;
    }

    /* make modal itself scrollable if content is long */
    .modal-content {
      max-height: 80vh !important;
      overflow-y: auto !important;
      word-wrap: break-word;
    }

    /* truncated preview in announcement cards */
    .announce-preview {
      max-height: 120px;
      overflow: hidden;
    }

    /* full text in view modal */
    #viewBody {
      white-space: pre-wrap;
      word-wrap: break-word;
      line-height: 1.6;
    }

    /* equal-size buttons in delete modal (prof view) */
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
    <?php include('sidebar_prof.php'); ?>

    <main class="main-content">

      <!-- CLEAN TOPBAR -->
      <header class="topbar">
        <div></div>
        <div class="profile-section">
          <img src="<?= $avatar ?>" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
        </div>
      </header>

      <section class="dashboard-body">
        <h1>Announcements</h1>
        <p class="semester-text">Create, review, or manage your class announcements for students.</p>

        <!-- ================= CREATE FORM ================= -->
        <div class="announcement-form">
          <h3><i class="fa-solid fa-bullhorn"></i> Create New Announcement</h3>

          <form method="POST">
            <input type="hidden" name="action" value="create">

            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Message:</label>
            <textarea name="content" rows="5" required></textarea>

            <label>Select Course:</label>
            <select name="course_id" required>
              <option value="">-- Select a course --</option>
              <?php foreach ($courses as $c): ?>
                <option value="<?= $c['course_id'] ?>">
                  <?= htmlspecialchars($c['course_code'] . ": " . $c['course_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <button type="submit" class="save-btn">Post Announcement</button>
          </form>
        </div>

        <!-- ================= LIST ================= -->
        <section class="announcements-section">
          <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

          <?php if ($announcements->num_rows > 0): ?>
            <?php while ($a = $announcements->fetch_assoc()): ?>
              <?php
                $aid     = (int)$a['announcement_id'];
                $title   = $a['title'] ?? '';
                $content = $a['content'] ?? '';

                // truncated preview like admin view
                if (mb_strlen($content) > 300) {
                    $preview = mb_substr($content, 0, 300) . "…";
                } else {
                    $preview = $content;
                }

                // target label (no nested ternary to avoid PHP 8 warning)
                if ($a['audience'] === 'all') {
                    $target = "All Users";
                } elseif ($a['audience'] === 'teachers') {
                    $target = "All Teachers";
                } elseif ($a['audience'] === 'class') {
                    $target = $a['course_code'] ?: "Class";
                } else {
                    $target = "Unknown";
                }

                $author = $a['author_name'] ?? 'Unknown';
              ?>

              <div class="announcement-card"
                data-id="<?= $aid ?>"
                data-title="<?= htmlspecialchars($title, ENT_QUOTES) ?>"
                data-content="<?= htmlspecialchars($content, ENT_QUOTES) ?>"
                data-target="<?= htmlspecialchars($target, ENT_QUOTES) ?>"
                data-author="<?= htmlspecialchars($author, ENT_QUOTES) ?>"
                data-course="<?= (int)$a['course_id'] ?>"
              >
                <h3><?= htmlspecialchars($title) ?></h3>
                <p class="announce-date">
                  Posted: <?= date("F j, Y", strtotime($a['date_posted'])) ?> |
                  Target: <?= htmlspecialchars($target) ?> |
                  By: <?= htmlspecialchars($author) ?>
                </p>

                <p class="announce-preview"><?= nl2br(htmlspecialchars($preview)) ?></p>

                <div class="card-actions">
                  <!-- VIEW -->
                  <button class="details-btn" onclick="openView(this)">
                    <i class="fa-solid fa-eye"></i> View Details
                  </button>

                  <!-- EDIT & DELETE: only if teacher authored it -->
                  <?php if ($a['author_name'] === $full_name): ?>
                    <button class="edit-btn" onclick="openEdit(this)">
                      <i class="fa-solid fa-pen"></i> Edit
                    </button>
                    <button class="delete-btn" onclick="openDelete(this)">
                      <i class="fa-solid fa-trash"></i> Delete
                    </button>
                  <?php endif; ?>
                </div>
              </div>

            <?php endwhile; ?>
          <?php else: ?>
            <p style="text-align:center; font-style:italic;">No announcements to show.</p>
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
        <input type="text" id="edit_title" name="title" required>

        <label>Message:</label>
        <textarea id="edit_content" name="content" rows="5" required></textarea>

        <label>Select Course:</label>
        <select name="course_id" id="edit_course" required>
          <?php foreach ($courses as $c): ?>
            <option value="<?= $c['course_id'] ?>">
              <?= htmlspecialchars($c['course_code'] . ": " . $c['course_name']) ?>
            </option>
          <?php endforeach; ?>
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
    <div class="modal-content" style="width:380px; text-align:center;">
      <span class="close" onclick="closeDelete()">&times;</span>
      <h2>Delete Announcement</h2>
      <p>Are you sure?</p>

      <form method="POST">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="announcement_id" id="delete_id">

        <div class="modal-buttons">
          <button class="delete-btn" type="submit">Delete</button>
          <button class="cancel-btn" type="button" onclick="closeDelete()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const viewModal   = document.getElementById('viewModal');
    const editModal   = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');

    const viewTitle = document.getElementById('viewTitle');
    const viewMeta  = document.getElementById('viewMeta');
    const viewBody  = document.getElementById('viewBody');

    const editId      = document.getElementById('edit_id');
    const editTitle   = document.getElementById('edit_title');
    const editContent = document.getElementById('edit_content');
    const editCourse  = document.getElementById('edit_course');

    const deleteId = document.getElementById('delete_id');

    function lockBody() {
      document.body.classList.add('modal-open');
    }
    function unlockBody() {
      document.body.classList.remove('modal-open');
    }

    // ---------- VIEW ----------
    function openView(btn) {
      const card    = btn.closest('.announcement-card');
      const title   = card.dataset.title || '';
      const content = card.dataset.content || '';
      const target  = card.dataset.target || '';
      const author  = card.dataset.author || '';

      viewTitle.textContent = title;

      let metaText = '';
      if (target) metaText += 'Target: ' + target;
      if (author) metaText += (metaText ? ' | ' : '') + 'By: ' + author;
      viewMeta.textContent = metaText;

      viewBody.textContent = content;

      viewModal.style.display = 'flex';
      lockBody();
    }
    function closeView() {
      viewModal.style.display = 'none';
      unlockBody();
    }

    // ---------- EDIT ----------
    function openEdit(btn) {
      const card    = btn.closest('.announcement-card');
      const id      = card.dataset.id;
      const title   = card.dataset.title || '';
      const content = card.dataset.content || '';
      const course  = card.dataset.course || '';

      editId.value      = id;
      editTitle.value   = title;
      editContent.value = content;
      editCourse.value  = course;

      editModal.style.display = 'flex';
      lockBody();
    }
    function closeEdit() {
      editModal.style.display = 'none';
      unlockBody();
    }

    // ---------- DELETE ----------
    function openDelete(btn) {
      const card = btn.closest('.announcement-card');
      const id   = card.dataset.id;

      deleteId.value = id;

      deleteModal.style.display = 'flex';
      lockBody();
    }
    function closeDelete() {
      deleteModal.style.display = 'none';
      unlockBody();
    }

    // click outside to close
    window.addEventListener('click', (e) => {
      if (e.target === viewModal)  closeView();
      if (e.target === editModal)  closeEdit();
      if (e.target === deleteModal) closeDelete();
    });
  </script>

</body>
</html>
