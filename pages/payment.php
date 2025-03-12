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

// Pobranie ID kampanii w bezpieczny sposób
$campaign_id = filter_input(INPUT_GET, 'campaign_id', FILTER_SANITIZE_NUMBER_INT);
$error_message = "";
$success_message = "";
$campaign = null;

// Pobranie danych kampanii
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

// Obsługa formularza
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $type_id = filter_input(INPUT_POST, 'type_id', FILTER_SANITIZE_NUMBER_INT);
    $timestamp = date("Y-m-d H:i:s");

    if ($amount === false || $amount <= 0) {
        $error_message = "Kwota musi być większa od 0.";
    } else {
        mysqli_begin_transaction($con);
        try {
            // Wstawienie transakcji
            $stmt = $con->prepare("INSERT INTO Transactions (timestamp, amount, type_id, account_id, campaign_id, status_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdiiii", $timestamp, $amount, $type_id, $account_id, $campaign_id, $status_id);
            if (!$stmt->execute()) {
                throw new Exception("Błąd podczas dodawania transakcji: " . $stmt->error);
            }
            $transaction_id = $stmt->insert_id;
            $stmt->close();

            // Aktualizacja kampanii
            $stmt = $con->prepare("UPDATE campaigns SET current_amount = current_amount + ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $campaign_id);
            if (!$stmt->execute()) {
                throw new Exception("Błąd podczas aktualizacji current_amount: " . $stmt->error);
            }
            $stmt->close();

            mysqli_commit($con);
            $success_message = "Transakcja #$transaction_id dodana pomyślnie, kwota zaktualizowana!";
            header("Refresh: 2; URL=tables.php");
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
        <div class='alert alert-success'><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
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
        <div id="bankDetails" class="">
                <label class="form-label">Wybierz Kartę</label>
                <div class="d-flex flex-wrap gap-2">

                    <!-- Karty -->

                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank1">
                        <img src="../assets/img/options/channel_71.png" alt="Masterpass" width="100">Masterpass
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank2">
                        <img src="../assets/img/options/channel_246.png" alt="Visa/Mastercard" width="100">Visa/Mastercard
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank3">
                        <img src="../assets/img/options/channel_249.png" alt="Visa SRD" width="100">Visa SRC
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank4">
                        <img src="../assets/img/options/channel_260.png" alt="Google Pay" width="100">Google Pay
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank5">
                        <img src="../assets/img/options/channel_262.png" alt="Apple Pay" width="100">Apple Pay
                    </label>

                    <!-- Banki -->

                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank6">
                        <img src="../assets/img/options/channel_1.png" alt="mTransfer" width="100">mTransfer
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank7">
                        <img src="../assets/img/options/channel_2.png" alt="Płacę z Inteligo" width="100">Płacę z Inteligo
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank8">
                        <img src="../assets/img/options/channel_4.png" alt="Płacę z iPKO" width="100">Płacę z iPKO
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank9">
                        <img src="../assets/img/options/channel_6.png" alt="Przelew24" width="100">Przelew24
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank10">
                        <img src="../assets/img/options/channel_36.png" alt="Pekao24Przelew" width="100">Pekao24Przelew
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank11">
                        <img src="../assets/img/options/channel_38.png" alt="Płać z ING" width="100">Płać z ING
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank12">
                        <img src="../assets/img/options/channel_44.png" alt="Millennium - Płatności Internetowe" width="100">Millennium - Płatności Internetowe
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank13">
                        <img src="../assets/img/options/channel_45.png" alt="Płacę z Alior Bankiem" width="100">Płacę z Alior Bankiem
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank14">
                        <img src="../assets/img/options/channel_46.png" alt="Płacę z Citi Handlowy" width="100">Płacę z Citi Handlowy
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank15">
                        <img src="../assets/img/options/channel_50.png" alt="Pay Way Toyota Bank" width="100">Pay Way Toyota Bank
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank16">
                        <img src="../assets/img/options/channel_51.png" alt="Płać z BOŚ" width="100">Płać z BOŚ
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank17">
                        <img src="../assets/img/options/channel_66.png" alt="Bank Nowy BFG S.A." width="100">Bank Nowy BFG S.A.
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank18">
                        <img src="../assets/img/options/channel_70.png" alt="Pocztowy24" width="100">Pocztowy24
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank19">
                        <img src="../assets/img/options/channel_73.png" alt="BLIK" width="100">BLIK
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank20">
                        <img src="../assets/img/options/channel_74.png" alt="Banki Spółdzielcze" width="100">Banki Spółdzielcze
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank21">
                        <img src="../assets/img/options/channel_75.png" alt="Płacę z Plus Bank" width="100">Płacę z Plus Bank
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank22">
                        <img src="../assets/img/options/channel_76.png" alt="Getin Bank PBL" width="100">Getin Bank PBL
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank23">
                        <img src="../assets/img/options/channel_80.png" alt="Noble Pay" width="100">Noble Pay
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank24">
                        <img src="../assets/img/options/channel_81.png" alt="Idea Cloud" width="100">Idea Cloud
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank25">
                        <img src="../assets/img/options/channel_83.png" alt="EnveloBank" width="100">EnveloBank
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank26">
                        <img src="../assets/img/options/channel_86.png" alt="TrustPay" width="100">TrustPay
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank27">
                        <img src="../assets/img/options/channel_87.png" alt="Credit Agricole PBL" width="100">Credit Agricole PBL
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank28">
                        <img src="../assets/img/options/channel_90.png" alt="BNP Paribas – płacę z Pl@net" width="100">BNP Paribas – płacę z Pl@net
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank29">
                        <img src="../assets/img/options/channel_91.png" alt="Nest Bank" width="100">Nest Bank
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank30">
                        <img src="../assets/img/options/channel_92.png" alt="Bank Spółdzielczy w Brodnicy" width="100">Bank Spółdzielczy w Brodnicy
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank31">
                        <img src="../assets/img/options/channel_93.png" alt="Kasa Stefczyka" width="100">Kasa Stefczyka
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank32">
                        <img src="../assets/img/options/channel_52.png" alt="SkyCash" width="100">SkyCash
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank33">
                        <img src="../assets/img/options/channel_59.png" alt="CinkciarzPAY" width="100">CinkciarzPAY
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank34">
                        <img src="../assets/img/options/channel_218.png" alt="paysafecard" width="100">paysafecard
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank35">
                        <img src="../assets/img/options/channel_212.png" alt="PayPal" width="100">PayPal
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank36">
                        <img src="../assets/img/options/channel_94.png" alt="Kupuj teraz zapłać później" width="100">Kupuj teraz zapłać później
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank37">
                        <img src="../assets/img/options/channel_95.png" alt="PayPo" width="100">PayPo
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank38">
                        <img src="../assets/img/options/channel_231.png" alt="Orange" width="100">Orange
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank39">
                        <img src="../assets/img/options/channel_232.png" alt="T-Mobile" width="100">T-Mobile
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank40">
                        <img src="../assets/img/options/channel_233.png" alt="PLAY" width="100">PLAY
                    </label>
                    <label class="bank-option border border-primary rounded p-2">
                        <input type="radio" name="bank" value="bank41">
                        <img src="../assets/img/options/channel_234.png" alt="Plus" width="100">Plus
                    </label>
                </div>
            </div>
        <button type="submit" class="btn btn-primary">Wpłać</button>
    </form>

</body>
</html>