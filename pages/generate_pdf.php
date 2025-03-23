<?php
require('./tfpdf/tfpdf.php');
require 'database.php'; // Połączenie do bazy

// Inicjalizacja połączenia z bazą danych
$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

// Inicjalizacja zmiennych
$total_revenue = 0;
$total_transactions = 0;
$avg_transaction = 0;
$monthly_revenue = 0;
$report_date = date('Y-m-d');

// Przygotowanie zapytań
try {
    $stmt = $conn->prepare("SELECT SUM(amount) AS total_revenue FROM transactions");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_revenue = $result->fetch_assoc()['total_revenue'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total_transactions FROM transactions");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_transactions = $result->fetch_assoc()['total_transactions'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT AVG(amount) AS avg_transaction FROM transactions");
    $stmt->execute();
    $result = $stmt->get_result();
    $avg_transaction = $result->fetch_assoc()['avg_transaction'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT SUM(amount) AS monthly_revenue FROM transactions WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $result = $stmt->get_result();
    $monthly_revenue = $result->fetch_assoc()['monthly_revenue'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT 
            accounts.username AS username, 
            COUNT(*) AS total_transactions, 
            SUM(amount) AS total_amount, 
            MAX(timestamp) AS last_payment_date 
        FROM transactions 
        JOIN accounts ON transactions.account_id = accounts.id
        GROUP BY account_id 
        ORDER BY total_transactions DESC 
        LIMIT 4
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $top_users_by_transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT 
            accounts.username AS username, 
            SUM(amount) AS total_amount 
        FROM transactions 
        JOIN accounts ON transactions.account_id = accounts.id
        GROUP BY account_id 
        ORDER BY total_amount DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $top_donors = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT 
            accounts.username AS username, 
            amount, 
            timestamp 
        FROM transactions 
        JOIN accounts ON transactions.account_id = accounts.id
        WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ORDER BY timestamp DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $recent_transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    die("Błąd podczas pobierania danych: " . $e->getMessage());
}

$conn->close();

// Inicjalizacja PDF
$pdf = new tFPDF();
$pdf->AddPage('P', 'A4');
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);    // Standardowa czcionka
$pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true); // Pogrubiona czcionka
$pdf->SetFont('DejaVu', '', 10);
$pdf->SetMargins(15, 15, 15);

// Nagłówek raportu
$pdf->SetFont('DejaVu', 'B', 14);
$pdf->Cell(0, 10, 'Raport Fundacji Makówka', 0, 1, 'C');
$pdf->SetFont('DejaVu', '', 10);
$pdf->Cell(0, 5, 'Data raportu: ' . date('d.m.Y', strtotime($report_date)), 0, 1, 'C');
$pdf->Ln(10);

// Statystyki
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(0, 10, 'Statystyki', 0, 1);
$pdf->SetFont('DejaVu', '', 10);
$pdf->Cell(0, 8, 'Łączny przychód: ' . number_format($total_revenue, 2, ',', ' ') . ' zł', 0, 1);
$pdf->Cell(0, 8, 'Liczba transakcji: ' . number_format($total_transactions, 0, ',', ' '), 0, 1);
$pdf->Cell(0, 8, 'Średnia transakcja: ' . number_format($avg_transaction, 2, ',', ' ') . ' zł', 0, 1);
$pdf->Cell(0, 8, 'Wpływy z ostatnich 30 dni: ' . number_format($monthly_revenue, 2, ',', ' ') . ' zł', 0, 1);
$pdf->Ln(10);

// Tabela regularnych darczyńców
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(0, 10, 'Regularni darczyńcy', 0, 1);
$pdf->SetFont('DejaVu', 'B', 10);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(50, 8, 'Użytkownik', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Liczba wpłat', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Wpłaty łącznie', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Ostatnia wpłata', 1, 1, 'C', true);

$pdf->SetFont('DejaVu', '', 9);
foreach ($top_users_by_transactions as $user) {
    $pdf->Cell(50, 8, $user['username'], 1);
    $pdf->Cell(30, 8, $user['total_transactions'], 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($user['total_amount'], 2, ',', ' ') . ' zł', 1, 0, 'R');
    $pdf->Cell(40, 8, date('d.m.Y H:i', strtotime($user['last_payment_date'])), 1, 1, 'C');
}
$pdf->Ln(10);

// Tabela najhojniejszych darczyńców
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(0, 10, 'Najhojniejsi darczyńcy', 0, 1);
$pdf->SetFont('DejaVu', 'B', 10);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(80, 8, 'Użytkownik', 1, 0, 'C', true);
$pdf->Cell(80, 8, 'Łączna kwota wpłat', 1, 1, 'C', true);

$pdf->SetFont('DejaVu', '', 9);
foreach ($top_donors as $donor) {
    $pdf->Cell(80, 8, $donor['username'], 1);
    $pdf->Cell(80, 8, number_format($donor['total_amount'], 2, ',', ' ') . ' zł', 1, 1, 'R');
}
$pdf->Ln(10);

// Tabela wpłat z ostatnich 30 dni
$pdf->SetFont('DejaVu', 'B', 12);
$pdf->Cell(0, 10, 'Wpłaty z ostatnich 30 dni', 0, 1);
$pdf->SetFont('DejaVu', 'B', 10);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(60, 8, 'Użytkownik', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Kwota', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Data wpłaty', 1, 1, 'C', true);

$pdf->SetFont('DejaVu', '', 9);
foreach ($recent_transactions as $transaction) {
    $pdf->Cell(60, 8, $transaction['username'], 1);
    $pdf->Cell(40, 8, number_format($transaction['amount'], 2, ',', ' ') . ' zł', 1, 0, 'R');
    $pdf->Cell(60, 8, date('d.m.Y H:i', strtotime($transaction['timestamp'])), 1, 1, 'C');
}

// Stopka
$pdf->SetY(-20);
$pdf->SetFont('DejaVu', '', 8);
$pdf->Cell(0, 10, 'Wygenerowano: ' . date('d.m.Y H:i'), 0, 0, 'C');

// Nazwa pliku
$filename = "raport_fundacja_makowka_" . date('Y-m-d') . ".pdf";

// Wyślij PDF do przeglądarki
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$pdf->Output('D', $filename);
exit;