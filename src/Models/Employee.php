<?php

namespace App\Models;
use App\Models\BaseModel;
use \PDO;


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

 
 public function getEmployeeData()
 {
     
     $query = "
         SELECT e.ID, e.Name, e.Email, e.Birthdate, e.HireDate, u.Username, e.DepartmentID, d.DepartmentName
         FROM Employee e
         JOIN Department d ON e.DepartmentID = d.ID
         LEFT JOIN User u ON e.ID = u.EmployeeID
         WHERE d.DepartmentName <> 'Admin'
     ";
 
     
     $stmt = $this->db->prepare($query);
     $stmt->execute();
     
     
     return $stmt->fetchAll(\PDO::FETCH_ASSOC);
 }
 

 
 public function getEmployeesByDepartment($departmentId)
 {
     $sql = "SELECT e.ID, e.Name, e.Email, d.DepartmentName 
             FROM Employee e 
             INNER JOIN Department d ON e.DepartmentID = d.ID 
             WHERE e.DepartmentID = ?";
     $stmt = $this->db->prepare($sql);
     $stmt->execute([$departmentId]);
     return $stmt->fetchAll();
 }

 
public function getEmployeeById($id)
{
   
    $query = "
        SELECT e.ID, e.Name, e.Email, e.Birthdate, e.HireDate, u.Username, e.DepartmentID, d.DepartmentName
        FROM Employee e
        JOIN Department d ON e.DepartmentID = d.ID
        LEFT JOIN User u ON e.ID = u.EmployeeID  -- Left Join to get Username
        WHERE e.ID = :id AND d.DepartmentName <> 'Admin'
    ";

    
    $stmt = $this->db->prepare($query);

    
    $stmt->execute(['id' => $id]);

    
    return $stmt->fetch(\PDO::FETCH_ASSOC);  
}

public function getAdminById($id)
{

    $query = "
        SELECT e.ID, e.Name, e.Email, e.Birthdate, e.HireDate, u.Username, e.DepartmentID, d.DepartmentName
        FROM Employee e
        JOIN Department d ON e.DepartmentID = d.ID
        LEFT JOIN User u ON e.ID = u.EmployeeID  -- Left Join to get Username
        WHERE e.ID = :id AND d.DepartmentName = 'Admin'
    ";

    
    $stmt = $this->db->prepare($query);

    
    $stmt->execute(['id' => $id]);

    
    return $stmt->fetch(\PDO::FETCH_ASSOC);  
}



public function updateEmployee($employeeId, $data)
{
    
    $query = "
        UPDATE Employee
        SET 
            Name = :Name,
            Email = :Email,
            Birthdate = :Birthdate,
            DepartmentID = :DepartmentID
        WHERE ID = :EmployeeID
    ";

    
    $stmt = $this->db->prepare($query);

    
    $stmt->execute([
        'Name' => $data['Name'],
        'Email' => $data['Email'],
        'Birthdate' => $data['Birthdate'],
        'DepartmentID' => $data['DepartmentID'],
        'EmployeeID' => $employeeId
    ]);
}

public function getById($id)
{
    $stmt = $this->db->prepare("
        SELECT e.*, u.Username
        FROM Employee e
        LEFT JOIN User u ON e.ID = u.EmployeeID
        WHERE e.ID = :id
    ");
    $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC); 
}


    
    public function delete($id)
{
    try {
        
        $this->db->beginTransaction();

        
        $stmtUser = $this->db->prepare("DELETE FROM User WHERE EmployeeID = :id");
        $stmtUser->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmtUser->execute();

        
        $stmtEmployee = $this->db->prepare("DELETE FROM Employee WHERE ID = :id");
        $stmtEmployee->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmtEmployee->execute();

        
        $this->db->commit();

        return true;
    } catch (\PDOException $e) {
        
        $this->db->rollBack();
        error_log("Delete failed: " . $e->getMessage());
        return false;
    }
}



}
