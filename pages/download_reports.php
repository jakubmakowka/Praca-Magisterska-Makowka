<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['loggedin']) || empty($_POST['report_ids'])) {
    header('Location: reports.php');
    exit;
}

$conn = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ($conn->connect_error) {
    die('Błąd połączenia: ' . $conn->connect_error);
}

$report_ids = implode(',', array_map('intval', $_POST['report_ids']));
$result = $conn->query("SELECT file_path FROM reports WHERE report_id IN ($report_ids)");

$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row['file_path'];
}

if (count($files) === 1) {
    $file = $files[0];
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    readfile($file);
} else {
    $zip = new ZipArchive();
    $zip_name = 'reports_' . date('YmdHis') . '.zip';
    if ($zip->open($zip_name, ZipArchive::CREATE) === TRUE) {
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_name . '"');
        readfile($zip_name);
        unlink($zip_name);
    }
}

$conn->close();
exit;