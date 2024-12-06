<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;

class RegisterController extends BaseController
{
    public function index()
    {
        $departmentModel = new Department();
        $departments = $departmentModel->getAll();

        $this->render('register', ['departments' => $departments]);
    }

    public function store()
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
    
        // Get the department name for role assignment
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
    
        header('Location: /login'); // Redirect after successful registration
    }
    

}
