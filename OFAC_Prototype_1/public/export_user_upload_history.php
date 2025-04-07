<?php
include("../templates/session_management.php");
require __DIR__ . '/../../vendor/autoload.php'; // Correct path to autoload.php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['eid']) || ($_SESSION['user_type'] !== 'admin')) {
    header('Location: ../index.php');
    exit();
}

include("../templates/db_connection.php");

// Fetch user's name using eid from session
$eid = $_SESSION['eid'];
$query = "SELECT fname, lname FROM employees WHERE eid = '$eid'";
$userResult = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($userResult);

$userName = $userData ? $userData['fname'] . ' ' . $userData['lname'] : 'Unknown User';

// Get last query from session
$query = $_SESSION['last_query'];
$result = mysqli_query($conn, $query);



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
                $this->SetAlpha(0); // Set transparency level (0 = fully transparent, 1 = fully opaque)
                $this->Image('../assets/images/image___Copy_half_trans.png', 55, 85, 100); // Adjust position and size as needed
                $this->SetAlpha(1); // Reset transparency

                // Set Y position for header
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, "Upload History", 0, 1, 'C'); // Centered heading
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
        $columns = 7; // Number of columns
        $colWidth = $pageWidth / $columns; // Equal column width
        $lineHeight = 5; // Line height for text
        $maxRowHeight = $lineHeight * 3; // Maximum height for wrapped text
        $rowsPerPage = 15; // Data rows per page
        $currentRow = 0; // Track rows on the current page

        // Define the headers for the export files
        $headers = ['Sl No.', 'Business Name', 'Owner', 'Address', 'Reg. No.','Status', 'Uploaded At'];

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
                $counter,  // Sl No.
                $row['business_name'],
                $row['owner'],
                $row['address'],
                $row['reg_no'],
                $row['status'],
                $row['uploaded_at']
            ];
        
            // Render table row
            renderTableRow($pdf, $colWidth, $lineHeight, $cellData, $maxRowHeight);
        
            $counter++; // Increment serial number
            $currentRow++; // Increment row counter
        }
        
        // Output the PDF
        $pdf->Output('D', 'Upload History.pdf');
        exit();
        break;


        case 'excel':

            // Define headers for the Excel sheet
            $headers = ['Sl No.', 'Business Name', 'Owner', 'Address', 'Reg. No.', 'Status', 'Uploaded At'];
            $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Insert headers
        $sheet->fromArray($headers, NULL, 'A1');

        // Insert data
        $rowIndex = 2;
        while ($row = mysqli_fetch_assoc($result)) {
            $sheet->fromArray([
                $rowIndex - 1, // Sl No.

                $row['business_name'],
                $row['owner'],
                $row['address'],
                $row['reg_no'],
                $row['status'],
                $row['uploaded_at']
            ], NULL, "A{$rowIndex}");
            $rowIndex++;
        }
        // Output Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Upload History.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
           
        

            case 'csv':

                $filename = 'Upload History.csv';
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
        
                $output = fopen('php://output', 'w');

                // Define headers for the CSV file
                $headers = ['Sl No.', 'Business Name', 'Owner', 'Address', 'Reg. No.', 'Status', 'Uploaded At'];
        
                // Insert headers
                fputcsv($output, $headers);
        
                // Insert data
                $counter = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    fputcsv($output, [
                        $counter,
                        $row['business_name'],
                        $row['owner'],
                        $row['address'],
                        $row['reg_no'],
                        $row['status'],
                        $row['uploaded_at']
                    ]);
                    $counter++;
                }
        
                fclose($output);
                exit;
        }

// Close the database connection
mysqli_close($conn);
?>
