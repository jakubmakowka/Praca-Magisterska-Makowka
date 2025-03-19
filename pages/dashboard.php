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
    exit();
}

$user_id = $_SESSION['id'];

// Połączenie z bazą danych
$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobranie danych
$total_revenue = 0;
$total_transactions = 0;
$avg_transaction = 0;
$monthly_revenue = 0;

// Łączny przychód
$sql_total_revenue = "SELECT SUM(amount) AS total_revenue FROM transactions";
$result_total_revenue = $conn->query($sql_total_revenue);
if ($result_total_revenue->num_rows > 0) {
    $row = $result_total_revenue->fetch_assoc();
    $total_revenue = $row['total_revenue'];
}

// Liczba transakcji
$sql_total_transactions = "SELECT COUNT(*) AS total_transactions FROM transactions";
$result_total_transactions = $conn->query($sql_total_transactions);
if ($result_total_transactions->num_rows > 0) {
    $row = $result_total_transactions->fetch_assoc();
    $total_transactions = $row['total_transactions'];
}

// Średnia kwota transakcji
$sql_avg_transaction = "SELECT AVG(amount) AS avg_transaction FROM transactions";
$result_avg_transaction = $conn->query($sql_avg_transaction);
if ($result_avg_transaction->num_rows > 0) {
    $row = $result_avg_transaction->fetch_assoc();
    $avg_transaction = $row['avg_transaction'];
}

// Miesięczne wpływy
$sql_monthly_revenue = "SELECT SUM(amount) AS monthly_revenue 
                        FROM transactions 
                        WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$result_monthly_revenue = $conn->query($sql_monthly_revenue);
if ($result_monthly_revenue->num_rows > 0) {
    $row = $result_monthly_revenue->fetch_assoc();
    $monthly_revenue = $row['monthly_revenue'];
}

// Pobranie danych do wykresu
$sql = "SELECT DATE(timestamp) AS date, SUM(amount) AS total_amount 
        FROM transactions 
        WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
        GROUP BY DATE(timestamp) 
        ORDER BY date ASC";
$result = $conn->query($sql);

$chart_data = [];
while ($row = $result->fetch_assoc()) {
    $chart_data[] = $row;
}

// Pobranie 5 użytkowników z największymi wpłatami
$sql_top_users = "SELECT accounts.username AS username, SUM(amount) AS total_amount 
                  FROM transactions
                  JOIN accounts ON transactions.account_id = accounts.id
                  GROUP BY account_id 
                  ORDER BY total_amount DESC 
                  LIMIT 5";
$result_top_users = $conn->query($sql_top_users);

$top_users = [];
$top_users_labels = [];
$top_users_data = [];

if ($result_top_users->num_rows > 0) {
    while ($row = $result_top_users->fetch_assoc()) {
        $top_users[] = $row;
        $top_users_labels[] = "" . $row['username']; // Możesz zastąpić "User X" nazwami użytkowników, jeśli masz dostęp do tabeli użytkowników
        $top_users_data[] = $row['total_amount'];
    }
}

// Pobranie 4 użytkowników z największą liczbą transakcji
$sql_top_users_by_transactions = "SELECT 
        accounts.username AS username, 
        COUNT(*) AS total_transactions, 
        SUM(amount) AS total_amount, 
        MAX(timestamp) AS last_payment_date 
    FROM transactions 
    JOIN accounts ON transactions.account_id = accounts.id
    GROUP BY account_id 
    ORDER BY total_transactions DESC 
    LIMIT 4";
$result_top_users_by_transactions = $conn->query($sql_top_users_by_transactions);

$top_users_by_transactions = [];
if ($result_top_users_by_transactions->num_rows > 0) {
    while ($row = $result_top_users_by_transactions->fetch_assoc()) {
        $top_users_by_transactions[] = $row;
    }
}

// Zamknięcie połączenia
$conn->close();

// Przekazanie danych do JavaScript
echo "<script>";
echo "var chartData = " . json_encode($chart_data) . ";";
echo "</script>";

echo "<script>";
echo "var topUsersLabels = " . json_encode($top_users_labels) . ";";
echo "var topUsersData = " . json_encode($top_users_data) . ";";
echo "</script>";
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
  <!-- Fonts and icons -->
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

<body class="g-sidenav-show bg-gray-100">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 bg-slate-900 fixed-start " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand d-flex align-items-center m-0" href="../pages/home.php" target="_blank">
        <span class="font-weight-bold text-lg">Fundacja Makówka</span>
      </a>
    </div>
    <div class="collapse navbar-collapse px-4  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link " href="../pages/home.php">
                <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                    <svg width="20px" height="20px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <title>home</title>
                        <path fill="none" d="M0 0h24v24H0z"/>
                        <path d="M12 3l10 9h-3v9h-6v-6h-4v6H3v-9H0l12-9z" fill="#FFFFFF"/>
                    </svg>
                </div>
                <span class="nav-link-text ms-1">Strona główna</span>
            </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="../pages/dashboard.php">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
              <svg width="30px" height="30px" viewBox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>dashboard</title>
                <g id="dashboard" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g id="template" transform="translate(12.000000, 12.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <path class="color-foreground" d="M0,1.71428571 C0,0.76752 0.76752,0 1.71428571,0 L22.2857143,0 C23.2325143,0 24,0.76752 24,1.71428571 L24,5.14285714 C24,6.08962286 23.2325143,6.85714286 22.2857143,6.85714286 L1.71428571,6.85714286 C0.76752,6.85714286 0,6.08962286 0,5.14285714 L0,1.71428571 Z" id="Path"></path>
                    <path class="color-background" d="M0,12 C0,11.0532171 0.76752,10.2857143 1.71428571,10.2857143 L12,10.2857143 C12.9468,10.2857143 13.7142857,11.0532171 13.7142857,12 L13.7142857,22.2857143 C13.7142857,23.2325143 12.9468,24 12,24 L1.71428571,24 C0.76752,24 0,23.2325143 0,22.2857143 L0,12 Z" id="Path"></path>
                    <path class="color-background" d="M18.8571429,10.2857143 C17.9103429,10.2857143 17.1428571,11.0532171 17.1428571,12 L17.1428571,22.2857143 C17.1428571,23.2325143 17.9103429,24 18.8571429,24 L22.2857143,24 C23.2325143,24 24,23.2325143 24,22.2857143 L24,12 C24,11.0532171 23.2325143,10.2857143 22.2857143,10.2857143 L18.8571429,10.2857143 Z" id="Path"></path>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Raporty - panel</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link  " href="../pages/tables.php">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
              <svg width="30px" height="30px" viewBox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>table</title>
                <g id="table" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g id="view-grid" transform="translate(12.000000, 12.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <path class="color-foreground" d="M3.42857143,0 C1.53502286,0 0,1.53502286 0,3.42857143 L0,6.85714286 C0,8.75069143 1.53502286,10.2857143 3.42857143,10.2857143 L6.85714286,10.2857143 C8.75069143,10.2857143 10.2857143,8.75069143 10.2857143,6.85714286 L10.2857143,3.42857143 C10.2857143,1.53502286 8.75069143,0 6.85714286,0 L3.42857143,0 Z" id="Path"></path>
                    <path class="color-background" d="M3.42857143,13.7142857 C1.53502286,13.7142857 0,15.2492571 0,17.1428571 L0,20.5714286 C0,22.4650286 1.53502286,24 3.42857143,24 L6.85714286,24 C8.75069143,24 10.2857143,22.4650286 10.2857143,20.5714286 L10.2857143,17.1428571 C10.2857143,15.2492571 8.75069143,13.7142857 6.85714286,13.7142857 L3.42857143,13.7142857 Z" id="Path"></path>
                    <path class="color-background" d="M13.7142857,3.42857143 C13.7142857,1.53502286 15.2492571,0 17.1428571,0 L20.5714286,0 C22.4650286,0 24,1.53502286 24,3.42857143 L24,6.85714286 C24,8.75069143 22.4650286,10.2857143 20.5714286,10.2857143 L17.1428571,10.2857143 C15.2492571,10.2857143 13.7142857,8.75069143 13.7142857,6.85714286 L13.7142857,3.42857143 Z" id="Path"></path>
                    <path class="color-foreground" d="M13.7142857,17.1428571 C13.7142857,15.2492571 15.2492571,13.7142857 17.1428571,13.7142857 L20.5714286,13.7142857 C22.4650286,13.7142857 24,15.2492571 24,17.1428571 L24,20.5714286 C24,22.4650286 22.4650286,24 20.5714286,24 L17.1428571,24 C15.2492571,24 13.7142857,22.4650286 13.7142857,20.5714286 L13.7142857,17.1428571 Z" id="Path"></path>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Wspomóż nas</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link  " href="../pages/wallet.php">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
              <svg width="30px" height="30px" viewBox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>wallet</title>
                <g id="wallet" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g id="credit-card" transform="translate(12.000000, 15.000000)" fill="#FFFFFF">
                    <path class="color-background" d="M3,0 C1.343145,0 0,1.343145 0,3 L0,4.5 L24,4.5 L24,3 C24,1.343145 22.6569,0 21,0 L3,0 Z" id="Path" fill-rule="nonzero"></path>
                    <path class="color-foreground" d="M24,7.5 L0,7.5 L0,15 C0,16.6569 1.343145,18 3,18 L21,18 C22.6569,18 24,16.6569 24,15 L24,7.5 Z M3,13.5 C3,12.67155 3.67158,12 4.5,12 L6,12 C6.82842,12 7.5,12.67155 7.5,13.5 C7.5,14.32845 6.82842,15 6,15 L4.5,15 C3.67158,15 3,14.32845 3,13.5 Z M10.5,12 C9.67158,12 9,12.67155 9,13.5 C9,14.32845 9.67158,15 10.5,15 L12,15 C12.82845,15 13.5,14.32845 13.5,13.5 C13.5,12.67155 12.82845,12 12,12 L10.5,12 Z" id="Shape"></path>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Portfel</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../pages/news.php">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
              <svg width="30px" height="30px" viewBox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>news</title>
                <g id="news-icon" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g id="news" transform="translate(12.000000, 15.000000)" fill="#FFFFFF">
                    <rect x="0" y="0" width="24" height="15" rx="2" class="color-background" fill="#FFFFFF" stroke="#000" stroke-width="1.5"/>
                    <rect x="2" y="3" width="10" height="2" class="color-foreground" fill="#000"/>
                    <rect x="2" y="6" width="14" height="2" class="color-foreground" fill="#000"/>
                    <rect x="2" y="9" width="14" height="2" class="color-foreground" fill="#000"/>
                    <rect x="2" y="12" width="6" height="2" class="color-foreground" fill="#000"/>
                    <rect x="10" y="12" width="6" height="2" class="color-foreground" fill="#000"/>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Aktualności</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../pages/reports.php">
            <div class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
              <svg width="30px" height="30px" viewBox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>reports</title>
                <g id="rtl" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g id="menu-alt-3" transform="translate(12.000000, 14.000000)" fill="#FFFFFF">
                    <path class="color-foreground" d="M0,1.71428571 C0,0.76752 0.76752,0 1.71428571,0 L22.2857143,0 C23.2325143,0 24,0.76752 24,1.71428571 C24,2.66105143 23.2325143,3.42857143 22.2857143,3.42857143 L1.71428571,3.42857143 C0.76752,3.42857143 0,2.66105143 0,1.71428571 Z" id="Path"></path>
                    <path class="color-background" d="M0,10.2857143 C0,9.33894857 0.76752,8.57142857 1.71428571,8.57142857 L22.2857143,8.57142857 C23.2325143,8.57142857 24,9.33894857 24,10.2857143 C24,11.2325143 23.2325143,12 22.2857143,12 L1.71428571,12 C0.76752,12 0,11.2325143 0,10.2857143 Z" id="Path"></path>
                    <path class="color-background" d="M10.2857143,18.8571429 C10.2857143,17.9103429 11.0532343,17.1428571 12,17.1428571 L22.2857143,17.1428571 C23.2325143,17.1428571 24,17.9103429 24,18.8571429 C24,19.8039429 23.2325143,20.5714286 22.2857143,20.5714286 L12,20.5714286 C11.0532343,20.5714286 10.2857143,19.8039429 10.2857143,18.8571429 Z" id="Path"></path>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Sprawozdania</span>
          </a>
        </li>
        <li class="nav-item mt-2">
          <div class="d-flex align-items-center nav-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="ms-2" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
              <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd" />
            </svg>
            <span class="font-weight-normal text-md ms-2">Zarządzanie kontem</span>
          </div>
        </li>
        <li class="nav-item border-start my-0 pt-2">
          <a class="nav-link position-relative ms-0 ps-2 py-2 " href="../pages/admin_panel.php">
            <span class="nav-link-text ms-1">Panel administratora</span>
          </a>
        </li>
        <li class="nav-item border-start my-0 pt-2">
          <a class="nav-link position-relative ms-0 ps-2 py-2 " href="../pages/profile.php">
            <span class="nav-link-text ms-1">Profil</span>
          </a>
        </li>
        <li class="nav-item border-start my-0 pt-2">
          <a class="nav-link position-relative ms-0 ps-2 py-2 " href="../pages/logout.php">
            <span class="nav-link-text ms-1">Wyloguj się</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer mx-4 ">
      <div class="card border-radius-md" id="sidenavCard">
        <div class="card-body  text-start  p-3 w-100">
          <div class="docs-info">
            <h6 class="font-weight-bold up mb-2">Potrzebujesz pomocy?</h6>
            <p class="text-sm font-weight-normal">Sprawdź naszą dokumentację:</p>
            <a href="https://www.creative-tim.com/learning-lab/bootstrap/license/corporate-ui-dashboard" target="_blank" class="font-weight-bold text-sm mb-0 icon-move-right w-100 mb-0">
              Dokumentacja
              <i class="fas fa-arrow-right-long text-sm ms-1" aria-hidden="true"></i>
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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Raporty</li>
          </ol>
          <h6 class="font-weight-bold mb-0">Raporty</h6>
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
        <div class="col-md-12">
          <div class="d-md-flex align-items-center mb-3 mx-2">
            <div class="mb-md-0 mb-3">
              <h3 class="font-weight-bold mb-0">Cześć, <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?></h3>
              <p class="mb-0">Witamy z powrotem!</p>
            </div>
            <button type="button" class="btn btn-sm btn-white btn-icon d-flex align-items-center mb-0 ms-md-auto mb-sm-0 mb-2 me-2" onclick="location.reload();">
              <span class="btn-inner--icon">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="d-block me-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
              </span>
              <span class="btn-inner--text">Sync</span>
            </button>
          </div>
        </div>
      </div>
      <hr class="my-0">
      <div class="row my-4">
      <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
        <div class="card shadow-xs border h-100">
          <div class="card-header pb-0">
            <h6 class="font-weight-semibold text-lg mb-0">Najwięksi darczyńcy</h6>
            <p class="text-sm">Użytkownicy z największą sumą wpłat łącznie.</p>
          </div>
          <div class="card-body py-3">
            <div class="chart mb-2">
              <canvas id="chart-top-users" class="chart-canvas" height="120"></canvas>
            </div>
          </div>
        </div>
      </div>
        <div class="col-lg-8 col-md-6">
          <div class="card shadow-xs border">
            <div class="card-header border-bottom pb-0">
              <div class="d-sm-flex align-items-center mb-3">
                <div>
                  <h6 class="font-weight-semibold text-lg mb-0">Regularni darczyńcy</h6>
                  <p class="text-sm mb-sm-0 mb-2">Użytkownicy z największą liczbą darowizn.</p>
                </div>
                <div class="ms-auto d-flex">
                <a href="generate_pdf.php" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0">
                    <span class="btn-inner--icon">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="d-block me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </span>
                    <span class="btn-inner--text">Pobierz</span>
                </a>
                </div>
              </div>
              <div class="pb-3 d-sm-flex align-items-center">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                  <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable1" autocomplete="off" checked>
                  <label class="btn btn-white px-3 mb-0" for="btnradiotable1">Wszystkie</label>
                  <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable2" autocomplete="off">
                  <label class="btn btn-white px-3 mb-0" for="btnradiotable2">Monitorowane</label>
                  <input type="radio" class="btn-check" name="btnradiotable" id="btnradiotable3" autocomplete="off">
                  <label class="btn btn-white px-3 mb-0" for="btnradiotable3">Potwierdzone</label>
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
            </div>
            <div class="card-body px-0 py-0">
              <div class="table-responsive p-0">
                  <table class="table align-items-center justify-content-center mb-0">
                      <thead class="bg-gray-100">
                          <tr>
                              <th class="text-secondary text-xs font-weight-semibold opacity-7">Użytkownik</th>
                              <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Liczba wpłat</th>
                              <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Wpłaty łącznie</th>
                              <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Ostatnia data płatności</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php foreach ($top_users_by_transactions as $user): ?>
                              <tr>
                                  <td>
                                      <div class="d-flex px-2">
                                          <div class="avatar avatar-sm rounded-circle bg-gray-100 me-2 my-2">
                                              <img src="../assets/img/apple-icon.png" class="w-80" alt="user">
                                          </div>
                                          <div class="my-auto">
                                              <h6 class="mb-0 text-sm">Użytkownik <?= htmlspecialchars($user['username'], ENT_QUOTES) ?></h6>
                                          </div>
                                      </div>
                                  </td>
                                  <td>
                                      <p class="text-sm font-weight-normal mb-0"><?= htmlspecialchars($user['total_transactions'], ENT_QUOTES) ?></p>
                                  </td>
                                  <td>
                                      <p class="text-sm font-weight-normal mb-0"><?= htmlspecialchars(number_format($user['total_amount'], 2, '.', ' ')) ?> zł</p>
                                  </td>
                                  <td>
                                      <span class="text-sm font-weight-normal"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($user['last_payment_date'])), ENT_QUOTES) ?></span>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
              </div>
          </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4.5 3.75a3 3 0 00-3 3v.75h21v-.75a3 3 0 00-3-3h-15z" />
                            <path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 003 3h15a3 3 0 003-3v-7.5zm-18 3.75a.75.75 0 01.75-.75h6a.75.75 0 010 1.5h-6a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">Łączny przychód</p>
                                <h4 class="mb-2 font-weight-bold"><?= number_format($total_revenue, 2) ?>zł</h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-success font-weight-bolder">
                                        <i class="fa fa-chevron-up text-xs me-1"></i>10.5%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.5 5.25a3 3 0 013-3h3a3 3 0 013 3v.205c.933.085 1.857.197 2.774.334 1.454.218 2.476 1.483 2.476 2.917v3.033c0 1.211-.734 2.352-1.936 2.752A24.726 24.726 0 0112 15.75c-2.73 0-5.357-.442-7.814-1.259-1.202-.4-1.936-1.541-1.936-2.752V8.706c0-1.434 1.022-2.7 2.476-2.917A48.814 48.814 0 017.5 5.455V5.25zm7.5 0v.09a49.488 49.488 0 00-6 0v-.09a1.5 1.5 0 011.5-1.5h3a1.5 1.5 0 011.5 1.5zm-3 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                            <path d="M3 18.4v-2.796a4.3 4.3 0 00.713.31A26.226 26.226 0 0012 17.25c2.892 0 5.68-.468 8.287-1.335.252-.084.49-.189.713-.311V18.4c0 1.452-1.047 2.728-2.523 2.923-2.12.282-4.282.427-6.477.427a49.19 49.19 0 01-6.477-.427C4.047 21.128 3 19.852 3 18.4z" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">Liczba transakcji</p>
                                <h4 class="mb-2 font-weight-bold"><?= $total_transactions ?></h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-success font-weight-bolder">
                                        <i class="fa fa-chevron-up text-xs me-1"></i>55%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm4.5 7.5a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0v-2.25a.75.75 0 01.75-.75zm3.75-1.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V12zm2.25-3a.75.75 0 01.75.75v6.75a.75.75 0 01-1.5 0V9.75A.75.75 0 0113.5 9zm3.75-1.5a.75.75 0 00-1.5 0v9a.75.75 0 001.5 0v-9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">Średnia transakcja</p>
                                <h4 class="mb-2 font-weight-bold"><?= number_format($avg_transaction, 2) ?>zł</h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-success font-weight-bolder">
                                        <i class="fa fa-chevron-up text-xs me-1"></i>22%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border shadow-xs mb-4">
                <div class="card-body text-start p-3 w-100">
                    <div class="icon icon-shape icon-sm bg-dark text-white text-center border-radius-sm d-flex align-items-center justify-content-center mb-3">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.25 2.25a3 3 0 00-3 3v4.318a3 3 0 00.879 2.121l9.58 9.581c.92.92 2.39 1.186 3.548.428a18.849 18.849 0 005.441-5.44c.758-1.16.492-2.629-.428-3.548l-9.58-9.581a3 3 0 00-2.122-.879H5.25zM6.375 7.5a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="w-100">
                                <p class="text-sm text-secondary mb-1">Wpływy - miesiąc</p>
                                <h4 class="mb-2 font-weight-bold"><?= number_format($monthly_revenue, 2) ?>zł</h4>
                                <div class="d-flex align-items-center">
                                    <span class="text-sm text-success font-weight-bolder">
                                        <i class="fa fa-chevron-up text-xs me-1"></i>18%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-xs border">
            <div class="card-header pb-5">
              <div class="d-sm-flex align-items-center mb-3">
                <div>
                  <h6 class="font-weight-semibold text-lg mb-0">Wpływy - wykres miesięczny</h6>
                  <p class="text-sm mb-sm-0 mb-2">Historia wpłat.</p>
                </div>
              </div>
            </div>
            <div class="card-body p-3">
              <div class="chart mt-n6">
                <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
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
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="fa fa-cog py-2"></i>
    </a>
    <div class="card shadow-lg ">
      <div class="card-header pb-0 pt-3 ">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Wspieracz - konfiguracja</h5>
          <p>Zobacz wszystkie opcje.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Kolor menu bocznego</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Typ menu</h6>
          <p class="text-sm">Wybierz jeden z dostępnych motywów.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-primary w-100 px-3 mb-2 active" data-class="bg-slate-900" onclick="sidebarType(this)">Ciemny</button>
          <button class="btn bg-gradient-primary w-100 px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">Jasny</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">Wybieraj między widokami.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3">
          <h6 class="mb-0">Statyczna nawigacja</h6>
        </div>
        <div class="form-check form-switch ps-0">
          <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
        </div>
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
        effect: "cards",
        grabCursor: true,
        initialSlide: 1,
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    };

    document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('chart-top-users').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topUsersLabels,
            datasets: [{
                label: 'Total Deposits',
                data: topUsersData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Kwota'
                    },
                    ticks: {
                        // Dodanie "zł" do wartości na osi Y
                        callback: function(value) {
                            return value + ' zł';
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Użytkownicy'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});


    var ctx2 = document.getElementById("chart-line").getContext("2d");

var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
gradientStroke1.addColorStop(1, 'rgba(45,168,255,0.2)');
gradientStroke1.addColorStop(0.2, 'rgba(45,168,255,0.0)');
gradientStroke1.addColorStop(0, 'rgba(45,168,255,0)'); // Niebieski gradient

// Przygotowanie danych
var labels = chartData.map(function(item) {
    return item.date; // Daty z ostatnich 30 dni
});

var data = chartData.map(function(item) {
    return item.total_amount; // Suma kwot dla każdego dnia
});

// Tworzenie wykresu
new Chart(ctx2, {
    type: "line",
    data: {
        labels: labels, // Etykiety osi X (daty)
        datasets: [{
            label: "Kwota",
            tension: 0,
            borderWidth: 2,
            pointRadius: 3,
            borderColor: "#2ca8ff",
            pointBorderColor: '#2ca8ff',
            pointBackgroundColor: '#2ca8ff',
            backgroundColor: gradientStroke1,
            fill: true,
            data: data, // Dane do wykresu (sumy kwot)
            maxBarThickness: 6
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    boxWidth: 6,
                    boxHeight: 6,
                    padding: 20,
                    pointStyle: 'circle',
                    borderRadius: 50,
                    usePointStyle: true,
                    font: {
                        weight: 400,
                    },
                },
            },
            tooltip: {
                backgroundColor: '#fff',
                titleColor: '#1e293b',
                bodyColor: '#1e293b',
                borderColor: '#e9ecef',
                borderWidth: 1,
                pointRadius: 2,
                usePointStyle: true,
                boxWidth: 8,
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
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [4, 4]
                },
                ticks: {
                    callback: function(value, index, ticks) {
                        return parseInt(value).toLocaleString() + ' zł'; // Formatowanie osi Y
                    },
                    display: true,
                    padding: 10,
                    color: '#b2b9bf',
                    font: {
                        size: 12,
                        family: "Noto Sans",
                        style: 'normal',
                        lineHeight: 2
                    },
                    color: "#64748B"
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                    borderDash: [4, 4]
                },
                ticks: {
                    display: true,
                    color: '#b2b9bf',
                    padding: 20,
                    font: {
                        size: 12,
                        family: "Noto Sans",
                        style: 'normal',
                        lineHeight: 2
                    },
                    color: "#64748B"
                }
            },
        },
    },
 });
  </script>
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