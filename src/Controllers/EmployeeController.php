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

    // Add a 'selected' flag to each department based on the employee's current department
    foreach ($departments as &$department) {
        $department['selected'] = ($department['ID'] == $employee['DepartmentID']) ? 'selected' : '';
    }

    // Pass employee and departments data to the view
    return $this->render('edit-employee', [
        'employee' => $employee,
        'departments' => $departments
    ]);
}

   

public function updateEmployee($employeeId)
{
    $employeeModel = new Employee();
    $departmentModel = new Department();
    $userModel = new User(); // Assuming you have a User model for updating username

    // Retrieve POST data
    $name = $_POST['Name'];         // Note: Case-sensitive in your form
    $email = $_POST['Email'];
    $birthdate = $_POST['Birthdate'];
    $hireDate = $_POST['HireDate'];
    $username = $_POST['Username'];
    $departmentId = $_POST['department'];

    // Create an array with updated employee data (excluding HireDate and Username for now)
    $employeeData = [
        'Name' => $name,
        'Email' => $email,
        'Birthdate' => $birthdate,
        'HireDate' => $hireDate,   // Include HireDate
        'DepartmentID' => $departmentId
    ];

    // Update employee data in the employee table
    $employeeModel->updateEmployee($employeeId, $employeeData);

    // If the username is being updated, update it in the User table as well
    if (!empty($username)) {
        $userData = [
            'Username' => $username,
            'EmployeeID' => $employeeId
        ];
        $userModel->updateUser($employeeId, $userData);  // Assuming this method exists in User model
    }

    // Redirect to the employee list page after successful update
    header('Location: /employee');
    exit;
}

public function deleteEmployee($ID)
{
    $employeeModel = new Employee();  
    $userModel = new User();  

    // Get the employee data using the Employee model
    $employee = $employeeModel->getById($ID);

    if (!$employee) {
        // Handle case when employee is not found
        header('Location: /employee');
        exit;
    }

    // Get the user associated with this employee
    $user = $userModel->getByEmployeeId($ID);

    // If a user is associated with the employee, delete it
    if ($user) {
        // Delete the associated user
        $userDeleted = $userModel->delete($user['ID']);
        if (!$userDeleted) {
            // Handle deletion error if user deletion fails
            $_SESSION['error_message'] = "Error deleting associated user. Employee deletion aborted.";
            header('Location: /employee');
            exit;
        }
    }

    // Now delete the employee
    $employeeDeleted = $employeeModel->delete($ID);

    if ($employeeDeleted) {
        // Optionally, you can add a success message or a redirect to the employee list page
        $_SESSION['success_message'] = "Employee and their associated user were successfully deleted.";
        header('Location: /employee');
        exit;
    } else {
        // Handle failure to delete employee
        $_SESSION['error_message'] = "Failed to delete the employee.";
        header('Location: /employee');
        exit;
    }
}



}
