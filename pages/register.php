<?php
require 'database.php';

// Funkcja do bezpiecznego przekierowania
function redirect($location) {
    header("Location: $location");
    exit;
}

try {
    // Połączenie z bazą danych
    $con = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
    if ($con->connect_error) {
        throw new Exception("Błąd połączenia z bazą danych: " . $con->connect_error);
    }

    $error = '';
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

        // Walidacja danych wejściowych
        if (!$username || !$password || !$email) {
            $error = "Proszę wypełnić wszystkie pola!";
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            $error = "Nazwa użytkownika może zawierać tylko litery i cyfry!";
        } elseif (strlen($password) < 5 || strlen($password) > 20) {
            $error = "Hasło musi mieć od 5 do 20 znaków!";
        } else {
            // Sprawdzenie unikalności nazwy użytkownika
            $stmt = $con->prepare("SELECT id FROM accounts WHERE username = ?");
            if (!$stmt) {
                throw new Exception("Błąd zapytania SQL: " . $con->error);
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Nazwa użytkownika jest już zajęta!";
            } else {
                // Rejestracja nowego użytkownika
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $con->prepare("INSERT INTO accounts (username, password, email, active) VALUES (?, ?, ?, 0)");
                if (!$stmt) {
                    throw new Exception("Błąd zapytania SQL: " . $con->error);
                }
                
                $stmt->bind_param("sss", $username, $password_hash, $email);
                
                if ($stmt->execute()) {
                    $stmt->close();
                    $con->close();
                    redirect("sign-in.html");
                } else {
                    $error = "Błąd podczas rejestracji!";
                }
            }
            $stmt->close();
        }
    }
    
    $con->close();

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Fundacja Makówka - Rejestracja</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Noto+Sans:300,400,500,600,700,800|PT+Mono:300,400,500,600,700" rel="stylesheet">
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet">
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/349ee9c857.js" crossorigin="anonymous"></script>
    <link href="../assets/css/corporate-ui-dashboard.css?v=1.0.0" rel="stylesheet">
</head>
<body>
    <div class="container position-sticky z-index-sticky top-0">
        <nav class="navbar navbar-expand-lg blur border-radius-sm top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
            <div class="container-fluid px-1">
                <a class="navbar-brand font-weight-bolder ms-lg-0" href="#">Fundacja Makówka</a>
                <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon mt-2">
                        <span class="navbar-toggler-bar bar1"></span>
                        <span class="navbar-toggler-bar bar2"></span>
                        <span class="navbar-toggler-bar bar3"></span>
                    </span>
                </button>
                <div class="collapse navbar-collapse" id="navigation">
                    <ul class="navbar-nav mx-auto ms-xl-auto">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center me-2" href="../pages/sign-in.html">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-dark me-1">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" />
                                </svg>
                                Logowanie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center me-2 text-dark font-weight-bold" href="../pages/sign-up.html">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="opacity-6 me-1">
                                    <path fill-rule="evenodd" d="M15.75 1.5a6.75 6.75 0 00-6.651 7.906c.067.39-.032.717-.221.906l-6.5 6.499a3 3 0 00-.878 2.121v2.818c0 .414.336.75.75.75H6a.75.75 0 00.75-.75v-1.5h1.5A.75.75 0 009 19.5V18h1.5a.75.75 0 00.53-.22l2.658-2.658c.19-.189.517-.288.906-.22A6.75 6.75 0 1015.75 1.5zm0 3a.75.75 0 000 1.5A2.25 2.25 0 0118 8.25a.75.75 0 001.5 0 3.75 3.75 0 00-3.75-3.75z" clip-rule="evenodd" />
                                </svg>
                                Rejestracja
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 d-md-block d-none">
                            <div class="position-absolute w-40 top-0 start-0 h-100">
                                <div class="oblique-image position-absolute fixed-top h-100 z-index-0 bg-cover me-n8" style="background-image:url('../assets/img/signin.jpg')">
                                    <?php if ($error): ?>
                                        <div class="my-auto text-start max-width-350 ms-7">
                                            <h1 class="mt-3 text-white font-weight-bolder"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></h1>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-start position-absolute fixed-bottom ms-7">
                                        <h6 class="text-white text-sm mb-5">Copyright © <?= date('Y') ?> Jakub Makówka</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex flex-column mx-auto">
                            <div class="card card-plain mt-8">
                                <div class="card-header pb-0 text-left bg-transparent">
                                    <h3 class="font-weight-black text-dark display-6">Rejestracja</h3>
                                    <p class="mb-0">Miło Cię poznać! Podaj swoje dane.</p>
                                </div>
                                <div class="card-body">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger text-white" role="alert">
                                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    <?php endif; ?>
                                    <form role="form" action="register.php" method="post">
                                        <div class="mb-3">
                                            <label class="form-label">Nazwa użytkownika</label>
                                            <input type="text" class="form-control" placeholder="Wprowadź nazwę" name="username" id="username" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Adres Email</label>
                                            <input type="email" class="form-control" placeholder="Wprowadź email" name="email" id="email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hasło</label>
                                            <input type="password" class="form-control" placeholder="Wprowadź hasło" name="password" id="password" required>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="terms" required>
                                            <label class="form-check-label" for="terms">
                                                Akceptuję <a href="#" class="text-dark font-weight-bold">regulamin</a>
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-dark w-100 mt-4 mb-3">Zarejestruj się</button>
                                        <button type="button" class="btn btn-white btn-icon w-100 mb-3">
                                            <span class="btn-inner--icon me-1">
                                                <img class="w-5" src="../assets/img/logos/google-logo.svg" alt="Google">
                                            </span>
                                            Zarejestruj się przez Google
                                        </button>
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-1">
                                    <p class="mb-0 text-xs">
                                        Masz już konto? <a href="./sign-in.html" class="text-dark font-weight-bold">Zaloguj się</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
        if (navigator.platform.includes('Win') && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: 0.5 });
        }
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="../assets/js/corporate-ui-dashboard.min.js?v=1.0.0"></script>
</body>
</html>