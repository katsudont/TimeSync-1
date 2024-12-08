DROP TABLE IF EXISTS DepartmentShifts;
DROP TABLE IF EXISTS Attendance;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS User_Role;
DROP TABLE IF EXISTS Shift;
DROP TABLE IF EXISTS Employee;
DROP TABLE IF EXISTS Department;


CREATE TABLE Department (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    DepartmentName VARCHAR(100) NOT NULL
);


CREATE TABLE Employee (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Birthdate DATE,
    HireDate DATE,
    DepartmentID INT
);


CREATE TABLE Shift (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    TimeIn TIME NOT NULL,
    TimeOut TIME NOT NULL
);


CREATE TABLE User_Role (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Role VARCHAR(50) NOT NULL
);


CREATE TABLE User (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    EmployeeID INT,
    RoleID INT
);


CREATE TABLE Attendance (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    EmployeeID INT NOT NULL,
    DepartmentID INT NOT NULL,
    ShiftID INT NOT NULL,
    InTime DATETIME,
    InStatus VARCHAR(50),
    OutTime DATETIME,
    OutStatus VARCHAR(50)
);


CREATE TABLE DepartmentShifts (
    DepartmentID INT NOT NULL,
    ShiftID INT NOT NULL,
    PRIMARY KEY (DepartmentID, ShiftID)
);


ALTER TABLE Employee
ADD CONSTRAINT FK_Employee_Department
FOREIGN KEY (DepartmentID) REFERENCES Department(ID);

ALTER TABLE User
ADD CONSTRAINT FK_User_Employee
FOREIGN KEY (EmployeeID) REFERENCES Employee(ID),
ADD CONSTRAINT FK_User_Role
FOREIGN KEY (RoleID) REFERENCES User_Role(ID);

ALTER TABLE Attendance
ADD CONSTRAINT FK_Attendance_Employee
FOREIGN KEY (EmployeeID) REFERENCES Employee(ID),
ADD CONSTRAINT FK_Attendance_Department
FOREIGN KEY (DepartmentID) REFERENCES Department(ID),
ADD CONSTRAINT FK_Attendance_Shift
FOREIGN KEY (ShiftID) REFERENCES Shift(ID);

ALTER TABLE DepartmentShifts
ADD CONSTRAINT FK_DepartmentShifts_Department
FOREIGN KEY (DepartmentID) REFERENCES Department(ID),
ADD CONSTRAINT FK_DepartmentShifts_Shift
FOREIGN KEY (ShiftID) REFERENCES Shift(ID),
ADD CONSTRAINT unique_department_shift UNIQUE (DepartmentID, ShiftID);


INSERT INTO Department (DepartmentName) VALUES
('Admin'),
('Engineering'),
('Sales'),
('Marketing'),
('Finance');

INSERT INTO Employee (Name, Email, Birthdate, HireDate, DepartmentID) VALUES
('Alice Johnson', 'alice.johnson@example.com', '1990-05-10', '2015-03-25', 1),
('Bob Smith', 'bob.smith@example.com', '1985-09-12', '2010-01-15', 2),
('Carol Davis', 'carol.davis@example.com', '1992-11-21', '2018-08-30', 3),
('David Martinez', 'david.martinez@example.com', '1988-06-13', '2016-02-17', 4),
('Eva Brown', 'eva.brown@example.com', '1995-01-27', '2020-05-05', 5);

INSERT INTO Shift (TimeIn, TimeOut) VALUES
('08:00:00', '16:00:00'),
('16:00:00', '00:00:00'),
('00:00:00', '08:00:00');

INSERT INTO User_Role (Role) VALUES
('Admin'),
('Employee');

INSERT INTO User (Username, Password, EmployeeID, RoleID) VALUES
('alice_admin', 'adminpassword123', 1, 1),
('bob_engineer', 'password123', 2, 2),
('carol_sales', 'salespassword123', 3, 2),
('david_marketing', 'marketing123', 4, 2),
('eva_finance', 'finance123', 5, 2);


INSERT INTO Attendance (EmployeeID, DepartmentID, ShiftID, InTime, InStatus, OutTime, OutStatus) VALUES
(1, 1, 1, '2024-11-01 08:00:00', 'On Time', '2024-11-01 16:00:00', 'Completed'),
(2, 2, 2, '2024-11-01 16:00:00', 'On Time', '2024-11-01 00:00:00', 'Completed'),
(3, 3, 3, '2024-11-01 00:00:00', 'Late', '2024-11-01 08:00:00', 'Completed'),
(4, 4, 1, '2024-11-01 08:30:00', 'On Time', '2024-11-01 16:30:00', 'Completed'),
(5, 5, 2, '2024-11-01 16:30:00', 'On Time', '2024-11-01 00:30:00', 'Completed');

INSERT INTO DepartmentShifts (DepartmentID, ShiftID) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 1),
(5, 2);
