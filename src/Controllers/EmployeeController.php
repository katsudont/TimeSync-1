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

    // Modify the addEmployee method to handle employee creation
    public function addEmployee()
{
    session_start();

    if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['birthdate']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['department'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Debug: Log the data received
    error_log("Received Data: " . json_encode($_POST));

    $name = $_POST['name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $departmentId = $_POST['department'];

    $employeeModel = new Employee();
    $userModel = new User();

    try {
        // Save employee data to Employee table
        $employeeId = $employeeModel->create([
            'Name' => $name,
            'Email' => $email,
            'Birthdate' => $birthdate,
            'HireDate' => date('Y-m-d'),
            'DepartmentID' => $departmentId
        ]);

        // Debug: Confirm Employee creation
        if (!$employeeId) {
            error_log("Failed to create Employee");
            echo json_encode(['success' => false, 'message' => 'Failed to create Employee']);
            exit;
        }

        // Save user data to User table
        $userModel->create([
            'Username' => $username,
            'Password' => $password,
            'EmployeeID' => $employeeId,
            'RoleID' => 2 // Assuming non-admin role
        ]);

        // Debug: Confirm User creation
        error_log("Employee and User successfully created with ID: $employeeId");

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

    
}
