<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body { 
        font-family: "Poppins", sans-serif;
        margin: 0; 
        background: #f5f7fb;
        display: flex;
    }

   .sidebar { 
        width: 240px; 
        background: #1f2937; 
        color: white; 
        height: 100vh; 
        padding-top: 25px;
        position: fixed; 
        left: 0; 
        top: 0;
    }

    .sidebar h3 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 20px;   /* unified size */
    font-weight: 600;
}

   .sidebar a {
    display: block;
    padding: 12px 25px;
    color: #fff;
    text-decoration: none;
    font-size: 15px;   /* unified size */
    transition: 0.2s;
}


    .sidebar a:hover { 
        background: #374151; 
    }

 /* ✅ DASHBOARD STYLE WHITE HEADER */
.top-header {
    position: fixed;
    left: 240px;
    top: 0;
    width: 78%;   /* auto adjust like dashboard */
    height: 65px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    z-index: 999;
}

.top-header h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    color: #1f2937;
}

.logout-top-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #dc3545;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
}

    /* Content */
    .content { 
        margin-left: 240px; 
        padding: 80px 30px 30px; /* top padding increased for header */
        flex: 1; 
    }

    .content h2 {
        font-size: 28px; 
        font-weight: 600;
        margin-bottom: 10px;
    }

    .content p {
        font-size: 16px; 
        color: #6b7280;
    }

    .cards { 
        display: flex; 
        gap: 25px; 
        margin-top: 25px; 
        flex-wrap: wrap;
    }

    .card { 
        background: white; 
        width: 240px; 
        padding: 25px; 
        border-radius: 14px; 
        box-shadow: 0 0 12px rgba(0,0,0,0.12); 
        text-align: center;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h3 { 
        margin-bottom: 12px; 
        font-size: 18px;
        font-weight: 500;
    }

    .card p { 
        font-size: 28px; 
        font-weight: 700; 
        color: #2563eb;
        margin: 0;
    }
</style>

</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>Super Admin</h3>

    <a href="{{ route('superadmin.dashboard') }}">🏠 Dashboard</a>
    <a href="{{ route('company.index') }}">🏢 Companies</a>
    <a href="{{ route('reports') }}">📊 View Reports</a>
 
</div>

<!-- ✅ CORRECT DASHBOARD HEADER -->
<div class="top-header">
    <h2>Dashboard</h2>

    <form action="{{ route('superadmin.logout') }}" method="POST">
        @csrf
        <button class="logout-top-btn">
            <i data-lucide="log-out"></i> Logout
        </button>
    </form>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Welcome, Super Admin 👋</h2>
    <p>Manage companies, billing & platform activity.</p>

    <div class="cards">
        <div class="card">
            <h3>Total Companies</h3>
            <p>{{ $totalCompanies ?? 0 }}</p>
        </div>

        <div class="card">
            <h3>Active Companies</h3>
            <p>{{ $activeCompanies ?? 0 }}</p>
        </div>

        <div class="card">
            <h3>Inactive Companies</h3>
            <p>{{ $inactiveCompanies ?? 0 }}</p>
        </div>

        <div class="card">
            <h3>Total Revenue</h3>
            <p>₹{{ $totalRevenue ?? 0 }}</p>
        </div>
    </div>

</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>

</body>
</html>
