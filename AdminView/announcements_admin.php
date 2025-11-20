<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

/* ---------------- Create (ADMIN) ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
  $title    = trim($_POST['title'] ?? '');
  $content  = trim($_POST['content'] ?? '');
  $audience = trim($_POST['audience'] ?? '');

  // allow only known audiences
  $validAudiences = ['all', 'students', 'professors'];

  if ($title !== '' && $content !== '' && in_array($audience, $validAudiences, true)) {
    $stmt = $conn->prepare("
      INSERT INTO announcements (title, content, author_id, audience, date_posted)
      VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssis", $title, $content, $user_id, $audience);
    $stmt->execute();
    $stmt->close();
  }

  header("Location: announcements_admin.php");
  exit;
}

/* ---------------- Edit (ADMIN) ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
  $id       = (int)($_POST['announcement_id'] ?? 0);
  $title    = trim($_POST['title'] ?? '');
  $content  = trim($_POST['content'] ?? '');
  $audience = trim($_POST['audience'] ?? '');

  $validAudiences = ['all', 'students', 'professors'];

  if ($id > 0 && $title !== '' && $content !== '' && in_array($audience, $validAudiences, true)) {
    $stmt = $conn->prepare("
      UPDATE announcements
      SET title = ?, content = ?, audience = ?, date_posted = NOW()
      WHERE announcement_id = ? AND author_id = ?
    ");
    $stmt->bind_param("sssii", $title, $content, $audience, $id, $user_id);
    $stmt->execute();
    $stmt->close();
  }

  header("Location: announcements_admin.php");
  exit;
}

/* ---------------- Delete (ADMIN) ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  $id = (int)($_POST['announcement_id'] ?? 0);

  if ($id > 0) {
    $stmt = $conn->prepare("
      DELETE FROM announcements
      WHERE announcement_id = ? AND author_id = ?
    ");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
  }

  header("Location: announcements_admin.php");
  exit;
}

/* ---------------- Fetch announcements authored by this admin ---------------- */
$stmt = $conn->prepare("
  SELECT announcement_id, title, content, audience, date_posted
  FROM announcements
  WHERE author_id = ?
  ORDER BY date_posted DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();

// small helper for audience label
function format_audience(string $aud): string {
  switch ($aud) {
    case 'students':   return 'Students';
    case 'professors': return 'Professors';
    case 'all':
    default:           return 'All Users';
  }
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
    <!-- Sidebar -->
    <?php include('sidebar_admin.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">

  <!-- Removed Search Bar – leave empty space or adjust layout -->
  <div class="topbar-left"></div>

  <div class="profile-section">
      <img src="<?php echo $avatar; ?>" alt="User Avatar" class="avatar">

      <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
      <!-- Removed dropdown arrow -->
  </div>

</header>


      <!-- Body -->
      <section class="dashboard-body">
        <h1>Announcements</h1>
        <p class="semester-text">
          Post, review, or manage announcements across the Escolink Centra platform.
        </p>

        <!-- Create Announcement (backend-enabled) -->
        <div class="announcement-form">
          <h3><i class="fa-solid fa-bullhorn"></i> Create New Announcement</h3>

          <form method="POST" action="">
            <input type="hidden" name="action" value="create">

            <label for="title">Title:</label>
            <input
              type="text"
              id="title"
              name="title"
              placeholder="Enter announcement title..."
              required
            >

            <label for="message">Message:</label>
            <textarea
              id="message"
              name="content"
              placeholder="Write your announcement here..."
              rows="5"
              required
            ></textarea>

            <label for="audience">Target Audience:</label>
            <select id="audience" name="audience" required>
              <option value="">Select an audience</option>
              <option value="all">All Users</option>
              <option value="students">Students</option>
              <option value="professors">Professors</option>
            </select>

            <button type="submit" class="save-btn">Post Announcement</button>
          </form>
        </div>

        <!-- Recent Announcements -->
        <section class="announcements-section" id="annList">
          <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

          <?php if ($announcements->num_rows > 0): ?>
            <?php while ($a = $announcements->fetch_assoc()): ?>
              <?php
                $aid      = (int)$a['announcement_id'];
                $title    = $a['title'] ?? '';
                $body     = $a['content'] ?? '';
                $aud      = $a['audience'] ?? 'all';
                $audLabel = format_audience($aud);
              ?>
              <div
                class="announcement-card"
                data-id="<?php echo $aid; ?>"
                data-title="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>"
                data-content="<?php echo htmlspecialchars($body, ENT_QUOTES); ?>"
                data-audience="<?php echo htmlspecialchars($aud, ENT_QUOTES); ?>"
                data-audiencelabel="<?php echo htmlspecialchars($audLabel, ENT_QUOTES); ?>"
              >
                <h3><?php echo htmlspecialchars($title); ?></h3>
                <p class="announce-date">
                  Posted: <?php echo date("F j, Y", strtotime($a['date_posted'])); ?>
                  | Target: <?php echo htmlspecialchars($audLabel); ?>
                </p>
                <p class="announce-preview">
                  <?php echo nl2br(htmlspecialchars($body)); ?>
                </p>

                <div class="card-actions">
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
            <p style="text-align:center; font-style:italic;">
              No announcements posted yet.
            </p>
          <?php endif; ?>
        </section>
      </section>
    </main>
  </div>

  <!-- ===================== MODALS ===================== -->

  <!-- VIEW DETAILS -->
  <div id="viewModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeView()">&times;</span>
      <h2 id="viewTitle"></h2>
      <p id="viewMeta" style="color:#666;margin-top:-6px;"></p>
      <div id="viewBody" style="white-space:pre-wrap;margin-top:10px;"></div>
    </div>
  </div>

  <!-- EDIT ANNOUNCEMENT -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEdit()">&times;</span>
      <h2>Edit Announcement</h2>
      <form method="POST" action="">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="announcement_id" id="edit_id">

        <label>Title:</label>
        <input type="text" name="title" id="edit_title" required>

        <label>Message:</label>
        <textarea name="content" id="edit_content" rows="5" required></textarea>

        <label>Target Audience:</label>
        <select name="audience" id="edit_audience" required>
          <option value="all">All Users</option>
          <option value="students">Students</option>
          <option value="professors">Professors</option>
        </select>

        <div class="modal-buttons">
          <button class="save-btn" type="submit">Save Changes</button>
          <button class="cancel-btn" type="button" onclick="closeEdit()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- DELETE CONFIRM -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeDelete()">&times;</span>
      <h2>Delete Announcement</h2>
      <p>Are you sure you want to delete this announcement?</p>
      <form method="POST" action="" style="margin-top:10px;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="announcement_id" id="delete_id">
        <div class="modal-buttons">
          <button type="submit" class="delete-btn">Delete</button>
          <button type="button" class="cancel-btn" onclick="closeDelete()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===================== JS ===================== -->
  <script>
    // --- View ---
    function openView(btn) {
      const card = btn.closest('.announcement-card');
      const title = card.dataset.title || '';
      const body = card.dataset.content || '';
      const audLabel = card.dataset.audiencelabel || '';

      document.getElementById('viewTitle').textContent = title;
      document.getElementById('viewMeta').textContent = 'Target Audience: ' + audLabel;
      document.getElementById('viewBody').textContent = body;

      document.getElementById('viewModal').style.display = 'flex';
    }
    function closeView() {
      document.getElementById('viewModal').style.display = 'none';
    }

    // --- Edit ---
    function openEdit(btn) {
      const card = btn.closest('.announcement-card');

      document.getElementById('edit_id').value      = card.dataset.id || '';
      document.getElementById('edit_title').value   = card.dataset.title || '';
      document.getElementById('edit_content').value = card.dataset.content || '';
      document.getElementById('edit_audience').value = card.dataset.audience || 'all';

      document.getElementById('editModal').style.display = 'flex';
    }
    function closeEdit() {
      document.getElementById('editModal').style.display = 'none';
    }

    // --- Delete ---
    function openDelete(btn) {
      const card = btn.closest('.announcement-card');
      const id = card.dataset.id || '';

      document.getElementById('delete_id').value = id;
      document.getElementById('deleteModal').style.display = 'flex';
    }
    function closeDelete() {
      document.getElementById('deleteModal').style.display = 'none';
    }

    // Close any modal by clicking outside
    window.addEventListener('click', (e) => {
      ['viewModal', 'editModal', 'deleteModal'].forEach(id => {
        const m = document.getElementById(id);
        if (e.target === m) m.style.display = 'none';
      });
    });

    // Simple client-side filter (same style as prof view)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase();
        document.querySelectorAll('#annList .announcement-card').forEach(card => {
          const text = card.textContent.toLowerCase();
          card.style.display = text.includes(q) ? '' : 'none';
        });
      });
    }
  </script>
</body>
</html>
