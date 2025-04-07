<?php
include("../templates/session_management.php");
require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Set error reporting to only show fatal errors (to prevent breaking Excel output)
error_reporting(E_ERROR);
ini_set('display_errors', 0);

include("../templates/user_auth.php");
include("../templates/db_connection.php");

// Fetch user's name using eid from session
$eid = $_SESSION['eid'];
$query = "SELECT fname, lname FROM employees WHERE eid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $eid);
$stmt->execute();
$userResult = $stmt->get_result();
$userData = $userResult->fetch_assoc();
$userName = $userData ? $userData['fname'] . ' ' . $userData['lname'] : 'Unknown User';

// Check if last query exists
if (!isset($_SESSION['last_query'])) {
    die("No query available for export.");
}

// Get last query from session
$query = $_SESSION['last_query'];
$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Check if there are any records
if (mysqli_num_rows($result) == 0) {
    die("No records found for export.");
}

// Define headers for the export files
$headers = ['Sl No.', 'First Name', 'Last Name', 'Employee ID', 'Login Time', 'Logout Time'];

// Handle export based on the selected type
$type = isset($_GET['type']) ? $_GET['type'] : 'pdf';

// Export logic based on the type
switch ($type) {
    case 'pdf':
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
                // Set Y position for footer
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
        
                // Footer text
                $footerText = "Export Date: {$this->exportDate} | Printed by: {$this->printedBy}";
                $this->Cell(0, 10, $footerText, 0, 1, 'C'); // Center the text
        
                // Page numbering
                $this->SetXY(50,-15);
                $this->Cell(0, 10, "Page " . $this->PageNo() . " of {nb}", 0, 0, 'R');
            }
        
            // Header method
            function Header()
            {
                // Set watermark image (Place behind everything)
                $this->SetAlpha(0.1); // Set transparency level (0.1 = slightly visible, 1 = fully opaque)
                $this->Image('../assets/images/image___Copy_half_trans.png', 55, 85, 100); // Adjust position and size as needed
                $this->SetAlpha(1); // Reset transparency

                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, "Employee Activity", 0, 1, 'C'); // Centered heading
                $this->Ln(10); // Space after header
            }
        }

        // Create PDF object
        $pdf = new CustomPDF();
        $pdf->AliasNbPages(); // Enable total pages
        $pdf->exportDate = date('d-m-Y h:i:s A');
        $pdf->printedBy = $userName;

        // Add a page
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        
        // Set A4 page size and margins
        $pageWidth = $pdf->GetPageWidth() - 20; // Total width minus margins
        $columns = 6; // Number of columns
        $colWidth = $pageWidth / $columns; // Equal column width
        $lineHeight = 5; // Line height for text
        $maxRowHeight = $lineHeight * 3; // Maximum height for wrapped text
        $rowsPerPage = 15; // Data rows per page
        $currentRow = 0; // Track rows on the current page

        // Render Table Header
        function renderTableHeader($pdf, $headers, $colWidth, $lineHeight)
        {   
            $pdf->SetFont('Arial', 'B', 10); // Set font to bold
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
        
            // Render cells with wrapping
            foreach ($rowData as $index => $text) {
                $xPos = $pdf->GetX();
                $yPos = $pdf->GetY();
        
                // Draw cell border
                $pdf->Rect($xPos, $yPos, $colWidth, $maxRowHeight);
        
                // Render wrapped text
                $pdf->MultiCell($colWidth, $lineHeight, $text, 0, 'C');
        
                // Move cursor to the next cell
                $pdf->SetXY($xPos + $colWidth, $yPos);
            }
        
            // Move cursor to the start of the next row
            $pdf->SetXY($xStart, $yStart + $maxRowHeight);
        }

        // Start table
        renderTableHeader($pdf, $headers, $colWidth, $lineHeight);
        
        // Fetch table data
        $counter = 1; // Serial number
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if a new page is needed
            if ($currentRow >= $rowsPerPage) {
                $pdf->AddPage();
                renderTableHeader($pdf, $headers, $colWidth, $lineHeight);
                $currentRow = 0; // Reset row counter for the new page
            }
        
            // Table row data
            $cellData = [
                $counter,
                $row['fname'] ?? 'N/A',
                $row['lname'] ?? 'N/A',
                $row['eid'] ?? 'N/A',
                $row['login_time'] ?? 'N/A',
                $row['logout_time'] ?? 'N/A'
            ];
        
            // Render table row
            renderTableRow($pdf, $colWidth, $lineHeight, $cellData, $maxRowHeight);
        
            $counter++; // Increment serial number
            $currentRow++; // Increment row counter
        }
        
        // Output the PDF
        $pdf->Output('D', 'Employee_Activity.pdf');
        break;

    case 'excel':
        // Reset the result pointer for Excel export
        mysqli_data_seek($result, 0);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employee Activity');

        // Add title
        $sheet->setCellValue('A1', 'Employee Activity');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add export info
        $sheet->setCellValue('A2', 'Exported By: ' . $userName);
        $sheet->setCellValue('D2', 'Export Date: ' . date('Y-m-d H:i:s'));
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('D2:F2');
        $sheet->getStyle('A2:F2')->getFont()->setItalic(true);

        // Insert headers at row 4
        $sheet->fromArray($headers, NULL, 'A4');
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        
        // Style the header row
        $sheet->getStyle('A4:F4')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setRGB('DDDDDD');
        
        // Insert data starting from row 5
        $rowIndex = 5;
        $counter = 1;
        
        while ($row = mysqli_fetch_assoc($result)) {
            $sheet->fromArray([
                $counter,
                $row['fname'] ?? '',
                $row['lname'] ?? '',
                $row['eid'] ?? '',
                $row['login_time'] ?? '',
                $row['logout_time'] ?? ''
            ], NULL, "A{$rowIndex}");
            
            $rowIndex++;
            $counter++;
        }
        
        // Auto-size columns
        foreach(range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Output Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Employee_Activity.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Important: Clear any output buffering to prevent corruption
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        $writer->save('php://output');
        exit;
        break;

    case 'csv':
        // Reset the result pointer for CSV export
        mysqli_data_seek($result, 0);
        
        $filename = 'Employee_Activity.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        // Important: Clear any output buffering to prevent corruption
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        $output = fopen('php://output', 'w');
        
        // Insert headers
        fputcsv($output, $headers);
        
        // Insert data
        $counter = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $counter,
                $row['fname'] ?? 'N/A',
                $row['lname'] ?? 'N/A',
                $row['eid'] ?? 'N/A',
                $row['login_time'] ?? 'N/A',
                $row['logout_time'] ?? 'N/A'
            ]);
            $counter++;
        }
        
        fclose($output);
        exit;
        break;
        
    default:
        // Handle unknown export type
        header("HTTP/1.0 404 Not Found");
        echo "The requested URL was not found on this server.";
        exit();
}

// Close the database connection
mysqli_close($conn);
?>