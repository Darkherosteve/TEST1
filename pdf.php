<?php
require 'vendor/autoload.php'; // Ensure mPDF is installed via Composer

use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define paths for the Excel file and the generated PDF
$excelFilePath = __DIR__ . '/MEPAYROLL_Updated.xlsx';  // Path to the Excel file
$pdfFilePath = __DIR__ . '/Charges_and_Amounts.pdf';   // Path to save the generated PDF

try {
    // Check if the Excel file exists
    if (!file_exists($excelFilePath)) {
        throw new Exception('Excel file not found at ' . $excelFilePath);
    }

    // Load the Excel file
    $spreadsheet = IOFactory::load($excelFilePath);

    // Convert Excel data to HTML
    $htmlWriter = IOFactory::createWriter($spreadsheet, 'Html');
    ob_start();
    $htmlWriter->save('php://output');
    $htmlContent = ob_get_clean();

    // Initialize mPDF
    $mpdf = new Mpdf();

    // Write HTML content to the PDF
    $mpdf->WriteHTML($htmlContent);

    // Save the PDF to the file path
    $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

    // Return the file path in the response
    echo json_encode([
        'status' => 'success',
        'message' => 'PDF created successfully!',
        'filePath' => 'Charges_and_Amounts.pdf' // Relative path from the frontend perspective
    ]);
} catch (Exception $e) {
    // Handle any exceptions during the process
    echo json_encode([
        'status' => 'error',
        'message' => 'Error generating PDF: ' . $e->getMessage()
    ]);
}
?>
