<?php

namespace App\Models;

class Department extends BaseModel
{
    public function getAll()
    {
        $query = "SELECT ID, DepartmentName FROM Department";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
    $query = "SELECT * FROM Department WHERE ID = :id";
    $stmt = $this->db->prepare($query);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    

    // Count all departments
    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM Department");
        return $stmt->fetchColumn();
    }

    public function getByName($name)
    {
        $sql = "SELECT * FROM Department WHERE DepartmentName = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
    
    // Method to create a new department
    public function create($data)
    {
        $sql = "INSERT INTO Department (DepartmentName) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$data['DepartmentName']]);
        return $this->db->lastInsertId(); // Returns the ID of the newly inserted department
    }

    public function getByIdWithShift($id)
{
    $query = "
        SELECT d.ID, d.DepartmentName, ds.ShiftID, s.TimeIn, s.TimeOut 
        FROM Department d
        JOIN DepartmentShifts ds ON ds.DepartmentID = d.ID
        JOIN Shift s ON s.ID = ds.ShiftID
        WHERE d.ID = :id
    ";
    $stmt = $this->db->prepare($query);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}


public function updateDepartment($departmentId, $data)
{
    // Update the Department Name
    $sql = "UPDATE Department SET DepartmentName = :departmentName WHERE ID = :departmentId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':departmentName', $data['DepartmentName']);
    $stmt->bindParam(':departmentId', $departmentId);
    $stmt->execute();

    // Now update the DepartmentShifts table (this assumes the relationship is already in the table)
    $sql = "UPDATE DepartmentShifts SET ShiftID = :shiftId WHERE DepartmentID = :departmentId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':shiftId', $data['ShiftID']);
    $stmt->bindParam(':departmentId', $departmentId);
    $stmt->execute();
}



}
