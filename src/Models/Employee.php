<?php

namespace App\Models;

class Employee extends BaseModel
{
    public function create($data)
{
    $query = "INSERT INTO Employee (Name, Email, Birthdate, HireDate, DepartmentID)
              VALUES (:Name, :Email, :Birthdate, :HireDate, :DepartmentID)";
    $stmt = $this->db->prepare($query);
    $stmt->execute([
        'Name' => $data['Name'],
        'Email' => $data['Email'],
        'Birthdate' => $data['Birthdate'],
        'HireDate' => $data['HireDate'],
        'DepartmentID' => $data['DepartmentID']
    ]);
    return $this->db->lastInsertId();
}

public function countAll()
{
    $stmt = $this->db->query("SELECT COUNT(*) FROM Employee");
    return $stmt->fetchColumn();
}

 // Get employee details with department name
 public function getEmployeeData()
 {
     $query = "
         SELECT 
             e.ID as EmployeeID, 
             e.Name, 
             e.Email, 
             u.Username, 
             e.Birthdate, 
             e.HireDate, 
             d.DepartmentName, 
             e.DepartmentID
         FROM Employee e
         JOIN Department d ON e.DepartmentID = d.ID
         JOIN User u ON u.EmployeeID = e.ID
     ";

     $stmt = $this->db->query($query);
     return $stmt->fetchAll();
 }

 // Get employees by department
 public function getEmployeesByDepartment($departmentId)
 {
     $sql = "SELECT e.ID, e.Name, e.Email, d.DepartmentName 
             FROM employee e 
             INNER JOIN department d ON e.DepartmentID = d.ID 
             WHERE e.DepartmentID = ?";
     $stmt = $this->db->prepare($sql);
     $stmt->execute([$departmentId]);
     return $stmt->fetchAll();
 }

 public function getEmployeeById($employeeId)
{
    $query = "
        SELECT 
            e.ID as EmployeeID, 
            e.Name, 
            e.Email, 
            u.Username, 
            e.Birthdate, 
            e.HireDate, 
            d.DepartmentName, 
            e.DepartmentID
        FROM Employee e
        JOIN Department d ON e.DepartmentID = d.ID
        JOIN User u ON u.EmployeeID = e.ID
        WHERE e.ID = :EmployeeID
    ";

    $stmt = $this->db->prepare($query);
    $stmt->execute(['EmployeeID' => $employeeId]);
    return $stmt->fetch(); // Fetch a single row
}

}
