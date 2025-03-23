<?php
require './tfpdf/tfpdf.php'; // Ścieżka do tFPDF
require 'database.php';      // Połączenie z bazą danych

// Funkcja do bezpiecznego zamknięcia połączenia i wyjścia
function terminate($conn, $message = null) {
    if ($message) {
        die($message);
    }
    $conn->close();
    exit;
}

try {
    // Połączenie z bazą danych
    $conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
    if ($conn->connect_error) {
        throw new Exception("Błąd połączenia z bazą danych: " . $conn->connect_error);
    }

    // Inicjalizacja PDF
    $pdf = new tFPDF();
    $pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->AddPage();

    // Nagłówek dokumentu
    $pdf->SetFillColor(200, 220, 255);
    $pdf->Cell(190, 10, 'Lista Kampanii', 1, 1, 'C', true);

    // Nagłówki tabeli
    $pdf->SetFont('DejaVu', '', 10);
    $pdf->SetFillColor(230, 230, 230);
    $columns = [
        ['Kampania', 60],
        ['Zebrana kwota', 30],
        ['Cel', 30],
        ['Status', 40],
        ['Zakończenie', 30]
    ];
    
    foreach ($columns as $column) {
        $pdf->Cell($column[1], 10, $column[0], 1, 0, 'C', true);
    }
    $pdf->Ln();

    // Pobieranie i wyświetlanie danych
    $query = "SELECT name, current_amount, goal_amount, end_date FROM campaigns";
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Błąd zapytania SQL: " . $conn->error);
    }

    $pdf->SetFont('DejaVu', '', 9);
    while ($row = $result->fetch_assoc()) {
        $status = $row['current_amount'] >= $row['goal_amount'] ? 'Zakończono' : 'W trakcie';
        $pdf->Cell(60, 8, $row['name'], 1);
        $pdf->Cell(30, 8, number_format($row['current_amount'], 2, ',', ' ') . ' zł', 1, 0, 'R');
        $pdf->Cell(30, 8, number_format($row['goal_amount'], 2, ',', ' ') . ' zł', 1, 0, 'R');
        $pdf->Cell(40, 8, $status, 1, 0, 'C');
        $pdf->Cell(30, 8, date('d.m.Y', strtotime($row['end_date'])), 1, 0, 'C');
        $pdf->Ln();
    }

    // Zamknięcie połączenia i generowanie pliku
    $conn->close();
    $pdf->Output('D', 'kampanie_' . date('Y-m-d') . '.pdf');

} catch (Exception $e) {
    terminate($conn, $e->getMessage());
}