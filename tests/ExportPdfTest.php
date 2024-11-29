<?php
use PHPUnit\Framework\TestCase;

class ExportPdfTest extends TestCase
{
    protected function setUp(): void
    {
        // Start a session to mock $_SESSION
        session_start();
        $_SESSION['user_id'] = 1; // Mock user_id
    }

    public function testExportPdfGeneratesFile()
    {
        

        // Run the export_pdf.php script (you may need to include or require the script in the test)
        ob_start(); // Start output buffering
        require_once '../public/export_tasks_pdf.php'; // Path to the export_pdf.php script
        $output = ob_get_clean(); // Capture the output (PDF or any other output)

        // Check if the file was generated
        $pdfFilePath = __DIR__ . '/../tasks.pdf'; // Adjust the path to where the PDF is saved

        // Assert that the file exists after the script runs
        $this->assertFileExists($pdfFilePath, 'The PDF file was not generated.');

        // Optionally, check for specific content inside the generated PDF.
        // For this, you would need a library to read and check the contents of the PDF.
    }

    protected function tearDown(): void
    {
        // Clean up (e.g., remove the generated PDF file)
        $pdfFilePath = __DIR__ . '/../tasks.pdf'; // Adjust the path to where the PDF is saved
        if (file_exists($pdfFilePath)) {
            unlink($pdfFilePath);
        }
    }
}









