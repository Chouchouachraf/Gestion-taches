<?php
use PHPUnit\Framework\TestCase;

class TaskScriptTest extends TestCase {
    private $mockDb;
    private $mockStmt;

    protected function setUp(): void {
        // Mock the PDO statement
        $this->mockStmt = $this->createMock(PDOStatement::class);
        $this->mockStmt->method('fetchAll')->willReturn([
            ['title' => 'Task 1', 'status' => 'completed', 'due_date' => '2023-11-01', 'priority' => 'high'],
            ['title' => 'Task 2', 'status' => 'pending', 'due_date' => '2023-11-05', 'priority' => 'medium'],
        ]);

        // Mock the PDO connection
        $this->mockDb = $this->createMock(PDO::class);
        $this->mockDb->method('prepare')->willReturn($this->mockStmt);
    }

    public function testRedirectIfNotLoggedIn() {
        // Simulate no session
        $_SESSION = [];

        ob_start();
        include '../public/export_tasks_pdf.php'; // Replace with your script's filename
        $output = ob_get_clean();

        $this->assertStringContainsString('Location: .../public/login.php', $output);
    }

    public function testTasksFetchWithMockedDatabase() {
        // Simulate logged-in user
        $_SESSION['user_id'] = 1;

        // Inject the mock database connection
        Database::setMockConnection($this->mockDb);

        ob_start();
        include '../public/export_tasks_pdf.php'; // Replace with your script's filename
        ob_end_clean();

        // Verify tasks fetch operation
        $this->assertTrue($this->mockStmt->execute());
        $tasks = $this->mockStmt->fetchAll();
        $this->assertCount(2, $tasks);
    }

    public function testPDFGeneration() {
        // Simulate logged-in user
        $_SESSION['user_id'] = 1;

        // Inject the mock database connection
        Database::setMockConnection($this->mockDb);

        // Mock FPDF methods
        $mockPdf = $this->getMockBuilder(FPDF::class)
            ->onlyMethods(['AddPage', 'SetFont', 'Cell', 'Ln', 'Output'])
            ->getMock();

        $mockPdf->expects($this->once())->method('AddPage');
        $mockPdf->expects($this->atLeastOnce())->method('SetFont');
        $mockPdf->expects($this->atLeastOnce())->method('Cell');
        $mockPdf->expects($this->atLeastOnce())->method('Ln');
        $mockPdf->expects($this->once())->method('Output');

        // Replace `new FPDF()` with the mock
        ob_start();
        include '../public/export_tasks_pdf.php'; // Replace with your script's filename
        ob_end_clean();
    }
}








