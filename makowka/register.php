<?php
include 'database.php';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
	// Could not get the data that should have been sent.
	exit('Please complete the registration form!');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
	// One or more values are empty.
	exit('Please complete the registration form');
}
// Validating Form Data
// Email Validation
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	exit('Email is not valid!');
}
// Invalid Characters Validation
if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
    exit('Username is not valid!');
}
// Character Length Check
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
	exit('Password must be between 5 and 20 characters long!');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// Username already exists
		$result='Username exists, please choose another!';
	} else {
		// Username doesn't exist, insert new account as inactive
        if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, active) VALUES (?, ?, ?, 0)')) {
            // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
            $stmt->execute();
            $result='You have successfully registered! You can now login!';
        } else {
            // Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all three fields.
            $result='Could not prepare statement!';
        }
	}
	$stmt->close();
} else {
	// Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
	$result='Could not prepare statement!';
}
$con->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Register</title>
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
      <?php echo '<div class="error">'.$result.'</div>'; ?>
	</body>
</html>
