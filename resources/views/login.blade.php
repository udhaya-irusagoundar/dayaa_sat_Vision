<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Customer Management</title>
    
    <link rel="stylesheet" href="{{ asset('public/css/login.css') }}">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width:100%; height:100%; background: rgba(0,0,0,0.4); justify-content:center; align-items:center; }
        .modal-content { background: #fff; margin: auto; padding: 30px; border-radius:10px; width: 90%; max-width: 420px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .modal .close { float:right; font-size:20px; cursor:pointer; color:#666; }

        .change-password-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        .change-password-link:hover {
            text-decoration: underline;
            color: #0056b3;
            transform: translateY(1px);
        }

        /* 👁️ Eye Icon Stable Fix */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 40px; /* space for eye */
            box-sizing: border-box;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            pointer-events: auto;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
            pointer-events: none;
        }

        .toggle-password:hover {
            color: #000;
        }
        /* 🧩 Better spacing between labels and inputs inside modal */
#changePasswordForm label {
    display: block;
    margin-top: 5px;
    margin-bottom: 5px;
    font-weight: 500;
}

#changePasswordForm input {
    margin-bottom: 2px;
      margin-top: 5px;
}

    </style>
</head>

<body>
    <div class="login-container">
        <!-- Background Image -->
        <div class="background-wrapper">
            <img src="{{ asset('public/img/cable_bg.png') }}" alt="Sky blue background" class="background-image">
            <div class="background-overlay"></div>
        </div>
        
        <!-- Login Card -->
        <div class="login-card">
            <div class="card-header">
                <div class="icon-container">
                    <div class="icon-wrapper">
                        <img src="{{ asset('public/img/new_logo.png') }}" alt="logo" width="auto" height="100px">
                    </div>
                </div>
                <h1 class="card-title">Admin Login</h1>
                <p class="card-subtitle">Sign in to access your dashboard</p>
            </div>
            
            <div class="card-content">
                <form method="POST" action="{{ route('login_submit') }}" id="loginForm" class="login-form">
                    @csrf
                  <div class="form-group">
   <label for="company_code" class="form-label">Company Code</label>
<input id="company_code" name="company_code" type="text" class="form-input"
       placeholder="Enter Company Code" required
       value="{{ old('company_code', session('company_code')) }}"
       style="text-transform: uppercase;"
       {{ session('company_code') ? 'readonly' : '' }}>

@error('company_code')
    <div style="color:red; font-size:14px; margin-top:4px;">
        {{ $message }}
    </div>
@enderror


</div>

{{-- Username & Password only appear after company_code is submitted --}}
@if(session()->has('company_code'))
    <div class="form-group">
        <label for="username" class="form-label">Username</label>
        <input id="username" name="username" type="text" class="form-input @error('username') is-invalid @enderror"
               placeholder="Enter your username" value="{{ old('username') }}" required>
        @error('username')
            <div style="color:red;font-size:14px;margin-top:5px">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group password-wrapper">
        <label for="password" class="form-label">Password</label>
        <div class="password-wrapper">
            <input id="password" type="password" name="password"
                   class="form-input @error('password') is-invalid @enderror"
                   placeholder="Enter your password" required>
            <i data-lucide="eye" class="toggle-password"></i>
        </div>
        @error('password')
            <div style="color:red;font-size:14px;margin-top:5px">{{ $message }}</div>
        @enderror
    </div>
@endif

<button type="submit" class="login-button">
    @if(session()->has('company_code'))
        Login
    @else
        Next
    @endif
</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
