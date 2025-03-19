<?php
require('./tfpdf/tfpdf.php');
require 'database.php';  // Połączenie do bazy

$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
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

// Pobranie danych do tabeli regularnych darczyńców
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

// Pobranie danych do tabeli najhojniejszych darczyńców
$sql_top_donors = "SELECT 
        accounts.username AS username, 
        SUM(amount) AS total_amount 
    FROM transactions 
    JOIN accounts ON transactions.account_id = accounts.id
    GROUP BY account_id 
    ORDER BY total_amount DESC 
    LIMIT 5";
$result_top_donors = $conn->query($sql_top_donors);

$top_donors = [];
if ($result_top_donors->num_rows > 0) {
    while ($row = $result_top_donors->fetch_assoc()) {
        $top_donors[] = $row;
    }
}

// Pobranie danych do tabeli wpłat z ostatnich 30 dni
$sql_recent_transactions = "SELECT 
        accounts.username AS username, 
        amount, 
        timestamp 
    FROM transactions 
    JOIN accounts ON transactions.account_id = accounts.id
    WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY timestamp DESC";
$result_recent_transactions = $conn->query($sql_recent_transactions);

$recent_transactions = [];
if ($result_recent_transactions->num_rows > 0) {
    while ($row = $result_recent_transactions->fetch_assoc()) {
        $recent_transactions[] = $row;
    }
}

// Zamknięcie połączenia
$conn->close();

// Utwórz nowy dokument PDF
$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true); // Dodaj czcionkę z obsługą UTF-8
$pdf->SetFont('DejaVu', '', 10);

// Nagłówek raportu
$pdf->Cell(0, 10, 'Raport Fundacji Makówka', 0, 1, 'C');
$pdf->Ln(10);

// Statystyki
$pdf->Cell(0, 10, 'Statystyki:', 0, 1);
$pdf->Cell(0, 10, 'Łączny przychód: ' . number_format($total_revenue, 2) . ' zł', 0, 1);
$pdf->Cell(0, 10, 'Liczba transakcji: ' . $total_transactions, 0, 1);
$pdf->Cell(0, 10, 'Średnia transakcja: ' . number_format($avg_transaction, 2) . ' zł', 0, 1);
$pdf->Cell(0, 10, 'Wpływy z ostatnich 30 dni: ' . number_format($monthly_revenue, 2) . ' zł', 0, 1);
$pdf->Ln(10);

// Tabela z regularnymi darczyńcami
$pdf->Cell(0, 10, 'Regularni darczyńcy:', 0, 1);
$pdf->SetFillColor(200, 220, 255); // Kolor tła nagłówka
$pdf->Cell(60, 10, 'Użytkownik', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Liczba wpłat', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Wpłaty łącznie', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Ostatnia data płatności', 1, 1, 'C', true);

foreach ($top_users_by_transactions as $user) {
    $pdf->Cell(60, 10, $user['username'], 1);
    $pdf->Cell(40, 10, $user['total_transactions'], 1, 0, 'C');
    $pdf->Cell(50, 10, number_format($user['total_amount'], 2) . ' zł', 1, 0, 'R');
    $pdf->Cell(40, 10, date('Y-m-d H:i', strtotime($user['last_payment_date'])), 1, 1, 'C');
}
$pdf->Ln(10);

// Tabela z najhojniejszymi darczyńcami
$pdf->Cell(0, 10, 'Najhojniejsi darczyńcy:', 0, 1);
$pdf->SetFillColor(200, 220, 255); // Kolor tła nagłówka
$pdf->Cell(80, 10, 'Użytkownik', 1, 0, 'C', true);
$pdf->Cell(80, 10, 'Łączna kwota wpłat', 1, 1, 'C', true);

foreach ($top_donors as $donor) {
    $pdf->Cell(80, 10, $donor['username'], 1);
    $pdf->Cell(80, 10, number_format($donor['total_amount'], 2) . ' zł', 1, 1, 'R');
}
$pdf->Ln(10);

// Tabela z wpłatami z ostatnich 30 dni
$pdf->Cell(0, 10, 'Wpłaty z ostatnich 30 dni:', 0, 1);
$pdf->SetFillColor(200, 220, 255); // Kolor tła nagłówka
$pdf->Cell(60, 10, 'Użytkownik', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Kwota', 1, 0, 'C', true);
$pdf->Cell(80, 10, 'Data wpłaty', 1, 1, 'C', true);

foreach ($recent_transactions as $transaction) {
    $pdf->Cell(60, 10, $transaction['username'], 1);
    $pdf->Cell(50, 10, number_format($transaction['amount'], 2) . ' zł', 1, 0, 'R');
    $pdf->Cell(80, 10, date('Y-m-d H:i', strtotime($transaction['timestamp'])), 1, 1, 'C');
}

// Wygeneruj nazwę pliku
$filename = "raport_fundacja_makowka_" . date('Y-m-d') . ".pdf";

// Wyślij PDF do przeglądarki
$pdf->Output('D', $filename); // 'D' oznacza pobranie pliku