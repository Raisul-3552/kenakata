# Kenakata Online Shopping Platform - Architecture & Logic Documentation

This file provides a comprehensive overview of the `kenakata_online_shoping_platform` database architecture, the business logic behind each operation, and how they map to your application's Controller and Repository layers.

---

## 1. Database Overview & Schema Logic

The database is structured to support a fully-featured e-commerce platform with distinct role-based access control (Admin, Employee, Customer, DeliveryMan). It supports complex real-world use cases, such as discount coupons, limited-time offers, an integrated wallet system, and dynamic order tracking.

### 1.1 User & Role Management
- **`Admin` Table**: The superusers of the system. They have the highest level of authorization, primarily tasked with system monitoring and creating Employee profiles.
- **`Employee` Table**: Associated with an `Admin` creator. They act as the operational workforce—managing inventory (`Product`), applying promotional campaigns (`Offer`, `Coupon`), and confirming customer `Orders`.
- **`Customer` Table**: End-users who browse, add items to their `Cart`, place orders, and manage funds via their `Wallet`.
- **`DeliveryMan` Table**: Personnel responsible for fulfilling the logistical side of an order. They keep a `Status` (`Available`/`Busy`) that dictates their assignment availability.

### 1.2 Inventory & Product Management
- **`Category` Table**: Ensures products are categorized logically for front-end browsing and filtering.
- **`Product` & `ProductDetails` Tables**: 
  - `Product`: Stores primary lookup details like Name, Brand, Price, and active Stock, linking directly to the Employee who listed it.
  - `ProductDetails`: Uses a 1-to-1 relationship with `Product` to separate heavy descriptive data, images, and specifications, which helps speed up lightweight queries when simply listing products on the storefront.

### 1.3 Marketing & Promotional Mechanics
- **`Offer` Table**: Creates temporary, time-bound discounts directly applied to specific products. This enables "Flash Sales" and "Holiday Discounts."
- **`Coupon` Table**: A global code-based discount requiring action from the user during the checkout phase to redeem value. Requires rigorous date and validity checking.

### 1.4 The Shopping Lifecycle & Operations
- **`Cart` & `CartItem`**: Tied uniquely to a `Customer`. This represents an ephemeral state. As customers add items, `CartItem` tracks quantities. 
- **`Order` & `OrderItem`**: 
  - When checkout occurs, the `Cart` items are migrated into persistent state as an `Order` with associated `OrderItem`s.
  - Includes a strict constraint `OrderStatus IN ('Pending', 'Confirmed', 'Cancelled')`. 
- **`Delivery` Table**: Once an order is confirmed, a `Delivery` record maps that order to a `DeliveryMan`, shifting states from `Pending` -> `In Progress` -> `Delivered`.

### 1.5 Financial Logic (Wallet)
- **`Wallet` & `WalletTransaction`**: Allows customers to maintain an on-platform digital balance. 
- Transactions track if funds were added (`Credit`) or spent on an order (`Debit`). This is particularly useful for rapid refunds without relying on external payment gateway delays.

---

## 2. Core Controller Logic & Database Interactions

To properly handle application logic against this schema, controllers typically adhere to the following workflow patterns:

### 2.1 Authentication & Authorization Logic
- **Action**: Handles logins for all roles securely (e.g., matching the `$2y$12$...` hashed passwords).
- **Database Interaction**: Querying users across different tables based on login context (`Admin`, `Employee`, `Customer`, `DeliveryMan`).
- **Logic**: Setting session data or tokens that identify the user ID and user role, locking them into their respective dashboards.

### 2.2 Customer & Cart Operations
- **Add to Cart**: 
  - Validates `ProductID` and checks `Stock` in the `Product` table.
  - Inserts/Updates quantities in the `CartItem` table.
- **Checkout / Place Order**:
  - Validates all `CartItem`s against current active `Stock`.
  - Calculates `TotalAmount`, applying discounts via the `ValidateCoupon` stored procedure.
  - Initiates an SQL Transaction to ensure data integrity:
      1. Deduct wallet balance (Log to `WalletTransaction` as Debit, Update `Wallet` Balance).
      2. Insert into `Order`.
      3. Move `CartItem`s to `OrderItem`s using the generated `OrderID`.
      4. Deduct `Product` Stock.
      5. Clear `CartItem`s.
  - If any failure occurs during this step, the transaction rolls back.

### 2.3 Employee Operations
- **Product Management**: 
  - Employees create a `Product` entry.
  - The controller captures the new `ProductID` and immediately creates the associated `ProductDetails` row.
- **Order Confirmation**:
  - Invokes raw DB operation or calls the assigned stored procedure:
    `EXEC ConfirmOrder @OrderID, @EmployeeID`
  - This marks the `OrderStatus` from "Pending" to "Confirmed", assigning responsibility to the specific employee.

### 2.4 Logistics & Delivery Management
- **Assign Delivery**: 
  - An Admin or Employee retrieves an `Available` DeliveryMan.
  - Calls the Stored Procedure: 
    `EXEC AssignDelivery @OrderID, @DelManID`
  - Generates the record in the `Delivery` table and changes the `DeliveryMan.Status` from `Available` to `Busy`.
- **Status Updates**: The DeliveryMan portal interacts via controllers to update the `DeliveryStatus` to "Delivered."

---

## 3. Stored Procedures: Purpose & Usage

Your SQL Stored Procedures act as direct data encapsulations for repeated or complex logic:

1. **`ConfirmOrder (@OrderID, @EmployeeID)`**
   - **Reasoning**: Secures the database interaction by cleanly shifting state. Binds the confirming `EmployeeID` permanently to the `Order` trace for accountability and updates status in one go.

2. **`AssignDelivery (@OrderID, @DelManID)`**
   - **Reasoning**: Automatically sets up the initial mapping into the Delivery tracking table, defaulting gracefully to "Pending" without needing default assignment code in the controller.

3. **`ValidateCoupon (@CouponCode)`**
   - **Reasoning**: Ensures that the coupon is valid directly on the database execution level using `GETDATE()`. This avoids time-zone mismatching errors between the Application Server and the Database Server by letting SQL Server dictate "now".

---

## 4. Key Considerations & Potential Improvements

When interacting with this database in your codebase (like a Laravel Controller):

- **Transactions are critical**: You absolutely must wrap Cart-to-Order operations and Wallet deductions inside `DB::transaction()` blocks. If order insertion fails but your wallet deduct function succeeded, your funds drop out of sync with purchases.
- **Role Scalability**: In the future, keeping Admin, Employee, Customer, and DeliveryMan as entirely separate tables makes single-point unified-authentication more complex. Often systems use a single `users` table mapped to a `roles` table, but the current segregation simplifies rigid relationships.
- **Concurrency**: Use locking mechanisms (like `sharedLock()` or `lockForUpdate()` in Eloquent / ORMs) when modifying `Product.Stock` so two simultaneous buyers don't check out the exact same final product unit.
