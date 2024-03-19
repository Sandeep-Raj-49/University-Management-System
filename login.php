<?php
session_start();
error_reporting(0);
// Retrieve the form data
$username = $_POST['username'];
$password = $_POST['password'];

// Connect to the database
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "university";

// Create a new MySQLi object
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT * FROM students WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if a matching record is found
if ($result->num_rows == 1) {
    // Fetch the student record
    $student = $result->fetch_assoc();

    // Set the student ID in the session
    $_SESSION['student_id'] = $student['student_id'];

    // Redirect to student_dashboard.php
    header("Location: student_dashboard.php");
    exit();
} else {
    // Redirect back to the login page with an error message
    header("Location: student.html?error=1");
    exit();
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>
