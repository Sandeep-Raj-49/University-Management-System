<?php
session_start();
require_once 'db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the credentials against the database
    $query = "SELECT * FROM faculties WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the login is successful
    if ($result->num_rows === 1) {
        // Set the faculty's username in the session for later use
        $_SESSION['faculty_username'] = $username;

        // Redirect to faculty_dashboard.php
        header('Location: faculty_dashboard.php');
        exit();
    } else {
        // Invalid credentials, display an error message or perform any other actions
        echo "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Login</title>
    <!-- Add your CSS styling here -->
</head>
<body>
    <h2>Faculty Login</h2>
    <form action="faculty_login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
