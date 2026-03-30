create database kenakata_online_shoping_platform
use kenakata_online_shoping_platform


CREATE TABLE Admin (
    AdminID INT PRIMARY KEY,
    AdminName VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL
);

CREATE TABLE EmploymentCode (
    CodeID INT PRIMARY KEY IDENTITY(1,1),
    RegCode VARCHAR(100) UNIQUE NOT NULL, 
    AdminID INT NOT NULL,                 
    IsUsed BIT DEFAULT 0,                 -- 0 = Available, 1 = Used
    CreatedAt DATETIME DEFAULT GETDATE(),

    CONSTRAINT FK_Code_Admin FOREIGN KEY (AdminID) 
        REFERENCES Admin(AdminID)
);




CREATE TABLE Customer (
    CustomerID INT PRIMARY KEY IDENTITY(1,1),
    CustomerName VARCHAR(255) NOT NULL,
    Phone VARCHAR(20) NOT NULL,
    Email VARCHAR(255) UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Address VARCHAR(500) NOT NULL
);



CREATE TABLE DeliveryMan (
    DelManID INT PRIMARY KEY IDENTITY(1,1),
    DelManName VARCHAR(255) NOT NULL,
    Phone VARCHAR(20) NOT NULL,
    Email VARCHAR(255) UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Address VARCHAR(500) NOT NULL
);




CREATE TABLE UserSessions (
    SessionID NVARCHAR(255) PRIMARY KEY,
    UserID INT NOT NULL,                -- The ID from the specific actor table
    UserType NVARCHAR(50) NOT NULL,     -- 'Admin', 'Employee', 'Customer', 'DeliveryMan'
    IPAddress NVARCHAR(45),
    UserAgent NVARCHAR(MAX),
    Payload NVARCHAR(MAX) NOT NULL,
    LastActivity INT NOT NULL,
    CreatedAt DATETIME DEFAULT GETDATE()
);