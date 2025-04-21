<?php
// Database connection details
$host = 'localhost'; 
$dbname = 'sports_inventory'; 
$username = 'sreya'; 
$password = 'sreya'; 

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users data
$users = [];
$usersQuery = "SELECT * FROM users";
$usersResult = $conn->query($usersQuery);

if (!$usersResult) {
    die("Error fetching users data: " . $conn->error); // Debugging: Check for query errors
}

if ($usersResult->num_rows > 0) {
    while ($row = $usersResult->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch activity data
$activity = [];
$activityQuery = "SELECT * FROM activity";
$activityResult = $conn->query($activityQuery);

if (!$activityResult) {
    die("Error fetching activity data: " . $conn->error); // Debugging: Check for query errors
}

if ($activityResult->num_rows > 0) {
    while ($row = $activityResult->fetch_assoc()) {
        $activity[] = $row;
    }
}

// Close connection
$conn->close();

// Return data as JSON
echo json_encode([
    'users' => $users,
    'activity' => $activity
]);
?>