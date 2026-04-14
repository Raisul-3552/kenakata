
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
