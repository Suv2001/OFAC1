<?php
include("../templates/session_management.php");
require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include("../templates/user_auth.php");
include("../templates/db_connection.php");

// Set error reporting to only show fatal errors (to prevent breaking Excel output)
error_reporting(E_ERROR);
ini_set('display_errors', 0);

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
$headers = ['Sl No.', 'Business Name', 'Owner', 'Address', 'Reg. No.', 'Status', 'Uploaded By', 'Uploaded At'];

// Handle export based on the selected type
$type = isset($_GET['type']) ? $_GET['type'] : 'pdf';

// Export logic based on the type
switch ($type) {
    case 'pdf':
        // Generate PDF
        require('../fpdf/fpdf.php');
        require('../fpdf/fpdf_alpha.php'); // Ensure this file exists in your fpdf directory
        
        // Custom PDF class with watermark
        class CustomPDF extends FPDF_Alpha
        {
            public $exportDate;
            public $printedBy;

            // Header method
            function Header()
            {
                // Set watermark image (Place behind everything)
                $this->SetAlpha(0.1); // Set transparency level (0 = fully transparent, 1 = fully opaque)
                $this->Image('../assets/images/image___Copy_half_trans.png', 55, 85, 100); // Adjust position and size as needed
                $this->SetAlpha(1); // Reset transparency

                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, "Upload History (ADMIN)", 0, 1, 'C');
                $this->Ln(10);
            }

            // Footer method
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $footerText = "Export Date: {$this->exportDate} | Printed by: {$this->printedBy}";
                $this->Cell(0, 10, $footerText, 0, 1, 'C');
                $this->SetXY(50, -15);
                $this->Cell(0, 10, "Page " . $this->PageNo() . " of {nb}", 0, 0, 'R');
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
        $columns = 8; // Number of columns
        $colWidth = $pageWidth / $columns; // Equal column width
        $lineHeight = 5; // Line height for text
        $maxRowHeight = $lineHeight * 3; // Maximum height for wrapped text
        $rowsPerPage = 15; // Data rows per page
        $currentRow = 0; // Track rows on the current page
        
        // Render Table Header with Wrapped and Centered Text
        function renderTableHeader($pdf, $headers, $colWidth, $lineHeight, $maxRowHeight)
        {
            $xStart = $pdf->GetX();
            $yStart = $pdf->GetY();
            $pdf->SetFont('Arial', 'B', 10); // Set font to bold

            foreach ($headers as $header) {
                $xPos = $pdf->GetX();
                $yPos = $pdf->GetY();

                // Draw cell border
                $pdf->Rect($xPos, $yPos, $colWidth, $maxRowHeight);

                // Calculate text width & height for centering
                $pdf->SetXY($xPos, $yPos + ($maxRowHeight - $lineHeight * substr_count($header, "\n") - $lineHeight) / 2);
                $pdf->MultiCell($colWidth, $lineHeight, $header, 0, 'C');

                // Move cursor to the next cell
                $pdf->SetXY($xPos + $colWidth, $yPos);
            }

            // Move cursor to the start of the next row
            $pdf->SetXY($xStart, $yStart + $maxRowHeight);
            $pdf->SetFont('Arial', '', 10); // Reset font to normal
        }

        // Render the table row with wrapped text
        function renderTableRow($pdf, $colWidth, $lineHeight, $rowData, $maxRowHeight)
        {
            $xStart = $pdf->GetX();
            $yStart = $pdf->GetY();
        
            // Render cells with wrapping
            foreach ($rowData as $text) {
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
        renderTableHeader($pdf, $headers, $colWidth, $lineHeight, $maxRowHeight);

        // Fetch table data
        $counter = 1; // Serial number
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if a new page is needed
            if ($currentRow >= $rowsPerPage) {
                $pdf->AddPage();
                renderTableHeader($pdf, $headers, $colWidth, $lineHeight, $maxRowHeight); // Fixed parameter count
                $currentRow = 0; // Reset row counter for the new page
            }
        
            // Table row data
            $cellData = [
                $counter,
                $row['business_name'] ?? 'N/A',
                $row['owner'] ?? 'N/A',
                $row['address'] ?? 'N/A',
                $row['reg_no'] ?? 'N/A',
                $row['status'] ?? 'N/A',
                $row['uploaded_by'] ?? 'N/A',
                $row['uploaded_at'] ?? 'N/A'
            ];
        
            // Render table row
            renderTableRow($pdf, $colWidth, $lineHeight, $cellData, $maxRowHeight);
        
            $counter++; // Increment serial number
            $currentRow++; // Increment row counter
        }
        
        // Output the PDF
        $pdf->Output('D', 'Upload_History_ADMIN.pdf');
        break;

    case 'excel':
        // Reset the result pointer for Excel export
        mysqli_data_seek($result, 0);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Upload History');

        // Add title
        $sheet->setCellValue('A1', 'Upload History (ADMIN)');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Add export info
        $sheet->setCellValue('A2', 'Exported By: ' . $userName);
        $sheet->setCellValue('F2', 'Export Date: ' . date('Y-m-d H:i:s'));
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('F2:H2');
        $sheet->getStyle('A2:H2')->getFont()->setItalic(true);

        // Insert headers at row 4
        $sheet->fromArray($headers, NULL, 'A4');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        
        // Style the header row
        $sheet->getStyle('A4:H4')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setRGB('DDDDDD');

        // Insert data
        $rowIndex = 5;
        $counter = 1;
        
        while ($row = mysqli_fetch_assoc($result)) {
            $sheet->fromArray([
                $counter,
                $row['business_name'] ?? '',
                $row['owner'] ?? '',
                $row['address'] ?? '',
                $row['reg_no'] ?? '',
                $row['status'] ?? '',
                $row['uploaded_by'] ?? '',
                $row['uploaded_at'] ?? ''
            ], NULL, "A{$rowIndex}");
            
            $rowIndex++;
            $counter++;
        }
        
        // Auto-size columns
        foreach(range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Output Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Upload_History_ADMIN.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Important: Clear any output buffering to prevent corruption
        ob_end_clean();
        
        $writer->save('php://output');
        exit;
        break;

    case 'csv':
        // Reset the result pointer for CSV export
        mysqli_data_seek($result, 0);
        
        $filename = 'Upload_History_ADMIN.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        // Important: Clear any output buffering to prevent corruption
        ob_end_clean();
        
        $output = fopen('php://output', 'w');
        
        // Insert headers
        fputcsv($output, $headers);
        
        // Insert data
        $counter = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $counter,
                $row['business_name'] ?? '',
                $row['owner'] ?? '',
                $row['address'] ?? '',
                $row['reg_no'] ?? '',
                $row['status'] ?? '',
                $row['uploaded_by'] ?? '',
                $row['uploaded_at'] ?? ''
            ]);
            $counter++;
        }
        
        fclose($output);
        exit;
        break;
        
    default:
        echo "Invalid export type specified.";
}

// Close the database connection
mysqli_close($conn);
?>