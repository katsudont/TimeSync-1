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
        $stmt->execute(['id' => $id]); // Ensure 'id' matches ':id' in the query
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

 

}
