<?php
session_start();
include 'database.php';

if (!isset($_SESSION['id'])) {
    die("Błąd: Musisz być zalogowany, aby dodać transakcję.");
}

$account_id = $_SESSION['id']; 
$status_id = 1; 

$con = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ($con->connect_error) {
    die("Błąd połączenia: " . $con->connect_error);
}

$campaign_id = isset($_GET['campaign_id']) ? (int)$_GET['campaign_id'] : 0;
$error_message = "";
$success_message = "";
$campaign = null;

if ($campaign_id > 0) {
    $stmt = $con->prepare("SELECT name, goal_amount, current_amount FROM campaigns WHERE id = ?");
    $stmt->bind_param("i", $campaign_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $campaign = $result->fetch_assoc();
    $stmt->close();
}

if (!$campaign) {
    die("Nie znaleziono kampanii.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $type_id = $_POST['type_id'];
    $timestamp = date("Y-m-d H:i:s");

    if ($amount <= 0) {
        $error_message = "Kwota musi być większa od 0.";
    } else {
        mysqli_begin_transaction($con);

        try {
            $stmt = $con->prepare("INSERT INTO Transactions (timestamp, amount, type_id, account_id, campaign_id, status_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdiiii", $timestamp, $amount, $type_id, $account_id, $campaign_id, $status_id);

            if (!$stmt->execute()) {
                throw new Exception("Błąd podczas dodawania transakcji: " . $stmt->error);
            }
            $stmt->close();

            $stmt = $con->prepare("UPDATE Campaigns SET current_amount = current_amount + ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $campaign_id);

            if (!$stmt->execute()) {
                throw new Exception("Błąd podczas aktualizacji current_amount: " . $stmt->error);
            }
            $stmt->close();

            mysqli_commit($con);
            $success_message = "Transakcja dodana pomyślnie, kwota zaktualizowana!";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'tables.php';
                }, 2000);
            </script>";
        } catch (Exception $e) {
            mysqli_rollback($con);
            $error_message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wpłata na kampanię</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Wpłata na kampanię: <?= htmlspecialchars($campaign['name']) ?></h2>

    <?php if (!empty($success_message)) : ?>
        <div class='alert alert-success'><?= $success_message ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)) : ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="campaign_id" value="<?= $campaign_id ?>">

        <div class="mb-3">
            <label class="form-label">Nazwa kampanii</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($campaign['name']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Cel kampanii</label>
            <input type="text" class="form-control" value="<?= $campaign['goal_amount'] ?> zł" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Zebrana kwota</label>
            <input type="text" class="form-control" value="<?= $campaign['current_amount'] ?> zł" readonly>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Wpłacana kwota</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required min="1">
        </div>

        <div class="mb-3">
            <label for="type_id" class="form-label">Typ transakcji</label>
            <select class="form-select" id="type_id" name="type_id" required>
                <option value="1">Sport</option>
                <option value="2">Praca</option>
                <option value="3">Choroby</option>
                <option value="4">Wsparcie</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Wpłać</button>
    </form>

</body>
</html>
