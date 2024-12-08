<?php
namespace App\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;

class ChartDataController extends BaseController
{
    public function getChartData()
    {
        header('Content-Type: application/json');

        try {
                $employeeModel = new Employee();
                $departmentModel = new Department();
                $userModel = new User();

                // Fetch all departments
                $employeesByDepartment = $departmentModel->getAll();

                // Get employee count for each department using the Employee model
                foreach ($employeesByDepartment as &$department) {
                    // Get employees in the department and count them
                    $department['EmployeeCount'] = count(
                        $employeeModel->getEmployeesByDepartment($department['ID'])
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
