<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GauMitra</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                linear-gradient(135deg, rgba(234, 88, 12, 0.92), rgba(251, 146, 60, 0.88)),
                url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        .login-left {
            padding: 60px 45px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.28));
        }

        .login-left h1 {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 14px;
            line-height: 1.2;
        }

        .login-left p {
            font-size: 15px;
            line-height: 1.8;
            opacity: 0.95;
        }

        .login-left .badge-box {
            display: inline-block;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            margin-bottom: 22px;
            font-size: 13px;
            font-weight: 600;
            width: fit-content;
        }

        .login-right {
            background: #ffffff;
            padding: 55px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
        }

        .logo-box {
            text-align: center;
            margin-bottom: 18px;
        }

        .login-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff7ed;
            padding: 10px;
            box-shadow: 0 8px 20px rgba(234, 88, 12, 0.15);
            border: 1px solid #fed7aa;
        }

        .login-box h2 {
            font-size: 30px;
            color: #1f2937;
            margin-bottom: 8px;
            text-align: center;
        }

        .login-box .subtitle {
            color: #6b7280;
            margin-bottom: 28px;
            font-size: 14px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }

        .input-box {
            position: relative;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            font-size: 15px;
            outline: none;
            transition: 0.3s ease;
            background: #f9fafb;
        }

        input:focus {
            border-color: #ea580c;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.12);
        }

        .btn {
            width: 100%;
            border: none;
            padding: 14px;
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            border-radius: 14px;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 10px 25px rgba(234, 88, 12, 0.28);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(234, 88, 12, 0.35);
        }

        .error-text {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
        }

        .footer-text {
            margin-top: 18px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .login-left {
                display: none;
            }

            .login-right {
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-left">
            <div class="badge-box">GauMitra Secure Admin Panel</div>
            <h1>Welcome Back to GauMitra Admin</h1>
            <p>
                Manage users, reports, gaushala data, roles, and system activity from one secure dashboard.
                Sign in with your admin credentials to continue.
            </p>
        </div>

        <div class="login-right">
            <div class="login-box">
                <div class="logo-box">
                    <img src="{{ asset('image/logo.png') }}" alt="GauMitra Logo" class="login-logo">
                </div>

                <h2>Admin Login</h2>
                <p class="subtitle">Enter your credentials to access the dashboard</p>

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="form-group">
                        <label>User ID</label>
                        <div class="input-box">
                            <input
                                type="text"
                                name="user_id"
                                value="{{ old('user_id') }}"
                                placeholder="Enter your user ID"
                                autocomplete="username"
                            >
                        </div>
                        @error('user_id')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-box">
                            <input
                                type="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                            >
                        </div>
                        @error('password')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn">Login Now</button>
                </form>

                <div class="footer-text">
                    Secure access only for authorized administrators
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ea580c'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: "{{ $errors->first() }}",
                confirmButtonColor: '#ea580c'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                confirmButtonColor: '#ea580c'
            });
        </script>
    @endif

</body>
</html>