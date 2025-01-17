<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$templatePath = __DIR__ . '/MEPAYROLL_Updated.xlsx';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $charges = strtoupper($_POST['Charges']);
    $amount = $_POST['Amount'];

    try {
        if (!file_exists($templatePath)) {
            throw new Exception("Template file not found.");
        }

        // Load the Excel file
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Find the next available row to insert data
        $nextRow = 9;
        while ($sheet->getCell("E$nextRow")->getValue() !== null) {
            $nextRow++;
        }

        // Set charges and amount in the spreadsheet
        $sheet->setCellValue("E$nextRow", $charges);
        $sheet->setCellValue("H$nextRow", $amount);

        // Save changes back to the same file
        $writer = new Xlsx($spreadsheet);
        $writer->save($templatePath);

        // Refresh the sheet to ensure calculations are updated
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Get the updated budget, balance, and total values
        $budget = $sheet->getCell('M7')->getValue();
        $balance = $sheet->getCell('R11')->getCalculatedValue();
        $total = $sheet->getCell('H41')->getCalculatedValue();

        echo json_encode(['monthlyBudget' => $budget, 'balance' => $balance, 'total' => $total]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
