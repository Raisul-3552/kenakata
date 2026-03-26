<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | KHOJ</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #764ba2, #667eea);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

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

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 16px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #764ba2;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #5a3a9b;
        }

        .link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
        }

        .link a {
            color: #764ba2;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .error {
            background: #ffe0e0;
            color: #b00020;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .success {
            background: #e0ffe0;
            color: #007a00;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Register</h2>

        <!-- Display Success Message -->
        @if(session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

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

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="text" name="name" placeholder="Full Name" required value="{{ old('name') }}">
            <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>

        <div class="link">
            <span>Already have an account? <a href="{{ route('login.form') }}">Login</a></span>
        </div>
    </div>
</body>
</html>