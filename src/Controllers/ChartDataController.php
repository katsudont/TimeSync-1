<?php
namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\User;

class ChartDataController extends BaseController
{
    public function getChartData()
    {
        header('Content-Type: application/json');

        try {
            $attendanceModel = new Attendance();
            $departmentModel = new Department();
            $userModel = new User();

            // Fetch employees by department
            $employeesByDepartment = $departmentModel->getAll();
            foreach ($employeesByDepartment as &$department) {
                $department['EmployeeCount'] = count(
                    $attendanceModel->getFilteredAttendance(['DepartmentName' => $department['DepartmentName']])
                );
            }

            // Role-based employee count (for pie chart)
            $roleCounts = $userModel->getRoleCounts();

            // Output as JSON
            echo json_encode([
                'employeesByDepartment' => $employeesByDepartment,
                'roleCounts' => [
                    'Admin' => (int)$roleCounts['Admins'],
                    'Employee' => (int)$roleCounts['Employees'],
                ],
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
