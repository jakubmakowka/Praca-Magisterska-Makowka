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
session_start();
include 'database.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: sign-in.html');
    exit;
}

// Połączenie z bazą danych
$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Zapytanie SQL dla kampanii
$sql = "SELECT id, name, current_amount, goal_amount, end_date FROM campaigns";
if ($stmt = $conn->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Błąd zapytania SQL: " . $conn->error);
}

// Ustawienia paginacji dla transakcji
$results_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Pobranie transakcji wszystkich użytkowników
$sql_transactions = "SELECT transactions.timestamp, transactions.amount, campaigns.name AS campaign_name, types.name AS payment_type
                     FROM transactions 
                     JOIN campaigns ON transactions.campaign_id = campaigns.id
                     JOIN types ON transactions.type_id = types.id
                     ORDER BY transactions.timestamp DESC 
                     LIMIT ? OFFSET ?";

$stmt_transactions = $conn->prepare($sql_transactions);
if (!$stmt_transactions) {
    die("Błąd zapytania SQL: " . $conn->error);
}
$stmt_transactions->bind_param("ii", $results_per_page, $offset);
$stmt_transactions->execute();
$result_transactions = $stmt_transactions->get_result();

// Pobranie liczby wszystkich transakcji
$sql_count = "SELECT COUNT(*) AS total FROM transactions";
$stmt_count = $conn->prepare($sql_count);
if (!$stmt_count) {
    die("Błąd zapytania SQL: " . $conn->error);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_transactions = $result_count->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_transactions / $results_per_page);

// Zamknięcie zapytań
$stmt_count->close();

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/corporate-ui-dashboard.css?v=1.0.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">
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
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg mx-5 px-0 shadow-none rounded" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-2">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-1 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Aplikacja</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Wspieranie</li>
          </ol>
          <h6 class="font-weight-bold mb-0">Wspieranie</h6>
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
    <!-- End Navbar -->
    <div class="container-fluid py-4 px-5">
      <div class="row">
        <div class="col-12">
          <div class="card card-background card-background-after-none align-items-start mt-4 mb-5">
            <div class="full-background" style="background-image: url('../assets/img/header-blue-purple.jpg')"></div>
            <div class="card-body text-start p-4 w-100">
              <h3 class="text-white mb-2">Miło widzieć Cię z powrotem <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?> 🔥</h3>
              <p class="mb-4 font-weight-semibold">
                Sprawdź historię swoich ostatnich darowizn w zakładce Portfel.
              </p>
              <button type="button" class="btn btn-outline-white btn-blur btn-icon d-flex align-items-center mb-0">
                <span class="btn-inner--icon">
                  <svg width="14" height="14" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="d-block me-2">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7 14C10.866 14 14 10.866 14 7C14 3.13401 10.866 0 7 0C3.13401 0 0 3.13401 0 7C0 10.866 3.13401 14 7 14ZM6.61036 4.52196C6.34186 4.34296 5.99664 4.32627 5.71212 4.47854C5.42761 4.63081 5.25 4.92731 5.25 5.25V8.75C5.25 9.0727 5.42761 9.36924 5.71212 9.52149C5.99664 9.67374 6.34186 9.65703 6.61036 9.47809L9.23536 7.72809C9.47879 7.56577 9.625 7.2926 9.625 7C9.625 6.70744 9.47879 6.43424 9.23536 6.27196L6.61036 4.52196Z" />
                  </svg>
                </span>
                <span class="btn-inner--text">Zobacz więcej! Więcej informacji na naszym kanale YouTube.</span>
              </button>
              <img src="../assets/img/3d-cube.png" alt="3d-cube" class="position-absolute top-0 end-1 w-25 max-width-200 mt-n6 d-sm-block d-none" />
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-5">
        <div class="position-relative overflow-hidden">
          <div class="swiper mySwiper mt-4 mb-2">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                  <div class="card card-background shadow-none border-radius-xl card-background-after-none align-items-start mb-0">
                    <div class="full-background bg-cover" style="background-image: url('../assets/img/goals/1.jpg')"></div>
                    <div class="card-body text-start px-3 py-0 w-100">
                      <div class="row mt-12">
                        <div class="col-sm-3 mt-auto">
                          <h4 class="text-white font-weight-bolder">#1</h4>
                          <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Nazwa</p>
                          <h5 class="text-white font-weight-bolder">Drużyna AMP Kraków</h5>
                        </div>
                        <div class="col-sm-3 ms-auto mt-auto">
                          <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Kategoria</p>
                          <h5 class="text-white font-weight-bolder">Sport</h5>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="swiper-slide">
                <div class="card card-background shadow-none border-radius-xl card-background-after-none align-items-start mb-0">
                <div class="full-background bg-cover" style="background-image: url('../assets/img/goals/2.jpg')"></div>
                  <div class="card-body text-start px-3 py-0 w-100">
                    <div class="row mt-12">
                      <div class="col-sm-3 mt-auto">
                        <h4 class="text-white font-weight-bolder">#2</h4>
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Nazwa</p>
                        <h5 class="text-white font-weight-bolder">Na wózku do pracy - projekt</h5>
                      </div>
                      <div class="col-sm-3 ms-auto mt-auto">
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Kategoria</p>
                        <h5 class="text-white font-weight-bolder">Choroby</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="card card-background shadow-none border-radius-xl card-background-after-none align-items-start mb-0">
                <div class="full-background bg-cover" style="background-image: url('../assets/img/goals/3.jpg')"></div>
                  <div class="card-body text-start px-3 py-0 w-100">
                    <div class="row mt-12">
                      <div class="col-sm-3 mt-auto">
                        <h4 class="text-white font-weight-bolder">#3</h4>
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Nazwa</p>
                        <h5 class="text-white font-weight-bolder">Wsparcie pacjentów - onkologia</h5>
                      </div>
                      <div class="col-sm-3 ms-auto mt-auto">
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Kategoria</p>
                        <h5 class="text-white font-weight-bolder">Choroby</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="card card-background shadow-none border-radius-xl card-background-after-none align-items-start mb-0">
                <div class="full-background bg-cover" style="background-image: url('../assets/img/goals/4.jpg')"></div>
                  <div class="card-body text-start px-3 py-0 w-100">
                    <div class="row mt-12">
                      <div class="col-sm-3 mt-auto">
                        <h4 class="text-white font-weight-bolder">#4</h4>
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Nazwa</p>
                        <h5 class="text-white font-weight-bolder">Dom samotnej matki </h5>
                      </div>
                      <div class="col-sm-3 ms-auto mt-auto">
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Kategoria</p>
                        <h5 class="text-white font-weight-bolder">Wsparcie</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="card card-background shadow-none border-radius-xl card-background-after-none align-items-start mb-0">
                <div class="full-background bg-cover" style="background-image: url('../assets/img/goals/5.jpg')"></div>
                  <div class="card-body text-start px-3 py-0 w-100">
                    <div class="row mt-12">
                      <div class="col-sm-3 mt-auto">
                        <h4 class="text-white font-weight-bolder">#5</h4>
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Nazwa</p>
                        <h5 class="text-white font-weight-bolder">Dla naszych dzieci - wsparcie</h5>
                      </div>
                      <div class="col-sm-3 ms-auto mt-auto">
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Kategoria</p>
                        <h5 class="text-white font-weight-bolder">Choroby</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-slide">
                <div class="card card-background shadow-none border-radius-xl card-background-after-none align-items-start mb-0">
                <div class="full-background bg-cover" style="background-image: url('../assets/img/goals/6.jpg')"></div>
                  <div class="card-body text-start px-3 py-0 w-100">
                    <div class="row mt-12">
                      <div class="col-sm-3 mt-auto">
                        <h4 class="text-white font-weight-bolder">#6</h4>
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Nazwa</p>
                        <h5 class="text-white font-weight-bolder">Dom starców w Krakowie</h5>
                      </div>
                      <div class="col-sm-3 ms-auto mt-auto">
                        <p class="text-white opacity-6 text-xs font-weight-bolder mb-0">Kategoria</p>
                        <h5 class="text-white font-weight-bolder">Wsparcie</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
        </div>
      </div>
      <div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Wszystkie nasze kampanie</h6>
                        <p class="text-sm mb-sm-0">Pomóż nam osiągnąć wspólne cele, wpłacając pieniądze na jedną z poniższych kampanii.</p>
                    </div>
                    <div class="ms-auto d-flex">
                        <div class="input-group input-group-sm ms-auto me-2">
                            <span class="input-group-text text-body">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control form-control-sm" placeholder="Szukaj" id="searchInput">
                        </div>
                        <a href="export_pdf.php" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0 me-2">
                            <span class="btn-inner--icon">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="d-block me-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </span>
                            <span class="btn-inner--text">Pobierz</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 py-0">
                <div class="table-responsive p-0">
                    <?php
                    if ($result->num_rows > 0) {
                        echo '<table class="table table-hover align-items-center justify-content-center mb-0" id="campaignsTable">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-secondary text-xs font-weight-semibold opacity-7">Kampania</th>
                                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2 sortable" data-sort="current_amount">
                                            Zebrana kwota
                                            <i class="fas fa-sort ms-2"></i> <!-- Ikona sortowania -->
                                        </th>
                                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2 sortable" data-sort="goal_amount">
                                            Cel
                                            <i class="fas fa-sort ms-2"></i> <!-- Ikona sortowania -->
                                        </th>
                                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2 sortable" data-sort="status">
                                            Status
                                            <i class="fas fa-sort ms-2"></i> <!-- Ikona sortowania -->
                                        </th>
                                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Pasek progresu</th>
                                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2 sortable" data-sort="end_date">
                                            Data zakończenia
                                            <i class="fas fa-sort ms-2"></i> <!-- Ikona sortowania -->
                                        </th>
                                        <th class="text-center text-secondary text-xs font-weight-semibold opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>';

                        while ($row = $result->fetch_assoc()) {
                            $name = htmlspecialchars($row['name']);
                            $goal_amount = number_format($row['goal_amount'], 2, ',', ' ') . " zł";
                            $current_amount = number_format($row['current_amount'], 2, ',', ' ') . " zł";
                            $end_date = htmlspecialchars($row['end_date']);
                            $progress = ($row['goal_amount'] > 0) ? min(($row['current_amount'] / $row['goal_amount']) * 100, 100) : 0;
                            
                            // Status
                            $statusBadge = ($row['current_amount'] >= $row['goal_amount']) ?
                                '<td><span class="badge badge-sm border border-success text-success bg-success">Zakończono</span></td>' :
                                '<td><span class="badge badge-sm border border-warning text-warning bg-warning">W trakcie</span></td>';
                            
                            echo "<tr>
                                    <td>
                                        <div class='d-flex px-2'>
                                            <div class='avatar avatar-sm rounded-circle bg-gray-100 me-2 my-2'>
                                                <img src='../assets/img/favicon.png' class='w-80' alt='kampania'>
                                            </div>
                                            <div class='my-auto'>
                                                <h6 class='mb-0 text-sm'>$name</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><p class='text-sm font-weight-normal mb-0'>$current_amount</p></td>
                                    <td><p class='text-sm font-weight-normal mb-0'>$goal_amount</p></td>
                                    $statusBadge
                                    <td>
                                        <div class='progress' style='height: 10px; width: 150px;'>
                                            <div class='progress-bar bg-success' role='progressbar' style='width: $progress%;' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100'></div>
                                        </div>
                                    </td>
                                    <td><p class='text-sm font-weight-normal mb-0'>$end_date</p></td>
                                    <td class='align-middle'>
                                        <a href='../pages/payment.php?campaign_id=" . intval($row['id']) . "' class='btn btn-sm btn-success btn-icon align-items-center mb-0 me-2'>Wpłać datek</a>
                                    </td>
                                  </tr>";
                        }

                        echo '</tbody></table>';
                    } else {
                        echo '<div class="text-center p-4">Brak dostępnych kampanii.</div>';
                    }
                    ?>
                </div>
                <div class="border-top py-3 px-3 d-flex align-items-center">
                    <button class="btn btn-sm btn-white d-sm-block d-none mb-0" id="prevPage">Poprzednia</button>
                    <nav aria-label="..." class="ms-auto">
                        <ul class="pagination pagination-light mb-0" id="pagination">
                            <!-- Pagination links will be dynamically inserted here -->
                        </ul>
                    </nav>
                    <button class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto" id="nextPage">Następna</button>
                </div>
            </div>
        </div>
    </div>
</div>
      <div class="row">
        <div class="col-12">
        <div class="card border shadow-xs mb-4">
    <div class="card-header border-bottom pb-0">
        <div class="d-sm-flex align-items-center">
            <div>
                <h6 class="font-weight-semibold text-lg mb-0">Ostatnie wpłaty</h6>
                <p class="text-sm">Historia wszystkich darowizn</p>
            </div>
            <div class="ms-auto d-flex">
                <button type="button" class="btn btn-sm btn-white me-2">
                    Zobacz więcej
                </button>
            </div>
        </div>
    </div>
    <div class="card-body px-0 py-0">
        <div class="border-bottom py-3 px-3 d-sm-flex align-items-center">
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable1" autocomplete="off" checked>
                <label class="btn btn-white px-3 mb-0" for="btnradiotable1">Wszystkie</label>
                <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable2" autocomplete="off">
                <label class="btn btn-white px-3 mb-0" for="btnradiotable2">Filtruj</label>
                <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable3" autocomplete="off">
                <label class="btn btn-white px-3 mb-0" for="btnradiotable3">Sortuj</label>
            </div>
            <div class="input-group w-sm-25 ms-auto">
                <span class="input-group-text text-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                    </svg>
                </span>
                <input type="text" class="form-control" placeholder="Szukaj">
            </div>
        </div>
        <div class="table-responsive p-0">
            <table class="table table-hover align-items-center justify-content-center mb-0" id="transactionsTable">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-secondary text-xs font-weight-semibold opacity-7">Data</th>
                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2 sortable" data-sort="amount">
                            Kwota
                            <i class="fas fa-sort ms-2"></i>
                        </th>
                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2 sortable" data-sort="campaign_name">
                            Kampania
                            <i class="fas fa-sort ms-2"></i>
                        </th>
                        <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2 sortable" data-sort="payment_type">
                            Forma płatności
                            <i class="fas fa-sort ms-2"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_transactions->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class='d-flex px-2'>
                                    <div class='avatar avatar-sm rounded-circle bg-gray-100 me-2 my-2'>
                                        <img src='../assets/img/coin.png' class='w-80' alt='data'>
                                    </div>
                                    <div class='my-auto'>
                                        <h6 class='mb-0 text-sm'><?php echo htmlspecialchars($row['timestamp']); ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class='d-flex px-2'>
                                    <div class='avatar avatar-sm rounded-circle bg-gray-100 me-2 my-2'>
                                        <img src='../assets/img/stonksup.png' class='w-80' alt='kwota'>
                                    </div>
                                    <div class='my-auto'>
                                        <p class='text-sm font-weight-normal mb-0'><?php echo number_format($row['amount'], 2, ',', ' '); ?> zł</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class='text-sm font-weight-normal mb-0'><?php echo htmlspecialchars($row['campaign_name']); ?></p>
                            </td>
                            <td>
                                <p class='text-sm font-weight-normal mb-0'><?php echo htmlspecialchars($row['payment_type']); ?></p>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="border-top py-3 px-3 d-flex align-items-center">
            <button class="btn btn-sm btn-white d-sm-block d-none mb-0" id="prevPageTransactions">Poprzednia</button>
            <nav aria-label="..." class="ms-auto">
                <ul class="pagination pagination-light mb-0" id="paginationTransactions">
                    <!-- Pagination links will be dynamically inserted here -->
                </ul>
            </nav>
            <button class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto" id="nextPageTransactions">Następna</button>
        </div>
    </div>
</div>
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
  <script src="../assets/js/plugins/swiper-bundle.min.js" type="text/javascript"></script>
  <script>
if (document.getElementsByClassName('mySwiper')) {
  var swiper = new Swiper(".mySwiper", {
    effect: "slide", // Use the slide effect (default)
    grabCursor: true,
    centeredSlides: true, // Center the slides
    slidesPerView: "auto", // View multiple slides at once
    initialSlide: 0,
    autoplay: {
      delay: 2000, // Delay between slides
      disableOnInteraction: true, // Don't stop autoplay on interaction
    },
    loop: true, // Infinite loop
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
}

  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
</script>

<!-- Skrypty JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sortowanie tabeli kampanii
    document.querySelectorAll('#campaignsTable .sortable').forEach(header => {
        header.addEventListener('click', () => {
            const table = document.getElementById('campaignsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const index = Array.from(header.parentNode.children).indexOf(header);
            const isAscending = header.classList.toggle('asc');

            // Usuń ikony sortowania z innych nagłówków
            document.querySelectorAll('#campaignsTable .sortable i').forEach(icon => {
                icon.classList.remove('fa-sort-up', 'fa-sort-down', 'text-primary');
            });

            // Dodaj odpowiednią ikonę do aktualnego nagłówka
            const icon = header.querySelector('i');
            if (isAscending) {
                icon.classList.remove('fa-sort');
                icon.classList.add('fa-sort-up', 'text-primary');
            } else {
                icon.classList.remove('fa-sort');
                icon.classList.add('fa-sort-down', 'text-primary');
            }

            // Sortowanie wierszy
            rows.sort((a, b) => {
                const aValue = a.children[index].textContent.trim();
                const bValue = b.children[index].textContent.trim();
                return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            });

            // Aktualizacja tabeli
            rows.forEach(row => tbody.appendChild(row));
        });
    });

    // Wyszukiwanie w tabeli kampanii
    document.getElementById('searchInput').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#campaignsTable tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('td h6').textContent.toLowerCase();
            row.style.display = name.includes(searchTerm) ? '' : 'none';
        });
    });

    // Paginacja tabeli kampanii
    const rowsPerPage = 10;
    const rows = document.querySelectorAll('#campaignsTable tbody tr');
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    const pagination = document.getElementById('pagination');

    const updatePagination = (currentPage) => {
        pagination.innerHTML = '';
        for (let i = 1; i <= pageCount; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link border-0 font-weight-bold" href="javascript:;">${i}</a>`;
            li.addEventListener('click', () => showPage(i));
            pagination.appendChild(li);
        }
    };

    const showPage = (page) => {
        rows.forEach((row, index) => {
            row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? '' : 'none';
        });
        updatePagination(page);
    };

    document.getElementById('prevPage').addEventListener('click', () => {
        const currentPage = parseInt(document.querySelector('#pagination .page-item.active').textContent);
        if (currentPage > 1) showPage(currentPage - 1);
    });

    document.getElementById('nextPage').addEventListener('click', () => {
        const currentPage = parseInt(document.querySelector('#pagination .page-item.active').textContent);
        if (currentPage < pageCount) showPage(currentPage + 1);
    });

    // Inicjalizacja paginacji dla tabeli kampanii
    showPage(1);

    // Sortowanie tabeli transakcji
    document.querySelectorAll('#transactionsTable .sortable').forEach(header => {
        header.addEventListener('click', () => {
            const table = document.getElementById('transactionsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const index = Array.from(header.parentNode.children).indexOf(header);
            const isAscending = header.classList.toggle('asc');

            // Usuń ikony sortowania z innych nagłówków
            document.querySelectorAll('#transactionsTable .sortable i').forEach(icon => {
                icon.classList.remove('fa-sort-up', 'fa-sort-down', 'text-primary');
            });

            // Dodaj odpowiednią ikonę do aktualnego nagłówka
            const icon = header.querySelector('i');
            if (isAscending) {
                icon.classList.remove('fa-sort');
                icon.classList.add('fa-sort-up', 'text-primary');
            } else {
                icon.classList.remove('fa-sort');
                icon.classList.add('fa-sort-down', 'text-primary');
            }

            // Sortowanie wierszy
            rows.sort((a, b) => {
                const aValue = a.children[index].textContent.trim();
                const bValue = b.children[index].textContent.trim();
                return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
            });

            // Aktualizacja tabeli
            rows.forEach(row => tbody.appendChild(row));
        });
    });

    // Paginacja tabeli transakcji
    const rowsPerPageTransactions = 10;
    const rowsTransactions = document.querySelectorAll('#transactionsTable tbody tr');
    const pageCountTransactions = Math.ceil(rowsTransactions.length / rowsPerPageTransactions);
    const paginationTransactions = document.getElementById('paginationTransactions');

    const updatePaginationTransactions = (currentPage) => {
        paginationTransactions.innerHTML = '';
        for (let i = 1; i <= pageCountTransactions; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link border-0 font-weight-bold" href="javascript:;">${i}</a>`;
            li.addEventListener('click', () => showPageTransactions(i));
            paginationTransactions.appendChild(li);
        }
    };

    const showPageTransactions = (page) => {
        rowsTransactions.forEach((row, index) => {
            row.style.display = (index >= (page - 1) * rowsPerPageTransactions && index < page * rowsPerPageTransactions) ? '' : 'none';
        });
        updatePaginationTransactions(page);
    };

    document.getElementById('prevPageTransactions').addEventListener('click', () => {
        const currentPage = parseInt(document.querySelector('#paginationTransactions .page-item.active').textContent);
        if (currentPage > 1) showPageTransactions(currentPage - 1);
    });

    document.getElementById('nextPageTransactions').addEventListener('click', () => {
        const currentPage = parseInt(document.querySelector('#paginationTransactions .page-item.active').textContent);
        if (currentPage < pageCountTransactions) showPageTransactions(currentPage + 1);
    });

    // Inicjalizacja paginacji dla tabeli transakcji
    showPageTransactions(1);
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