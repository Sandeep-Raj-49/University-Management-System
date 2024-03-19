<?php
session_start();
$student_id = $_SESSION['student_id'];

// Database connection configuration
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

// Retrieve the list of subjects for the logged-in student
$subjectQuery = "SELECT subjects.subject_id, subjects.subject_name
                FROM subjects
                JOIN enrollment ON subjects.subject_id = enrollment.subject_id
                WHERE enrollment.student_id = '$student_id'";
$subjectResult = mysqli_query($connection, $subjectQuery);

// Get the selected subject ID, default to the first subject if not selected
$selectedSubject = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;
if (!$selectedSubject && mysqli_num_rows($subjectResult) > 0) {
    $row = mysqli_fetch_assoc($subjectResult);
    $selectedSubject = $row['subject_id'];
}

// Retrieve attendance data for the selected subject
function getAttendanceData($connection, $selectedSubject, $student_id) {
    // Prepare the SQL statement
    $query = "SELECT a.attendance_date, a.attendance_status
              FROM attendance AS a
              WHERE a.subject_id = ? AND a.student_id = ?
              ORDER BY a.attendance_date";

    // Prepare the statement
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $selectedSubject, $student_id);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    // Check if there are any attendance records
    if ($result->num_rows > 0) {
        // Fetch the attendance data into an associative array
        $attendanceData = $result->fetch_all(MYSQLI_ASSOC);
        return $attendanceData;
    } else {
        return null; // Return null if no attendance records found
    }
}

// Get the attendance data for the selected subject
$attendanceData = getAttendanceData($connection, $selectedSubject, $student_id);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Attendance</title>
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
    </style>
</head>

<body>
<div class="container">
        <h1>Student Attendance</h1>

        <!-- Select subject form -->
        <form method="POST" action="attendance.php">
            <label for="subject">Select Subject:</label>
            <select name="subject_id" id="subject">
                <?php
                // Populate the dropdown with subjects
                while ($row = mysqli_fetch_assoc($subjectResult)) {
                    $subjectID = $row['subject_id'];
                    $subjectName = $row['subject_name'];
                    $selected = ($subjectID == $selectedSubject) ? "selected" : "";
                    echo "<option value='$subjectID' $selected>$subjectName</option>";
                }
                ?>
            </select>
            <input type="submit" value="Submit">
        </form>

        <!-- Display attendance data -->
        <?php
        if ($attendanceData) {
            echo "<h2>Attendance Data</h2>";
            echo "<table>";
            echo "<tr><th>Date</th><th>Status</th></tr>";
            foreach ($attendanceData as $attendance) {
                $date = $attendance['attendance_date'];
                $status = $attendance['attendance_status'];
                echo "<tr><td>$date</td><td>$status</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No attendance data found for the selected subject.</p>";
        }

        // Close the database connection
        $connection->close();
        ?>
    </div>

</body>

</html>

