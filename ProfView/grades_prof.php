<?php
include('../includes/auth_session.php');
include("../includes/auth_teacher.php");
include('../config/db_connect.php');
include("../includes/logging.php"); // <-- ADDED

// Block non-teachers
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../LoginPage/login.php");
    exit;
}

$teacher_id  = $_SESSION['user_id'];
$full_name   = $_SESSION['full_name'] ?? 'Teacher';

// ===== FETCH PROFILE PIC =====
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = (!empty($profile_pic))
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfileImg.png";

// Pre-set values
$selected_course_id = $_GET['course_id'] ?? null;
$students = [];
$success_message = "";


// ===============================================================
//  SAVE GRADES (LOGGING ADDED)
// ===============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grades'])) {

    $course_id = (int)($_POST['course_id'] ?? 0);

    // Fetch course label for logging clarity
    $stmtC = $conn->prepare("SELECT course_code, course_name FROM courses WHERE course_id = ?");
    $stmtC->bind_param("i", $course_id);
    $stmtC->execute();
    $stmtC->bind_result($ccode, $cname);
    $stmtC->fetch();
    $stmtC->close();
    $courseLabel = "{$ccode} - {$cname}";

    foreach ($_POST['grades'] as $student_id => $grade) {

        if ($grade === "") continue;

        $student_id = (int)$student_id;
        $newGrade = floatval($grade);

        // Fetch old grade for logging comparison
        $stmtOld = $conn->prepare("SELECT grade FROM grades WHERE student_id=? AND course_id=?");
        $stmtOld->bind_param("ii", $student_id, $course_id);
        $stmtOld->execute();
        $stmtOld->bind_result($oldGrade);
        $stmtOld->fetch();
        $stmtOld->close();

        $hadOld = ($oldGrade !== null);

        // Save grade to DB
        $stmt = $conn->prepare("
            INSERT INTO grades (student_id, course_id, grade, encoded_by, date_encoded)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                grade = VALUES(grade),
                encoded_by = VALUES(encoded_by),
                date_encoded = NOW()
        ");
        $stmt->bind_param("iidi", $student_id, $course_id, $newGrade, $teacher_id);
        $stmt->execute();
        $stmt->close();

        // LOGGING: identify if new grade or edit
        if (!$hadOld) {
            // New assigned grade
            log_activity(
                $conn,
                (int)$teacher_id,
                "Assigned Grade",
                "Assigned grade {$newGrade} to student {$student_id} for {$courseLabel}.",
                "success"
            );
        } else {
            // Updated grade
            log_activity(
                $conn,
                (int)$teacher_id,
                "Updated Grade",
                "Changed grade of student {$student_id} for {$courseLabel} from {$oldGrade} to {$newGrade}.",
                "success"
            );
        }
    }

    $success_message = "Grades successfully saved!";
}


// ===============================================================
//  FETCH COURSES OF THIS TEACHER
// ===============================================================
$stmt = $conn->prepare("
    SELECT course_id, course_code, course_name
    FROM courses
    WHERE teacher_id = ?
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses = $stmt->get_result();


// ===============================================================
//  FETCH STUDENTS + PROGRAM + YEAR LEVEL FOR SELECTED COURSE
// ===============================================================
if ($selected_course_id) {

    $stmt = $conn->prepare("
        SELECT 
            u.user_id AS student_id,
            u.full_name,
            u.email,
            COALESCE(si.student_id, '') AS real_student_id,
            COALESCE(si.program, '—') AS program,
            COALESCE(si.year_level, '—') AS year_level,
            g.grade
        FROM enrollments e
        JOIN users u ON u.user_id = e.student_id
        LEFT JOIN student_info si ON si.user_id = u.user_id
        LEFT JOIN grades g 
            ON g.student_id = u.user_id AND g.course_id = e.course_id
        WHERE e.course_id = ?
        ORDER BY u.full_name ASC
    ");
    $stmt->bind_param("i", $selected_course_id);
    $stmt->execute();
    $students = $stmt->get_result();
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Escolink Centra | Grades Management</title>
    <link rel="stylesheet" href="CSS/format.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<div class="portal-layout">

    <?php include('sidebar_prof.php'); ?>

    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div></div> <!-- empty to align layout -->

            <div class="profile-section">
                <img src="<?= $avatar ?>" class="avatar">
                <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
            </div>
        </header>

        <!-- BODY -->
        <section class="grades-management">
            <h1>Grades Management</h1>
            <p class="semester-text">A.Y. 2025–2026 | 1st Semester</p>

            <!-- CLASS DROPDOWN -->
            <form method="GET" action="">
                <div class="class-dropdown">
                    <label for="classSelect"><i class="fa-solid fa-book"></i> Select Class:</label>
                    <select id="classSelect" name="course_id" onchange="this.form.submit()">
                        <option value="">-- Choose a class --</option>

                        <?php while ($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['course_id'] ?>"
                                <?= ($selected_course_id == $c['course_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['course_code'] . ": " . $c['course_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>


            <!-- TABLE -->
            <form method="POST">

                <input type="hidden" name="course_id"
                       value="<?= htmlspecialchars($selected_course_id ?? '') ?>">

                <div class="table-wrapper" id="gradesTableContainer">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Year Level</th>
                            <th>Grade</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if ($selected_course_id && $students->num_rows > 0): ?>

                            <?php while ($st = $students->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($st['real_student_id']) ?></td>
                                    <td><?= htmlspecialchars($st['full_name']) ?></td>
                                    <td><?= htmlspecialchars($st['program']) ?></td>
                                    <td><?= htmlspecialchars($st['year_level']) ?></td>

                                    <td>
                                        <input type="text"
                                               name="grades[<?= $st['student_id'] ?>]"
                                               class="editable-grade"
                                               value="<?= htmlspecialchars($st['grade'] ?? '') ?>"
                                               pattern="^[0-9]{1,2}(\.[0-9]{1,2})?$"
                                               title="Enter a valid number (e.g., 1.00)">
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="5"
                                    style="text-align:center; color:#777;">
                                    Select a class to view students.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($selected_course_id): ?>
                    <button type="submit" class="save-btn">Save Changes</button>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <p style="color:green; text-align:center; margin-top:10px;">
                        <?= htmlspecialchars($success_message) ?>
                    </p>
                <?php endif; ?>

            </form>
        </section>

    </main>
</div>
</body>
</html>
