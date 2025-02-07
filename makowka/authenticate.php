<?php
session_start();
// Change this to your connection info.
include 'database.php';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (!isset($_POST['username'], $_POST['password'])) {
    // Could not get the data that should have been sent.
    exit('Please fill both the username and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password, active FROM accounts WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $active);
        $stmt->fetch();

        // Check if the account is active
        if ($active == 1) {
            // Account exists and is active, now we verify the password.
            if (password_verify($_POST['password'], $password)) {
                // Verification success! User has logged-in!
                // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id(true);
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
                $_SESSION['id'] = $id;
                header('Location: home.php');
                exit;
            } else {
                // Incorrect password
                $result = 'Incorrect username and/or password!';
            }
        } else {
            // Account is not active, waiting for administrator approval
            $result = 'Your account is inactive. Please wait for administrator approval.';
        }
    } else {
        // Incorrect username
        $result = 'Incorrect username and/or password!';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body class="loggedin">
    <nav class="navtop">
        <div>
            <h1><a href="index.html">Makowka DB APP</a></h1>
            <a href="login.html"><i class="fas fa-user-circle"></i>Login</a>
            <a href="register.html"><i class="fas fa-user-plus"></i>Register</a>
        </div>
    </nav>
    <?php if (isset($result)) echo '<div class="error">'.htmlspecialchars($result, ENT_QUOTES, 'UTF-8').'</div>'; ?>
</body>
</html>