<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

/* ---------------------------------------------------------
   HELPER: Build query strings safely
--------------------------------------------------------- */
function build_query(array $overrides = []): string {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null) unset($params[$k]);
        else $params[$k] = $v;
    }
    $q = http_build_query($params);
    return $q ? ("?" . $q) : "";
}

/* ---------------------------------------------------------
   SAVE ENROLLMENTS
--------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $course_id = (int)($_POST["course_id"] ?? 0);
    $selected_students = $_POST["students"] ?? [];

    if ($course_id > 0) {

        $selected_students = array_map("intval", $selected_students);

        // Clear old enrollments
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $stmt->close();

        // Insert new
        if (!empty($selected_students)) {
            $stmt = $conn->prepare("
                INSERT INTO enrollments (student_id, course_id, date_enrolled)
                VALUES (?, ?, NOW())
            ");
            foreach ($selected_students as $sid) {
                $stmt->bind_param("ii", $sid, $course_id);
                $stmt->execute();
            }
            $stmt->close();
        }
    }

    header("Location: enroll_students_admin.php" . build_query([
        "course" => $course_id,
        "saved" => 1,
        "student_search" => null
    ]));
    exit;
}

/* ---------------------------------------------------------
   FILTERS
--------------------------------------------------------- */
$course_id      = isset($_GET["course"]) ? (int)$_GET["course"] : 0;
$course_search  = trim($_GET["course_search"] ?? "");
$student_search = trim($_GET["student_search"] ?? "");

/* ---------------------------------------------------------
   LOAD COURSES (WITH SEARCH)
--------------------------------------------------------- */
$sql = "SELECT course_id, course_code, course_name FROM courses WHERE 1 ";
$params = [];
$types = "";

if ($course_search !== "") {
    $sql .= " AND (LOWER(course_code) LIKE ? OR LOWER(course_name) LIKE ?)";
    $like = "%" . strtolower($course_search) . "%";
    $params = [$like, $like];
    $types = "ss";
}

$sql .= " ORDER BY course_code ASC";

$stmt = $conn->prepare($sql);
if ($types !== "") $stmt->bind_param($types, ...$params);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Auto-select if search returns exactly 1 result
if ($course_id === 0 && $course_search !== "" && count($courses) === 1) {
    $course_id = (int)$courses[0]["course_id"];
}

/* ---------------------------------------------------------
   LOAD CURRENT COURSE DETAILS
--------------------------------------------------------- */
$currentCourse = null;
if ($course_id > 0) {
    $stmt = $conn->prepare("
        SELECT 
            c.course_id,
            c.course_code,
            c.course_name,
            c.units,
            c.semester,
            c.schedule_day,
            c.schedule_time,
            u.full_name AS teacher_name
        FROM courses c
        LEFT JOIN users u ON c.teacher_id = u.user_id
        WHERE c.course_id = ?
    ");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $currentCourse = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* ---------------------------------------------------------
   GET ENROLLED STUDENT IDS
--------------------------------------------------------- */
$enrolledIds = [];
if ($course_id > 0) {

    $stmt = $conn->prepare("SELECT student_id FROM enrollments WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();

    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $enrolledIds[] = (int)$row["student_id"];
    }
    $stmt->close();
}

/* ---------------------------------------------------------
   LOAD STUDENTS
--------------------------------------------------------- */
$students = [];
if ($course_id > 0) {
    $baseSql = "
        SELECT
            u.user_id,
            u.full_name,
            s.student_id AS academic_id,
            s.program,
            s.year_level,
            s.section
        FROM users u
        LEFT JOIN student_info s ON u.user_id = s.user_id
        WHERE u.role = 'student'
    ";

    $params = [];
    $types = "";

    if ($student_search !== "") {
        $baseSql .= "
            AND (
                u.full_name LIKE ?
                OR COALESCE(s.student_id,'') LIKE ?
                OR COALESCE(s.program,'') LIKE ?
                OR COALESCE(s.section,'') LIKE ?
            )
        ";
        $like = "%" . $student_search . "%";
        $params = [$like, $like, $like, $like];
        $types = "ssss";
    }

    $baseSql .= " ORDER BY u.full_name ASC";

    $stmt = $conn->prepare($baseSql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Escolink Centra | Enroll Students</title>
<link rel="stylesheet" href="CSS/format.css">
<link rel="stylesheet" href="CSS/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
<div class="portal-layout">

<?php include("sidebar_admin.php"); ?>

<main class="main-content">

<header class="topbar">
    <form class="search-container">
        <input type="text" class="search-bar" placeholder="Enrollment Page" disabled>
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
    </form>

    <div class="profile-section">
        <img src="images/ProfileImg.png" class="avatar">
        <span class="profile-name">System Administrator</span>
        <i class="fa-solid fa-chevron-down dropdown-icon"></i>
    </div>
</header>

<section class="dashboard-body">
<h1>Enroll Students</h1>
<p class="semester-text">Assign students to their respective subjects.</p>

<!-- TOP TOOLBAR -->
<div class="enroll-toolbar">
<form method="get" class="enroll-course-form">

    <!-- DROPDOWN -->
    <select name="course"
        onchange="
            if (this.value === '') this.form.course_search.value='';
            this.form.submit();
        ">
        <option value="">-- Select Course --</option>

        <?php foreach ($courses as $c): ?>
        <option value="<?= $c['course_id']; ?>"
            <?= ($course_id == $c['course_id']) ? "selected" : ""; ?>>
            <?= htmlspecialchars($c['course_code'] . " — " . $c['course_name']); ?>
        </option>
        <?php endforeach; ?>

    </select>

    <!-- SEARCH INPUT -->
    <input type="text"
        name="course_search"
        placeholder="Search course code or name..."
        value="<?= htmlspecialchars($course_search); ?>"
    >

    <!-- BUTTON -->
    <button type="submit" class="enroll-search-btn"
        onclick="this.form.course.value='';">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>

</form>
</div>

<!-- COURSE DETAILS -->
<?php if ($course_id > 0 && $currentCourse): ?>
<div class="enroll-course-summary">

    <div class="enroll-course-main">
        <h2><?= htmlspecialchars($currentCourse['course_code']); ?></h2>
        <p><?= htmlspecialchars($currentCourse['course_name']); ?></p>
    </div>

    <div class="enroll-course-meta">
        <?php if ($currentCourse["teacher_name"]): ?>
            <span><strong>Teacher:</strong> <?= htmlspecialchars($currentCourse["teacher_name"]); ?></span>
        <?php endif; ?>

        <?php if ($currentCourse["semester"]): ?>
            <span><strong>Semester:</strong> <?= htmlspecialchars($currentCourse["semester"]); ?></span>
        <?php endif; ?>

        <?php if ($currentCourse["schedule_day"] || $currentCourse["schedule_time"]): ?>
            <span><strong>Schedule:</strong>
                <?= htmlspecialchars(trim($currentCourse["schedule_day"] . " " . $currentCourse["schedule_time"])); ?>
            </span>
        <?php endif; ?>
    </div>

</div>

<?php else: ?>
<p class="enroll-empty-hint">Select a course above to manage enrollments.</p>
<?php endif; ?>

<!-- STUDENT SEARCH -->
<?php if ($course_id > 0): ?>
<form method="get" class="enroll-student-search">
    <input type="hidden" name="course" value="<?= $course_id; ?>">
    <input type="text" name="student_search"
        placeholder="Search students by name, ID, program, section..."
        value="<?= htmlspecialchars($student_search); ?>"
    >
    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>
<?php endif; ?>

<!-- STUDENT LIST -->
<?php if ($course_id > 0): ?>
<form method="post" class="enroll-students-form">
    <input type="hidden" name="course_id" value="<?= $course_id; ?>">

    <div class="enroll-students-card">

        <?php if (empty($students)): ?>
            <p class="enroll-no-students">No students found.</p>

        <?php else: ?>

            <?php foreach ($students as $st): ?>

            <?php
                $uid = (int)$st["user_id"];
                $isChecked = in_array($uid, $enrolledIds);

                $fullName = htmlspecialchars($st["full_name"] ?? "");
                $acadID   = $st["academic_id"] ? htmlspecialchars($st["academic_id"]) : "N/A";
                $prog     = $st["program"]     ? htmlspecialchars($st["program"]) : "N/A";
                $year     = $st["year_level"]  ? htmlspecialchars($st["year_level"]) : "N/A";
                $sec      = $st["section"]     ? htmlspecialchars($st["section"]) : "N/A";
            ?>

            <label class="enroll-students-row">

                <div class="enroll-student-left">
                    <input type="checkbox"
                        name="students[]"
                        value="<?= $uid; ?>"
                        <?= $isChecked ? "checked" : ""; ?>
                    >

                    <div class="enroll-student-info">
                        <span class="student-name"><?= $fullName; ?></span>

                        <div class="student-meta">
                            <span><strong>ID:</strong> <?= $acadID; ?></span>
                            <span><strong>Program:</strong> <?= $prog; ?></span>
                            <span><strong>Year:</strong> <?= $year; ?></span>
                            <span><strong>Section:</strong> <?= $sec; ?></span>
                        </div>
                    </div>
                </div>

            </label>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <button type="submit" class="save-btn enroll-save-btn">
        Save Enrollments
    </button>

    <?php if (!empty($_GET["saved"])): ?>
        <p class="enroll-status-msg">Enrollments updated.</p>
    <?php endif; ?>

</form>
<?php endif; ?>

</section>
</main>
</div>
</body>
</html>
