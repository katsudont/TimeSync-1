<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\Shift;

class DepartmentController extends BaseController
{
    public function index()
    {
        $departmentModel = new Department();
        $shiftModel = new Shift();
        
        // Get all departments
        $departments = $departmentModel->getAll();

        // Get all shifts for each department
        foreach ($departments as &$department) {
            // Fetch shifts assigned to the department
            $shifts = $shiftModel->getShiftsByDepartment($department['ID']);
            $department['Shifts'] = $shifts;
        }

        // Render the department list view
        $this->render('department', ['departments' => $departments]);
    }

        public function assignShift($departmentId)
    {
        $departmentModel = new Department();
        $shiftModel = new Shift();
        
        // Fetch the department by ID
        $department = $departmentModel->getById($departmentId);
        
        if (!$department) {
            echo "Department not found.";
            return;
        }

        // Fetch all available shifts (not just those assigned to the department)
        $allShifts = $shiftModel->getAllShifts(); // This will get all shifts, regardless of department

        // Check if it's a POST request to assign the shift
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shiftId = $_POST['shiftId'];  // Get the selected shift ID

            // Assign the new shift to the department (this will overwrite the existing one)
            $success = $shiftModel->assignShiftToDepartment($shiftId, $departmentId);

            if ($success) {
                // Redirect to the department list after assigning
                header('Location: /department');
                exit();
            } else {
                // Handle any unexpected errors (though there shouldn't be any)
                echo "An error occurred while assigning the shift.";
            }
        }

        // Render the assign shift form with department and available shifts
        $this->render('assignShift', [
            'department' => $department,
            'shifts' => $allShifts
        ]);
    }

    public function createDepartmentForm()
    {
        // Initialize department and shift models to fetch department list and available shifts
        $departmentModel = new Department();
        $shiftModel = new Shift();

        // Fetch all available shifts
        $shifts = $shiftModel->getAllShifts();

        // Render the add-department.mustache view and pass the shifts data
        return $this->render('add-department', ['shifts' => $shifts]);
    }


    public function addDepartment()
    {
    // Method to handle adding new departments and assigning a shift
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $departmentName = $_POST['departmentName'];
        $shiftId = $_POST['shiftId']; // Get the selected shift ID from the form

        // Create the department
        $departmentModel = new Department();
        $departmentId = $departmentModel->create([
            'DepartmentName' => $departmentName
        ]);

        if ($departmentId) {
            // If department creation was successful, assign the shift (if provided)
            if (!empty($shiftId)) {
                $shiftModel = new Shift();
                $success = $shiftModel->assignShiftToDepartment($shiftId, $departmentId);
                
                if (!$success) {
                    echo "Error occurred while assigning the shift.";
                    return;
                }
            }
            
            // Redirect to the departments list after successful creation and shift assignment
            header('Location: /department');
            exit();
        } else {
            echo "An error occurred while creating the department.";
        }
    }

    // Render the add department form with the available shifts
    $shiftModel = new Shift();
    $allShifts = $shiftModel->getAllShifts(); // Fetch all available shifts
    $this->render('addDepartment', [
        'shifts' => $allShifts // Pass available shifts to the view
    ]);
    }

    public function editDepartmentForm($departmentId)
{
    $departmentModel = new Department();
    $shiftModel = new Shift();

    // Get department details from the database
    $department = $departmentModel->getByIdWithShift($departmentId);

    // Get all available shifts
    $shifts = $shiftModel->getAllShifts();

    // Check if the department exists, if not redirect with an error
    if (!$department) {
        header('Location: /department');  // Redirect to department list
        exit;
    }

        // Mark the department's current shift as selected
    $shiftId = $department['ShiftID'] ?? null; // Use null coalescing to avoid undefined index error

    foreach ($shifts as &$shift) {
        $shift['selected'] = ($shift['ID'] == $shiftId) ? 'selected' : '';
    }


    // Pass the department and shifts data to the view
    return $this->render('edit-department', [
        'department' => $department,
        'shifts' => $shifts
    ]);    
}


public function updateDepartment($departmentId)
{
    $departmentModel = new Department();
    $shiftModel = new Shift();

    // Retrieve POST data
    $departmentName = $_POST['departmentName'];
    $shiftId = $_POST['shiftId']; // Single selected shift ID

    // Ensure the shiftId is not empty and is valid
    if (empty($shiftId)) {
        // Handle the case where no shift is selected
        echo "Please select a shift.";
        exit;
    }

    // Update department name and associated shift in the Department table
    $departmentData = [
        'DepartmentName' => $departmentName,
        'ShiftID' => $shiftId // Store the associated shift ID
    ];

    $departmentModel->updateDepartment($departmentId, $departmentData);

    // Redirect to the department list page after successful update
    header('Location: /department');
    exit;
}


}
