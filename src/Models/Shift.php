<?php

namespace App\Models;

class Shift extends BaseModel
{
    
    public function getAllShifts()
    {
        $sql = "SELECT * FROM Shift";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    public function getById($shiftId)
    {
        $sql = "SELECT * FROM Shift WHERE ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$shiftId]);
        return $stmt->fetch();
    }

    
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

    
    public function create($data)
{
    $sql = "INSERT INTO Shift (TimeIn, TimeOut) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$data['TimeIn'], $data['TimeOut']]);
    return $this->db->lastInsertId();
}


    
    public function assignShiftToDepartment($shiftId, $departmentId)
{
    
    $sql = "INSERT INTO DepartmentShifts (DepartmentID, ShiftID) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$departmentId, $shiftId]);
    return true; 
}



public function updateShift($shiftId, $timeIn, $timeOut)
{
    $sql = "UPDATE Shift SET TimeIn = ?, TimeOut = ? WHERE ID = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$timeIn, $timeOut, $shiftId]);
}

public function deleteShift($shiftId)
{
    
    $sql = "DELETE FROM Shift WHERE ID = ?";
    $stmt = $this->db->prepare($sql);

    
    $stmt->execute([$shiftId]);

   
    return $stmt->rowCount() > 0; 
}

    
}
