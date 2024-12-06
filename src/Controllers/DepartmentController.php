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

    
    public function addDepartment()
    {
        // Method to handle adding new departments
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $departmentName = $_POST['departmentName'];

            $departmentModel = new Department();
            $departmentModel->create([
                'DepartmentName' => $departmentName
            ]);

            header('Location: /department'); // Redirect back to the departments page after adding
        }

        // Render the add department form
        $this->render('addDepartment');
    }
}
