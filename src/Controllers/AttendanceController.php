<?php

namespace App\Controllers;

use App\Models\Attendance;
use FPDF; 

class AttendanceController extends BaseController
{
    public function index()
    {
        session_start();


        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: /login');
        exit;
        }

        $attendanceModel = new Attendance();
        

        $filters = [
            'EmployeeID' => $_GET['EmployeeID'] ?? null,
            'EmployeeName' => $_GET['EmployeeName'] ?? null,
            'DepartmentName' => $_GET['DepartmentName'] ?? null,
            'ShiftID' => $_GET['ShiftID'] ?? null,
            'InStatus' => $_GET['InStatus'] ?? null,
            'OutStatus' => $_GET['OutStatus'] ?? null
        ];


        $attendanceRecords = $attendanceModel->getFilteredAttendance($filters);


        $this->render('attendance', [
            'username' => $_SESSION['username'] ?? 'Admin',
            'attendanceRecords' => $attendanceRecords
        ]);
    }


public function exportToPDF()
{
    $attendanceModel = new Attendance();


    $filters = [
        'EmployeeID' => $_GET['EmployeeID'] ?? null,
        'EmployeeName' => $_GET['EmployeeName'] ?? null,
        'DepartmentName' => $_GET['DepartmentName'] ?? null,
        'ShiftID' => $_GET['ShiftID'] ?? null,
        'InStatus' => $_GET['InStatus'] ?? null,
        'OutStatus' => $_GET['OutStatus'] ?? null,
    ];


    $attendanceRecords = $attendanceModel->getFilteredAttendance($filters);


    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(290, 10, 'Attendance Record', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 10);

    
    $pdf->Cell(25, 10, 'Employee ID', 1, 0, 'C');
    $pdf->Cell(45, 10, 'Employee Name', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Department', 1, 0, 'C');
    $pdf->Cell(45, 10, 'Time In', 1, 0, 'C');
    $pdf->Cell(25, 10, 'In Status', 1, 0, 'C');
    $pdf->Cell(45, 10, 'Out Time', 1, 0, 'C');
    $pdf->Cell(25, 10, 'Out Status', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Shift ID', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 10);

   
    foreach ($attendanceRecords as $record) {
        $pdf->Cell(25, 10, $record['EmployeeID'], 1, 0, 'C');
        $pdf->Cell(45, 10, $record['EmployeeName'], 1, 0, 'C');
        $pdf->Cell(30, 10, $record['DepartmentName'], 1, 0, 'C');
        $pdf->Cell(45, 10, $record['InTime'], 1, 0, 'C');
        $pdf->Cell(25, 10, $record['InStatus'], 1, 0, 'C');
        $pdf->Cell(45, 10, $record['OutTime'], 1, 0, 'C');
        $pdf->Cell(25, 10, $record['OutStatus'], 1, 0, 'C');
        $pdf->Cell(30, 10, $record['ShiftID'], 1, 1, 'C');
    }

    
    $pdf->Output('D', 'attendance_record.pdf');
}

}
