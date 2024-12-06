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
        // Start session (only once)
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            header('Location: /login');
            exit;
        }

        // Initialize models
        $employeeModel = new Employee();
        $departmentModel = new Department(); // To fetch department data

        // Fetch employee and department data
        $employeeData = $employeeModel->getEmployeeData();
        $departmentData = $departmentModel->getAll(); // Fetch all departments for dropdown

        // Prepare data for the view
        $data = [
            'employees' => $employeeData,
            'departments' => $departmentData // Pass departments to the view
        ];

        return $this->render('employee', $data); // Render employee view with department data
    }

    // Render the employee creation form
    public function createEmployeeForm()
    {
        // Initialize department model to fetch department list
        $departmentModel = new Department();
        $departments = $departmentModel->getAll();

        // Render the add-employee.mustache view and pass the departments data
        return $this->render('add-employee', ['departments' => $departments]);
    }

    // Process the creation of a new employee
    public function createEmployee()
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
    
        // Redirect to employee list page after successful creation
        header('Location: /employee');
        exit;
    }

   // Render the employee edit form with employee data
   public function editEmployeeForm($employeeId)
   {
       $employeeModel = new Employee();
       $departmentModel = new Department();
   
       // Get employee details from the database using the correct method
       $employee = $employeeModel->getEmployeeById($employeeId);
   
       // Get all departments for dropdown
       $departments = $departmentModel->getAll();
   
       // Check if employee data was found, if not redirect with an error
       if (!$employee) {
           // Optionally, flash an error message or log the failed attempt
           header('Location: /employee');
           exit;
       }
   
       // Pass employee and departments data to the view
       return $this->render('edit-employee', [
           'employee' => $employee,
           'departments' => $departments
       ]);
   }
   

   // Update employee data
   public function updateEmployee($employeeId)
{
    $employeeModel = new Employee();
    $departmentModel = new Department();

    // Retrieve POST data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $departmentId = $_POST['department'];

    // Create an array with updated employee data
    $data = [
        'Name' => $name,
        'Email' => $email,
        'Birthdate' => $birthdate,
        'DepartmentID' => $departmentId
    ];

    // Call the model's updateEmployee method to update the database
    $employeeModel->updateEmployee($employeeId, $data);

    // Optionally, update user data here (e.g., username, password) if necessary.

    // Redirect to the employee list page after successful update
    header('Location: /employee');
    exit;
}


    // Delete employee
    public function deleteEmployee($employeeId)
    {
        $employeeModel = new Employee();
        $userModel = new User();

        // Delete user associated with the employee (if any)
        $userModel->deleteByEmployeeId($employeeId);

        // Delete the employee record
        $employeeModel->delete($employeeId);

        // Redirect back to employee list
        header('Location: /employee');
        exit;
    }


}
