# Kenakata Project Flow Documentation

## 1. Purpose of This README

This document explains end-to-end feature flow in the project:

- Which frontend view is loaded
- Which API route is called
- Which controller method handles it
- Which SQL query/procedure/trigger is used
- Which transaction block is used and why

This is meant for maintenance, demo explanation, and onboarding.

---

## 2. High-Level Architecture

### 2.1 Tech Stack

- Backend: Laravel (PHP)
- Frontend: Blade views with inline JavaScript `fetch` API calls
- Database: MS SQL Server
- Auth: Laravel Sanctum tokens (token creation on model instances)

### 2.2 Main Layers

- View layer: `resources/views/**`
- Web route layer (view rendering): `routes/web.php`
- API route layer: `routes/api.php`
- Business logic/API: `app/Http/Controllers/Api/**`
- SQL server-side components: `database/sql/03_stored_procedures.sql`, `database/sql/05_triggers.sql`

### 2.3 Frontend Code Location

Frontend code is mostly inside Blade files (HTML + JS in the same file):

- Auth: `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`
- Admin pages: `resources/views/admin/*.blade.php`
- Employee pages: `resources/views/employee/*.blade.php`
- Customer pages: `resources/views/customer/*.blade.php`
- Delivery pages: `resources/views/deliveryman/*.blade.php`
- Shared JS helpers and API base URL: `resources/views/layouts/app.blade.php`

---

## 3. View Routing (Which View Is Called)

Defined in `routes/web.php`.

### 3.1 Public

- `/login` -> `auth.login`
- `/register` -> `auth.register`

### 3.2 Admin

- `/admin/dashboard` -> `admin.dashboard`
- `/admin/profile` -> `admin.profile`
- `/admin/admins` -> `admin.admins`

### 3.3 Employee

- `/employee/dashboard` -> `employee.dashboard`
- `/employee/profile` -> `employee.profile`
- `/employee/products` -> `employee.products`
- `/employee/coupons` -> `employee.coupons`
- `/employee/deliverymen` -> `employee.deliverymen`

### 3.4 Customer

- `/customer/dashboard` -> `customer.home`
- `/customer/profile` -> `customer.profile`
- `/customer/cart` -> `customer.cart`
- `/customer/orders` -> `customer.orders`
- `/customer/wallet` -> `customer.wallet`

### 3.5 DeliveryMan

- `/deliveryman/dashboard` -> `deliveryman.dashboard`
- `/deliveryman/profile` -> `deliveryman.profile`

---

## 4. API Routing (Which Controller Method Is Called)

Defined in `routes/api.php`.

### 4.1 Public APIs

- `POST /api/login` -> `AuthController@login`
- `POST /api/register` -> `AuthController@register`
- Role-specific login/register aliases -> `AuthController`
- `GET /api/products` -> `ProductController@index`
- `GET /api/products/{id}` -> `ProductController@show`

### 4.2 Admin APIs

- Employee management -> `AdminController@getEmployees/addEmployee/deleteEmployee`
- Customer search -> `AdminController@searchCustomers`
- Admin management -> `AdminController@getAdmins/addAdmin/deleteAdmin`
- Dashboard stats/profile/password -> `AdminController`

### 4.3 Employee APIs

- Profile/password -> `EmployeeController`
- Product/offer/coupon CRUD -> `EmployeeController`
- Order handling -> `EmployeeController@getOrders/confirmOrder/cancelOrder/assignDelivery`
- DeliveryMan management + availability -> `EmployeeController`
- Categories -> `EmployeeController@getCategories`

### 4.4 Customer APIs

- Profile/password -> `CustomerController`
- Cart APIs -> `CustomerController@getCart/syncCart/addToCart/updateCartItem/removeCartItem/clearCart`
- Order APIs -> `CustomerController@placeOrder/getOrderHistory/cancelOrder`
- Coupon validation -> `CustomerController@validateCoupon`
- Wallet APIs -> `CustomerController@getWallet/addWalletBalance`

### 4.5 DeliveryMan APIs

- Deliveries list/status update -> `DeliveryController@getAssignedDeliveries/updateStatus`
- Profile/password/status toggle -> `DeliveryController`

### 4.6 Common

- `POST /api/logout` -> `AuthController@logout`

---

## 5. Feature-by-Feature Flow Map

This section explains each major feature from view to SQL.

### 5.1 Authentication

#### 5.1.1 Login

- Frontend view: `resources/views/auth/login.blade.php`
- API called: `POST /api/login`
- Controller: `AuthController@login`
- Core logic:
  1. Find account by email using raw SQL across role tables via `InteractsWithAccountEmails::findAccountByEmail`
  2. Verify password hash with `Hash::check`
  3. Hydrate tokenable model instance (`makeTokenableUser`) from DB row
  4. Generate Sanctum token
- SQL used:
  - `SELECT * FROM [Admin|Employee|Customer|DeliveryMan] WHERE Email = ?`

#### 5.1.2 Customer Register

- Frontend view: `resources/views/auth/register.blade.php`
- API called: `POST /api/register` (and alias `/api/customer/register`)
- Controller: `AuthController@register`
- Transaction: Yes (`DB::beginTransaction`)
- SQL used:
  - `INSERT INTO Customer (...) VALUES (...)`
  - `SELECT TOP 1 * FROM Customer WHERE Email = ?`

#### 5.1.3 DeliveryMan Register

- API called: `POST /api/deliveryman/register`
- Controller: `AuthController@deliveryManRegister`
- Transaction: Yes
- SQL used:
  - `INSERT INTO DeliveryMan (...) VALUES (...)`
  - `SELECT TOP 1 * FROM DeliveryMan WHERE Email = ?`

---

### 5.2 Product Browsing (Customer)

- Frontend view: `resources/views/customer/home.blade.php`
- API called: `GET /api/products`
- Controller: `ProductController@index`
- SQL used:
  - Product list with category/details via joins
  - Offers loaded separately from `Offer`
- UI logic:
  - Category filter is client-side (from loaded products)
  - Active offer computed by date in JS

No transaction and no stored procedure for this read-only flow.

---

### 5.3 Customer Cart

#### 5.3.1 Cart Load

- Views: `resources/views/customer/cart.blade.php`, `resources/views/layouts/customer.blade.php`
- API: `GET /api/customer/cart`
- Controller: `CustomerController@getCart`
- SQL:
  - `SELECT * FROM Cart WHERE CustomerID = ?` (or create)
  - Join query over `CartItem`, `Product`, `ProductDetails`

#### 5.3.2 Add Item

- API: `POST /api/customer/cart/items`
- Controller: `CustomerController@addToCart`
- SQL:
  - Check item existence by `CartID + ProductID`
  - `UPDATE` quantity if exists, `INSERT` if not

#### 5.3.3 Update/Remove/Clear

- APIs:
  - `PUT /api/customer/cart/items/{productId}`
  - `DELETE /api/customer/cart/items/{productId}`
  - `DELETE /api/customer/cart`
- Controller: `CustomerController@updateCartItem/removeCartItem/clearCart`

#### 5.3.4 Sync Cart

- API: `POST /api/customer/cart/sync`
- Controller: `CustomerController@syncCart`
- Transaction: Yes
- SQL:
  - Delete old cart rows
  - Insert current rows from payload

---

### 5.4 Customer Checkout / Place Order

- View entry: `resources/views/customer/cart.blade.php`
- API: `POST /api/customer/orders`
- Controller: `CustomerController@placeOrder`
- Transaction: Yes (critical)

#### Logic

1. Resolve wallet
2. Check balance
3. Choose least busy employee (active load = not Delivered/Cancelled)
4. Insert order as Pending with assigned EmployeeID
5. For each item:
   - check product exists
   - check stock
   - decrement stock
   - insert `OrderItem`
6. Clear cart items
7. Deduct wallet balance
8. Insert wallet transaction (Debit)

#### SQL blocks involved

- `INSERT INTO [Order]`
- `SELECT TOP 1 OrderID ...`
- `SELECT * FROM Product WHERE ProductID = ?`
- `UPDATE Product SET Stock = Stock - ?`
- `INSERT INTO OrderItem`
- `DELETE FROM CartItem`
- `UPDATE Wallet`
- `INSERT INTO WalletTransaction`

---

### 5.5 Customer Order History / Cancellation

#### 5.5.1 Order History

- View: `resources/views/customer/orders.blade.php`
- API: `GET /api/customer/orders`
- Controller: `CustomerController@getOrderHistory`
- SQL:
  - Order list query by `CustomerID`
  - Separate query for items and delivery details per order

#### 5.5.2 Cancel Order

- API: `POST /api/customer/orders/{id}/cancel`
- Controller: `CustomerController@cancelOrder`
- Transaction: Yes

#### Logic

1. Ensure order belongs to customer and status is Pending
2. Restore stock from `OrderItem`
3. Refund wallet and add Credit transaction
4. Mark order as Cancelled

---

### 5.6 Wallet

#### 5.6.1 Get Wallet

- View: `resources/views/customer/wallet.blade.php`
- API: `GET /api/customer/wallet`
- Controller: `CustomerController@getWallet`
- SQL:
  - wallet row
  - latest 50 transactions

#### 5.6.2 Add Balance

- API: `POST /api/customer/wallet/add-balance`
- Controller: `CustomerController@addWalletBalance`
- Transaction: Yes
- SQL:
  - update balance
  - insert Credit transaction

---

### 5.7 Admin Features

#### 5.7.1 Employee CRUD

- View: `resources/views/admin/dashboard.blade.php`
- APIs: `/api/admin/employees` (GET/POST/DELETE)
- Controller: `AdminController@getEmployees/addEmployee/deleteEmployee`

#### Important deletion logic

`deleteEmployee` now reassigns that employee's active (not Delivered/Cancelled) orders to least-busy remaining employees before deletion.

- Transaction: Yes
- If no other employee exists and active orders exist, delete is blocked.

#### 5.7.2 Admin CRUD + Profile + Stats

- Views: `resources/views/admin/admins.blade.php`, `resources/views/admin/profile.blade.php`
- APIs:
  - `/api/admin/all-admins`
  - `/api/admin/profile`
  - `/api/admin/dashboard-stats`
- Controller: `AdminController`
- Stats query computes employee count, customer count, product count, order count, confirmed revenue.

---

### 5.8 Employee Features

#### 5.8.1 Product Management

- View: `resources/views/employee/products.blade.php`
- APIs: `/api/employee/products`, `/api/employee/categories`, `/api/employee/offers`
- Controller: `EmployeeController@getProducts/addProduct/editProduct/deleteProduct/getCategories/addOffer/deleteOffer`

`addProduct` uses transaction to insert into `Product` and `ProductDetails` atomically.

#### 5.8.2 Coupon Management

- View: `resources/views/employee/coupons.blade.php`
- APIs: `/api/employee/coupons` and `/api/employee/offers`
- Controller: `EmployeeController@getCoupons/addCoupon/deleteCoupon/getOffers/deleteOffer`

#### 5.8.3 Order Management

- View: `resources/views/employee/dashboard.blade.php`
- APIs:
  - `GET /api/employee/orders`
  - `POST /api/employee/orders/{id}/confirm`
  - `POST /api/employee/orders/{id}/cancel`
  - `POST /api/employee/orders/{id}/assign-delivery`

#### Logic notes

- Employee sees only orders assigned to that EmployeeID
- Pending orders are rebalanced across employees on fetch
- Confirm updates order status (transaction)
- Cancel restores stock and refunds customer wallet (transaction)

#### 5.8.4 DeliveryMan Management

- View: `resources/views/employee/deliverymen.blade.php`
- APIs:
  - `/api/employee/deliverymen`
  - `/api/employee/deliverymen/all`
  - `/api/employee/deliverymen/available`
- Controller: `EmployeeController`

---

### 5.9 DeliveryMan Features

#### 5.9.1 Delivery Dashboard

- View: `resources/views/deliveryman/dashboard.blade.php`
- APIs:
  - `GET /api/deliveryman/deliveries`
  - `POST /api/deliveryman/deliveries/{id}/update-status`
- Controller: `DeliveryController@getAssignedDeliveries/updateStatus`

`updateStatus` also updates related order state and rider availability as needed.

#### 5.9.2 DeliveryMan Profile

- View: `resources/views/deliveryman/profile.blade.php`
- APIs:
  - `/api/deliveryman/profile`
  - `/api/deliveryman/profile/change-password`
  - `/api/deliveryman/profile/toggle-status`

---

## 6. Stored Procedures (Where They Are Triggered)

Defined in `database/sql/03_stored_procedures.sql`.

### 6.1 `AssignDelivery`

- Called by: `EmployeeController@assignDelivery`
- Invocation: `EXEC AssignDelivery ?, ?`
- Purpose: create `Delivery` row for an order with a rider.

### 6.2 `ValidateCoupon`

- Called by: `CustomerController@validateCoupon`
- Invocation: `EXEC ValidateCoupon ?`
- Purpose: date-valid coupon check in SQL Server.

### 6.3 `ConfirmOrder`, `CancelDelivery`

- Present in SQL file.
- Current controllers mostly use direct SQL updates for order confirmation and delivery cancellation logic.

---

## 7. Triggers (Automatic DB Logic)

Defined in `database/sql/05_triggers.sql`.

### 7.1 `trg_AfterOrderConfirmed`

- Fires after update on `[Order]`
- If status becomes `Confirmed`, deducts stock using `OrderItem` quantities.

### 7.2 `trg_AfterDeliveryCancelled`

- Fires after update on `Delivery`
- If `DeliveryStatus` becomes `Cancelled`, restores stock.

---

## 8. Transaction Usage Summary

### 8.1 Uses Transactions

- `AuthController@register`
- `AuthController@deliveryManRegister`
- `CustomerController@syncCart`
- `CustomerController@placeOrder`
- `CustomerController@cancelOrder`
- `CustomerController@addWalletBalance`
- `EmployeeController@addProduct`
- `EmployeeController@confirmOrder`
- `EmployeeController@cancelOrder`
- `AdminController@deleteEmployee`

### 8.2 Why Transactions Are Used

- Prevent partial writes across money, stock, and order tables
- Keep consistency when one business action touches multiple tables
- Ensure rollback on failure

---

## 9. Important Implementation Notes

1. API base URL is centralized in `resources/views/layouts/app.blade.php` as `API_URL = '/api'`.
2. Customer add-to-cart from product cards is implemented in `resources/views/layouts/customer.blade.php` and customer pages.
3. Authentication uses raw SQL for account lookup, then hydrates tokenable model objects only for Sanctum token creation.
4. Least-busy assignment is applied in:
   - Customer order placement
   - Employee pending-order rebalance
   - Admin employee delete reassignment

---

## 10. Quick Feature Trace Examples

### Example A: Customer places order

- View: `customer/cart.blade.php`
- API: `POST /api/customer/orders`
- Controller: `CustomerController@placeOrder`
- SQL: Order + items + stock + wallet + wallet transaction
- Transaction: Yes
- Procedure: No
- Trigger that may run later: `trg_AfterOrderConfirmed` when order is confirmed

### Example B: Employee assigns rider

- View: `employee/dashboard.blade.php`
- API: `POST /api/employee/orders/{id}/assign-delivery`
- Controller: `EmployeeController@assignDelivery`
- Procedure: `AssignDelivery`
- SQL after procedure: update rider status to Busy
- Transaction: No explicit app transaction in this method

### Example C: DeliveryMan marks delivered

- View: `deliveryman/dashboard.blade.php`
- API: `POST /api/deliveryman/deliveries/{id}/update-status`
- Controller: `DeliveryController@updateStatus`
- SQL: update delivery, update order status, update rider status
- Trigger: none for Delivered in current SQL trigger file

---

If needed, a follow-up can add sequence diagrams per role (Admin, Employee, Customer, DeliveryMan) for viva/presentation use.
