<?php
include("../includes/auth_session.php");
include("../includes/auth_teacher.php");
include("../config/db_connect.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

/* ---------------------------------------------------------
   CREATE ANNOUNCEMENT
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
    $stmt->close();
  }
  header("Location: announcements_prof.php");
  exit;
}

/* ---------------------------------------------------------
   EDIT ANNOUNCEMENT
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
  }
  header("Location: announcements_prof.php");
  exit;
}

/* ---------------------------------------------------------
   DELETE ANNOUNCEMENT
--------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  $id = (int)($_POST['announcement_id'] ?? 0);
  if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM announcements WHERE announcement_id = ? AND author_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
  }
  header("Location: announcements_prof.php");
  exit;
}

/* ---------------------------------------------------------
   GET COURSES HANDLED BY THIS TEACHER
--------------------------------------------------------- */
$courses = [];
$stmt = $conn->prepare("SELECT course_id, course_code, course_name FROM courses WHERE teacher_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $courses[] = $row;
$stmt->close();

/* ---------------------------------------------------------
   ANNOUNCEMENT VISIBILITY LOGIC
   Teachers should see:
   - audience = 'all'
   - audience = 'teachers'
   - audience = 'class' WHERE they teach the course
   - their own announcements
--------------------------------------------------------- */
$stmt = $conn->prepare("
  SELECT 
      a.announcement_id,
      a.title,
      a.content,
      a.date_posted,
      a.course_id,
      a.audience,
      c.course_code,
      c.course_name,
      u.full_name AS author_name
  FROM announcements a
  LEFT JOIN courses c ON c.course_id = a.course_id
  LEFT JOIN users u ON u.user_id = a.author_id
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
</head>

<body>
  <div class="portal-layout">
    <?php include('sidebar_prof.php'); ?>

    <main class="main-content">

      <!-- Topbar -->
      <header class="topbar">
        <div class="search-container">
          <input type="text" id="searchInput" class="search-bar" placeholder="Search announcements...">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>
        <div class="profile-section">
          <img src="images/ProfileImg.png" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
        </div>
      </header>

      <section class="dashboard-body">
        <h1>Announcements</h1>
        <p class="semester-text">Create, review, or manage your class announcements for students.</p>

        <!-- CREATE FORM -->
        <div class="announcement-form">
          <h3><i class="fa-solid fa-bullhorn"></i> Create New Announcement</h3>

          <form method="POST">
            <input type="hidden" name="action" value="create">

            <label>Title:</label>
            <input type="text" name="title" placeholder="Enter announcement title..." required>

            <label>Message:</label>
            <textarea name="content" rows="5" placeholder="Write your announcement here..." required></textarea>

            <label>Select Course:</label>
            <select name="course_id" required>
              <option value="">-- Select a course --</option>
              <?php foreach ($courses as $c): ?>
                <option value="<?= (int)$c['course_id'] ?>">
                  <?= htmlspecialchars($c['course_code'] . ': ' . $c['course_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <button type="submit" class="save-btn">Post Announcement</button>
          </form>
        </div>

        <!-- ANNOUNCEMENT LIST -->
        <section class="announcements-section" id="annList">
          <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

          <?php if ($announcements->num_rows > 0): ?>
            <?php while ($a = $announcements->fetch_assoc()): ?>

              <?php
                // Determine target label
                if ($a['audience'] === 'all') {
                    $target = "All Users";
                } elseif ($a['audience'] === 'teachers') {
                    $target = "All Teachers";
                } elseif ($a['audience'] === 'class') {
                    $target = $a['course_code'] ?: "Class";
                } else {
                    $target = "Unknown";
                }
              ?>

              <div class="announcement-card"
                data-id="<?= $a['announcement_id'] ?>"
                data-title="<?= htmlspecialchars($a['title']) ?>"
                data-content="<?= htmlspecialchars($a['content']) ?>"
                data-course="<?= (int)$a['course_id'] ?>"
              >
                <h3><?= htmlspecialchars($a['title']) ?></h3>
                <p class="announce-date">
                  Posted: <?= date("F j, Y", strtotime($a['date_posted'])) ?> |
                  Target: <?= htmlspecialchars($target) ?>
                </p>

                <p class="announce-preview"><?= nl2br(htmlspecialchars($a['content'])) ?></p>

                <div class="card-actions">

                  <!-- VIEW -->
                  <button class="details-btn" onclick="openView(this)">
                    <i class="fa-solid fa-eye"></i> View Details
                  </button>

                  <!-- EDIT: only if this teacher is the author -->
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
      <div id="viewBody" style="white-space:pre-wrap;"></div>
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
              <?= htmlspecialchars($c['course_code'] . ': ' . $c['course_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <div class="modal-buttons">
          <button type="submit" class="save-btn">Save Changes</button>
          <button type="button" class="cancel-btn" onclick="closeEdit()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- DELETE MODAL -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeDelete()">&times;</span>
      <h2>Delete Announcement</h2>
      <p>Are you sure you want to delete this announcement?</p>
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
    // -------- VIEW --------
    function openView(btn) {
      const card = btn.closest('.announcement-card');
      document.getElementById('viewTitle').textContent = card.dataset.title;
      document.getElementById('viewBody').textContent = card.dataset.content;
      document.getElementById('viewMeta').textContent = "";
      document.getElementById('viewModal').style.display = 'flex';
    }
    function closeView(){ document.getElementById('viewModal').style.display = 'none'; }

    // -------- EDIT --------
    function openEdit(btn) {
      const card = btn.closest('.announcement-card');
      document.getElementById('edit_id').value      = card.dataset.id;
      document.getElementById('edit_title').value   = card.dataset.title;
      document.getElementById('edit_content').value = card.dataset.content;
      document.getElementById('edit_course').value  = card.dataset.course;
      document.getElementById('editModal').style.display = 'flex';
    }
    function closeEdit(){ document.getElementById('editModal').style.display = 'none'; }

    // -------- DELETE --------
    function openDelete(btn){
      const id = btn.closest('.announcement-card').dataset.id;
      document.getElementById('delete_id').value = id;
      document.getElementById('deleteModal').style.display = 'flex';
    }
    function closeDelete(){ document.getElementById('deleteModal').style.display = 'none'; }

    // Close modals by clicking outside
    window.addEventListener('click', e => {
      ['viewModal','editModal','deleteModal'].forEach(id => {
        if (e.target === document.getElementById(id)) {
          document.getElementById(id).style.display = 'none';
        }
      });
    });

    // Filter announcements
    const searchInput = document.getElementById('searchInput');
    searchInput?.addEventListener('input', () => {
      const q = searchInput.value.toLowerCase();
      document.querySelectorAll('.announcement-card').forEach(card => {
        card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  </script>

</body>
</html>
