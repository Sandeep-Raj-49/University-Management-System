<?php
    // Check if the user has clicked the attendance link
    if (isset($_GET['attendance'])) {
        // Redirect to the attendance page
        header("Location: attendance.php");
        exit();
    }

    // Check if the user has clicked the marks link
    if (isset($_GET['marks'])) {
        // Redirect to the marks page
        header("Location: marks.php");
        exit();
    }
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h3 {
            color: #666;
            margin-bottom: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        a {
            display: block;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Student Dashboard</h1>

        <h3>Links:</h3>
        <ul>
            <li><a href="attendance.php">Attendance</a></li>
            <li><a href="marks.php">Marks</a></li>
        </ul>
    </div>
</body>
</html>
