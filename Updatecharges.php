<?php

// Include the Composer autoloader
require 'vendor/autoload.php';

// Now you can use PhpSpreadsheet classes
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$templatePath = __DIR__ . '/MEPAYROLL_Updated.xlsx';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $charge = $_POST['charge'] ?? null; // Get the charge from POST
    $amount = $_POST['amount'] ?? null;

    // Validate data
    if (!$charge || !$amount) {
        echo json_encode(['status' => 'error', 'message' => 'Missing or invalid data.']);
        exit;
    }

    try {
        // Check if template file exists
        if (!file_exists($templatePath)) {
            throw new Exception("Template file not found.");
        }

        // Load the spreadsheet
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Find the row for the given charge
        $highestRow = $sheet->getHighestRow();
        $rowToUpdate = null;

        for ($row = 9; $row <= $highestRow; $row++) { // Assuming data starts from row 9
            $chargeCell = "E$row"; // Column E contains the Charges
            $chargeValue = $sheet->getCell($chargeCell)->getValue();

            // Compare charge values (case-insensitive)
            if (strcasecmp(trim($chargeValue), trim($charge)) === 0) {
                $rowToUpdate = $row;
                break;
            }
        }

        // Check if the charge was found
        if (!$rowToUpdate) {
            echo json_encode(['status' => 'error', 'message' => 'Charge not found in the sheet.']);
            exit;
        }

        // Update the amount in the corresponding row
        $sheet->setCellValue("H{$rowToUpdate}", $amount); // Update amount in column H

        // Save the updated spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($templatePath);

        echo json_encode(['status' => 'success', 'message' => 'Amount updated successfully for the given charge.']);
    } catch (Exception $e) {
        // Handle any exceptions during the process
        error_log('Error updating spreadsheet: ' . $e->getMessage()); // Log error for debugging
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
