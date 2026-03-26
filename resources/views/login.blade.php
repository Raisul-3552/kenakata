<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | KHOJ</title>
    <style>
        /* Body & Background */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Card Container */
        .card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }

        /* Input Fields */
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 16px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        /* Button */
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #5a67d8;
        }

        /* Links */
        .link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
        }

        .link a {
            color: #667eea;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }

        /* Error Message */
        .error {
            background: #ffe0e0;
            color: #b00020;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Login</h2>

        <!-- Display Validation Errors -->
        @if($errors->any())
            <div class="error">
                <ul style="margin:0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="link">
            <span>Don't have an account? <a href="{{ route('register.form') }}">Register</a></span>
        </div>
    </div>
</body>
</html>