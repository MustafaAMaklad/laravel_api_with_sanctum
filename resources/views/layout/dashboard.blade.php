<!-- resources/views/layouts/dashboard.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>@yield('title')</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1>@yield('table-title')</h1>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @yield('content')
            </tbody>
        </table>
        <div class="pagination-links">
            @yield('pagination-links')
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
