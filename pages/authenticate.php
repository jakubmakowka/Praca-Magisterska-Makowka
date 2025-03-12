<?php
session_start();
include 'database.php';

// Połączenie z bazą danych
$con = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ($con->connect_error) {
    exit('Błąd połączenia z bazą danych: ' . $con->connect_error);
}

// Sprawdzenie, czy dane logowania zostały przesłane
if (empty($_POST['username']) || empty($_POST['password'])) {
    exit('Proszę wypełnić oba pola: nazwa użytkownika i hasło.');
}

// Przygotowanie zapytania SQL w celu zapobiegania SQL Injection
$stmt = $con->prepare('SELECT id, password, active FROM accounts WHERE username = ?');
if (!$stmt) {
    exit('Błąd zapytania SQL: ' . $con->error);
}

$stmt->bind_param('s', $_POST['username']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $password_hash, $active);
    $stmt->fetch();

    // Sprawdzenie, czy konto jest aktywne
    if ($active == 1) {
        // Weryfikacja hasła
        if (password_verify($_POST['password'], $password_hash)) {
            // Zalogowano pomyślnie
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
            $_SESSION['id'] = $id;
            header('Location: home.php');
            exit;
        } else {
            $result = 'Nieprawidłowa nazwa użytkownika lub hasło!';
        }
    } else {
        $result = 'Twoje konto jest nieaktywne. Poczekaj na potwierdzenie od administratora.';
    }
} else {
    $result = 'Nieprawidłowa nazwa użytkownika lub hasło!';
}

// Zamknięcie zapytania i połączenia
$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Fundacja Makówka
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Noto+Sans:300,400,500,600,700,800|PT+Mono:300,400,500,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/349ee9c857.js" crossorigin="anonymous"></script>
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/corporate-ui-dashboard.css?v=1.0.0" rel="stylesheet" />
</head>

<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur border-radius-sm top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid px-1">
            <a class="navbar-brand font-weight-bolder ms-lg-0 " href="../pages/dashboard.php">
              Fundacja Makówka
            </a>
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
                  <a class="nav-link d-flex align-items-center me-2 text-dark font-weight-bold" href="../pages/sign-in.html">
                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="opacity-6 me-1">
                      <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" />
                    </svg>
                    Logowanie
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link d-flex align-items-center me-2" href="../pages/sign-up.html">
                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class=" text-dark  me-1">
                      <path fill-rule="evenodd" d="M15.75 1.5a6.75 6.75 0 00-6.651 7.906c.067.39-.032.717-.221.906l-6.5 6.499a3 3 0 00-.878 2.121v2.818c0 .414.336.75.75.75H6a.75.75 0 00.75-.75v-1.5h1.5A.75.75 0 009 19.5V18h1.5a.75.75 0 00.53-.22l2.658-2.658c.19-.189.517-.288.906-.22A6.75 6.75 0 1015.75 1.5zm0 3a.75.75 0 000 1.5A2.25 2.25 0 0118 8.25a.75.75 0 001.5 0 3.75 3.75 0 00-3.75-3.75z" clip-rule="evenodd" />
                    </svg>
                    Rejestracja
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-md-6 d-flex flex-column mx-auto">
              <div class="card card-plain mt-8">
                <div class="card-header pb-0 text-left bg-transparent">
                  <h3 class="font-weight-black text-dark display-6">Logowanie</h3>
                  <p class="mb-0">Witaj ponownie! Wprowadź swoje dane.</p>
                </div>
                <div class="card-body">
                  <form role="form" action="authenticate.php" method="post">
                    <label>Nazwa użytkownika</label>
                    <div class="mb-3">
                      <input type="text" class="form-control" placeholder="Wprowadź nazwę" aria-label="Name" aria-describedby="name-addon" name="username" id="username" required>
                    </div>
                    <label>Hasło</label>
                    <div class="mb-3">
                      <input type="password" class="form-control" placeholder="Wprowadź hasło" aria-label="Password" aria-describedby="password-addon" name="password" id="password" required>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="form-check form-check-info text-left mb-0">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="font-weight-normal text-dark mb-0" for="flexCheckDefault">
                          Zapamiętaj na 14 dni
                        </label>
                      </div>
                      <a href="javascript:;" class="text-xs font-weight-bold ms-auto">Brak dostępu do hasła?</a>
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn btn-dark w-100 mt-4 mb-3">Zaloguj się</button>
                      <button type="button" class="btn btn-white btn-icon w-100 mb-3">
                        <span class="btn-inner--icon me-1">
                          <img class="w-5" src="../assets/img/logos/google-logo.svg" alt="google-logo" />
                        </span>
                        <span class="btn-inner--text">Zaloguj się przy użyciu Google</span>
                      </button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-xs mx-auto">
                    Nie masz konta?
                    <a href="./sign-up.html" class="text-dark font-weight-bold">Zarejestruj się</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="position-absolute w-40 top-0 end-0 h-100 d-md-block d-none">
                <div class="oblique-image position-absolute fixed-top ms-auto h-100 z-index-0 bg-cover ms-n8" style="background-image:url('../assets/img/signup.jpg')">
                  <div class="blur mt-12 p-4 text-center border border-white border-radius-md position-absolute fixed-bottom m-4">
                    <?php if (isset($result)) echo '<h2 class="mt-3 text-dark font-weight-bold">'.htmlspecialchars($result, ENT_QUOTES, 'UTF-8').'</h2>'; ?>
                    <h6 class="text-dark text-sm mt-5">Copyright © 2025 Jakub Makówka</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Corporate UI Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/corporate-ui-dashboard.min.js?v=1.0.0"></script>
</body>

</html>