<?php

namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;

class EmployeeDashboardController extends BaseController
{
    public function index()
    {
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /login');
            exit();
        }

        // Initialize models
        $attendanceModel = new Attendance();
        $employeeModel = new Employee();
        $departmentModel = new Department();

        // Fetch the logged-in employee's ID
        $employeeID = $_SESSION['user_id'];

        // Fetch all attendance records of the logged-in employee
        $attendanceRecords = $attendanceModel->getAllAttendanceByEmployee($employeeID);  // Updated method to fetch all records
        
        // Fetch additional statistics (if needed)
        $employeeCount = $employeeModel->countAll();
        $departmentCount = $departmentModel->countAll();

        // Prepare data for the view
        $data = [
            'username' => $_SESSION['username'] ?? 'Employee', // Set default value
            'attendanceRecords' => $attendanceRecords,  // Updated key for all attendance records
            'employeeCount' => $employeeCount,
            'departmentCount' => $departmentCount,
        ];

        // Render the employee dashboard view
        return $this->render('employee-dashboard', $data);
    }

    public function clockInAction()
    {
        session_start();

        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /');
            exit;
        }

        // Logic for clocking in
        $attendanceModel = new Attendance();
        $employeeID = $_SESSION['user_id']; // Assuming the employee's ID is stored in the session

        $attendanceModel->recordTimeIn($employeeID);

        // Redirect to dashboard after clocking in
        header('Location: /employee-dashboard');
        exit;
    }

    public function clockOutAction()
    {
        session_start();

        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /');
            exit;
        }

        // Logic for clocking out
        $attendanceModel = new Attendance();
        $employeeID = $_SESSION['user_id']; // Assuming the employee's ID is stored in the session

        $attendanceModel->recordTimeOut($employeeID);

        // Redirect to dashboard after clocking out
        header('Location: /employee-dashboard');
        exit;
    }
}
