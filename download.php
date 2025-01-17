<?php
require_once __DIR__ . '/vendor/autoload.php'; // Load Composer autoload

use Ilovepdf\Ilovepdf;

try {
    // Initialize iLovePDF API with your public key
    $ilovepdf = new Ilovepdf('project_public_ecb67f93a0e0e512dab61997547e0ff7_ZgkfT24cff8d522d4e968b3be7101be8ccd86', 'secret_key_7002d96b9faa19230174f9c82d17effe_A1u7xa5cead2dee42457735e170dc325f7f8f');
    
    // Start a new Excel-to-PDF task
    $task = $ilovepdf->newTask('officepdf');

    // Add the Excel file
    $filePath = 'C:\xampp\htdocs\dashboard\Stevetest\LR.xlsx'; // Replace with your Excel file path
    $file = $task->addFile($filePath);

    // Execute the task
    $task->execute();

    // Download the resulting PDF
    $outputDir = 'C:\xampp\htdocs\dashboard\Stevetest'; // Replace with your desired output directory
    $task->download($outputDir);

    echo "Excel file converted to PDF successfully! Check the output directory.";
} catch (Exception $e) {
    echo 'An error occurred: ' . $e->getMessage();
}
