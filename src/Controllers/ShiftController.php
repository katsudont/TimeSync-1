<?php

namespace App\Controllers;

use App\Models\Shift;

class ShiftController extends BaseController
{
    
    public function index()
    {
        session_start();

        
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: /login');
        exit;
    }
        $shiftModel = new Shift();

        
        $shifts = $shiftModel->getAllShifts();

        
        $this->render('shift', [
            'username' => $_SESSION['username'] ?? 'Admin', 
            'shifts' => $shifts
        ]);
    }

    
    public function edit($shiftId)
    {
        session_start();

        
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: /login');
        exit;
        }

        $shiftModel = new Shift();

        
        $shift = $shiftModel->getById($shiftId);
        if (!$shift) {
            echo "Shift not found.";
            return;
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $timeIn = $_POST['timeIn'];  
            $timeOut = $_POST['timeOut']; 

            
            $shiftModel->updateShift($shiftId, $timeIn, $timeOut);

            
            header('Location: /shift');
            exit();
        }

        
        $this->render('edit-shift', [
            'username' => $_SESSION['username'] ?? 'Admin', 
            'shift' => $shift
        ]);
    }

    //Add Shift
    public function add()
    {
        session_start();

        
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
        header('Location: /login');
        exit;
        }

        $shiftModel = new Shift();

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ensure timeIn and timeOut are set in the form data
            if (empty($_POST['timeIn']) || empty($_POST['timeOut'])) {
                echo "Both TimeIn and TimeOut are required.";
                return;
            }

            $timeIn = $_POST['timeIn']; 
            $timeOut = $_POST['timeOut']; 

            
            $shiftModel->create([
                'TimeIn' => $timeIn,
                'TimeOut' => $timeOut
            ]);

            
            header('Location: /shift');
            exit();
        }

        
        $this->render('add-shift', ['username' => $_SESSION['username'] ?? 'Admin' ]);
    }

    public function deleteShift($shiftId)
{
    $shiftModel = new Shift();

    
    $shift = $shiftModel->getById($shiftId);

    if (!$shift) {
        
        $_SESSION['error_message'] = "Shift not found.";
        header('Location: /shift');
        exit;
    }

    
    $shiftDeleted = $shiftModel->deleteShift($shiftId);

    if ($shiftDeleted) {
        
        $_SESSION['success_message'] = "Shift was successfully deleted.";
        header('Location: /shift');
        exit;
    } else {
        
        $_SESSION['error_message'] = "Failed to delete the shift.";
        header('Location: /shift');
        exit;
    }
}


}
