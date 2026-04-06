-- FILE: 02_sample_data.sql
-- Description: INSERT sample data for testing all roles and functionality in Kenakata.
-- All passwords are set to "password" using Laravel's bcrypt hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- 1. Insert Categories
INSERT INTO Category (CategoryName, Description) VALUES
('Electronics', 'Gadgets, appliances, and tech accessories.'),
('Clothing', 'Fashionable apparel for men and women.'),
('Home & Garden', 'Furniture, decor, and gardening tools.'),
('Books', 'Educational, fiction, and non-fiction books.'),
('Beauty', 'Cosmetics, skincare, and personal care products.');

-- 2. Insert Admin
INSERT INTO [Admin] (AdminName, Email, [Password]) VALUES
('Root Admin', 'admin@kenakata.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 3. Insert Employees
INSERT INTO Employee (AdminID, EmployeeName, Phone, Email, [Password], Address) VALUES
(1, 'John Doe', '01711223344', 'john@kenakata.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dhaka, Bangladesh'),
(1, 'Jane Smith', '01811223344', 'jane@kenakata.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Chittagong, Bangladesh'),
(1, 'Bob Wilson', '01911223344', 'bob@kenakata.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sylhet, Bangladesh');

-- 4. Insert Customers
INSERT INTO Customer (CustomerName, Phone, Email, [Password], Address) VALUES
('Alice Brown', '01511223344', 'alice@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dhanmondi, Dhaka'),
('Charlie Davis', '01611223344', 'charlie@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Uttara, Dhaka'),
('Emma White', '01311223344', 'emma@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Banani, Dhaka'),
('Frank Miller', '01411223344', 'frank@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mirpur, Dhaka'),
('Grace Lee', '01211223344', 'grace@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gulshan, Dhaka');

-- 5. Insert DeliveryMen
INSERT INTO DeliveryMan (DelManName, Phone, Email, [Password], Address) VALUES
('Speedy Sam', '01111223344', 'sam@delivery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mohakhali, Dhaka'),
('Reliable Ray', '01011223344', 'ray@delivery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Farmgate, Dhaka'),
('Quick Quinn', '01222334455', 'quinn@delivery.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Badda, Dhaka');

-- 6. Insert Products
INSERT INTO Product (EmployeeID, CategoryID, ProductName, Brand, Price, Stock) VALUES
(1, 1, 'iPhone 15 Pro', 'Apple', 120000.00, 50),
(1, 1, 'Samsung S24 Ultra', 'Samsung', 110000.00, 40),
(2, 2, 'Polo T-Shirt', 'Uniqlo', 1500.00, 100),
(2, 2, 'Denim Jeans', 'Levis', 3500.00, 80),
(3, 3, 'Ergonomic Chair', 'Steelcase', 25000.00, 20),
(3, 3, 'Smart LED Bulb', 'Xiaomi', 800.00, 200),
(1, 4, 'Clean Code', 'Prentice Hall', 2500.00, 30),
(1, 4, 'Atomic Habits', 'Penguin', 1200.00, 150),
(2, 5, 'Sunscreen SPF 50', 'Neutrogena', 1800.00, 60),
(2, 5, 'Moisturizer', 'CeraVe', 2200.00, 45);

-- 7. Insert ProductDetails
INSERT INTO ProductDetails (ProductID, Description, Specification, Warranty, Image) VALUES
(1, 'Latest iPhone with Titanium build.', '6.1 inch, A17 Pro Chip, 256GB', '1 Year Apple Warranty', 'iphone_15.jpg'),
(2, 'Ultimate Android experience with AI.', '6.8 inch, SD 8 Gen 3, 512GB', '1 Year Samsung Warranty', 's24_ultra.jpg'),
(3, 'Cotton polo shirt for casual wear.', '100% Cotton, Breathable', 'No Warranty', 'polo_tshirt.jpg'),
(4, 'Classic denim jeans with stretch.', 'Denim, Regular Fit', 'No Warranty', 'denim_jeans.jpg'),
(5, 'Office chair with lumbar support.', 'Adjustable Height, Mesh Back', '2 Years Warranty', 'office_chair.jpg'),
(6, 'Wi-Fi enabled smart LED bulb.', '9W, RGB, Google Home Sync', '6 Months Warranty', 'smart_bulb.jpg'),
(7, 'A handbook of agile software craftsmanship.', 'Hardcover, Robert C. Martin', 'No Warranty', 'clean_code.jpg'),
(8, 'An easy and proven way to build good habits.', 'Paperback, James Clear', 'No Warranty', 'atomic_habits.jpg'),
(9, 'Ultra sheer dry-touch sunscreen.', 'SPF 50, Non-greasy, 88ml', 'Exp: 12/2025', 'sunscreen.jpg'),
(10, 'Daily moisturizing lotion for dry skin.', 'Ceramides, Hyaluronic Acid', 'Exp: 06/2025', 'moisturizer.jpg');

-- 8. Insert Coupons
INSERT INTO Coupon (CouponCode, DiscountAmount, StartDate, EndDate) VALUES
('WELCOME10', 500.00, '2024-01-01', '2026-12-31'),
('EID2024', 1000.00, '2024-04-01', '2024-05-31'),
('DUMMY50', 50.00, '2024-01-01', '2026-12-31');

-- 9. Insert Offers
INSERT INTO Offer (ProductID, DiscountAmount, StartDate, EndDate) VALUES
(1, 5000.00, '2024-04-01', '2024-04-30'),
(3, 200.00, '2024-04-01', '2024-04-15'),
(6, 100.00, '2024-04-01', '2024-05-01');

-- 10. Insert Orders
-- Order 1: Confirmed
INSERT INTO [Order] (CustomerID, EmployeeID, CouponID, OrderStatus, TotalAmount, OrderDate, Address) VALUES
(1, 1, 1, 'Confirmed', 119500.00, '2024-04-01', 'Dhanmondi, Dhaka');
INSERT INTO OrderItem (OrderID, ProductID, Quantity, UnitPrice) VALUES (1, 1, 1, 120000.00);

-- Order 2: Pending
INSERT INTO [Order] (CustomerID, EmployeeID, CouponID, OrderStatus, TotalAmount, OrderDate, Address) VALUES
(2, NULL, NULL, 'Pending', 1500.00, '2024-04-02', 'Uttara, Dhaka');
INSERT INTO OrderItem (OrderID, ProductID, Quantity, UnitPrice) VALUES (2, 3, 1, 1500.00);

-- Order 3: Cancelled
INSERT INTO [Order] (CustomerID, EmployeeID, CouponID, OrderStatus, TotalAmount, OrderDate, Address) VALUES
(3, 2, NULL, 'Cancelled', 3700.00, '2024-04-03', 'Banani, Dhaka');
INSERT INTO OrderItem (OrderID, ProductID, Quantity, UnitPrice) VALUES (3, 7, 1, 2500.00), (3, 8, 1, 1200.00);

-- Order 4: Confirmed
INSERT INTO [Order] (CustomerID, EmployeeID, CouponID, OrderStatus, TotalAmount, OrderDate, Address) VALUES
(4, 1, NULL, 'Confirmed', 110000.00, '2024-04-04', 'Mirpur, Dhaka');
INSERT INTO OrderItem (OrderID, ProductID, Quantity, UnitPrice) VALUES (4, 2, 1, 110000.00);

-- Order 5: Pending
INSERT INTO [Order] (CustomerID, EmployeeID, CouponID, OrderStatus, TotalAmount, OrderDate, Address) VALUES
(5, NULL, 3, 'Pending', 750.00, '2024-04-05', 'Gulshan, Dhaka');
INSERT INTO OrderItem (OrderID, ProductID, Quantity, UnitPrice) VALUES (5, 6, 1, 800.00);
