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

        
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /login');
            exit();
        }

        
        $attendanceModel = new Attendance();
        $employeeModel = new Employee();
        $departmentModel = new Department();

        
        $employeeID = $_SESSION['user_id'];

        
        $attendanceRecords = $attendanceModel->getAllAttendanceByEmployee($employeeID);  
        
        
        $employeeCount = $employeeModel->countAll();
        $departmentCount = $departmentModel->countAll();

        
        $data = [
            'username' => $_SESSION['username'] ?? 'Employee', 
            'attendanceRecords' => $attendanceRecords,  
            'employeeCount' => $employeeCount,
            'departmentCount' => $departmentCount,
        ];

        
        return $this->render('employee-dashboard', $data);
    }

    public function clockInAction()
    {
        session_start();

        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /');
            exit;
        }

        
        $attendanceModel = new Attendance();
        $employeeID = $_SESSION['user_id']; 

        $attendanceModel->recordTimeIn($employeeID);

        
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

        
        $attendanceModel = new Attendance();
        $employeeID = $_SESSION['user_id']; 

        $attendanceModel->recordTimeOut($employeeID);

        
        header('Location: /employee-dashboard');
        exit;
    }
}
