<?php
session_start();

// Regeneracja identyfikatora sesji dla bezpieczeństwa
if (!isset($_SESSION['loggedin'])) {
    session_regenerate_id(true);
    header('Location: sign-in.html');
    exit;
}

// Limit czasu sesji (30 minut)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: sign-in.html');
    exit;
}
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Fundacja Makówka</title>
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Noto+Sans:300,400,500,600,700,800|PT+Mono:300,400,500,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/corporate-ui-dashboard.css?v=1.0.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <header class="bg-gradient-dark">
            <div class="page-header min-vh-75" style="background-image: url('../assets/img/bg9.jpg');">
                <span class="mask bg-gradient-dark opacity-6"></span>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 text-center mx-auto my-auto">
                            <h1 class="text-white">Wspieraj naszą misję</h1>
                            <p class="lead mb-4 text-white opacity-8">Razem możemy zmieniać świat na lepsze i realizować nasze marzenia. <br>Twoje wsparcie ma ogromne znaczenie.</p>
                            <a href="../pages/tables.php" class="btn btn-lg bg-white text-dark">Przejdź do serwisu</a>
                            <h6 class="text-white mb-2 mt-5">Znajdź nas na</h6>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:;"><i class="fab fa-facebook text-white me-4 fa-3x"></i></a>
                                <a href="javascript:;"><i class="fab fa-instagram text-white me-4 fa-3x"></i></a>
                                <a href="javascript:;"><i class="fab fa-twitter text-white me-4 fa-3x"></i></a>
                                <a href="javascript:;"><i class="fab fa-google-plus text-white fa-3x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="card card-body shadow-xl mx-3 mx-md-4 mt-n6">
            <!-- Section with Info -->
            <section class="py-7">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="row justify-content-start">
                                <div class="col-md-6">
                                    <div class="info">
                                        <i class="ni ni-hat-3 text-3xl text-gradient text-info mb-3"></i>
                                        <h5>Edukacja i rozwój</h5>
                                        <p>Organizujemy szkolenia i warsztaty, które pomagają rozwijać umiejętności i wiedzę.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info">
                                        <i class="ni ni-money-coins text-3xl text-gradient text-info mb-3"></i>
                                        <h5>Pomagamy potrzebującym</h5>
                                        <p>Fundacja Makówka działa na rzecz osób w trudnej sytuacji życiowej, oferując wsparcie i pomoc.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-start mt-4">
                                <div class="col-md-6">
                                    <div class="info">
                                        <i class="ni ni-bullet-list-67 text-3xl text-gradient text-info mb-3"></i>
                                        <h5>Wspieramy lokalne inicjatywy</h5>
                                        <p>Angażujemy się w projekty społeczne i kulturalne, które poprawiają jakość życia mieszkańców.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info">
                                        <i class="ni ni-spaceship text-3xl text-gradient text-info mb-3"></i>
                                        <h5>Transparentność działań</h5>
                                        <p>Dbamy o przejrzystość finansową i regularnie publikujemy raporty z naszej działalności.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 ms-auto mt-lg-0 mt-4">
                            <div class="card">
                                <div class="card-header p-0 position-relative mt-2 mx-2 z-index-2">
                                    <a class="d-block blur-shadow-image">
                                        <img src="../assets/img/reports.jpg" alt="img-colored-shadow" class="img-fluid border-radius-lg">
                                    </a>
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="font-weight-normal">
                                        <a href="javascript:;">Sprawozdania finansowe</a>
                                    </h5>
                                    <p class="mb-0">Zobacz, jak zarządzamy środkami i na co przeznaczamy fundusze. Nasze raporty są dostępne dla każdego.</p>
                                    <a type="button" href="../pages/reports.php" class="btn bg-gradient-info btn-sm mb-0 mt-3">Sprawozdania</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Team Section -->
            <section class="pb-5 position-relative bg-gradient-dark mx-n3">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 text-start mb-5 mt-5">
                            <h3 class="text-white z-index-1 position-relative">Nasza drużyna</h3>
                            <p class="text-white opacity-8 mb-0">Naszą misją jest wspieranie lokalnych społeczności poprzez innowacyjne projekty i inicjatywy.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="card card-profile mt-4">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-12 mt-n5">
                                        <a href="javascript:;">
                                            <div class="p-3 pe-md-0">
                                                <img class="w-100 border-radius-md shadow-lg" src="../assets/img/team-5.jpg" alt="image">
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-8 col-md-6 col-12 my-auto">
                                        <div class="card-body ps-lg-0">
                                            <h5 class="mb-0">Jan Makówka</h5>
                                            <h6 class="text-info">Prezes Zarządu</h6>
                                            <p class="mb-0">Założyciel i prezes Fundacji Makówka. Doświadczony menedżer i działacz społeczny.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="card card-profile mt-lg-4 mt-5">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-12 mt-n5">
                                        <a href="javascript:;">
                                            <div class="p-3 pe-md-0">
                                                <img class="w-100 border-radius-md shadow-lg" src="../assets/img/bruce-mars.jpg" alt="image">
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-8 col-md-6 col-12 my-auto">
                                        <div class="card-body ps-lg-0">
                                            <h5 class="mb-0">Robert Łęczyński</h5>
                                            <h6 class="text-info">Wiceprezes Zarządu</h6>
                                            <p class="mb-0">Specjalista ds. zarządzania projektami z doświadczeniem w sektorze non-profit.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-6 col-12">
                            <div class="card card-profile mt-4 z-index-2">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-12 mt-n5">
                                        <a href="javascript:;">
                                            <div class="p-3 pe-md-0">
                                                <img class="w-100 border-radius-md shadow-lg" src="../assets/img/ivana-squares.jpg" alt="image">
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-8 col-md-6 col-12 my-auto">
                                        <div class="card-body ps-lg-0">
                                            <h5 class="mb-0">Maria Kowalczyk</h5>
                                            <h6 class="text-info">Skarbnik</h6>
                                            <p class="mb-0">Doświadczona księgowa i doradca finansowy. Zarządza finansami fundacji.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="card card-profile mt-lg-4 mt-5 z-index-2">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-12 mt-n5">
                                        <a href="javascript:;">
                                            <div class="p-3 pe-md-0">
                                                <img class="w-100 border-radius-md shadow-lg" src="../assets/img/ivana-square.jpg" alt="image">
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-8 col-md-6 col-12 my-auto">
                                        <div class="card-body ps-lg-0">
                                            <h5 class="mb-0">Piotr Wiśniewski</h5>
                                            <h6 class="text-info">Sekretarz</h6>
                                            <p class="mb-0">Ekspert w dziedzinie komunikacji i PR. Promuje działania fundacji.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="pt-4 pb-6" id="count-stats">
                <div class="container">
                    <div class="row mb-7">
                        <div class="col-lg-2 col-md-4 col-6 mb-4">
                            <img class="w-100 opacity-7" src="../assets/img/logos/gray-logos/logo-coinbase.svg" alt="logo">
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-4">
                            <img class="w-100 opacity-7" src="../assets/img/logos/gray-logos/logo-nasa.svg" alt="logo">
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-4">
                            <img class="w-100 opacity-7" src="../assets/img/logos/gray-logos/logo-netflix.svg" alt="logo">
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-4">
                            <img class="w-100 opacity-7" src="../assets/img/logos/gray-logos/logo-pinterest.svg" alt="logo">
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-4">
                            <img class="w-100 opacity-7" src="../assets/img/logos/gray-logos/logo-spotify.svg" alt="logo">
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-4">
                            <img class="w-100 opacity-7" src="../assets/img/logos/gray-logos/logo-vodafone.svg" alt="logo">
                        </div>
                    </div>
                    <div class="row justify-content-center text-center">
                        <div class="col-md-3">
                            <h1 class="text-gradient text-info" id="state1" countTo="5234">0</h1>
                            <h5>Podopiecznych</h5>
                            <p>Tylu osobom udzieliliśmy wsparcia dzięki naszym darczyńcom.</p>
                        </div>
                        <div class="col-md-3">
                            <h1 class="text-gradient text-info"><span id="state2" countTo="3400">0</span></h1>
                            <h5>Miesięcznych wpłat</h5>
                            <p>Tyle wpłat w złotówkach miesięcznie udaje nam się uzyskać na rzecz naszych projektów.</p>
                        </div>
                        <div class="col-md-3">
                            <h1 class="text-gradient text-info"><span id="state3" countTo="1524">0</span></h1>
                            <h5>Godzin</h5>
                            <p>Łącznie tyle godzin poświęcamy rocznie na promowanie naszych działań i projektów.</p>
                        </div>
                    </div>
                </div>
            </section>
            <img src="../assets/img/poppy.jpg" class="img-fluid shadow border-radius-lg"></img>
            <!-- Subscribe Section -->
            <section class="my-5 pt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 m-auto">
                            <h4>Bądź pierwszym, który dowie się o naszych nowościach</h4>
                            <p class="mb-4">Dołącz do nas, aby być na bieżąco z naszymi działaniami i wydarzeniami.</p>
                            <div class="row">
                                <div class="col-8">
                                    <div class="input-group input-group-outline">
                                        <input type="text" class="form-control mb-sm-0" placeholder="Adres email">
                                    </div>
                                </div>
                                <div class="col-4 ps-0">
                                    <a href="../pages/tables.php" class="btn btn-lg bg-gradient-info text-white">Subskrybuj</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 ms-auto">
                            <div class="position-relative">
                                <img class="max-width-50 w-100 position-relative z-index-2" src="../assets/img/macbook.png" alt="image">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Initiatives Section -->
            <section class="py-3">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <h3 class="mb-5">Sprawdź nasze najnowsze inicjatywy</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card card-plain">
                                <div class="card-header p-0 position-relative">
                                    <a class="d-block blur-shadow-image">
                                        <img src="../assets/img/examples/testimonial-6-2.jpg" alt="img-blur-shadow" class="img-fluid shadow border-radius-lg" loading="lazy">
                                    </a>
                                </div>
                                <div class="card-body px-0">
                                    <h5><a href="javascript:;" class="text-dark font-weight-bold">Fundusze na projekty edukacyjne</a></h5>
                                    <p>Dzięki naszym projektom wsparliśmy tysiące dzieci i młodzieży w rozwoju ich umiejętności.</p>
                                    <a href="javascript:;" class="text-info text-sm icon-move-right">Czytaj więcej <i class="fas fa-arrow-right text-xs ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card card-plain">
                                <div class="card-header p-0 position-relative">
                                    <a class="d-block blur-shadow-image">
                                        <img src="../assets/img/examples/testimonial-6-3.jpg" alt="img-blur-shadow" class="img-fluid shadow border-radius-lg" loading="lazy">
                                    </a>
                                </div>
                                <div class="card-body px-0">
                                    <h5><a href="javascript:;" class="text-dark font-weight-bold">Inicjatywy zdrowotne</a></h5>
                                    <p>Organizujemy bezpłatne badania i kampanie zdrowotne dla mieszkańców.</p>
                                    <a href="javascript:;" class="text-info text-sm icon-move-right">Czytaj więcej <i class="fas fa-arrow-right text-xs ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card card-plain">
                                <div class="card-header p-0 position-relative">
                                    <a class="d-block blur-shadow-image">
                                        <img src="../assets/img/examples/blog-9-4.jpg" alt="img-blur-shadow" class="img-fluid shadow border-radius-lg" loading="lazy">
                                    </a>
                                </div>
                                <div class="card-body px-0">
                                    <h5><a href="javascript:;" class="text-dark font-weight-bold">Wsparcie dla seniorów</a></h5>
                                    <p>Zapewniamy pomoc w codziennym życiu i organizujemy zajęcia integracyjne.</p>
                                    <a href="javascript:;" class="text-info text-sm icon-move-right">Czytaj więcej <i class="fas fa-arrow-right text-xs ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-12 col-12">
                            <div class="card card-blog card-background cursor-pointer">
                                <div class="full-background" style="background-image: url('../assets/img/examples/blog2.jpg')" loading="lazy"></div>
                                <div class="card-body">
                                    <div class="content-left text-start my-auto py-4">
                                        <h2 class="card-title text-white">Elastyczne godziny pracy</h2>
                                        <p class="card-description text-white">Wolontariaty i programy wsparcia dostosowane do możliwości uczestników.</p>
                                        <a href="javascript:;" class="text-white text-sm icon-move-right">Czytaj więcej <i class="fas fa-arrow-right text-xs ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Map Section -->
            <section class="py-5 bg-gradient-light">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h3 class="mb-4">Nasza lokalizacja</h3>
                            <p class="mb-5">Odwiedź nas w naszym biurze w Krakowie!</p>
                            <div class="card shadow-lg">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2561.3074855716687!2d19.935656615650645!3d50.06465017942388!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47165b10f91b33e9%3A0x2b8e8d9c6d9e5e0f!2sRynek%20G%C5%82%C3%B3wny%2C%2031-008%20Krak%C3%B3w!5e0!3m2!1spl!2spl!4v1698765432100!5m2!1spl!2spl" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 mx-auto text-center">
                            <h3 class="mb-4">Skontaktuj się z nami</h3>
                            <p class="mb-5">Masz pytania? Chcesz wesprzeć naszą misję? Napisz lub zadzwoń!</p>
                            <div class="card shadow-lg p-4">
                                <div class="row text-start">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-map-marker-alt text-info me-2"></i> Adres</h5>
                                        <p>Fundacja Makówka<br>Rynek Główny 1/3<br>31-008 Kraków</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-phone text-info me-2"></i> Telefon</h5>
                                        <p>+48 123 456 789</p>
                                        <h5><i class="fas fa-envelope text-info me-2"></i> Email</h5>
                                        <p>kontakt@fundacjamakowka.pl</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Core JS Files -->
    <script src="../assets/js/core/popper.min.js" type="text/javascript"></script>
    <script src="../assets/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/countup.min.js"></script>
    <script>
        // CountUp Animation
        var element = document.getElementById('count-stats');
        var elementHeight = element.clientHeight;
        document.addEventListener('scroll', animate);

        function inView() {
            var windowHeight = window.innerHeight;
            var scrollY = window.scrollY || window.pageYOffset;
            var scrollPosition = scrollY + windowHeight;
            var elementPosition = element.getBoundingClientRect().top + scrollY + elementHeight;
            return scrollPosition > elementPosition;
        }

        var animateComplete = true;
        function animate() {
            if (inView() && animateComplete) {
                ['state1', 'state2', 'state3'].forEach(id => {
                    if (document.getElementById(id)) {
                        const countUp = new CountUp(id, document.getElementById(id).getAttribute("countTo"));
                        if (!countUp.error) countUp.start();
                        else console.error(countUp.error);
                    }
                });
                animateComplete = false;
            }
        }
    </script>
</body>

</html>