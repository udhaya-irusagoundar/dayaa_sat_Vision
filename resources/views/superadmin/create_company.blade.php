<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Company - Popup</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: "Poppins", sans-serif;
        background: rgba(0,0,0,0.45); /* Dark overlay */
        backdrop-filter: blur(3px); /* Blur effect */
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    /* POPUP BOX */
    .popup-box {
        width: 380px;
        background: #fff;
        padding: 35px 30px;
        border-radius: 16px;
        box-shadow: 0 6px 30px rgba(0,0,0,0.25);
        animation: popup 0.35s ease-out;
        position: relative;
    }

    @keyframes popup {
        from { opacity: 0; transform: scale(0.85); }
        to { opacity: 1; transform: scale(1); }
    }

    /* CLOSE (X) BUTTON */
    .close-btn {
        position: absolute;
        top: 12px;
        right: 14px;
        font-size: 20px;
        color: #333;
        cursor: pointer;
        transition: 0.2s;
    }

    .close-btn:hover {
        color: #e60000;
        transform: scale(1.1);
    }

    h2 {
        text-align: center;
        margin: 0 0 20px 0;
        font-size: 22px;
        font-weight: 600;
        background: linear-gradient(90deg, #2563eb, #1e40af);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    label {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 6px;
        display: block;
        color: #1f2937;
    }

    input {
        width: 100%;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 15px;
        background: #f8fafc;
        transition: 0.25s;
        margin-bottom: 14px;
    }

    input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 6px rgba(37,99,235,0.25);
        background: #fff;
        outline: none;
    }

    button {
        width: 100%;
        padding: 12px;
        background: #2563eb;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: 0.25s;
        margin-top: 10px;
    }

    button:hover {
        background: #1e3fae;
        transform: translateY(-2px);
    }

    .success {
        padding: 12px;
        background: #d1fad1;
        color: #1b6d1b;
        border-radius: 8px;
        border-left: 6px solid #1b6d1b;
        margin-bottom: 15px;
        animation: popup 0.3s ease;
    }
</style>

</head>
<body>

<div class="popup-box">

    <!-- CLOSE BUTTON -->
    <span class="close-btn" onclick="window.location='{{ route('company.index') }}'">&times;</span>

    <h2>Create Cable Company</h2>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('company.store') }}">
        @csrf

        <label>Company Name</label>
        <input type="text" name="company_name" required value="{{ old('company_name') }}">

        <label>Company Code</label>
        <input type="text" maxlength="4" style="text-transform: uppercase;" name="company_code" required value="{{ old('company_code') }}">

        <button type="submit">Create Company</button>
    </form>
</div>

</body>
</html>
