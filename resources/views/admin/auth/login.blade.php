<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GauMitra</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }
        body{
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .login-box{
            width:100%;
            max-width:420px;
            background:#fff;
            padding:35px;
            border-radius:18px;
            box-shadow:0 12px 35px rgba(0,0,0,0.12);
        }
        .login-box h2{
            text-align:center;
            color:#c2410c;
            margin-bottom:10px;
        }
        .login-box p{
            text-align:center;
            color:#666;
            margin-bottom:25px;
        }
        .form-group{
            margin-bottom:18px;
        }
        label{
            display:block;
            margin-bottom:8px;
            font-weight:600;
            color:#333;
        }
        input{
            width:100%;
            padding:12px 14px;
            border:1px solid #ddd;
            border-radius:10px;
            outline:none;
            font-size:15px;
        }
        input:focus{
            border-color:#ea580c;
        }
        .btn{
            width:100%;
            background:#ea580c;
            color:#fff;
            border:none;
            padding:13px;
            border-radius:10px;
            font-size:16px;
            font-weight:700;
            cursor:pointer;
        }
        .btn:hover{
            background:#c2410c;
        }
        .alert{
            padding:12px;
            border-radius:10px;
            margin-bottom:16px;
            font-size:14px;
        }
        .alert-danger{
            background:#fee2e2;
            color:#b91c1c;
        }
        .alert-success{
            background:#dcfce7;
            color:#166534;
        }
        .error-text{
            color:red;
            font-size:13px;
            margin-top:6px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>GauMitra Admin</h2>
        <p>Login to admin panel</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label>User ID</label>
                <input type="text" name="user_id" value="{{ old('user_id') }}" placeholder="Enter user ID">
                @error('user_id')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password">
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>