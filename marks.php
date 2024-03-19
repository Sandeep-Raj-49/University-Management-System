<?php
session_start();
require_once 'db_connection.php';

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the subjects from the database
$subjects_query = "SELECT * FROM subjects";
$subjects_result = $conn->query($subjects_query);

// Fetch the student's marks for the selected subject
if (isset($_POST['subject_id'])) {
    $selected_subject_id = $_POST['subject_id'];

    $marks_query = "SELECT * FROM marks WHERE student_id = ? AND subject_id = ?";
    $marks_stmt = $conn->prepare($marks_query);
    $marks_stmt->bind_param("ii", $student_id, $selected_subject_id);
    $marks_stmt->execute();
    $marks_result = $marks_stmt->get_result();
    $marks_data = $marks_result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Marks</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Add additional styling here or modify the existing styles */
        .container {
            text-align: center;
            margin-top: 100px;
        }

        /* Rest of the existing styles from style.css */
        /* ... */
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Marks</h1>
        <form method="POST">
            <div class="form-group">
                <label for="subject">Select Subject:</label>
                <select name="subject_id" id="subject">
                    <option value="">Select Subject</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()) { ?>
                        <option value="<?php echo $subject['subject_id']; ?>"><?php echo $subject['subject_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" value="View Marks">
            </div>
        </form>
        <?php if (isset($selected_subject_id) && $marks_data) { ?>
            <h2>Marks for <?php echo $marks_data['subject']; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $marks_data['subject']; ?></td>
                        <td><?php echo $marks_data['marks']; ?></td>
                        <td><?php echo $marks_data['grade']; ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
        <a href="student_dashboard.php" class="btn">Go Back</a>
    </div>
</body>
</html>
