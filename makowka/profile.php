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

// Check if the user has reached the limit of databases
$username = $_SESSION['name'];
$result = $conn->query("SELECT database_count FROM accounts WHERE username='$username'");
if ($result === false) {
    die("Error: " . $conn->error);
}
$row = $result->fetch_assoc();
$database_count = $row['database_count'];
if ($database_count >= 10) {
    echo "You have reached the limit of 10 databases.";
    exit;
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user has created less than 10 databases
    if ($database_count < 10) {
        $result = $conn->query("SHOW DATABASES LIKE '".$_POST['dbname']."'");
        if ($result === false) {
            die("Error: " . $conn->error);
        }
        if ($result->num_rows == 0) {
            $dbname = $_POST['dbname'];
            // Create database
            $sql = "CREATE DATABASE $dbname";
            if ($conn->query($sql) === TRUE) {
                // Update database count for the user
                $database_count++;
                $conn->query("UPDATE accounts SET database_count=$database_count WHERE username='$username'");
                echo "Database created successfully";
            } else {
                echo "Error creating database: " . $conn->error;
            }
        } else {
            echo "You have already created a database with this name";
        }
    } else {
        echo "You have reached the limit of 10 databases.";
    }
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
                <a href="Home.php"><i class="fas fa-user-circle"></i>Home</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
            </div>
        </nav>
        <div class="content">
            <h2>Home Page</h2>
            <p>Welcome back, <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?>!</p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label for="dbname">Enter Database Name:</label>
                <input type="text" id="dbname" name="dbname" required>
                <input type="submit" value="Create Database">
            </form>
        </div>
    </body>
</html>