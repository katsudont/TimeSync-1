<?php

namespace App\Controllers;

use App\Models\Shift;

class ShiftController extends BaseController
{
    // Display the list of shifts
    public function index()
    {
        $shiftModel = new Shift();

        // Get all shifts
        $shifts = $shiftModel->getAllShifts();

        // Render the shifts table
        $this->render('shift', ['shifts' => $shifts]);
    }

    // Edit a shift
    public function edit($shiftId)
    {
        $shiftModel = new Shift();

        // Get the shift by ID
        $shift = $shiftModel->getById($shiftId);
        if (!$shift) {
            echo "Shift not found.";
            return;
        }

        // Check if it's a POST request to update the shift
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $timeIn = $_POST['timeIn'];  // Get the new TimeIn
            $timeOut = $_POST['timeOut']; // Get the new TimeOut

            // Update the shift
            $shiftModel->updateShift($shiftId, $timeIn, $timeOut);

            // Redirect back to the shift list
            header('Location: /shift');
            exit();
        }

        // Render the edit form
        $this->render('edit-shift', ['shift' => $shift]);
    }

    // Add a new shift
    public function add()
    {
        $shiftModel = new Shift();

        // Check if it's a POST request to add the new shift
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ensure timeIn and timeOut are set in the form data
            if (empty($_POST['timeIn']) || empty($_POST['timeOut'])) {
                echo "Both TimeIn and TimeOut are required.";
                return;
            }

            $timeIn = $_POST['timeIn'];  // Get the timeIn from the form
            $timeOut = $_POST['timeOut']; // Get the timeOut from the form

            // Insert the new shift into the database
            $shiftModel->create([
                'TimeIn' => $timeIn,
                'TimeOut' => $timeOut
            ]);

            // Redirect back to the shift list after successful creation
            header('Location: /shift');
            exit();
        }

        // Render the add shift form
        $this->render('add-shift');
    }

    public function deleteShift($shiftId)
{
    $shiftModel = new Shift();

    // Get the shift data using the Shift model
    $shift = $shiftModel->getById($shiftId);

    if (!$shift) {
        // Handle case when shift is not found
        $_SESSION['error_message'] = "Shift not found.";
        header('Location: /shift');
        exit;
    }

    // Delete the shift
    $shiftDeleted = $shiftModel->deleteShift($shiftId);

    if ($shiftDeleted) {
        // Add a success message and redirect to the shift list page
        $_SESSION['success_message'] = "Shift was successfully deleted.";
        header('Location: /shift');
        exit;
    } else {
        // Handle failure to delete the shift
        $_SESSION['error_message'] = "Failed to delete the shift.";
        header('Location: /shift');
        exit;
    }
}


}
