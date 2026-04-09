<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GauMitra</title>
    <style>
        body{
            margin:0;
            font-family:Arial, sans-serif;
            background:#f8fafc;
        }
        .header{
            background:#ea580c;
            color:#fff;
            padding:18px 30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .container{
            padding:30px;
        }
        .card{
            background:#fff;
            border-radius:16px;
            padding:25px;
            box-shadow:0 8px 20px rgba(0,0,0,0.08);
        }
        .logout-btn{
            background:#fff;
            color:#ea580c;
            border:none;
            padding:10px 16px;
            border-radius:8px;
            font-weight:700;
            cursor:pointer;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>GauMitra Admin Dashboard</h2>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <div class="container">
        <div class="card">
            <h3>Welcome, {{ session('admin_name') }}</h3>
            <p>User ID: {{ session('admin_user_id') }}</p>
            <p>Admin panel login successful.</p>
        </div>
    </div>

</body>
</html>