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

// Regeneracja identyfikatora sesji dla bezpieczeństwa
if (!isset($_SESSION['loggedin'])) {
    session_regenerate_id(true);
    header('Location: sign-in.html');
    exit;
}

// Opcjonalnie: ustawienie limitu czasu na sesję
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset(); 
    session_destroy(); 
    header('Location: sign-in.html');
    exit;
}
$_SESSION['last_activity'] = time(); // Aktualizacja znacznika czasu aktywności
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
  <script src="https://kit.fontawesome.com/349ee9c857.js" crossorigin="anonymous"></script>
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
    <div class="container">
      <div class="card card-body py-2 bg-transparent shadow-none">
        <div class="row">
          <div class="col-auto pt-7">
            <div class="avatar avatar-2xl rounded-circle position-relative mt-n7 border border-gray-100 border-4">
              <img src="../assets/img/team-2.jpg" alt="profile_image" class="w-100">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h3 class="mb-0 font-weight-bold">
                <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?>
              </h3>
              <p class="mb-0">
                noah_mclaren@mail.com
              </p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3 text-sm-end">
            <a href="javascript:;" class="btn btn-sm btn-white">Cancel</a>
            <a href="javascript:;" class="btn btn-sm btn-dark">Save</a>
          </div>
        </div>
      </div>
    </div>
    <div class="container my-3 py-3">
      <div class="row">
        <div class="col-12 col-xl-4 mb-4">
          <div class="card border shadow-xs h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0 font-weight-semibold text-lg">Notifications settings</h6>
              <p class="text-sm mb-1">Here you can set preferences.</p>
            </div>
            <div class="card-body p-3">
              <h6 class="text-dark font-weight-semibold mb-1">Account</h6>
              <ul class="list-group">
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckDefault" checked>
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckDefault">Email me when someone follows me</label>
                  </div>
                </li>
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckDefault1">
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckDefault1">Email me when someone answers on my post</label>
                  </div>
                </li>
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckDefault2" checked>
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckDefault2">Email me when someone mentions me</label>
                  </div>
                </li>
              </ul>
              <h6 class="text-dark font-weight-semibold mt-2 mb-1">Application</h6>
              <ul class="list-group">
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckDefault3">
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckDefault3">New launches and projects</label>
                  </div>
                </li>
                <li class="list-group-item border-0 px-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckDefault4" checked>
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckDefault4">Monthly product updates</label>
                  </div>
                </li>
                <li class="list-group-item border-0 px-0 pb-0">
                  <div class="form-check form-switch ps-0">
                    <input class="form-check-input ms-auto" type="checkbox" id="flexSwitchCheckDefault5">
                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="flexSwitchCheckDefault5">Subscribe to newsletter</label>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4 mb-4">
          <div class="card border shadow-xs h-100">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-md-8 col-9">
                  <h6 class="mb-0 font-weight-semibold text-lg">Profile information</h6>
                  <p class="text-sm mb-1">Edit the information about you.</p>
                </div>
                <div class="col-md-4 col-3 text-end">
                  <button type="button" class="btn btn-white btn-icon px-2 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32L19.513 8.2z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            <div class="card-body p-3">
              <ul class="list-group">
                <li class="list-group-item border-0 ps-0 text-dark font-weight-semibold pt-0 pb-1 text-sm"><span class="text-secondary">First Name:</span> &nbsp; Noah</li>
                <li class="list-group-item border-0 ps-0 text-dark font-weight-semibold pb-1 text-sm"><span class="text-secondary">Last Name:</span> &nbsp; Mclaren</li>
                <li class="list-group-item border-0 ps-0 text-dark font-weight-semibold pb-1 text-sm"><span class="text-secondary">Mobile:</span> &nbsp; +(44) 123 1234 123</li>
                <li class="list-group-item border-0 ps-0 text-dark font-weight-semibold pb-1 text-sm"><span class="text-secondary">Function:</span> &nbsp; Manager - Organization</li>
                <li class="list-group-item border-0 ps-0 text-dark font-weight-semibold pb-1 text-sm"><span class="text-secondary">Location:</span> &nbsp; USA</li>
              </ul>
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
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
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