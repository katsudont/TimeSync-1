<?php

namespace App\Models;

use App\Models\BaseModel;

class Attendance extends BaseModel
{
    
    public function getLatestAttendance($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT 
                a.EmployeeID, 
                e.Name as EmployeeName, 
                d.DepartmentName, 
                a.InTime, 
                a.InStatus, 
                a.OutTime, 
                a.OutStatus 
            FROM Attendance a
            JOIN Employee e ON a.EmployeeID = e.ID
            JOIN Department d ON a.DepartmentID = d.ID
            ORDER BY a.InTime DESC
            LIMIT :limit
        ");
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllAttendance()
{
    $stmt = $this->db->prepare("
        SELECT 
            a.EmployeeID, 
            e.Name as EmployeeName, 
            d.DepartmentName, 
            a.InTime, 
            a.InStatus, 
            a.OutTime, 
            a.OutStatus,
            a.ShiftID
        FROM Attendance a
        JOIN Employee e ON a.EmployeeID = e.ID
        JOIN Department d ON a.DepartmentID = d.ID
        ORDER BY a.InTime DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC); 
}

public function getFilteredAttendance($filters)
{
    $query = "
        SELECT a.EmployeeID, e.Name as EmployeeName, d.DepartmentName, 
               a.InTime, a.InStatus, a.OutTime, a.OutStatus, a.ShiftID 
        FROM Attendance a
        JOIN Employee e ON a.EmployeeID = e.ID
        JOIN Department d ON a.DepartmentID = d.ID
        WHERE 1=1
    ";

    
    if (!empty($filters['EmployeeID'])) {
        $query .= " AND a.EmployeeID = :EmployeeID";
    }
    if (!empty($filters['EmployeeName'])) {
        $query .= " AND e.Name LIKE :EmployeeName";
    }
    if (!empty($filters['DepartmentName'])) {
        $query .= " AND d.DepartmentName LIKE :DepartmentName";
    }
    if (!empty($filters['ShiftID'])) {
        $query .= " AND a.ShiftID = :ShiftID";
    }
    if (!empty($filters['InStatus'])) {
        $query .= " AND a.InStatus = :InStatus";
    }
    if (!empty($filters['OutStatus'])) {
        $query .= " AND a.OutStatus = :OutStatus";
    }

    $stmt = $this->db->prepare($query);

    if (!empty($filters['EmployeeID'])) {
        $stmt->bindParam(':EmployeeID', $filters['EmployeeID']);
    }
    if (!empty($filters['EmployeeName'])) {
        $stmt->bindValue(':EmployeeName', "%" . $filters['EmployeeName'] . "%");
    }
    if (!empty($filters['DepartmentName'])) {
        $stmt->bindValue(':DepartmentName', "%" . $filters['DepartmentName'] . "%");
    }
    if (!empty($filters['ShiftID'])) {
        $stmt->bindParam(':ShiftID', $filters['ShiftID']);
    }
    if (!empty($filters['InStatus'])) {
        $stmt->bindParam(':InStatus', $filters['InStatus']);
    }
    if (!empty($filters['OutStatus'])) {
        $stmt->bindParam(':OutStatus', $filters['OutStatus']);
    }

    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}



    
    public function countPresent()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM Attendance 
            WHERE InStatus = 'Present' AND DATE(InTime) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

   
    public function countLate()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM Attendance 
            WHERE InStatus = 'Late' AND DATE(InTime) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAllAttendanceByEmployee($employeeId)
    {
        
        $stmt = $this->db->prepare("
            SELECT shiftID, InTime, InStatus, OutTime, OutStatus 
            FROM Attendance 
            WHERE EmployeeID = :employeeId
            ORDER BY InTime DESC
        ");
        $stmt->bindParam(':employeeId', $employeeId);
        $stmt->execute();
    
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    

    public function recordTimeIn($employeeId)
{
    $stmt = $this->db->prepare("
        SELECT e.DepartmentID, ds.ShiftID, s.TimeIn 
        FROM Employee e
        JOIN DepartmentShifts ds ON e.DepartmentID = ds.DepartmentID
        JOIN Shift s ON ds.ShiftID = s.ID
        WHERE e.ID = :employeeId
    ");
    $stmt->bindParam(':employeeId', $employeeId);
    $stmt->execute();
    $shiftData = $stmt->fetch();

    if ($shiftData) {
        $departmentID = $shiftData['DepartmentID'];
        $shiftID = $shiftData['ShiftID'];
        $scheduledTimeIn = $shiftData['TimeIn']; // The scheduled start time of the shift

        // Get current time
        $currentTime = new \DateTime(); // Use DateTime for comparison
        $scheduledTime = new \DateTime($scheduledTimeIn);

        // Calculate the difference in minutes
        $interval = $currentTime->diff($scheduledTime);
        $minutesLate = $interval->i + ($interval->h * 60); // Total minutes late

        if ($minutesLate > 60) {
            $inStatus = 'Absent'; // More than 60 minutes late
        } elseif ($minutesLate > 10) {
            $inStatus = 'Late'; // Between 10 and 60 minutes late
        } else {
            $inStatus = 'On Time'; // On time (within 10 minutes)
        }

        // Record the clock-in time
        $stmt = $this->db->prepare("
            INSERT INTO Attendance (EmployeeID, DepartmentID, ShiftID, InTime, InStatus)
            VALUES (:employeeId, :departmentID, :shiftID, NOW(), :inStatus)
        ");
        $stmt->bindParam(':employeeId', $employeeId);
        $stmt->bindParam(':departmentID', $departmentID);
        $stmt->bindParam(':shiftID', $shiftID);
        $stmt->bindParam(':inStatus', $inStatus);
        $stmt->execute();
    }
}

public function recordTimeOut($employeeId)
{
    $stmt = $this->db->prepare("
        SELECT e.DepartmentID, ds.ShiftID, s.TimeOut 
        FROM Employee e
        JOIN DepartmentShifts ds ON e.DepartmentID = ds.DepartmentID
        JOIN Shift s ON ds.ShiftID = s.ID
        WHERE e.ID = :employeeId
    ");
    $stmt->bindParam(':employeeId', $employeeId);
    $stmt->execute();
    $shiftData = $stmt->fetch();

    if ($shiftData) {
        $departmentID = $shiftData['DepartmentID'];
        $shiftID = $shiftData['ShiftID'];
        $scheduledTimeOut = $shiftData['TimeOut']; // The scheduled end time of the shift

        // Get current time
        $currentTime = new \DateTime(); // Current date and time
        $scheduledTime = new \DateTime($scheduledTimeOut); // Scheduled time-out

        // Calculate the difference in minutes
        $interval = $currentTime->diff($scheduledTime); // Difference between current and scheduled times
        $minutesLate = $interval->i + ($interval->h * 60); // Convert hours to minutes and add the minutes difference

        if ($minutesLate > 15) {
            $outStatus = 'Overtime'; // More than 15 minutes late
        } else {
            $outStatus = 'Completed'; // Within 15 minutes grace period
        }

        // Update the clock-out time and status for the specific employee and today's date
        $stmt = $this->db->prepare("
            UPDATE Attendance 
            SET OutTime = NOW(), OutStatus = :outStatus 
            WHERE EmployeeID = :employeeId AND DATE(InTime) = CURDATE() AND OutTime IS NULL
        ");
        $stmt->bindParam(':employeeId', $employeeId);
        $stmt->bindParam(':outStatus', $outStatus);
        $stmt->execute();
    }
}




}
