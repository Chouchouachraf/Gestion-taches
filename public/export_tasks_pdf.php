<?php
require('../fpdf/fpdf.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';

// Récupérer l'ID utilisateur connecté
$user_id = $_SESSION['user_id'];

// Retrieve tasks from the database
$query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY FIELD(priority, 'high', 'medium', 'low'), due_date ASC";
$db = Database::getConnection();
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

// Initialize FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Your Tasks', 0, 1, 'C');

// Add table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Title', 1);
$pdf->Cell(30, 10, 'Status', 1);
$pdf->Cell(40, 10, 'Due Date', 1);
$pdf->Cell(40, 10, 'Priority', 1);
$pdf->Ln();

// Add task rows
$pdf->SetFont('Arial', '', 12);
foreach ($tasks as $task) {
    $pdf->Cell(60, 10, $task['title'], 1);
    $pdf->Cell(30, 10, ucfirst($task['status']), 1);
    $pdf->Cell(40, 10, $task['due_date'], 1);
    $pdf->Cell(40, 10, ucfirst($task['priority']), 1);
    $pdf->Ln();
}

// Output the PDF
$pdf->Output('D', 'tasks.pdf');


