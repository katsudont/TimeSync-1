<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;

class AdminController extends BaseController
{
    public function index()
{
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: /login');
        exit;
    }


    $employeeModel = new Employee();
    $departmentModel = new Department(); 


    $adminDepartment = $departmentModel->getByName('Admin');
    

    if ($adminDepartment) {

        $employees = $employeeModel->getEmployeesByDepartment($adminDepartment['ID']);
    } else {

        $employees = [];
    }


    $departmentData = $departmentModel->getAll(); // Fetch all departments


    $data = [
        'username' => $_SESSION['username'] ?? 'Admin', 
        'employees' => $employees,
        'departments' => $departmentData 
    ];


    return $this->render('admin', $data);
}

    public function createAdminForm()
    {

        $departmentModel = new Department();
        $departments = $departmentModel->getAll();


        return $this->render('add-admin', ['departments' => $departments]);
    }


    public function createAdmin()
{
    $employeeModel = new Employee();
    $userModel = new User();
    $departmentModel = new Department();
    

    $name = $_POST['name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $departmentName = $_POST['department']; 
    

    $department = $departmentModel->getByName($departmentName);  
    if (!$department) {

        die("Error: Department 'Admin' does not exist in the database.");
    }
    
    $departmentId = $department['ID']; 
    

    $roleId = ($departmentName === 'Admin') ? 1 : 2;
    

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
    

    header('Location: /admin');
    exit;
}

public function editAdminForm($employeeId)
{
    $employeeModel = new Employee();
    $departmentModel = new Department();


    $employee = $employeeModel->getAdminById($employeeId);


    $departments = $departmentModel->getAll();


    if (!$employee) {

        header('Location: /admin');
        exit;
    }


    foreach ($departments as &$department) {
        $department['selected'] = ($department['ID'] == $employee['DepartmentID']) ? 'selected' : '';
    }


    return $this->render('edit-admin', [
        'username' => $_SESSION['username'] ?? 'Admin', 
        'employee' => $employee,
        'departments' => $departments
    ]);
}

   

public function updateAdmin($employeeId)
{
    $employeeModel = new Employee();
    $departmentModel = new Department();
    $userModel = new User(); 

    // Retrieve POST data
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


    header('Location: /admin');
    exit;
}

public function deleteAdmin($ID)
{
    $employeeModel = new Employee();  
    $userModel = new User();  


    $employee = $employeeModel->getById($ID);

    if (!$employee) {

        $_SESSION['error_message'] = "Employee not found.";
        header('Location: /admin');
        exit;
    }


    $user = $userModel->getByEmployeeId($ID);


    if ($user) {

        $userDeleted = $userModel->delete($user['ID']);
        if (!$userDeleted) {

            $_SESSION['error_message'] = "Error deleting associated user. Employee deletion aborted.";
            header('Location: /admin');
            exit;
        }
    }


    $employeeDeleted = $employeeModel->delete($ID);

    if ($employeeDeleted) {

        $_SESSION['success_message'] = "Employee and their associated user were successfully deleted.";
        header('Location: /admin');
        exit;
    } else {

        $_SESSION['error_message'] = "Failed to delete the employee.";
        header('Location: /admin');
        exit;
    }
}

}
