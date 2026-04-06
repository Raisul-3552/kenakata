-- FILE: 01_create_tables.sql
-- Description: Create all tables for the Kenakata e-commerce system with relationships.
-- Database: MS SQL Server

-- 1. Category Table
CREATE TABLE Category (
    CategoryID INT PRIMARY KEY IDENTITY(1,1),
    CategoryName VARCHAR(100) NOT NULL,
    Description TEXT
);

-- 2. Admin Table
CREATE TABLE [Admin] (
    AdminID INT PRIMARY KEY IDENTITY(1,1),
    AdminName VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    [Password] VARCHAR(255) NOT NULL
);

-- 3. Employee Table
CREATE TABLE Employee (
    EmployeeID INT PRIMARY KEY IDENTITY(1,1),
    AdminID INT NOT NULL,
    EmployeeName VARCHAR(100) NOT NULL,
    Phone VARCHAR(15) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    [Password] VARCHAR(255) NOT NULL,
    Address VARCHAR(255) NOT NULL,
    CONSTRAINT FK_Employee_Admin FOREIGN KEY (AdminID) REFERENCES [Admin](AdminID)
);

-- 4. Customer Table
CREATE TABLE Customer (
    CustomerID INT PRIMARY KEY IDENTITY(1,1),
    CustomerName VARCHAR(100) NOT NULL,
    Phone VARCHAR(15) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    [Password] VARCHAR(255) NOT NULL,
    Address VARCHAR(255) NOT NULL
);

-- 5. DeliveryMan Table
CREATE TABLE DeliveryMan (
    DelManID INT PRIMARY KEY IDENTITY(1,1),
    DelManName VARCHAR(100) NOT NULL,
    Phone VARCHAR(15) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    [Password] VARCHAR(255) NOT NULL,
    Address VARCHAR(255) NOT NULL
);

-- 6. Product Table
CREATE TABLE Product (
    ProductID INT PRIMARY KEY IDENTITY(1,1),
    EmployeeID INT NOT NULL,
    CategoryID INT NOT NULL,
    ProductName VARCHAR(150) NOT NULL,
    Brand VARCHAR(100) NOT NULL,
    Price DECIMAL(18,2) NOT NULL,
    Stock INT NOT NULL,
    CONSTRAINT FK_Product_Employee FOREIGN KEY (EmployeeID) REFERENCES Employee(EmployeeID),
    CONSTRAINT FK_Product_Category FOREIGN KEY (CategoryID) REFERENCES Category(CategoryID)
);

-- 7. ProductDetails Table (One-to-One connection with Product)
CREATE TABLE ProductDetails (
    ProductID INT PRIMARY KEY,
    Description TEXT NOT NULL,
    Specification TEXT NOT NULL,
    Warranty VARCHAR(100) NULL,
    Image VARCHAR(255) NULL,
    CONSTRAINT FK_ProductDetails_Product FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- 8. Offer Table
CREATE TABLE Offer (
    OfferID INT PRIMARY KEY IDENTITY(1,1),
    ProductID INT NOT NULL,
    DiscountAmount DECIMAL(18,2) NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE NOT NULL,
    CONSTRAINT FK_Offer_Product FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- 9. Coupon Table
CREATE TABLE Coupon (
    CouponID INT PRIMARY KEY IDENTITY(1,1),
    CouponCode VARCHAR(50) UNIQUE NOT NULL,
    DiscountAmount DECIMAL(18,2) NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE NOT NULL
);

-- 10. Order Table
CREATE TABLE [Order] (
    OrderID INT PRIMARY KEY IDENTITY(1,1),
    CustomerID INT NOT NULL,
    EmployeeID INT NULL,  -- Can be null initially (Pending)
    CouponID INT NULL,
    OrderStatus VARCHAR(50) DEFAULT 'Pending' CHECK (OrderStatus IN ('Pending', 'Confirmed', 'Cancelled')),
    TotalAmount DECIMAL(18,2) NOT NULL,
    OrderDate DATE NOT NULL,
    Address VARCHAR(255) NOT NULL,
    CONSTRAINT FK_Order_Customer FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    CONSTRAINT FK_Order_Employee FOREIGN KEY (EmployeeID) REFERENCES Employee(EmployeeID),
    CONSTRAINT FK_Order_Coupon FOREIGN KEY (CouponID) REFERENCES Coupon(CouponID)
);

-- 11. OrderItem Table
CREATE TABLE OrderItem (
    OrderItemID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT NOT NULL,
    ProductID INT NOT NULL,
    Quantity INT NOT NULL,
    UnitPrice DECIMAL(18,2) NOT NULL,
    CONSTRAINT FK_OrderItem_Order FOREIGN KEY (OrderID) REFERENCES [Order](OrderID),
    CONSTRAINT FK_OrderItem_Product FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- 12. Delivery Table
CREATE TABLE Delivery (
    DeliveryID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT NOT NULL,
    DelManID INT NOT NULL,
    DeliveryStatus VARCHAR(50) DEFAULT 'Pending' CHECK (DeliveryStatus IN ('Pending', 'In Progress', 'Delivered', 'Cancelled')),
    DeliveryDate DATE NULL,
    CONSTRAINT FK_Delivery_Order FOREIGN KEY (OrderID) REFERENCES [Order](OrderID),
    CONSTRAINT FK_Delivery_DelMan FOREIGN KEY (DelManID) REFERENCES DeliveryMan(DelManID)
);
