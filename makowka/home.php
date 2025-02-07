<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.html');
    exit;
}

// Database connection
include 'database.php';
// Establish connection
$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get list of all databases
$result = $conn->query("SHOW DATABASES");
$databases = [];
while ($row = $result->fetch_assoc()) {
    $databases[] = $row['Database'];
}

// Get number of databases created by the user
$username = $_SESSION['name'];
$sql = "SELECT database_count FROM accounts WHERE username='$username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $database_count = $row['database_count'];
} else {
    $database_count = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home Page</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="loggedin">
<nav class="navtop">
    <div>
        <h1>Welcome!</h1>
		<a href="admin_panel.php"><i class="fas fa-user-circle"></i>Admin</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<div class="content">
    <h2>Home Page</h2>
    <p>Welcome back, <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?>!</p>
    <p>You have created <?= $database_count ?> databases so far.</p>
    <h3>List of Databases:</h3>
    <ul>
        <?php foreach ($databases as $db) : ?>
            <li><?= htmlspecialchars($db, ENT_QUOTES) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
