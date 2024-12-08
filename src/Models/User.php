<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;


class User extends BaseModel
{
    public function create($data)
{
    $query = "INSERT INTO User (Username, Password, EmployeeID, RoleID)
              VALUES (:Username, :Password, :EmployeeID, :RoleID)";
    $stmt = $this->db->prepare($query);
    $stmt->execute([
        'Username' => $data['Username'],
        'Password' => $data['Password'],
        'EmployeeID' => $data['EmployeeID'],
        'RoleID' => $data['RoleID']
    ]);
}


public function login($username, $password)
{
    $query = "SELECT u.ID as id, u.Username as username, u.Password as password, u.RoleID as role_id 
              FROM User u
              WHERE u.Username = :username";

    $stmt = $this->db->prepare($query);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    
    if ($user && password_verify($password, $user['password'])) {
        return $user; 
    }
    return false; 
}

public function getRoleCounts()
{
    $query = "
        SELECT 
            COUNT(*) AS TotalEmployees,
            SUM(CASE WHEN r.Role = 'Admin' THEN 1 ELSE 0 END) AS Admins,
            SUM(CASE WHEN r.Role = 'Employee' THEN 1 ELSE 0 END) AS Employees
        FROM User u
        JOIN User_Role r ON u.RoleID = r.ID;
    ";

    $stmt = $this->db->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    
    public function countAdmins()
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) 
            FROM User u
            JOIN User_Role r ON u.RoleID = r.ID
            WHERE r.Role = 'Admin'
        ");
        return $stmt->fetchColumn();
    }

    public function getProfileData($userId)
    {
        $stmt = $this->db->prepare("
            SELECT u.ID as EmployeeID, e.Name, e.Email, u.Username, e.Birthdate, 
                   d.DepartmentName, e.HireDate
            FROM User u
            JOIN Employee e ON u.EmployeeID = e.ID
            JOIN Department d ON e.DepartmentID = d.ID
            WHERE u.ID = :userId
        ");
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateProfile($userId, $name, $email, $username, $birthdate)
    {
    try {
        $stmt = $this->db->prepare("
            UPDATE Employee e
            JOIN User u ON e.ID = u.EmployeeID
            SET e.Name = :name, e.Email = :email, u.Username = :username, e.Birthdate = :birthdate
            WHERE u.ID = :userId
        ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount(); 
        }
    } catch (\PDOException $e) {
        error_log("Update failed: " . $e->getMessage()); 
        return false;
    }

    return false;
    }

    
    public function getByEmployeeId($employeeId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM User WHERE EmployeeID = :employeeId
        ");
        $stmt->bindParam(':employeeId', $employeeId, \PDO::PARAM_INT);
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

    
    public function updateUser($employeeId, $data)
    {
        $query = "
            UPDATE User
            SET Username = :Username
            WHERE EmployeeID = :EmployeeID
        ";
    
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'Username' => $data['Username'],
            'EmployeeID' => $data['EmployeeID']
        ]);
    }
    

}
