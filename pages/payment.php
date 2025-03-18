<?php
session_start();
include 'database.php';

if (!isset($_SESSION['id'])) {
    die("Błąd: Musisz być zalogowany, aby dodać transakcję.");
}

$account_id = $_SESSION['id']; 
$status_id = 1; // Załóżmy, że status_id = 1 oznacza "oczekujące"

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

// Pobranie metod płatności z bazy danych
$payment_methods = [];
$stmt = $con->prepare("SELECT id, name, logo_path, `group` FROM types");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // Poprawienie ścieżki do obrazka
    $row['logo_path'] = str_replace('_images/', '../assets/img/options/', $row['logo_path']);
    $payment_methods[] = $row;
}
$stmt->close();

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
            $stmt = $con->prepare("INSERT INTO transactions (timestamp, amount, type_id, account_id, campaign_id, status_id) VALUES (?, ?, ?, ?, ?, ?)");
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
            header("Refresh: 1; URL=tables.php");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../assets/img/poppy-background.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            padding: 20px;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 2px solid #ddd;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .list-group-item {
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        .bank-option {
            border: 1px solid #28a745; /* Zielona ramka */
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .bank-option:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .amount-btn {
            border: 1px solid #6c757d; /* Szara ramka */
            border-radius: 5px;
            transition: background-color 0.2s, color 0.2s;
        }

        .amount-btn:hover {
            background-color: #6c757d; /* Szare tło po najechaniu */
            color: white;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .encouragement-text {
            text-align: center;
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .thank-you-img {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <h2 class="text-center mb-4">Wpłata na kampanię: <?= htmlspecialchars($campaign['name']) ?></h2>

                    <?php if (!empty($success_message)) : ?>
                        <div class='alert alert-success'><?= htmlspecialchars($success_message) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate id="payment-form">
                        <input type="hidden" name="campaign_id" value="<?= $campaign_id ?>">

                        <div class="row">
                            <!-- Lewa kolumna: Kwota i grupy płatności -->
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Kwota wpłaty</h5>
                                        <div class="d-grid gap-2 mb-3">
                                            <button type="button" class="btn btn-outline-secondary amount-btn" data-amount="50">50 zł</button>
                                            <button type="button" class="btn btn-outline-secondary amount-btn" data-amount="100">100 zł</button>
                                            <button type="button" class="btn btn-outline-secondary amount-btn" data-amount="150">150 zł</button>
                                            <button type="button" class="btn btn-outline-secondary amount-btn" data-amount="200">200 zł</button>
                                            <button type="button" class="btn btn-outline-secondary amount-btn" data-amount="250">250 zł</button>
                                        </div>
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Inna kwota</label>
                                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required min="1">
                                            <div class="invalid-feedback">
                                                Proszę podać kwotę.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Metoda płatności</h5>
                                        <div class="list-group">
                                            <button type="button" class="list-group-item list-group-item-action payment-group" data-group="bank-przelew">
                                                Bank - przelew
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action payment-group" data-group="karty">
                                                Karty płatnicze
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action payment-group" data-group="gotowka">
                                                Płatności gotówkowe
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action payment-group" data-group="raty">
                                                Raty
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action payment-group" data-group="odroczone">
                                                Płatności odroczone
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action payment-group" data-group="mobilne">
                                                Płatności mobilne
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action payment-group" data-group="inne">
                                                Inne
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prawa kolumna: Opcje płatności -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title payment-header">Wybierz opcję płatności</h5>
                                        <p class="encouragement-text">
                                            Dziękujemy za chęć wsparcia naszej fundacji! Każda wpłata pomaga nam realizować nasze cele.<br>
                                            <strong>Przykładowy numer konta:</strong><br>
                                            12 3456 7890 1234 5678 9012 3456<br>
                                            <strong>Numer telefonu do wpłat:</strong><br>
                                            +48 123 456 789
                                        </p>
                                        <img src="../assets/img/dziekujemy.png" alt="Dziękujemy" class="thank-you-img">
                                        <div id="payment-options" class="row">
                                            <!-- Tutaj będą dynamicznie ładowane opcje płatności -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Powrót</button>
                            <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#confirmationModal">Wpłać</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal z potwierdzeniem -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Potwierdź wpłatę</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                </div>
                <div class="modal-body">
                    Czy na pewno chcesz dokonać wpłaty na kampanię: <strong><?= htmlspecialchars($campaign['name']) ?></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success" id="confirm-payment">Potwierdź</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrypty JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pobranie metod płatności z PHP
        const paymentMethods = <?php echo json_encode($payment_methods); ?>;

        // Obsługa wyboru grupy płatności
        document.querySelectorAll('.payment-group').forEach(button => {
            button.addEventListener('click', () => {
                const group = button.getAttribute('data-group');
                const options = paymentMethods.filter(method => method.group === group);
                const paymentOptionsContainer = document.getElementById('payment-options');
                paymentOptionsContainer.innerHTML = '';

                options.forEach(option => {
                    const optionHtml = `
                        <div class="col-md-6 mb-3">
                            <label class="bank-option border border-success rounded p-2 d-block">
                                <input type="radio" name="type_id" value="${option.id}" class="me-2">
                                <img src="${option.logo_path}" alt="${option.name}" width="50"> ${option.name}
                            </label>
                        </div>
                    `;
                    paymentOptionsContainer.innerHTML += optionHtml;
                });
            });
        });

        // Obsługa przycisków z kwotami
        document.querySelectorAll('.amount-btn').forEach(button => {
            button.addEventListener('click', () => {
                const amount = button.getAttribute('data-amount');
                document.getElementById('amount').value = amount;
            });
        });

        // Obsługa potwierdzenia płatności
        document.getElementById('confirm-payment').addEventListener('click', () => {
            document.getElementById('payment-form').submit();
        });

        // Walidacja formularza
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>