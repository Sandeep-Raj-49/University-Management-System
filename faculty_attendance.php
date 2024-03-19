<?php
error_reporting(0);
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "university";

// Create a new database connection
$connection = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check if the connection was successful
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Retrieve the list of subjects
$subjectQuery = "SELECT * FROM subjects";
$subjectResult = mysqli_query($connection, $subjectQuery);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSubject = $_POST['subject_id'];
    $attendanceData = $_POST['attendance'];

    // Prepare the attendance data for insertion
    $attendanceRows = [];
    foreach ($attendanceData as $studentID => $status) {
        $attendanceStatus = ($status == 'present') ? 'Present' : 'Absent';
        $attendanceRows[] = "('$studentID', '$selectedSubject', CURDATE(), '$attendanceStatus')";
    }

    // Insert the attendance data into the database
    if (!empty($attendanceRows)) {
        $insertQuery = "INSERT INTO attendance (student_id, subject_id, attendance_date, attendance_status)
                        VALUES " . implode(", ", $attendanceRows);

        if (mysqli_query($connection, $insertQuery)) {
            $successMessage = "Attendance has been recorded successfully.";
        } else {
            $errorMessage = "Error: Unable to record attendance. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Faculty Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin: 0;
            color: maroon;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        form label {
            margin-right: 10px;
        }

        form select {
            padding: 5px;
            border-radius: 4px;
        }

        form input[type="submit"] {
            padding: 5px 10px;
            background-color: maroon;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: maroon;
            color: #fff;
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
<div class="container">
        <h1>Faculty Attendance</h1>

        <!-- Display success message -->
        <?php if (isset($successMessage)) : ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <!-- Display error message -->
        <?php if (isset($errorMessage)) : ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <!-- Attendance form -->
        <form method="POST" action="faculty_attendance.php">
            <label for="subject">Select Subject:</label>
            <select name="subject_id" id="subject">
                <?php while ($row = mysqli_fetch_assoc($subjectResult)) : ?>
                    <option value="<?php echo $row['subject_id']; ?>"><?php echo $row['subject_name']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="submit" value="Submit">
        </form>

        <?php if (mysqli_num_rows($subjectResult) > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') : ?>
            <?php
            $selectedSubject = $_POST['subject_id'];

            $studentQuery = "SELECT students.student_id, students.name
                            FROM students
                            JOIN enrollment ON students.student_id = enrollment.student_id
                            WHERE enrollment.subject_id = '$selectedSubject'";
            $studentResult = mysqli_query($connection, $studentQuery);
            ?>

            <!-- Attendance table -->
            <h2>Attendance</h2>
            <form method="POST" action="faculty_attendance.php">
                <table>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Attendance</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($studentResult)) : ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td>
                                <input type="radio" name="attendance[<?php echo $row['student_id']; ?>]" value="present" checked> Present
                                <input type="radio" name="attendance[<?php echo $row['student_id']; ?>]" value="absent"> Absent
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <input type="hidden" name="subject_id" value="<?php echo $selectedSubject; ?>">
                <input type="submit" value="Submit Attendance">
            </form>
        <?php endif; ?>

    </div>
</body>

</html>

   
