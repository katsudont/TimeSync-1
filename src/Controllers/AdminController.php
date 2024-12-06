<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;

class AdminController extends BaseController
{
    public function index()
    {
        $employeeModel = new Employee();
        $departmentModel = new Department();

        // Fetch the "Admin" department ID based on the department name
        $adminDepartment = $departmentModel->getByName('Admin');
        
        if ($adminDepartment) {
            // Fetch employees in the "Admin" department using the department's ID
            $employees = $employeeModel->getEmployeesByDepartment($adminDepartment['ID']);
        } else {
            // If there's no "Admin" department, return an empty list or handle error
            $employees = [];
        }

        // Render the view with the employees from the "Admin" department
        $this->render('admin', ['employees' => $employees]);
    }

    public function addAdmin()
    {
        // Fetch all departments to populate the department dropdown (if needed in the future)
        $departmentModel = new Department();
        $departments = $departmentModel->getAll();

        // Render the addAdmin view, passing the departments to populate dropdown if needed
        $this->render('addAdmin', ['departments' => $departments]);
    }

    public function storeAdmin()
    {
        $employeeModel = new Employee();
        $userModel = new User();
        $departmentModel = new Department();
    
        $name = $_POST['name'];
        $email = $_POST['email'];
        $birthdate = $_POST['birthdate'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $departmentId = $_POST['department'];
    
        // Get the department name for role assignment (ensuring it's Admin)
        $department = $departmentModel->getById($departmentId);
        $roleId = ($department['DepartmentName'] === 'Admin') ? 1 : 2;
    
        // Save to Employee table
        $employeeId = $employeeModel->create([
            'Name' => $name,
            'Email' => $email,
            'Birthdate' => $birthdate,
            'HireDate' => date('Y-m-d'),
            'DepartmentID' => $departmentId,
        ]);
    
        // Save to User table
        $userModel->create([
            'Username' => $username,
            'Password' => $password,
            'EmployeeID' => $employeeId,
            'RoleID' => $roleId,
        ]);
    
        header('Location: /admin'); // Redirect after successful admin creation
    }
}
