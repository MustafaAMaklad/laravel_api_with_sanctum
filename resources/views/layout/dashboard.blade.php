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
                    @yield('table-headers')
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
    {{-- <div class="modal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">@yield('modal-title')</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @yield('modal-body')
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div> --}}

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
