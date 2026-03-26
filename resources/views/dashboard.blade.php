<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Kenakata</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: #667eea;
            color: #fff;
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar form button {
            background: #ff5a5f;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .navbar form button:hover {
            background: #e14c50;
        }

        /* Main content */
        .container {
            padding: 40px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 30px 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        .card h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 12px;
        }

        .card p {
            font-size: 16px;
            color: #555;
        }

        .welcome-card {
            grid-column: 1 / -1;
        }

        @media (max-width: 500px) {
            .navbar h1 {
                font-size: 20px;
            }

            .card h2 {
                font-size: 20px;
            }

            .card p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>Kenakata</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <!-- Dashboard Content -->
    <div class="container">
        <!-- Welcome Card -->
        <div class="card welcome-card">
            <h2>Welcome, {{ auth()->user()->name }}!</h2>
            <p>You are now logged into your Kenakata dashboard.</p>
        </div>

        <!-- Feature Cards -->
        <div class="card">
            <h2>Orders</h2>
            <p>View and manage all customer orders.</p>
        </div>

        <div class="card">
            <h2>Products</h2>
            <p>Manage your products, prices, and stock.</p>
        </div>

        <div class="card">
            <h2>Customers</h2>
            <p>Check customer details and interactions.</p>
        </div>

        <div class="card">
            <h2>Analytics</h2>
            <p>Monitor sales, revenue, and performance.</p>
        </div>
    </div>

</body>
</html>