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
    <title>Formularz Płatności</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Formularz Płatności dla Fundacji Makówka</h2>
        <form>
            <div class="mb-3">
                <label for="donationAmount" class="form-label">Kwota Darowizny</label>
                <input type="number" class="form-control" id="donationAmount" placeholder="Wpisz kwotę">
            </div>
            <div class="mb-3">
                <label for="paymentMethod" class="form-label">Metoda Płatności</label>
                <select class="form-select" id="paymentMethod">
                    <option value="bank">Polskie Banki</option>
                    <option value="credit_card">Karta Kredytowa</option>
                    <option value="apple_pay">Apple Pay</option>
                </select>
            </div>
            <div id="bankDetails" class="d-none">
                <div class="mb-3">
                    <label for="bankName" class="form-label">Nazwa Banku</label>
                    <input type="text" class="form-control" id="bankName" placeholder="Wpisz nazwę banku">
                </div>
                <div class="mb-3">
                    <label for="bankAccount" class="form-label">Numer Konta</label>
                    <input type="text" class="form-control" id="bankAccount" placeholder="Wpisz numer konta">
                </div>
            </div>
            <div id="creditCardDetails" class="d-none">
                <div class="mb-3">
                    <label for="creditCardNumber" class="form-label">Numer Karty</label>
                    <input type="text" class="form-control" id="creditCardNumber" placeholder="Wpisz numer karty">
                </div>
                <div class="mb-3">
                    <label for="creditCardExpiration" class="form-label">Data Ważności</label>
                    <input type="text" class="form-control" id="creditCardExpiration" placeholder="MM/RR">
                </div>
                <div class="mb-3">
                    <label for="creditCardCVC" class="form-label">CVC</label>
                    <input type="text" class="form-control" id="creditCardCVC" placeholder="Wpisz CVC">
                </div>
            </div>
            <div id="applePayDetails" class="d-none">
                <p>Użyj Apple Pay na swoim urządzeniu do dokonania płatności.</p>
            </div>
            <button type="submit" class="btn btn-primary">Przekaż Darowiznę</button>
        </form>
    </div>
    <script>
        document.getElementById('paymentMethod').addEventListener('change', function() {
            var bankDetails = document.getElementById('bankDetails');
            var creditCardDetails = document.getElementById('creditCardDetails');
            var applePayDetails = document.getElementById('applePayDetails');
            bankDetails.classList.add('d-none');
            creditCardDetails.classList.add('d-none');
            applePayDetails.classList.add('d-none');
            if (this.value === 'bank') {
                bankDetails.classList.remove('d-none');
            } else if (this.value === 'credit_card') {
                creditCardDetails.classList.remove('d-none');
            } else if (this.value === 'apple_pay') {
                applePayDetails.classList.remove('d-none');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>