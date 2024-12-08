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

    $router->get('/login', '\App\Controllers\LoginController@login'); 
    $router->post('/login', '\App\Controllers\LoginController@login');

    // Define the route for the dashboard page
    $router->get('/admin-dashboard', '\App\Controllers\DashboardController@index');
    $router->get('/chart-data', '\App\Controllers\ChartDataController@getChartData');


    $router->get('/employee-dashboard', '\App\Controllers\EmployeeDashboardController@index');
    $router->post('/employee-dashboard/clockIn', '\App\Controllers\EmployeeDashboardController@clockInAction');
    $router->post('/employee-dashboard/clockOut', '\App\Controllers\EmployeeDashboardController@clockOutAction');
    

    $router->get('/profile', '\App\Controllers\ProfileController@index');
    $router->post('/profile/update', '\App\Controllers\ProfileController@update');


    // Route to display employee list or dashboard
    $router->get('/employee', '\App\Controllers\EmployeeController@index');
    $router->get('/add-employee', '\App\Controllers\EmployeeController@createEmployeeForm');
    $router->post('/add-employee', '\App\Controllers\EmployeeController@createEmployee');
    $router->get('/edit-employee/{employeeId}', '\App\Controllers\EmployeeController@editEmployeeForm');
    $router->post('/edit-employee/{employeeId}', '\App\Controllers\EmployeeController@updateEmployee');
    $router->post('/delete-employee/{employeeId}', '\App\Controllers\EmployeeController@deleteEmployee');



    // Display the Admin Dashboard (List of Admin employees)
    $router->get('/admin', '\App\Controllers\AdminController@index');
    $router->get('/add-admin', '\App\Controllers\AdminController@createAdminForm');
    $router->post('/add-admin', '\App\Controllers\AdminController@createAdmin');

    $router->get('/edit-admin/{employeeId}', '\App\Controllers\AdminController@editAdminForm');
    $router->post('/edit-admin/{employeeId}', '\App\Controllers\AdminController@updateAdmin');
    $router->post('/delete-admin/{employeeId}', '\App\Controllers\AdminController@deleteAdmin');



    // Route to display departments
    $router->get('/department', '\App\Controllers\DepartmentController@index');
    $router->get('/add-department', '\App\Controllers\DepartmentController@createDepartmentForm');
    $router->post('/add-department', '\App\Controllers\DepartmentController@addDepartment');
    $router->get('/edit-department/{departmentId}', '\App\Controllers\DepartmentController@editDepartmentForm');
    $router->post('/edit-department/{departmentId}', '\App\Controllers\DepartmentController@updateDepartment');
    $router->post('/delete-department/{departmentId}', '\App\Controllers\DepartmentController@deleteDepartment');


    // Route to show shift list
    $router->get('/shift', '\App\Controllers\ShiftController@index');
    $router->get('/add-shift', '\App\Controllers\ShiftController@add');
    $router->post('/add-shift', '\App\Controllers\ShiftController@add');
    $router->get('/edit-shift/{shiftId}', '\App\Controllers\ShiftController@edit');
    $router->post('/edit-shift/{shiftId}', '\App\Controllers\ShiftController@edit');
    $router->post('/delete-shift/{shiftId}', '\App\Controllers\ShiftController@deleteShift');


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
