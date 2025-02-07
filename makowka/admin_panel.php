<?php
session_start();
// Change this to your connection info.
include 'database.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['name']) || $_SESSION['name'] !== 'makowka') {
    // Jeśli użytkownik nie jest zalogowany lub nie jest to "makowka", przekieruj go do strony logowania
    header('Location: login.html');
    exit;
}

// Try and connect using the info above.
$conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Pobierz listę nieaktywnych kont
$result = $conn->query("SELECT * FROM accounts WHERE active = 0");
$pending_users = [];
while ($row = $result->fetch_assoc()) {
    $pending_users[] = $row;
}

// Potwierdzenie rejestracji
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])) {
    $username = $_POST['confirm'];
    $stmt = $conn->prepare('UPDATE accounts SET active = 1 WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->close();
    // Aktualizacja listy
    $result = $conn->query("SELECT * FROM accounts WHERE active = 0");
    $pending_users = [];
    while ($row = $result->fetch_assoc()) {
        $pending_users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Panel</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <h2>Pending Registrations</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php foreach ($pending_users as $user) : ?>
            <tr>
                <td><?= $user['username'] ?></td>
                <td><?= $user['email'] ?></td>
                <td>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <input type="hidden" name="confirm" value="<?= $user['username'] ?>">
                        <input type="submit" value="Confirm">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
