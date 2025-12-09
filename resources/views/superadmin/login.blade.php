<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #e9eef7;
        }

        /* Background Image Style */
        .background-wrapper {
            position: fixed;
            inset: 0;
            z-index: -1;
        }
        .background-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.65);
        }
        .background-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.45);
        }

        /* Login Card */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            background: #fff;
            width: 380px;
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 0 25px rgba(0,0,0,0.22);
            animation: fadeIn 0.5s ease;
            position: relative;
        }

        .card-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .icon-wrapper img {
            width: auto;
            height: 100px;
        }

        .card-title {
            font-size: 28px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 10px;
        }

        .card-subtitle {
            font-size: 15px;
            color: #64748b;
            margin-top: -5px;
        }

        .form-group {
            margin-top: 15px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 8px rgba(37,99,235,0.35);
            outline: none;
        }

        /* Password eye icon */
       .password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 69%;
    transform: translateY(-50%);
    cursor: pointer;
    width: 25px;
    height: 22px;
    color: #64748b;
}
.toggle-password:hover {
    color: #1e293b;
}



        /* Login Button */
        .login-button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            border: none;
            font-size: 17px;
            color: white;
            border-radius: 10px;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.25s;
        }

        .login-button:hover {
            background: #1e4fd8;
            transform: scale(1.02);
        }

        /* Error style */
        .error {
            background: #ffe1e1;
            padding: 10px;
            border-radius: 8px;
            color: #b91c1c;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

</head>

<body>

<!-- Background -->
<div class="background-wrapper">
    <img src="{{ asset('public/img/cable_bg.png') }}" class="background-image">
    <div class="background-overlay"></div>
</div>

<!-- Main Login Container -->
<div class="login-container">
    <div class="login-card">

        <div class="card-header">
            <div class="icon-wrapper">
                <img src="{{ asset('public/img/new_logo.png') }}" alt="Logo">
            </div>
            <h1 class="card-title">Super Admin Login</h1>
            <p class="card-subtitle">Sign in to manage companies</p>
        </div>

        @if(session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif

        <form method="POST" action="{{ route('superadmin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" placeholder="Enter username" required>
            </div>

            <div class="form-group password-wrapper">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="Enter password" required>
                <i data-lucide="eye" class="toggle-password" onclick="togglePassword()"></i>
            </div>

            <button type="submit" class="login-button">Login</button>
        </form>

    </div>
</div>

<script>
function togglePassword() {
    let input = document.getElementById("password");
    let icon = document.querySelector(".toggle-password");

    if (input.type === "password") {
        input.type = "text";
        icon.setAttribute("data-lucide", "eye-off");
    } else {
        input.type = "password";
        icon.setAttribute("data-lucide", "eye");
    }

    lucide.createIcons();
}

lucide.createIcons();
</script>

</body>
</html>
