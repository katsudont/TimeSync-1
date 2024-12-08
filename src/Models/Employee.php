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

 // Get employee details with department name
 public function getEmployeeData()
 {
     // Updated query to include Username and HireDate, and exclude employees in the "Admin" department
     $query = "
         SELECT e.ID, e.Name, e.Email, e.Birthdate, e.HireDate, u.Username, e.DepartmentID, d.DepartmentName
         FROM Employee e
         JOIN Department d ON e.DepartmentID = d.ID
         LEFT JOIN User u ON e.ID = u.EmployeeID
         WHERE d.DepartmentName <> 'Admin'
     ";
 
     // Prepare and execute the statement
     $stmt = $this->db->prepare($query);
     $stmt->execute();
     
     // Return the results
     return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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

 // Assuming you have the method getEmployeeById
public function getEmployeeById($id)
{
    // Updated query to also select HireDate and Username
    $query = "
        SELECT e.ID, e.Name, e.Email, e.Birthdate, e.HireDate, u.Username, e.DepartmentID, d.DepartmentName
        FROM Employee e
        JOIN Department d ON e.DepartmentID = d.ID
        LEFT JOIN User u ON e.ID = u.EmployeeID  -- Left Join to get Username
        WHERE e.ID = :id AND d.DepartmentName <> 'Admin'
    ";

    // Prepare the SQL statement
    $stmt = $this->db->prepare($query);

    // Execute the query with the employee ID
    $stmt->execute(['id' => $id]);

    // Fetch the result as an associative array
    return $stmt->fetch(\PDO::FETCH_ASSOC);  // Return employee data with HireDate and Username
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

    // Prepare the SQL statement
    $stmt = $this->db->prepare($query);

    // Execute the query with the employee ID
    $stmt->execute(['id' => $id]);

    // Fetch the result as an associative array
    return $stmt->fetch(\PDO::FETCH_ASSOC);  // Return employee data with HireDate and Username
}




public function updateEmployee($employeeId, $data)
{
    // Prepare the SQL statement to update the employee record
    $query = "
        UPDATE Employee
        SET 
            Name = :Name,
            Email = :Email,
            Birthdate = :Birthdate,
            DepartmentID = :DepartmentID
        WHERE ID = :EmployeeID
    ";

    // Prepare the statement
    $stmt = $this->db->prepare($query);

    // Execute the statement with the provided data
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

    return $stmt->fetch(\PDO::FETCH_ASSOC); // Return employee and user data as associative array
}


    // Delete an employee by ID
    public function delete($id)
{
    try {
        // Begin transaction
        $this->db->beginTransaction();

        // Delete associated user
        $stmtUser = $this->db->prepare("DELETE FROM User WHERE EmployeeID = :id");
        $stmtUser->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmtUser->execute();

        // Delete employee
        $stmtEmployee = $this->db->prepare("DELETE FROM Employee WHERE ID = :id");
        $stmtEmployee->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmtEmployee->execute();

        // Commit transaction
        $this->db->commit();

        return true;
    } catch (\PDOException $e) {
        // Rollback transaction on failure
        $this->db->rollBack();
        error_log("Delete failed: " . $e->getMessage());
        return false;
    }
}


}
