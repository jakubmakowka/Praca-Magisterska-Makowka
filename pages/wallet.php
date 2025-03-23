<!--
=========================================================
* Corporate UI - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/corporate-ui
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->

<?php
// Sprawdzenie, czy sesja już została uruchomiona
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'database.php';

// Jeśli użytkownik nie jest zalogowany, przekieruj do strony logowania
if (!isset($_SESSION['loggedin'])) {
    header('Location: sign-in.html');
    die();
}

$user_id = $_SESSION['id'];

// Połączenie z bazą danych
$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobranie sumy wpłat: całkowitej, dziennej, tygodniowej, miesięcznej i rocznej w jednym zapytaniu
$sql_summary = "SELECT 
    SUM(amount) AS total_amount,
    SUM(CASE WHEN DATE(timestamp) = CURDATE() THEN amount ELSE 0 END) AS day_amount,
    SUM(CASE WHEN timestamp >= CURDATE() - INTERVAL 1 WEEK THEN amount ELSE 0 END) AS week_amount,
    SUM(CASE WHEN timestamp >= CURDATE() - INTERVAL 1 MONTH THEN amount ELSE 0 END) AS month_amount,
    SUM(CASE WHEN timestamp >= CURDATE() - INTERVAL 1 YEAR THEN amount ELSE 0 END) AS year_amount
FROM transactions WHERE account_id = ?";

$stmt_summary = $conn->prepare($sql_summary);
if (!$stmt_summary) {
    die("Błąd zapytania SQL: " . $conn->error);
}
$stmt_summary->bind_param("i", $user_id);
$stmt_summary->execute();
$result_summary = $stmt_summary->get_result();
$summary = $result_summary->fetch_assoc();

$total_amount = $summary['total_amount'] ?? 0;
$day_amount = $summary['day_amount'] ?? 0;
$week_amount = $summary['week_amount'] ?? 0;
$month_amount = $summary['month_amount'] ?? 0;
$year_amount = $summary['year_amount'] ?? 0;

$stmt_summary->close();

// Ustawienia paginacji
$results_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Pobranie historii transakcji z paginacją
$sql_transactions = "SELECT transactions.timestamp, transactions.amount, campaigns.name AS campaign_name 
                     FROM transactions 
                     JOIN campaigns ON transactions.campaign_id = campaigns.id 
                     WHERE transactions.account_id = ? 
                     ORDER BY transactions.timestamp DESC 
                     LIMIT ? OFFSET ?";

$stmt_transactions = $conn->prepare($sql_transactions);
if (!$stmt_transactions) {
    die("Błąd zapytania SQL: " . $conn->error);
}
$stmt_transactions->bind_param("iii", $user_id, $results_per_page, $offset);
$stmt_transactions->execute();
$result_transactions = $stmt_transactions->get_result();

// Pobranie liczby wszystkich transakcji użytkownika
$sql_count = "SELECT COUNT(*) AS total FROM transactions WHERE account_id = ?";
$stmt_count = $conn->prepare($sql_count);
if (!$stmt_count) {
    die("Błąd zapytania SQL: " . $conn->error);
}
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_transactions = $result_count->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_transactions / $results_per_page);

// Zamknięcie zapytań i połączenia z bazą
$stmt_transactions->close();
$stmt_count->close();
$conn->close();
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Noto+Sans:300,400,500,600,700,800|PT+Mono:300,400,500,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/corporate-ui-dashboard.css?v=1.0.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 bg-slate-900 fixed-start" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand d-flex align-items-center m-0" href="../pages/home.php">
            <span class="font-weight-bold text-lg">Fundacja Makówka</span>
        </a>
    </div>
    <div class="collapse navbar-collapse px-4 w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- Strona główna -->
            <li class="nav-item mb-2">
                <a class="nav-link" href="../pages/home.php">
                    <div class="icon px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-home text-white"></i>
                    </div>
                    <span class="nav-link-text ms-3">Strona główna</span>
                </a>
            </li>

            <!-- Raporty - panel -->
            <li class="nav-item mb-2">
                <a class="nav-link" href="../pages/dashboard.php">
                    <div class="icon px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <span class="nav-link-text ms-3">Raporty - panel</span>
                </a>
            </li>

            <!-- Wspomóż nas -->
            <li class="nav-item mb-2">
                <a class="nav-link" href="../pages/tables.php">
                    <div class="icon px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-hand-holding-heart text-white"></i>
                    </div>
                    <span class="nav-link-text ms-3">Wspomóż nas</span>
                </a>
            </li>

            <!-- Portfel -->
            <li class="nav-item mb-2">
                <a class="nav-link" href="../pages/wallet.php">
                    <div class="icon px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                    <span class="nav-link-text ms-3">Portfel</span>
                </a>
            </li>

            <!-- Aktualności -->
            <li class="nav-item mb-2">
                <a class="nav-link" href="../pages/news.php">
                    <div class="icon px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-newspaper text-white"></i>
                    </div>
                    <span class="nav-link-text ms-3">Aktualności</span>
                </a>
            </li>

            <!-- Sprawozdania -->
            <li class="nav-item mb-2">
                <a class="nav-link" href="../pages/reports.php">
                    <div class="icon px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                    <span class="nav-link-text ms-3">Sprawozdania</span>
                </a>
            </li>

            <!-- Formularz kontaktowy -->
            <li class="nav-item mb-2">
                <a class="nav-link" href="../pages/contact.php">
                    <div class="icon px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-envelope text-white"></i>
                    </div>
                    <span class="nav-link-text ms-3">Kontakt</span>
                </a>
            </li>

            <!-- Zarządzanie kontem -->
            <li class="nav-item mt-4 mb-3">
                <div class="d-flex align-items-center nav-link">
                    <i class="fas fa-user-cog text-white ms-2"></i>
                    <span class="font-weight-normal text-md ms-3">Zarządzanie kontem</span>
                </div>
            </li>

            <!-- Panel administratora -->
            <li class="nav-item border-start border-light my-0">
                <a class="nav-link position-relative ms-0 ps-3 py-2" href="../pages/admin_panel.php">
                    <span class="nav-link-text ms-1">Panel administratora</span>
                </a>
            </li>

            <!-- Profil -->
            <li class="nav-item border-start border-light my-0">
                <a class="nav-link position-relative ms-0 ps-3 py-2" href="../pages/profile.php">
                    <span class="nav-link-text ms-1">Profil</span>
                </a>
            </li>

            <!-- Wyloguj się -->
            <li class="nav-item border-start border-light my-0">
                <a class="nav-link position-relative ms-0 ps-3 py-2" href="../pages/logout.php">
                    <span class="nav-link-text ms-1">Wyloguj się</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidenav-footer mx-4">
        <div class="card border-radius-md" id="sidenavCard">
            <div class="card-body text-start p-3 w-100">
                <div class="docs-info">
                    <h6 class="font-weight-bold up mb-2">Potrzebujesz pomocy?</h6>
                    <p class="text-sm font-weight-normal">Sprawdź naszą dokumentację:</p>
                    <a href="https://www.creative-tim.com/learning-lab/bootstrap/license/corporate-ui-dashboard" target="_blank" class="font-weight-bold text-sm mb-0 icon-move-right w-100">
                        Dokumentacja
                        <i class="fas fa-arrow-right-long text-sm ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <nav class="navbar navbar-main navbar-expand-lg mx-5 px-0 shadow-none rounded" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-2">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-1 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Aplikacja</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Portfel</li>
          </ol>
          <h6 class="font-weight-bold mb-0">Portfel</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="input-group-text text-body bg-white  border-end-0 ">
                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
              </span>
              <input type="text" class="form-control ps-0" placeholder="Szukaj">
            </div>
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" class="fixed-plugin-button-nav cursor-pointer" viewBox="0 0 24 24" fill="currentColor">
                  <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" />
                </svg>
              </a>
            </li>
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="cursor-pointers">
                  <path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0113.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 01-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 11-7.48 0 24.585 24.585 0 01-4.831-1.244.75.75 0 01-.298-1.205A8.217 8.217 0 005.25 9.75V9zm4.502 8.9a2.25 2.25 0 104.496 0 25.057 25.057 0 01-4.496 0z" clip-rule="evenodd" />
                </svg>
              </a>
              <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="../assets/img/team-2.jpg" class="avatar avatar-sm border-radius-sm  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">Nowa wiadomość</span> od Laury
                        </h6>
                        <p class="text-xs text-secondary mb-0 d-flex align-items-center ">
                          <i class="fa fa-clock opacity-6 me-1"></i>
                          13 minut temu
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="../assets/img/small-logos/logo-google.svg" class="avatar avatar-sm border-radius-sm bg-gradient-dark p-2  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">Nowy raport</span> od Google
                        </h6>
                        <p class="text-xs text-secondary mb-0 d-flex align-items-center ">
                          <i class="fa fa-clock opacity-6 me-1"></i>
                          Wczoraj
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="avatar avatar-sm border-radius-sm bg-slate-800  me-3  my-auto">
                        <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>credit-card</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(453.000000, 454.000000)">
                                  <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                                  <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          Płatność zakończona sukcesem
                        </h6>
                        <p class="text-xs text-secondary d-flex align-items-center mb-0 ">
                          <i class="fa fa-clock opacity-6 me-1"></i>
                          2 dni temu
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item ps-2 d-flex align-items-center">
              <a href="../pages/profile.php" class="nav-link text-body p-0">
                <img src="../assets/img/team-2.jpg" class="avatar avatar-sm" alt="avatar" />
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid py-4 px-5">
    <div class="row">
        <div class="col-md">
            <div class="card blur border border-white mb-4 shadow-xs card-hover">
                <div class="card-body p-4">
                    <div class="icon icon-shape bg-white shadow shadow-xs text-center border-radius-md d-flex align-items-center justify-content-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" height="19" width="19" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.584 2.376a.75.75 0 01.832 0l9 6a.75.75 0 11-.832 1.248L12 3.901 3.416 9.624a.75.75 0 01-.832-1.248l9-6z" />
                            <path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 010 1.5H3a.75.75 0 010-1.5h.75v-9.918a.75.75 0 01.634-.74A49.109 49.109 0 0112 9c2.59 0 5.134.202 7.616.592a.75.75 0 01.634.74zm-7.5 2.418a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0v-6.75zm3-.75a.75.75 0 01.75.75v6.75a.75.75 0 01-1.5 0v-6.75a.75.75 0 01.75-.75zM9 12.75a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0v-6.75z" clip-rule="evenodd" />
                            <path d="M12 7.875a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z" />
                        </svg>
                    </div>
                    <p class="text-sm mb-1">Wpłaty - ostatnie 24 godziny</p>
                    <h3 class="mb-0 font-weight-bold"><?php echo number_format($day_amount); ?> zł</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card blur border border-white mb-4 shadow-xs card-hover">
                <div class="card-body p-4">
                    <div class="icon icon-shape bg-white shadow shadow-xs text-center border-radius-md d-flex align-items-center justify-content-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" height="19" width="19" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.5 22.5a3 3 0 003-3v-8.174l-6.879 4.022 3.485 1.876a.75.75 0 01-.712 1.321l-5.683-3.06a1.5 1.5 0 00-1.422 0l-5.683 3.06a.75.75 0 01-.712-1.32l3.485-1.877L1.5 11.326V19.5a3 3 0 003 3h15z" />
                            <path d="M1.5 9.589v-.745a3 3 0 011.578-2.641l7.5-4.039a3 3 0 012.844 0l7.5 4.039A3 3 0 0122.5 8.844v.745l-8.426 4.926-.652-.35a3 3 0 00-2.844 0l-.652.35L1.5 9.59z" />
                        </svg>
                    </div>
                    <p class="text-sm mb-1">Wpłaty - ostatni tydzień</p>
                    <h3 class="mb-0 font-weight-bold"><?php echo number_format($week_amount); ?> zł</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card blur border border-white mb-4 shadow-xs card-hover">
                <div class="card-body p-4">
                    <div class="icon icon-shape bg-white shadow shadow-xs text-center border-radius-md d-flex align-items-center justify-content-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" height="19" width="19" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                        </svg>
                    </div>
                    <p class="text-sm mb-1">Wpłaty - ostatni miesiąc</p>
                    <h3 class="mb-0 font-weight-bold"><?php echo number_format($month_amount); ?> zł</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card blur border border-white mb-4 shadow-xs card-hover">
                <div class="card-body p-4">
                    <div class="icon icon-shape bg-white shadow shadow-xs text-center border-radius-md d-flex align-items-center justify-content-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" height="19" width="19" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4.5 3.75a3 3 0 00-3 3v.75h21v-.75a3 3 0 00-3-3h-15z" />
                            <path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 003 3h15a3 3 0 003-3v-7.5zm-18 3.75a.75.75 0 01.75-.75h6a.75.75 0 010 1.5h-6a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm mb-1">Wpłaty - ostatni rok</p>
                    <h3 class="mb-0 font-weight-bold"><?php echo number_format($year_amount); ?> zł</h3>
                </div>
            </div>
        </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <div class="d-flex align-items-center mb-4">
          </div>
          <div class="d-md-flex align-items-center mb-4">
            <div class="mb-md-0 mb-3">
              <h5 class="font-weight-semibold mb-1">Finanse - podsumowanie</h5>
              <p class="text-sm mb-0">Sprawdź historię swojego konta.</p>
            </div>
            <img src="../assets/img/Money_Flat_Icon.svg" class="align-items-center mb-0" style="max-width: 150px; max-height: 150px;" />
          </div>
        </div>
      </div>
      <hr class="horizontal mb-4 dark">
      <div class="row">
        <div class="col-md-4">
          <h6 class="text-sm font-weight-semibold mb-1">Przelewy</h6>
          <p class="text-sm">Prześledź historię rachunku swojego konta<br> i pobierz potwierdzenia przelewów.</p>
        </div>
        <div class="col-md-8 mb-6">
          <div class="card shadow-xs border mb-4">
            <div class="table-responsive p-0">
            <table class="table table-hover align-items-center mb-0">
              <thead>
                <tr>
                  <th class="d-flex align-items-center py-3 px-4 text-sm">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                    </div>
                    <span class="text-xs font-weight-semibold opacity-7 ms-1">Podsumowanie wpłat</span>
                  </th>
                  <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Status</th>
                  <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Kwota</th>
                  <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Nazwa kampanii</th>
                  <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2"></th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result_transactions->fetch_assoc()): ?>
                  <tr>
                    <td class="d-flex align-items-center py-3 px-4 text-sm">
                      <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                      </div>
                      <span class="font-weight-semibold text-dark ms-1"><?php echo htmlspecialchars($row['timestamp']); ?></span>
                    </td>
                    <td>
                      <span class="badge badge-sm border border-success text-success bg-success border-radius-sm">
                        <svg width="9" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" class="me-1">
                          <path d="M1 4.42857L3.28571 6.71429L9 1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Zapłacono
                      </span>
                    </td>
                    <td>
                      <span class="text-sm"><?php echo number_format($row['amount'], 2, ',', ' '); ?> zł</span>
                    </td>
                    <td>
                      <span class="text-sm"><?php echo htmlspecialchars($row['campaign_name']); ?></span>
                    </td>
                    <td class="text-sm font-weight-semibold text-dark">
                    <a href="../license" download class="text-sm">Pobierz potwierdzenie</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
            <!-- Paginacja -->
            <div class="d-flex justify-content-center align-items-center mt-3 px-3">
            <?php if ($page > 1): ?>
              <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary me-2">« Poprzednia</a>
            <?php endif; ?>

            <span class="d-flex align-items-center mb-3">
              Strona <?php echo $page; ?> z <?php echo $total_pages; ?>
            </span>

            <?php if ($page < $total_pages): ?>
              <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary ms-2">Następna »</a>
            <?php endif; ?>
          </div>
          </div>
          <div class="row mt-5">
            <div class="col-lg-6">
              <div class="card shadow-xs border mb-4">
                <div class="card-body py-0">
                  <div class="row">
                    <div class="col-4 pe-1">
                      <div class="chart">
                        <canvas id="chart-doughnut-info" class="chart-canvas" height="150"></canvas>
                      </div>
                    </div>
                    <div class="col-8 my-auto">
                      <div class="d-flex">
                        <div>
                          <h4 class="font-weight-semibold text-lg mb-4">Portfel Główny</h4>
                          <p class="text-sm mb-1">Podsumowanie</p>
                          <h3 class="mb-0 font-weight-bold"><?php echo number_format($total_amount, 2, ',', ' '); ?> zł</h3>
                        </div>
                        <div class="ms-auto text-end d-flex flex-column">
                          <div class="dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="ms-auto cursor-pointer dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                              <path fill-rule="evenodd" d="M10.5 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" clip-rule="evenodd" />
                            </svg>
                            <ul class="dropdown-menu dropdown-menu-end me-n4">
                              <li><a class="dropdown-item" href="#">Dodaj</a></li>
                              <li><a class="dropdown-item" href="#">Usuń</a></li>
                              <li><a class="dropdown-item" href="#">Odśwież</a></li>
                            </ul>
                          </div>
                          <span class="badge badge-sm border border-success text-success bg-success border-radius-sm mt-auto mb-2">
                            <svg width="9" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M0.46967 4.46967C0.176777 4.76256 0.176777 5.23744 0.46967 5.53033C0.762563 5.82322 1.23744 5.82322 1.53033 5.53033L0.46967 4.46967ZM5.53033 1.53033C5.82322 1.23744 5.82322 0.762563 5.53033 0.46967C5.23744 0.176777 4.76256 0.176777 4.46967 0.46967L5.53033 1.53033ZM5.53033 0.46967C5.23744 0.176777 4.76256 0.176777 4.46967 0.46967C4.17678 0.762563 4.17678 1.23744 4.46967 1.53033L5.53033 0.46967ZM8.46967 5.53033C8.76256 5.82322 9.23744 5.82322 9.53033 5.53033C9.82322 5.23744 9.82322 4.76256 9.53033 4.46967L8.46967 5.53033ZM1.53033 5.53033L5.53033 1.53033L4.46967 0.46967L0.46967 4.46967L1.53033 5.53033ZM4.46967 1.53033L8.46967 5.53033L9.53033 4.46967L5.53033 0.46967L4.46967 1.53033Z" fill="#67C23A" />
                            </svg>
                            100%
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card shadow-xs border mb-4">
                <div class="card-body py-0">
                  <div class="row">
                    <div class="col-4 pe-1">
                      <div class="chart">
                        <canvas id="chart-doughnut-dark" class="chart-canvas" height="150"></canvas>
                      </div>
                    </div>
                    <div class="col-8 my-auto">
                      <div class="d-flex">
                        <div>
                          <h4 class="font-weight-semibold text-lg mb-4">Portfel Dodatkowy</h4>
                          <p class="text-sm mb-1">Podsumowanie</p>
                          <h3 class="mb-0 font-weight-bold">0 zł</h3>
                        </div>
                        <div class="ms-auto text-end d-flex flex-column">
                          <div class="dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="ms-auto cursor-pointer dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                              <path fill-rule="evenodd" d="M10.5 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" clip-rule="evenodd" />
                            </svg>
                            <ul class="dropdown-menu dropdown-menu-end me-n4">
                            <li><a class="dropdown-item" href="#">Dodaj</a></li>
                              <li><a class="dropdown-item" href="#">Usuń</a></li>
                              <li><a class="dropdown-item" href="#">Odśwież</a></li>
                            </ul>
                          </div>
                          <span class="badge badge-sm border border-success text-success bg-success border-radius-sm mt-auto mb-2">
                            <svg width="9" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M0.46967 4.46967C0.176777 4.76256 0.176777 5.23744 0.46967 5.53033C0.762563 5.82322 1.23744 5.82322 1.53033 5.53033L0.46967 4.46967ZM5.53033 1.53033C5.82322 1.23744 5.82322 0.762563 5.53033 0.46967C5.23744 0.176777 4.76256 0.176777 4.46967 0.46967L5.53033 1.53033ZM5.53033 0.46967C5.23744 0.176777 4.76256 0.176777 4.46967 0.46967C4.17678 0.762563 4.17678 1.23744 4.46967 1.53033L5.53033 0.46967ZM8.46967 5.53033C8.76256 5.82322 9.23744 5.82322 9.53033 5.53033C9.82322 5.23744 9.82322 4.76256 9.53033 4.46967L8.46967 5.53033ZM1.53033 5.53033L5.53033 1.53033L4.46967 0.46967L0.46967 4.46967L1.53033 5.53033ZM4.46967 1.53033L8.46967 5.53033L9.53033 4.46967L5.53033 0.46967L4.46967 1.53033Z" fill="#67C23A" />
                            </svg>
                            0%
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="d-md-flex align-items-center mb-4">
            <div class="mb-md-0 mb-4">
              <h5 class="font-weight-semibold mb-1">Twoje karty</h5>
              <p class="text-sm mb-0">Zarządzaj swoimi kartami kredytowymi.</p>
            </div>
            <button type="button" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0 ms-md-auto">
              <span class="btn-inner--icon">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="d-block me-2">
                  <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32L19.513 8.2z" />
                </svg>
              </span>
              <span class="btn-inner--text">Ustawienia</span>
            </button>
          </div>
        </div>
        <hr>
        <div class="col-md-4">
          <h6 class="text-sm font-weight-semibold mb-1">Szczegóły</h6>
        </div>
        <div class="col-md-8 mb-4">
          <div class="card border shadow-xs">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-5">
                  <div class="card card-background card-background-after-none align-items-start mb-2">
                    <div class="full-background" style="background-image: url('../assets/img/curved-images/img-6.jpg')"></div>
                    <div class="card-body text-start ps-3 pe-2 pt-2 pb-2 w-100">
                      <div class="row">
                        <div class="col-8 py-2">
                          <p class="text-white text-sm font-weight-bold mb-6">Corporate UI</p>
                          <div class="d-flex align-items-center mb-0 mt-auto">
                            <p class="font-weight-semibold mb-0">Noah Jackes</p>
                            <span class="ms-auto text-xs font-weight-bolder text-pt-mono">08/28</span>
                          </div>
                          <span class="ms-auto text-sm font-weight-bolder text-pt-mono">1234&nbsp;&nbsp;6578&nbsp;&nbsp;9000&nbsp;&nbsp;1234</span>
                        </div>
                        <div class="col-4">
                          <div class="blur d-flex flex-column w-80 h-100 py-2 ms-auto border-radius-lg border border-white">
                            <div class="text-center w-100">
                              <img src="../assets/img/logos/wifi-white.png" class="w-25 mx-auto" alt="wifi" />
                            </div>
                            <div class="text-center mt-auto w-100">
                              <img src="../assets/img/logos/mastercard-white.png" class="w-40 mx-auto mt-2" alt="mastercard" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="progress-wrapper w-100 mb-lg-0 mb-5">
                    <div class="d-flex align-items-center mb-2">
                      <span class="text-sm font-weight-semibold">Miesiąc</span>
                      <p class="text-dark font-weight-bold ms-auto mb-0"><?php echo number_format($month_amount); ?> zł</p>
                    </div>
                    <div class="progress">
                      <div class="progress-bar progress-bar-lg bg-gradient-dark w-60" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-5">
                  <div class="card card-background card-background-after-none align-items-start mb-2">
                    <div class="full-background" style="background-image: url('../assets/img/curved-images/img-7.jpg')"></div>
                    <div class="card-body text-start p-2 w-100">
                      <div class="row">
                        <div class="col-12">
                          <div class="blur d-flex align-items-center w-100 border-radius-md border border-white mb-4 p-2">
                            <p class="text-white text-sm w-50 mb-0 font-weight-bold">Corporate UI</p>
                            <div class="text-end ms-auto w-100 pe-2">
                              <img src="../assets/img/logos/wifi-white.png" class="w-10 ms-auto" alt="wifi" />
                            </div>
                          </div>
                        </div>
                        <div class="col-8 py-2 mt-auto">
                          <div class="d-flex align-items-center mb-0 mt-auto ms-2">
                            <p class="font-weight-semibold mb-0 mt-3">Noah Jackes</p>
                            <span class="ms-auto text-xs font-weight-bolder text-pt-mono">08/28</span>
                          </div>
                          <span class="text-sm font-weight-bolder text-pt-mono ms-2">1234&nbsp;&nbsp;6578&nbsp;&nbsp;9000&nbsp;&nbsp;1234</span>
                        </div>
                        <div class="col-4 py-2 text-end mt-auto">
                          <img src="../assets/img/logos/visa-white.png" class="w-50 ms-auto me-3" alt="visa" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="progress-wrapper w-100">
                    <div class="d-flex align-items-center mb-2">
                      <span class="text-sm font-weight-semibold">Miesiąc</span>
                      <p class="text-dark font-weight-bold ms-auto mb-0">0 zł</p>
                    </div>
                    <div class="progress">
                      <div class="progress-bar progress-bar-lg bg-gradient-dark w-0" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-5">
  <div class="col-md-4">
    <h6 class="text-sm font-weight-semibold mb-1">Aktualny Plan Wsparcia</h6>
    <p class="text-sm">Wybierz plan wsparcia, który najlepiej pasuje do Twoich możliwości.</p>
  </div>
  <div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item border-info d-flex justify-content-between mb-3 border-radius-md shadow-xs p-3">
        <div class="d-flex align-items-start">
          <div class="icon icon-shape icon-sm bg-info text-white shadow-none text-center  border-radius-sm me-sm-2 me-3 mt-1 px-2 d-flex align-items-center justify-content-center">
            <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="d-flex flex-column">
            <h6 class="mb-0 text-sm text-info">Plan Podstawowy 0 zł/miesiąc</h6>
            <span class="text-sm text-info">Obejmuje 1 darczyńcę, 10GB danych i dostęp do wszystkich funkcji.</span>
          </div>
        </div>
        <div class="d-flex align-items-center text-danger text-gradient">
          <div class="form-check">
            <input type="radio" id="radio1" name="radioDisabled" class="form-check-input form-check-input-info" checked>
          </div>
        </div>
      </li>
      <li class="list-group-item border d-flex justify-content-between mb-3 border-radius-md shadow-xs p-3">
        <div class="d-flex align-items-start">
          <div class="icon icon-shape icon-sm bg-dark text-white shadow-none text-center  border-radius-sm me-sm-2 me-3 mt-1 px-2 d-flex align-items-center justify-content-center">
            <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
            </svg>
          </div>
          <div class="d-flex flex-column">
            <h6 class="mb-0 text-sm">Plan Wspierający 50 zł/miesiąc</h6>
            <span class="text-sm">Obejmuje do 10 darczyńców, 20GB danych i dostęp do wszystkich funkcji.</span>
          </div>
        </div>
        <div class="d-flex align-items-center text-danger text-gradient">
          <div class="form-check">
            <input type="radio" id="radio2" name="radioDisabled" class="form-check-input form-check-input-info">
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="col-md-4">
    <ul class="list-group">
      <li class="list-group-item border d-flex justify-content-between mb-3 border-radius-md shadow-xs p-3">
        <div class="d-flex align-items-start">
          <div class="icon icon-shape icon-sm bg-dark text-white shadow-none text-center  border-radius-sm me-sm-2 me-3 mt-1 px-2 d-flex align-items-center justify-content-center">
            <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z" clip-rule="evenodd" />
              <path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.486l-.115.04c-.567.2-1.156.349-1.764.441z" />
            </svg>
          </div>
          <div class="d-flex flex-column">
            <h6 class="mb-0 text-sm">Plan Firmowy 100 zł/miesiąc</h6>
            <span class="text-sm">Obejmuje do 20 darczyńców, 40GB danych i dostęp do wszystkich funkcji.</span>
          </div>
        </div>
        <div class="d-flex align-items-center text-danger text-gradient">
          <div class="form-check">
            <input type="radio" id="radio3" name="radioDisabled" class="form-check-input form-check-input-info">
          </div>
        </div>
      </li>
      <li class="list-group-item border d-flex justify-content-between mb-3 border-radius-md shadow-xs p-3">
        <div class="d-flex align-items-start">
          <div class="icon icon-shape icon-sm bg-dark text-white shadow-none text-center  border-radius-sm me-sm-2 me-3 mt-1 px-2 d-flex align-items-center justify-content-center">
            <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path fill-rule="evenodd" d="M7.5 5.25a3 3 0 013-3h3a3 3 0 013 3v.205c.933.085 1.857.197 2.774.334 1.454.218 2.476 1.483 2.476 2.917v3.033c0 1.211-.734 2.352-1.936 2.752A24.726 24.726 0="12 15.75c-2.73..." />
            </svg>
          </div>
          <div class="d-flex flex-column">
            <h6 class="mb-0 text-sm">Plan Premium 200 zł/miesiąc</h6>
            <span class="text-sm">Nielimitowana liczba darczyńców, nielimitowana ilość danych i dostęp do wszystkich funkcji.</span>
                </div>
              </div>
              <div class="d-flex align-items-center text-danger text-gradient">
                <div class="form-check">
                  <input type="radio" id="radio3" name="radioDisabled" id="customRadioDisabled" class="form-check-input form-check-input-info">
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-xs text-muted text-lg-start">
                Copyright
                © <script>
                  document.write(new Date().getFullYear())
                </script>
                Corporate UI by
                <a href="https://www.creative-tim.com" class="text-secondary" target="_blank">Creative Tim</a>.
              </div>
            </div>
            <div class="col-lg-6">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item">
                  <a href="https://www.creative-tim.com" class="nav-link text-xs text-muted" target="_blank">Creative Tim</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/presentation" class="nav-link text-xs text-muted" target="_blank">About Us</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/blog" class="nav-link text-xs text-muted" target="_blank">Blog</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/license" class="nav-link text-xs pe-0 text-muted" target="_blank">License</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2"><i class="fa fa-cog py-2"></i></a>
    <div class="card shadow-lg">
        <div class="card-header pb-0 pt-3">
            <div class="float-start">
                <h5 class="mt-3 mb-0">Wspieracz - konfiguracja</h5>
                <p>Zobacz wszystkie opcje.</p>
            </div>
            <div class="float-end mt-4">
                <button class="btn btn-link text-dark p-0 fixed-plugin-close-button"><i class="fa fa-close"></i></button>
            </div>
        </div>
        <hr class="horizontal dark my-1">
        <div class="card-body pt-sm-3 pt-0">
            <div><h6 class="mb-0">Kolor menu bocznego</h6></div>
            <a href="javascript:void(0)" class="switch-trigger background-color">
                <div class="badge-colors my-2 text-start">
                    <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
                </div>
            </a>
            <div class="mt-3"><h6 class="mb-0">Typ menu</h6><p class="text-sm">Wybierz jeden z dostępnych motywów.</p></div>
            <div class="d-flex">
                <button class="btn bg-gradient-primary w-100 px-3 mb-2 active" data-class="bg-slate-900" onclick="sidebarType(this)">Ciemny</button>
                <button class="btn bg-gradient-primary w-100 px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">Jasny</button>
            </div>
            <div class="mt-3"><h6 class="mb-0">Statyczna nawigacja</h6></div>
            <div class="form-check form-switch ps-0"><input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)"></div>
            <hr class="horizontal dark my-sm-4">
            <a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/corporate-ui-dashboard">Zobacz licencję</a>
            <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/license/corporate-ui-dashboard">Zobacz dokumentację</a>
        </div>
    </div>
</div>

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script>
    var ctx = document.getElementById("chart-doughnut-info").getContext("2d");
    var ctx2 = document.getElementById("chart-doughnut-dark").getContext("2d");

    new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: ["EGLD", "ETH", "SOL", "BTC"],
        datasets: [{
          label: "Wallet",
          cutout: 40,
          backgroundColor: ["#c3e1ff", "#add4fc", "#78baff", "#419eff"],
          data: [450, 200, 100, 220],
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            backgroundColor: '#fff',
            bodyColor: '#1e293b',
            borderColor: '#e9ecef',
            borderWidth: 1,
            usePointStyle: true
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
            },
            ticks: {
              display: false
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false
            },
            ticks: {
              display: false
            },
          },
        },
      },
    });

    new Chart(ctx2, {
      type: "doughnut",
      data: {
        labels: ["EGLD", "ETH", "SOL", "BTC"],
        datasets: [{
          label: "Wallet",
          cutout: 40,
          backgroundColor: ["#d3d8e1", "#1f293b", "#666f7f", "#3b465a"],
          data: [350, 200, 150, 300],
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            backgroundColor: '#fff',
            bodyColor: '#1e293b',
            borderColor: '#e9ecef',
            borderWidth: 1,
            usePointStyle: true
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
            },
            ticks: {
              display: false
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false
            },
            ticks: {
              display: false
            },
          },
        },
      },
    });
  </script>
  <script>
    function sidebarColor(element) {
        const color = element.getAttribute('data-color');
        const sidebar = document.getElementById('sidenav-main');
        sidebar.classList.remove('bg-slate-900', 'bg-primary', 'bg-info', 'bg-success', 'bg-warning', 'bg-danger');
        sidebar.classList.add(`bg-${color}`);

        // Zmiana koloru ikon
        const icons = document.querySelectorAll('.sidenav .nav-link i');
        if (color === 'white') {
            icons.forEach(icon => icon.classList.remove('text-white'));
            icons.forEach(icon => icon.classList.add('text-dark'));
        } else {
            icons.forEach(icon => icon.classList.remove('text-dark'));
            icons.forEach(icon => icon.classList.add('text-white'));
        }
    }

    function sidebarType(element) {
        const type = element.getAttribute('data-class');
        const sidebar = document.getElementById('sidenav-main');
        sidebar.classList.remove('bg-slate-900', 'bg-white');
        sidebar.classList.add(type);

        // Zmiana koloru ikon
        const icons = document.querySelectorAll('.sidenav .nav-link i');
        if (type === 'bg-white') {
            icons.forEach(icon => icon.classList.remove('text-white'));
            icons.forEach(icon => icon.classList.add('text-dark'));
        } else {
            icons.forEach(icon => icon.classList.remove('text-dark'));
            icons.forEach(icon => icon.classList.add('text-white'));
        }
    }
    // Pobierz aktualny URL
    const currentUrl = window.location.href;

    // Znajdź wszystkie linki w panelu bocznym
    const navLinks = document.querySelectorAll('.sidenav .nav-link');

    // Iteruj przez linki i dodaj klasę "active" do odpowiedniego elementu
    navLinks.forEach(link => {
        if (link.href === currentUrl) {
            link.parentElement.classList.add('active');
        }
    });
</script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Corporate UI Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/corporate-ui-dashboard.min.js?v=1.0.0"></script>
</body>

</html>