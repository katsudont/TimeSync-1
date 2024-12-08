<?php

namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;

class DashboardController extends BaseController
{
    public function index()
    {

        session_start();
        
        
        $attendanceModel = new Attendance();
        $employeeModel = new Employee();
        $departmentModel = new Department();
        $userModel = new User();

        
        $recentAttendance = $attendanceModel->getLatestAttendance();
        $presentCount = $attendanceModel->countPresent();
        $lateCount = $attendanceModel->countLate();
        $employeeCount = $employeeModel->countAll();
        $adminCount = $userModel->countAdmins();
        $departmentCount = $departmentModel->countAll();


        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']){
            header('Location: /admin-dashboard');
            exit;
        }

        

        $data = [
            'username' => $_SESSION['username'] ?? 'Admin', 
            'recentAttendance' => $recentAttendance,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'employeeCount' => $employeeCount,
            'adminCount' => $adminCount,
            'departmentCount' => $departmentCount
        ];

        return $this->render('admin-dashboard', $data);
    }
}
