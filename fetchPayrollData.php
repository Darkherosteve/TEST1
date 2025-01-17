<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$templatePath = __DIR__ . '/MEPAYROLL_Updated.xlsx';

header('Content-Type: application/json');

try {
    if (!file_exists($templatePath)) {
        throw new Exception("Excel file not found.");
    }

    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    $data = [];
    $highestRow = $sheet->getHighestRow();

    // Collect table data from rows
    for ($row = 9; $row <= $highestRow; $row++) {
        $charges = trim($sheet->getCell("E$row")->getValue());
        $amount = trim($sheet->getCell("H$row")->getValue());

        if (empty($charges) && empty($amount)) {
            continue; // Skip empty rows
        }

        error_log("Row $row: charges=$charges, amount=$amount"); // Debug each row

        $data[] = [
            'charges' => $charges,
            'amount' => is_numeric($amount) ? (float) $amount : null
        ];
    }

    // Fetch the total value from cell H41
    $total = $sheet->getCell("H41")->getCalculatedValue();
    error_log("Total (H41): $total"); // Debug the total value

    if (!is_numeric($total)) {
        throw new Exception("Total value in H41 is not a valid number.");
    }

    // Return data and total in JSON response
    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'total' => (float) $total
    ]);
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage()); // Log the error for debugging
    echo json_encode(['error' => $e->getMessage()]);
}
?>