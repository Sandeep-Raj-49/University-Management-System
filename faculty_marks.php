<?php
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

// Retrieve the student data based on selected subject
if (isset($_POST['subject_id'])) {
    $selectedSubject = $_POST['subject_id'];

    $studentQuery = "SELECT students.student_id, students.name, subjects.subject_name, subjects.subject_id, marks.marks 
                    FROM students
                    JOIN enrollment ON students.student_id = enrollment.student_id
                    JOIN subjects ON enrollment.subject_id = subjects.subject_id
                    LEFT JOIN marks ON students.student_id = marks.student_id AND subjects.subject_id = marks.subject_id
                    WHERE subjects.subject_id = '$selectedSubject'";
    $studentResult = mysqli_query($connection, $studentQuery);
}

// Handle mark submission
if (isset($_POST['submit_marks'])) {
    $studentID = $_POST['student_id'];
    $subjectID = $_POST['subject_id'];
    $marks = $_POST['marks'];

    // Check if marks record already exists for the student and subject
    $existingMarksQuery = "SELECT * FROM marks WHERE student_id = '$studentID' AND subject_id = '$subjectID'";
    $existingMarksResult = mysqli_query($connection, $existingMarksQuery);

    if (mysqli_num_rows($existingMarksResult) > 0) {
        // Update the existing marks record
        $updateMarksQuery = "UPDATE marks SET marks = '$marks' WHERE student_id = '$studentID' AND subject_id = '$subjectID'";
        $updateMarksResult = mysqli_query($connection, $updateMarksQuery);

        if ($updateMarksResult) {
            $message = "Marks updated successfully.";
        } else {
            $error = "Failed to update marks. Please try again.";
        }
    } else {
        // Insert new marks record
        $insertMarksQuery = "INSERT INTO marks (student_id, subject_id, marks) VALUES ('$studentID', '$subjectID', '$marks')";
        $insertMarksResult = mysqli_query($connection, $insertMarksQuery);

        if ($insertMarksResult) {
            $message = "Marks added successfully.";
        } else {
            $error = "Failed to add marks. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Faculty Updating Marks</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Faculty Updating Marks</h1>

        <!-- Select subject form -->
        <form method="POST" action="faculty_marks.php">
            <label for="subject">Select Subject:</label>
            <select name="subject_id" id="subject">
                <?php
                // Populate the dropdown with subjects
                while ($row = mysqli_fetch_assoc($subjectResult)) {
                    $subjectID = $row['subject_id'];
                    $subjectName = $row['subject_name'];
                    echo "<option value='$subjectID'>$subjectName</option>";
                }
                ?>
            </select>
            <input type="submit" value="Submit">
        </form>
    
        <!-- Display student data -->
        <?php
        if (isset($studentResult)) {
            if (mysqli_num_rows($studentResult) > 0) {
                echo "<h2>Student Data</h2>";
                echo "<table>";
                echo "<tr><th>Student ID</th><th>Student Name</th><th>Subject ID</th><th>Subject Name</th><th>Marks</th><th>Update Marks</th></tr>";
                while ($row = mysqli_fetch_assoc($studentResult)) {
                    $studentID = $row['student_id'];
                    $studentName = $row['name'];
                    $subjectID = $row['subject_id'];
                    $subjectName = $row['subject_name'];
                    $marks = $row['marks'];
    
                    echo "<tr>";
                    echo "<td>$studentID</td>";
                    echo "<td>$studentName</td>";
                    echo "<td>$subjectID</td>";
                    echo "<td>$subjectName</td>";
                    echo "<td>$marks</td>";
                    echo "<td>";
                    echo "<form method='POST' action='faculty_marks.php'>";
                    echo "<input type='hidden' name='student_id' value='$studentID'>";
                    echo "<input type='hidden' name='subject_id' value='$subjectID'>";
                    echo "<input type='number' name='marks' value='$marks'>";
                    echo "<input type='submit' name='submit_marks' value='Update'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No student data found for the selected subject.</p>";
            }
        }
        ?>
    
        <?php
        // Display success/error message
        if (isset($message)) {
            echo "<p class='message'>$message</p>";
        }
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    
    </div>
    </body>
</html>
<?php
// Close the database connection
$connection->close();
?>
    
               