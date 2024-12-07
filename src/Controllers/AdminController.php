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

    public function createAdminForm()
    {
        // Initialize department model to fetch department list
        $departmentModel = new Department();
        $departments = $departmentModel->getAll();

        // Render the add-employee.mustache view and pass the departments data
        return $this->render('add-admin', ['departments' => $departments]);
    }

    // Process the creation of a new employee
    public function createAdmin()
{
    $employeeModel = new Employee();
    $userModel = new User();
    $departmentModel = new Department();
    
    // Retrieve POST data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $departmentName = $_POST['department']; // 'Admin' value from the form
    
    // Check if department exists and get the department ID
    $department = $departmentModel->getByName($departmentName);  // Assuming you have a method to get by name
    if (!$department) {
        // Handle error if the department doesn't exist
        die("Error: Department 'Admin' does not exist in the database.");
    }
    
    $departmentId = $department['ID'];  // Get the department ID, not the name
    
    // Get the role ID based on department name (Admin => RoleID 1)
    $roleId = ($departmentName === 'Admin') ? 1 : 2;
    
    // Save to Employee table
    $employeeId = $employeeModel->create([
        'Name' => $name,
        'Email' => $email,
        'Birthdate' => $birthdate,
        'HireDate' => date('Y-m-d'),
        'DepartmentID' => $departmentId,  // Save correct DepartmentID
    ]);
    
    // Save to User table
    $userModel->create([
        'Username' => $username,
        'Password' => $password,
        'EmployeeID' => $employeeId,
        'RoleID' => $roleId,
    ]);
    
    // Redirect to employee list page after successful creation
    header('Location: /admin');
    exit;
}

}
