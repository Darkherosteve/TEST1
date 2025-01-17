<?php

// Include the Composer autoloader
require 'vendor/autoload.php';

// Now you can use PhpSpreadsheet classes
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$templatePath = __DIR__ . '/MEPAYROLL_Updated.xlsx';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $srNo = isset($_POST['srNo']) ? (int)$_POST['srNo'] : null; // Explicitly cast to integer
    $charge = $_POST['charge'] ?? null;
    $amount = $_POST['amount'] ?? null;

    // Validate data
    if (is_null($srNo) || !$charge || !$amount) {
        echo json_encode(['status' => 'error', 'message' => 'Missing or invalid data.']);
        exit;
    }

    if ($srNo <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Sr. No. provided.']);
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

        // Find the row for the given Sr. No.
        $highestRow = $sheet->getHighestRow();
        $rowToUpdate = null;

        for ($row = 9; $row <= $highestRow; $row++) { // Assuming data starts from row 9
            $srCell = "D$row"; // Column D contains the Sr. No.
            $srValue = $sheet->getCell($srCell)->getValue();

            // Compare as integer to avoid type mismatch issues
            if ((int)$srValue === $srNo) {
                $rowToUpdate = $row;
                break;
            }
        }

        // Check if the Sr. No. was found
        if (!$rowToUpdate) {
            echo json_encode(['status' => 'error', 'message' => 'Sr. No. not found in the sheet.']);
            exit;
        }

        // Update the charge and amount in the corresponding row
        $sheet->setCellValue("E{$rowToUpdate}", $charge); // Update charge in column E
        $sheet->setCellValue("H{$rowToUpdate}", $amount); // Update amount in column H

        // Save the updated spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($templatePath);

        echo json_encode(['status' => 'success', 'message' => 'Charge updated successfully.']);
    } catch (Exception $e) {
        // Handle any exceptions during the process
        error_log('Error updating spreadsheet: ' . $e->getMessage()); // Log error for debugging
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
