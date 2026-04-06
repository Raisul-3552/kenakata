-- FILE: 03_stored_procedures.sql
-- Description: Stored procedures for business logic: confirming orders, assigning deliveries, and validation.
-- Database: MS SQL Server

-- 1. ConfirmOrder(OrderID, EmployeeID)
-- Updates order status to 'Confirmed' and assigns the employee who confirmed it.
CREATE PROCEDURE ConfirmOrder
    @OrderID INT,
    @EmployeeID INT
AS
BEGIN
    UPDATE [Order]
    SET OrderStatus = 'Confirmed',
        EmployeeID = @EmployeeID
    WHERE OrderID = @OrderID;
    
    PRINT 'Order status updated to Confirmed.';
END;
GO

-- 2. AssignDelivery(OrderID, DelManID)
-- Creates a delivery record for a confirmed order and assigns a delivery man.
CREATE PROCEDURE AssignDelivery
    @OrderID INT,
    @DelManID INT
AS
BEGIN
    INSERT INTO Delivery (OrderID, DelManID, DeliveryStatus, DeliveryDate)
    VALUES (@OrderID, @DelManID, 'Pending', NULL);
    
    PRINT 'Delivery record created and assigned to Delivery Man.';
END;
GO

-- 3. CancelDelivery(DeliveryID)
-- Updates delivery status to 'Cancelled'. 
-- Note: Inventory restoration is handled by a trigger (trg_AfterDeliveryCancelled).
CREATE PROCEDURE CancelDelivery
    @DeliveryID INT
AS
BEGIN
    UPDATE Delivery
    SET DeliveryStatus = 'Cancelled',
        DeliveryDate = GETDATE()
    WHERE DeliveryID = @DeliveryID;
    
    PRINT 'Delivery status updated to Cancelled.';
END;
GO

-- 4. ValidateCoupon(CouponCode)
-- Checks if a coupon is valid, active, and within the date range.
CREATE PROCEDURE ValidateCoupon
    @CouponCode VARCHAR(50)
AS
BEGIN
    SELECT * 
    FROM Coupon
    WHERE CouponCode = @CouponCode
      AND StartDate <= CAST(GETDATE() AS DATE)
      AND EndDate >= CAST(GETDATE() AS DATE);
END;
GO
