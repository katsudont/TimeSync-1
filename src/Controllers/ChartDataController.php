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

                
                foreach ($employeesByDepartment as &$department) {
                    
                    $department['EmployeeCount'] = count(
                        $employeeModel->getEmployeesByDepartment($department['ID'])
                    );
        }

            
            $roleCounts = $userModel->getRoleCounts();

            
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
