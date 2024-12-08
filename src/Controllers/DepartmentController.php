<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\Shift;
use App\Models\User;

class DepartmentController extends BaseController
{
    public function index()
    {
        session_start();

        
    if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: /login');
        exit;
    }

        $departmentModel = new Department();
        $shiftModel = new Shift();
        
        
        $departments = $departmentModel->getAll();

        
        foreach ($departments as &$department) {
            
            $shifts = $shiftModel->getShiftsByDepartment($department['ID']);
            $department['Shifts'] = $shifts;
        }

        
        $this->render('department', [
            'username' => $_SESSION['username'] ?? 'Admin', 
            'departments' => $departments
        ]);
    }

        public function assignShift($departmentId)
    {
        $departmentModel = new Department();
        $shiftModel = new Shift();
        
        
        $department = $departmentModel->getById($departmentId);
        
        if (!$department) {
            echo "Department not found.";
            return;
        }

        
        $allShifts = $shiftModel->getAllShifts(); 

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shiftId = $_POST['shiftId'];  

            
            $success = $shiftModel->assignShiftToDepartment($shiftId, $departmentId);

            if ($success) {
                
                header('Location: /department');
                exit();
            } else {
                
                echo "An error occurred while assigning the shift.";
            }
        }

        
        $this->render('assignShift', [
            'department' => $department,
            'shifts' => $allShifts
        ]);
    }

    public function createDepartmentForm()
    {
       
        $departmentModel = new Department();
        $shiftModel = new Shift();

        
        $shifts = $shiftModel->getAllShifts();

        
        return $this->render('add-department', [
            'username' => $_SESSION['username'] ?? 'Admin', 
            'shifts' => $shifts, 
        ]);
    }


    public function addDepartment()
    {
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $departmentName = $_POST['departmentName'];
        $shiftId = $_POST['shiftId']; 

        
        $departmentModel = new Department();
        $departmentId = $departmentModel->create([
            'DepartmentName' => $departmentName
        ]);

        if ($departmentId) {
            
            if (!empty($shiftId)) {
                $shiftModel = new Shift();
                $success = $shiftModel->assignShiftToDepartment($shiftId, $departmentId);
                
                if (!$success) {
                    echo "Error occurred while assigning the shift.";
                    return;
                }
            }
            
            
            header('Location: /department');
            exit();
        } else {
            echo "An error occurred while creating the department.";
        }
    }

    
    $shiftModel = new Shift();
    $allShifts = $shiftModel->getAllShifts(); 
    $this->render('addDepartment', [
        'shifts' => $allShifts 
    ]);
    }

    public function editDepartmentForm($departmentId)
{
    $departmentModel = new Department();
    $shiftModel = new Shift();

    
    $department = $departmentModel->getByIdWithShift($departmentId);

    
    $shifts = $shiftModel->getAllShifts();

    
    if (!$department) {
        header('Location: /department');  
        exit;
    }

        
    $shiftId = $department['ShiftID'] ?? null; 

    foreach ($shifts as &$shift) {
        $shift['selected'] = ($shift['ID'] == $shiftId) ? 'selected' : '';
    }


    
    return $this->render('edit-department', [
        'username' => $_SESSION['username'] ?? 'Admin', 
        'department' => $department,
        'shifts' => $shifts
    ]);    
}


public function updateDepartment($departmentId)
{
    $departmentModel = new Department();
    $shiftModel = new Shift();

    
    $departmentName = $_POST['departmentName'];
    $shiftId = $_POST['shiftId']; 

    
    if (empty($shiftId)) {
        
        echo "Please select a shift.";
        exit;
    }

    
    $departmentData = [
        'DepartmentName' => $departmentName,
        'ShiftID' => $shiftId 
    ];

    $departmentModel->updateDepartment($departmentId, $departmentData);

    
    header('Location: /department');
    exit;
}

public function deleteDepartment($ID)
{
    $departmentModel = new Department();

    
    $department = $departmentModel->getById($ID);

    if (!$department) {
        
        $_SESSION['error_message'] = "Department not found.";
        header('Location: /department');
        exit;
    }

    
    $departmentDeleted = $departmentModel->delete($ID);

    if ($departmentDeleted) {
        
        $_SESSION['success_message'] = "Department and its associated shifts were successfully deleted.";
        header('Location: /department');
        exit;
    } else {
        
        $_SESSION['error_message'] = "Failed to delete the department.";
        header('Location: /department');
        exit;
    }
}


}
