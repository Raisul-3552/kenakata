-- FILE: 05_triggers.sql
-- Description: Automating inventory management based on status changes.
-- Database: MS SQL Server

-- 1. trg_AfterOrderConfirmed
-- Reduce product stock when an order is updated to 'Confirmed'.
CREATE TRIGGER trg_AfterOrderConfirmed
ON [Order]
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Check if OrderStatus is updated to 'Confirmed'
    IF UPDATE(OrderStatus)
    BEGIN
        DECLARE @OrderID INT, @NewStatus VARCHAR(50);
        
        -- Loop through updated orders (handling multiple rows)
        DECLARE order_cursor CURSOR FOR
        SELECT OrderID, OrderStatus FROM inserted;
        
        OPEN order_cursor;
        FETCH NEXT FROM order_cursor INTO @OrderID, @NewStatus;
        
        WHILE @@FETCH_STATUS = 0
        BEGIN
            -- Reduce stock for each product in the confirmed order
            IF @NewStatus = 'Confirmed'
            BEGIN
                UPDATE Product
                SET Stock = Product.Stock - oi.Quantity
                FROM Product
                INNER JOIN OrderItem oi ON Product.ProductID = oi.ProductID
                WHERE oi.OrderID = @OrderID;
            END
            
            FETCH NEXT FROM order_cursor INTO @OrderID, @NewStatus;
        END;
        
        CLOSE order_cursor;
        DEALLOCATE order_cursor;
    END
END;
GO

-- 2. trg_AfterDeliveryCancelled
-- Restore product stock when a delivery is updated to 'Cancelled'.
CREATE TRIGGER trg_AfterDeliveryCancelled
ON Delivery
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Check if DeliveryStatus is updated to 'Cancelled'
    IF UPDATE(DeliveryStatus)
    BEGIN
        DECLARE @DeliveryID INT, @NewDeliveryStatus VARCHAR(50), @OrderID INT;
        
        DECLARE del_cursor CURSOR FOR
        SELECT DeliveryID, OrderID, DeliveryStatus FROM inserted;
        
        OPEN del_cursor;
        FETCH NEXT FROM del_cursor INTO @DeliveryID, @OrderID, @NewDeliveryStatus;
        
        WHILE @@FETCH_STATUS = 0
        BEGIN
            -- Restore stock if delivery was cancelled
            IF @NewDeliveryStatus = 'Cancelled'
            BEGIN
                UPDATE Product
                SET Stock = Product.Stock + oi.Quantity
                FROM Product
                INNER JOIN OrderItem oi ON Product.ProductID = oi.ProductID
                WHERE oi.OrderID = @OrderID;
            END
            
            FETCH NEXT FROM del_cursor INTO @DeliveryID, @OrderID, @NewDeliveryStatus;
        END;
        
        CLOSE del_cursor;
        DEALLOCATE del_cursor;
    END
END;
GO
