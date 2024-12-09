-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 06:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `timesync`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `ID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `ShiftID` int(11) NOT NULL,
  `InTime` datetime DEFAULT NULL,
  `InStatus` varchar(50) DEFAULT NULL,
  `OutTime` datetime DEFAULT NULL,
  `OutStatus` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`ID`, `EmployeeID`, `DepartmentID`, `ShiftID`, `InTime`, `InStatus`, `OutTime`, `OutStatus`) VALUES
(1, 1, 1, 1, '2024-11-01 08:00:00', 'On Time', '2024-11-01 16:00:00', 'Completed'),
(2, 2, 2, 2, '2024-11-01 16:00:00', 'On Time', '2024-11-01 00:00:00', 'Completed'),
(3, 3, 3, 3, '2024-11-01 00:00:00', 'Late', '2024-11-01 08:00:00', 'Completed'),
(4, 4, 4, 1, '2024-11-01 08:30:00', 'On Time', '2024-11-01 16:30:00', 'Completed'),
(5, 5, 5, 2, '2024-11-01 16:30:00', 'On Time', '2024-11-01 00:30:00', 'Completed'),
(6, 7, 2, 2, '2024-12-09 13:25:09', 'On Time', '2024-12-09 13:25:10', 'Late');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `ID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`ID`, `DepartmentName`) VALUES
(1, 'Admin'),
(2, 'Engineering'),
(3, 'Sales'),
(4, 'Marketing'),
(5, 'Finance');

-- --------------------------------------------------------

--
-- Table structure for table `departmentshifts`
--

CREATE TABLE `departmentshifts` (
  `DepartmentID` int(11) NOT NULL,
  `ShiftID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departmentshifts`
--

INSERT INTO `departmentshifts` (`DepartmentID`, `ShiftID`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 1),
(5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Birthdate` date DEFAULT NULL,
  `HireDate` date DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`ID`, `Name`, `Email`, `Birthdate`, `HireDate`, `DepartmentID`) VALUES
(1, 'Alice Johnson', 'alice.johnson@example.com', '1990-05-10', '2015-03-25', 1),
(2, 'Bob Smith', 'bob.smith@example.com', '1985-09-12', '2010-01-15', 2),
(3, 'Carol Davis', 'carol.davis@example.com', '1992-11-21', '2018-08-30', 3),
(4, 'David Martinez', 'david.martinez@example.com', '1988-06-13', '2016-02-17', 4),
(5, 'Eva Brown', 'eva.brown@example.com', '1995-01-27', '2020-05-05', 5),
(6, 'Admin AUF', 'admin1@auf.com', '1999-09-08', '2024-12-09', 1),
(7, 'Employee Test', 'employee@test.com', '2004-12-08', '2024-12-09', 2);

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `ID` int(11) NOT NULL,
  `TimeIn` time NOT NULL,
  `TimeOut` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`ID`, `TimeIn`, `TimeOut`) VALUES
(1, '08:00:00', '16:00:00'),
(2, '16:00:00', '00:00:00'),
(3, '00:00:00', '08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `RoleID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `Username`, `Password`, `EmployeeID`, `RoleID`) VALUES
(1, 'alice_admin', 'adminpassword123', 1, 1),
(2, 'bob_engineer', 'password123', 2, 2),
(3, 'carol_sales', 'salespassword123', 3, 2),
(4, 'david_marketing', 'marketing123', 4, 2),
(5, 'eva_finance', 'finance123', 5, 2),
(6, 'admin', '$2y$10$ub./2FAB9RuBicC1owJFjO7Dt9AiaqfUkgsAB/lBTdEgoDexcBFEm', 6, 1),
(7, 'emptest', '$2y$10$jkb5A2Bkw8z.G8kUSMyIXeEma7aw1o/YYk9dleNlRZCiXxsE6Nggi', 7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `ID` int(11) NOT NULL,
  `Role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`ID`, `Role`) VALUES
(1, 'Admin'),
(2, 'Employee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_Attendance_Employee` (`EmployeeID`),
  ADD KEY `FK_Attendance_Department` (`DepartmentID`),
  ADD KEY `FK_Attendance_Shift` (`ShiftID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `departmentshifts`
--
ALTER TABLE `departmentshifts`
  ADD PRIMARY KEY (`DepartmentID`,`ShiftID`),
  ADD UNIQUE KEY `unique_department_shift` (`DepartmentID`,`ShiftID`),
  ADD KEY `FK_DepartmentShifts_Shift` (`ShiftID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `FK_Employee_Department` (`DepartmentID`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `FK_User_Employee` (`EmployeeID`),
  ADD KEY `FK_User_Role` (`RoleID`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `FK_Attendance_Department` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`ID`),
  ADD CONSTRAINT `FK_Attendance_Employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`ID`),
  ADD CONSTRAINT `FK_Attendance_Shift` FOREIGN KEY (`ShiftID`) REFERENCES `shift` (`ID`);

--
-- Constraints for table `departmentshifts`
--
ALTER TABLE `departmentshifts`
  ADD CONSTRAINT `FK_DepartmentShifts_Department` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`ID`),
  ADD CONSTRAINT `FK_DepartmentShifts_Shift` FOREIGN KEY (`ShiftID`) REFERENCES `shift` (`ID`);

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `FK_Employee_Department` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`ID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_User_Employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`ID`),
  ADD CONSTRAINT `FK_User_Role` FOREIGN KEY (`RoleID`) REFERENCES `user_role` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
