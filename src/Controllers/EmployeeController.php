<?php

namespace App\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Exception;

class EmployeeController extends BaseController
{
    public function index()
    {
        
        session_start();

        
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /login');
            exit;
        }

        
        $employeeModel = new Employee();
        $departmentModel = new Department(); 
        $userModel = new User();

        
        $employeeData = $employeeModel->getEmployeeData();
        $departmentData = $departmentModel->getAll(); 

        
        $data = [
            'username' => $_SESSION['username'] ?? 'Admin', 
            'employees' => $employeeData,
            'departments' => $departmentData 
            
        ];

        return $this->render('employee', $data); 
    }

    
    public function createEmployeeForm()
    {
        
        $departmentModel = new Department();
        $departments = $departmentModel->getAll();

        
        return $this->render('add-employee', ['departments' => $departments]);
    }

    
    public function createEmployee()
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
    
        
        $department = $departmentModel->getById($departmentId);
        $roleId = ($department['DepartmentName'] === 'Admin') ? 1 : 2;
    
        
        $employeeId = $employeeModel->create([
            'Name' => $name,
            'Email' => $email,
            'Birthdate' => $birthdate,
            'HireDate' => date('Y-m-d'),
            'DepartmentID' => $departmentId,
        ]);
    
        
        $userModel->create([
            'Username' => $username,
            'Password' => $password,
            'EmployeeID' => $employeeId,
            'RoleID' => $roleId,
        ]);
    
        
        header('Location: /employee');
        exit;
    }

   
   public function editEmployeeForm($employeeId)
{
    $employeeModel = new Employee();
    $departmentModel = new Department();

    
    $employee = $employeeModel->getEmployeeById($employeeId);

    
    $departments = $departmentModel->getAll();

    
    if (!$employee) {
        
        header('Location: /employee');
        exit;
    }

    
    foreach ($departments as &$department) {
        $department['selected'] = ($department['ID'] == $employee['DepartmentID']) ? 'selected' : '';
    }

    
    return $this->render('edit-employee', [
        'username' => $_SESSION['username'] ?? 'Admin', 
        'employee' => $employee,
        'departments' => $departments
    ]);
}

   

public function updateEmployee($employeeId)
{
    $employeeModel = new Employee();
    $departmentModel = new Department();
    $userModel = new User(); 

    
    $name = $_POST['Name'];   
    $email = $_POST['Email'];
    $birthdate = $_POST['Birthdate'];
    $hireDate = $_POST['HireDate'];
    $username = $_POST['Username'];
    $departmentId = $_POST['department'];

    
    $employeeData = [
        'Name' => $name,
        'Email' => $email,
        'Birthdate' => $birthdate,
        'HireDate' => $hireDate,   
        'DepartmentID' => $departmentId
    ];

    
    $employeeModel->updateEmployee($employeeId, $employeeData);

    
    if (!empty($username)) {
        $userData = [
            'Username' => $username,
            'EmployeeID' => $employeeId
        ];
        $userModel->updateUser($employeeId, $userData); 
    }

    
    header('Location: /employee');
    exit;
}

public function deleteEmployee($ID)
{
    $employeeModel = new Employee();  
    $userModel = new User();  

    
    $employee = $employeeModel->getById($ID);

    if (!$employee) {
        
        $_SESSION['error_message'] = "Employee not found.";
        header('Location: /employee');
        exit;
    }

    
    $user = $userModel->getByEmployeeId($ID);

    
    if ($user) {
        
        $userDeleted = $userModel->delete($user['ID']);
        if (!$userDeleted) {
            
            $_SESSION['error_message'] = "Error deleting associated user. Employee deletion aborted.";
            header('Location: /employee');
            exit;
        }
    }

    
    $employeeDeleted = $employeeModel->delete($ID);

    if ($employeeDeleted) {
        
        $_SESSION['success_message'] = "Employee and their associated user were successfully deleted.";
        header('Location: /employee');
        exit;
    } else {
        
        $_SESSION['error_message'] = "Failed to delete the employee.";
        header('Location: /employee');
        exit;
    }
}




}
