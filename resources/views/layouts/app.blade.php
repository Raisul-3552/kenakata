<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kenakata - @yield('title')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --kenakata-green: #28a745;
            --kenakata-dark: #1e7e34;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: var(--kenakata-green);
        }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link {
            color: #fff;
        }
        .btn-kenakata {
            background-color: var(--kenakata-green);
            color: white;
        }
        .btn-kenakata:hover {
            background-color: var(--kenakata-dark);
            color: white;
        }
    </style>
    @yield('styles')
</head>
<body>

    @yield('navbar')

    @yield('content')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_URL = '/api';
        
        function logout() {
            const token = localStorage.getItem('kenakata_token');
            if(token) {
                fetch(`${API_URL}/logout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    localStorage.removeItem('kenakata_token');
                    localStorage.removeItem('kenakata_role');
                    window.location.href = '/login';
                });
            } else {
                window.location.href = '/login';
            }
        }
        
        function getHeaders() {
            return {
                'Authorization': `Bearer ${localStorage.getItem('kenakata_token')}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
        }
    </script>
    @yield('scripts')
</body>
</html>
