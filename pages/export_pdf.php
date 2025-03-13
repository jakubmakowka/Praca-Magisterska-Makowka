<?php
require('./tfpdf/tfpdf.php');  // Jeśli nie używasz kompozytora, wskaż ścieżkę do FPDF
require 'database.php';  // Połączenie do bazy

$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$pdf = new tFPDF();
$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
$pdf->SetFont('DejaVu','', 12);
$pdf->AddPage();
$pdf->Cell(190, 10, 'Lista Kampanii', 1, 1, 'C');

$pdf->SetFont('DejaVu','', 10);
$pdf->Cell(60, 10, 'Kampania', 1);
$pdf->Cell(30, 10, 'Zebrana kwota', 1);
$pdf->Cell(30, 10, 'Cel', 1);
$pdf->Cell(40, 10, 'Status', 1);
$pdf->Cell(30, 10, 'Zakonczenie', 1);
$pdf->Ln();

$query = "SELECT name, current_amount, goal_amount, end_date FROM campaigns";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $status = ($row['current_amount'] >= $row['goal_amount']) ? 'Zakończono' : 'W trakcie';
    $pdf->Cell(60, 10, $row['name'], 1);
    $pdf->Cell(30, 10, $row['current_amount'] . ' zł', 1);
    $pdf->Cell(30, 10, $row['goal_amount'] . ' zł', 1);
    $pdf->Cell(40, 10, $status, 1);
    $pdf->Cell(30, 10, $row['end_date'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'kampanie.pdf');
exit();
?>