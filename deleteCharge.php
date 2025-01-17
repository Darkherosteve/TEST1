<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Define the path to the Excel template
$templatePath = __DIR__ . '/MEPAYROLL_Updated.xlsx';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['chargesToDelete'])) {
        echo json_encode(['error' => 'Invalid or missing charges']);
        exit;
    }

    $chargesToDelete = $_POST['chargesToDelete'];
    error_log("Charge to delete: $chargesToDelete");

    try {
        if (!file_exists($templatePath)) {
            throw new Exception("Template file not found: $templatePath");
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        error_log("Highest row: $highestRow");

        for ($row = 9; $row <= $highestRow; $row++) {
            $chargesCell = "E$row";
            $amountCell = "H$row";
            $chargeValue = trim($sheet->getCell($chargesCell)->getCalculatedValue());
            $targetValue = trim($chargesToDelete);
        
            error_log("Row $row - Charge value: '$chargeValue', Target: '$targetValue'");
        
            if (strcasecmp($chargeValue, $targetValue) === 0) { // Case-insensitive comparison
                $sheet->setCellValue($chargesCell, '');
                $sheet->setCellValue($amountCell, '');
                error_log("Cleared row $row");
            }
        }
        

        $writer = new Xlsx($spreadsheet);
        $writer->save($templatePath);

        echo json_encode(['success' => true, 'message' => 'Matching charges and amounts cleared successfully.', 'reload' => true]);
        
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(['error' => 'Error clearing charge data: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method. Only POST is allowed.']);
}
?>