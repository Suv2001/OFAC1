<?php
session_start();
require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['eid'])) {
    header('location: ../index.php');
    exit();
}
// Check if query result exists in session
if (!isset($_SESSION['query_result'])) {
    die('No data available for export.');
}

$query_result = $_SESSION['query_result'];
$eid = $_SESSION['eid']; // Ensure $eid is defined

require_once('../templates/db_connection.php');

$sql = "SELECT fname, lname FROM employees WHERE eid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $eid);
$stmt->execute();
$stmt->bind_result($fname, $lname);
$stmt->fetch();
$stmt->close();
$conn->close();

// Export Logic (CSV, PDF, Excel)
if ($_GET['type'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="business_report.csv"');
    $output = fopen('php://output', 'w');
    
    // Output the column headers
    fputcsv($output, ['Business Name', 'Owner', 'Address', 'Registration Number', 'Status']);
    
    // Output the data rows
    foreach ($query_result as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit(); // Ensure no further output
} elseif ($_GET['type'] === 'pdf') {
    // Generate PDF
    require('../fpdf/fpdf.php');
    require('../fpdf/fpdf_alpha.php'); // Ensure this file exists in your fpdf directory

    // Custom PDF class
    class CustomPDF extends FPDF_Alpha
    {   
        public $exportDate;
        public $printedBy;

        // Footer method to add export date, printed by, and page number
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $footerText = "Export Date: {$this->exportDate} | Printed by: {$this->printedBy}";
            $this->Cell(0, 10, $footerText, 0, 1, 'C');
            $this->SetXY(50, -15);
            $this->Cell(0, 10, "Page " . $this->PageNo() . " of {nb}", 0, 0, 'R');
        }

        // Header method
        function Header()
        {
            $this->SetAlpha(0);
            $this->Image('../assets/images/image___Copy_half_trans.png', 55, 85, 100); // Adjust position and size as needed
            $this->SetAlpha(1);
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, "Businesses Status Report", 0, 1, 'C');
            $this->Ln(10);
        }
    }

    // Create PDF object
    $pdf = new CustomPDF();
    $pdf->AliasNbPages();
    $pdf->exportDate = date('d-m-Y h:i:s A');
    $pdf->printedBy = $fname . ' ' . $lname;  // Use the fetched fname and lname

    // Add a page
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    
    $pageWidth = $pdf->GetPageWidth() - 20;
    $columns = 6;  // Number of columns
    $colWidth = $pageWidth / $columns;
    $lineHeight = 5;
    $maxRowHeight = $lineHeight * 3;
    $rowsPerPage = 15;
    $currentRow = 0;

    // Define the headers for the export files
    $headers = ['Sl No.', 'Business Name', 'Owner', 'Address', 'Reg. No.', 'Status'];

    // Render Table Header
    function renderTableHeader($pdf, $headers, $colWidth, $lineHeight)
    {
        $pdf->SetFont('Arial', 'B', 10);
        foreach ($headers as $header) {
            $pdf->Cell($colWidth, $lineHeight, $header, 1, 0, 'C');
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
    }

    // Render the table row with wrapped text
    function renderTableRow($pdf, $colWidth, $lineHeight, $rowData, $maxRowHeight)
    {
        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

        foreach ($rowData as $index => $text) {
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY();

            $pdf->Rect($xPos, $yPos, $colWidth, $maxRowHeight);
            $pdf->MultiCell($colWidth, $lineHeight, $text, 0, 'C');
            $pdf->SetXY($xPos + $colWidth, $yPos);
        }

        $pdf->SetXY($xStart, $yStart + $maxRowHeight);
    }

    renderTableHeader($pdf, $headers, $colWidth, $lineHeight);

    $counter = 1;
    foreach ($query_result as $row) {
        if ($currentRow >= $rowsPerPage) {
            $pdf->AddPage();
            renderTableHeader($pdf, $headers, $colWidth, $lineHeight);
            $currentRow = 0;
        }

        $cellData = [
            $counter,
            $row['business_name'],
            $row['owner'],
            $row['address'],
            $row['reg_no'],
            $row['status']
        ];

        renderTableRow($pdf, $colWidth, $lineHeight, $cellData, $maxRowHeight);

        $counter++;
        $currentRow++;
    }

    // Output the PDF
    $pdf->Output('D', 'Businesses Status Report.pdf');
    exit(); // Ensure no further output
}


if ($_GET['type'] === 'excel') {
    // Define headers for the Excel sheet
    $headers = ['Sl No.', 'Business Name', 'Owner', 'Address', 'Reg. No.', 'Status'];
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Insert headers with bold formatting
    $headerStyle = [
        'font' => [
            'bold' => false,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];

    // Set headers with bold font and center alignment
    $sheet->fromArray($headers, NULL, 'A1');
    $sheet->getStyle('A1:F1')->applyFromArray($headerStyle); // Apply header style

    // Insert data from the session query result
    $rowIndex = 2;
    $counter = 1;
    foreach ($query_result as $row) {
        $sheet->fromArray([
            $counter, // Sl No.
            $row['business_name'], // Business Name
            $row['owner'], // Owner
            $row['address'], // Address
            $row['reg_no'], // Registration No.
            $row['status'], // Status
        ], NULL, "A{$rowIndex}");
        $rowIndex++;
        $counter++;
    }

    // Output Excel file
    $writer = new Xlsx($spreadsheet);
    $filename = 'Businesses Status Report.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    $writer->save('php://output');
    exit;
}

?>
