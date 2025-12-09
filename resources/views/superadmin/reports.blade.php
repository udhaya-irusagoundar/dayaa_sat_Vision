<!DOCTYPE html>
<html>
<head>
    <title>Company Reports</title>

<style>
body {
    font-family: "Poppins", sans-serif;
    margin: 0;
    background: #f5f7fb;
    display: flex;
}

/* SIDEBAR */
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
    font-size: 20px;
    font-weight: 600;
}
.sidebar a {
    display: block;
    padding: 12px 25px;
    color: #fff;
    text-decoration: none;
    font-size: 15px;
    transition: 0.2s;
}
.sidebar a:hover {
    background: #374151;
}

/* TOP WHITE HEADER */
.top-header {
    position: fixed;
    left: 240px;
    top: 0;
    width: 78%;
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

/* MAIN CONTAINER */
.container {
    width: 95%;
    margin-left: 240px !important;
    margin-top: 85px !important;
    padding: 20px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    margin-top: 20px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
table th {
    background: #1e40af;
    color: white;
    padding: 10px;
    font-size: 14px;
}
table td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    text-align: center;
}
table tr:hover td {
    background: #eef3ff;
}

/* FILTER BAR */
.filter-bar {
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.filter-bar button {
    background: #2563eb;
    color: white;
    border: none;
    padding: 7px 14px;
    border-radius: 6px;
    cursor: pointer;
}
.filter-bar .reset {
    background: #6b7280;
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>Super Admin</h3>
    <a href="{{ route('superadmin.dashboard') }}">🏠 Dashboard</a>
    <a href="{{ route('company.index') }}">🏢 Companies</a>
    <a href="{{ route('reports') }}">📊 View Reports</a>
</div>

<!-- TOP HEADER -->
<div class="top-header">
    <h2>Company Reports</h2>
    <form action="{{ route('superadmin.logout') }}" method="POST">
        @csrf
        <button class="logout-top-btn">
            <i data-lucide="log-out"></i> Logout
        </button>
    </form>
</div>

<!-- MAIN CONTENT -->
<div class="container">
    <form method="GET" class="filter-bar">
        From: <input type="date" name="from_date" value="{{ request('from_date') }}">
        To: <input type="date" name="to_date" value="{{ request('to_date') }}">
        <button type="submit">Filter</button>
        <button type="button" class="reset" onclick="window.location='{{ url('superadmin/reports') }}'">Reset</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>Company Name</th>
            <th>Total Customers</th>
            <th>Total Staff</th>
            <th>Total Collection</th>
        </tr>
        </thead>
        <tbody>
        @foreach($companies as $c)
            <tr>
                <td>{{ $c->company_name }}</td>
                <td>{{ $c->total_customers }}</td>
                <td>{{ $c->total_staff }}</td>
                <td>{{ number_format($c->total_collected) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
</body>
</html>
