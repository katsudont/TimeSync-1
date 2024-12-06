<?php

require "vendor/autoload.php";
require "init.php";

// Database connection object (from init.php (DatabaseConnection))
global $conn;

try {

    // Create Router instance
    $router = new \Bramus\Router\Router();

    // Define routes

    $router->get('/', '\App\Controllers\LandingController@index');

    $router->get('/register', '\App\Controllers\RegisterController@index');
    $router->post('/register', '\App\Controllers\RegisterController@store');

    $router->get('/login', '\App\Controllers\LoginController@login'); // Temporarily for testing
    $router->post('/login', '\App\Controllers\LoginController@login');

    // Define the route for the dashboard page
    $router->get('/admin-dashboard', '\App\Controllers\DashboardController@index');

    $router->get('/employee-dashboard', '\App\Controllers\EmployeeDashboardController@index');
    $router->post('/employee-dashboard/clockIn', '\App\Controllers\EmployeeDashboardController@clockInAction');
    $router->post('/employee-dashboard/clockOut', '\App\Controllers\EmployeeDashboardController@clockOutAction');

    $router->get('/profile', '\App\Controllers\ProfileController@index');
    $router->post('/profile/update', '\App\Controllers\ProfileController@update');

    // Route to display employee list or dashboard
    $router->get('/employee', '\App\Controllers\EmployeeController@index');

    // Route to process adding a new employee
    $router->post('/employee', '\App\Controllers\EmployeeDashboardController@addEmployee');

    // Display the Admin Dashboard (List of Admin employees)
    $router->get('/admin', '\App\Controllers\AdminController@index');

    // Display the form to add a new Admin
    $router->get('/admin/add', '\App\Controllers\AdminController@addAdmin'); // This is the route for `addAdmin.mustache`

    // Store the new Admin data
    $router->post('/admin/store', '\App\Controllers\AdminController@storeAdmin');

    // Route to display departments
    $router->get('/department', '\App\Controllers\DepartmentController@index');

    // Route to show add department form
    $router->get('/add-department', '\App\Controllers\DepartmentController@addDepartment');

    // Route to handle form submission (POST)
    $router->post('/add-department', '\App\Controllers\DepartmentController@addDepartment');

    $router->get('/assign-shift/{departmentId}', '\App\Controllers\DepartmentController@assignShift');
    $router->post('/assign-shift/{departmentId}', '\App\Controllers\DepartmentController@assignShift');

    // Route to show shift list
    $router->get('/shift', '\App\Controllers\ShiftController@index');

    // Route to show the add shift form
    $router->get('/add-shift', '\App\Controllers\ShiftController@add');

    // Route to handle form submission for adding a new shift (POST)
    $router->post('/add-shift', '\App\Controllers\ShiftController@add');

    // Route to show the edit shift form (for a specific shift ID)
    $router->get('/edit-shift/{shiftId}', '\App\Controllers\ShiftController@edit');

    // Route to handle form submission for editing an existing shift (POST)
    $router->post('/edit-shift/{shiftId}', '\App\Controllers\ShiftController@edit');

    // Route to show attendance list
    $router->get('/attendance', '\App\Controllers\AttendanceController@index');

    // Route to export attendance to PDF
    $router->get('/attendance/export', '\App\Controllers\AttendanceController@exportToPDF');




    
    












    


    // Run it!
    $router->run();

} catch (Exception $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);

}
