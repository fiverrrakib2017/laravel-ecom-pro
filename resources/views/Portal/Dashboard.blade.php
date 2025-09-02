<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Customer Profile</title>
</head>
<body>
    <h1>Welcome, {{ Auth::guard('customer')->user()->fullname ?? $user->fullname ?? 'Guest'}}</h1>
    <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-outline-danger btn-sm">
        Logout
    </button>
</form>

</body>
</html>
