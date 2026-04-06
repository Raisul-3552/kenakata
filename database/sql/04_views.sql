-- FILE: 04_views.sql
-- Description: Reporting views for quick access to status, orders, and history.
-- Database: MS SQL Server

-- 1. vw_ProductWithOffer
-- Products with their active offer details (if any).
CREATE VIEW vw_ProductWithOffer AS
SELECT 
    p.ProductID,
    p.ProductName,
    p.Brand,
    p.Price AS OriginalPrice,
    o.DiscountAmount,
    (p.Price - ISNULL(o.DiscountAmount, 0)) AS DiscountedPrice,
    o.StartDate,
    o.EndDate
FROM Product p
LEFT JOIN Offer o ON p.ProductID = o.ProductID
  AND CAST(GETDATE() AS DATE) BETWEEN o.StartDate AND o.EndDate;
GO

-- 2. vw_OrderSummary
-- Detailed summary of orders, including the customer and the clerk who handled it.
CREATE VIEW vw_OrderSummary AS
SELECT 
    o.OrderID,
    c.CustomerName,
    e.EmployeeName AS ConfirmedBy,
    o.OrderStatus,
    o.TotalAmount,
    o.OrderDate,
    o.Address
FROM [Order] o
JOIN Customer c ON o.CustomerID = c.CustomerID
LEFT JOIN Employee e ON o.EmployeeID = e.EmployeeID;
GO

-- 3. vw_DeliveryStatus
-- Status of deliveries with corresponding order information and delivery man.
CREATE VIEW vw_DeliveryStatus AS
SELECT 
    d.DeliveryID,
    o.OrderID,
    o.OrderStatus,
    dm.DelManName,
    d.DeliveryStatus,
    d.DeliveryDate,
    o.Address AS ShippingAddress
FROM Delivery d
JOIN [Order] o ON d.OrderID = o.OrderID
JOIN DeliveryMan dm ON d.DelManID = dm.DelManID;
GO

-- 4. vw_CustomerOrderHistory
-- Complete order history for each customer.
CREATE VIEW vw_CustomerOrderHistory AS
SELECT 
    c.CustomerID,
    c.CustomerName,
    o.OrderID,
    o.OrderDate,
    o.TotalAmount,
    o.OrderStatus,
    (SELECT COUNT(*) FROM OrderItem oi WHERE oi.OrderID = o.OrderID) AS ItemsCount
FROM Customer c
JOIN [Order] o ON c.CustomerID = o.CustomerID;
GO
