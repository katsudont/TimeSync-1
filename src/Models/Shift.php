<?php

namespace App\Models;

class Shift extends BaseModel
{
    // Get all shifts
    public function getAllShifts()
    {
        $sql = "SELECT * FROM Shift";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get a shift by ID
    public function getById($shiftId)
    {
        $sql = "SELECT * FROM Shift WHERE ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$shiftId]);
        return $stmt->fetch();
    }

    // Method to get all shifts assigned to a given department
    public function getShiftsByDepartment($departmentId)
    {
        $sql = "SELECT s.* 
                FROM Shift s
                JOIN DepartmentShifts ds ON s.ID = ds.ShiftID
                WHERE ds.DepartmentID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll();
    }

    // Method to create a shift
    public function create($data)
{
    $sql = "INSERT INTO Shift (TimeIn, TimeOut) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$data['TimeIn'], $data['TimeOut']]);
    return $this->db->lastInsertId();
}


    // Method to assign a shift to a department (inserts into DepartmentShifts)
    public function assignShiftToDepartment($shiftId, $departmentId)
{
    // Insert the shift into the DepartmentShifts table
    $sql = "INSERT INTO DepartmentShifts (DepartmentID, ShiftID) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$departmentId, $shiftId]);
    return true; // Successfully assigned the new shift to the department
}


// Update an existing shift
public function updateShift($shiftId, $timeIn, $timeOut)
{
    $sql = "UPDATE Shift SET TimeIn = ?, TimeOut = ? WHERE ID = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$timeIn, $timeOut, $shiftId]);
}

public function deleteShift($shiftId)
{
    // Prepare the SQL query to delete a shift based on its ID
    $sql = "DELETE FROM Shift WHERE ID = ?";
    $stmt = $this->db->prepare($sql);

    // Execute the query with the shift ID as a parameter
    $stmt->execute([$shiftId]);

    // Check if the shift was successfully deleted by checking the row count
    return $stmt->rowCount() > 0; // Returns true if the shift was deleted
}

    
}
