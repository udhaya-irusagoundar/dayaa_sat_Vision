<!DOCTYPE html>
<html>
<head>
    <title>Staff Search</title>

    <style>
        body { 
            font-family: sans-serif; 
            padding: 20px; 
            background: #f5f7fa;
        }

        /* Top bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
        }

        .header {
            font-size: 22px;
            font-weight: bold;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #e63946;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .logout-btn:hover { background: #c62828; }

        /* Centered search box */
        .search-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .search-box {
            width: 60%;
            padding: 14px;
            font-size: 18px;
            border-radius: 8px;
            border: 1px solid #bbb;
            background: white;
        }

        .results-wrapper {
            width: 60%;
            margin: 25px auto;
        }

        .customer-row {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            font-size: 17px;
            background: white;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .customer-row:hover {
            background: #eef3ff;
        }

        /* Collection table */
        #todayCollectionTable {
            width: 60%;
            margin: 30px auto;
            background: white;
            border-radius: 8px;
            border-collapse: collapse;
            overflow: hidden;
        }

        #todayCollectionTable th {
            background: #eef3ff;
            padding: 10px;
            text-align: left;
            font-size: 16px;
        }

        #todayCollectionTable td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }

        .no-data {
            text-align: center;
            padding: 15px;
            font-size: 16px;
            color: #888;
        }

    </style>
</head>
<body>

<!-- 🔥 TOP BAR -->
<div class="top-bar">
    <div class="header">
        Welcome, {{ session('staff_name') ?? session('username') }}
    </div>

    <form action="{{ url('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>

<!-- 🔥 SEND STAFF NAME TO JS -->
<script>
    window.loggedStaff = "{{ session('username') }}";
</script>

<!-- 🔍 Search Box Center -->
<div class="search-container">
    <input type="text" id="searchInput" 
           class="search-box" 
           placeholder="Search by Box Number, Name or Place..."
           value="{{ $search ?? '' }}" />
</div>

<!-- Results -->
<div id="resultsContainer" class="results-wrapper">
    @if($search && count($customers) > 0)
        @foreach($customers as $c)
            <div class="customer-row" onclick="openCustomer({{ $c->id }})">
                <strong>{{ $c->customerNumber }}</strong> — {{ $c->name }} ({{ $c->place }})
            </div>
        @endforeach
    @elseif($search)
        <p>No matching customers found.</p>
    @endif
</div>


<!-- 🔥 TODAY COLLECTION TABLE -->
<h2 style="text-align:center; margin-top:40px;">Today's Collections</h2>

<table id="todayCollectionTable">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Box Number</th>
            <th>Name</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody id="collectionBody">
        <tr><td colspan="5" class="no-data">Loading...</td></tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3" style="text-align:right; font-weight:bold; font-size:17px;">Total:</td>
        <td colspan="2" id="totalAmount" style="font-weight:bold; font-size:17px;">₹0</td>
    </tr>
</tfoot>
</table>


<script>
function openCustomer(id) {
    window.location.href = "/laravel/dayaa_sat_Vision/admin/customers/edit/" + id + "?mode=payment";
}
let searchTimer;

document.getElementById("searchInput").addEventListener("keyup", function () {
    clearTimeout(searchTimer);

    const s = this.value;

    searchTimer = setTimeout(() => {
        window.location.href = "?search=" + s;
    }, 300); // semi-second delay, typing smooth
});


// 🔥 FETCH TODAY COLLECTIONS AUTOMATICALLY
document.addEventListener("DOMContentLoaded", () => {
    loadCollections();
});
function loadCollections() {

    fetch("{{ url('admin/customers/list') }}")
        .then(res => res.json())
        .then(data => {

            const customers = data.customers || [];
            const logged = window.loggedStaff; // "udhaya" or "surendhar" or "admin"
            const isAdmin = (logged.toLowerCase() === "admin"); // ⭐ NEW

            const d = new Date();
            const today =
                String(d.getDate()).padStart(2, '0') + "-" +
                String(d.getMonth() + 1).padStart(2, '0') + "-" +
                d.getFullYear();

            const tbody = document.getElementById("collectionBody");
            let rows = [];
            let sNo = 1;
            let total = 0; 
customers.forEach(cust => {
    let payments = cust.paymentDates;

    if (typeof payments === "string") {
        try { payments = JSON.parse(payments); } 
        catch(e) { payments = []; }
    }

    if (!Array.isArray(payments)) return;

    payments.forEach(monthArray => {
        if (!Array.isArray(monthArray)) return; // <-- FIX

        let last = monthArray[monthArray.length - 1];
        if (!last || !last.date || !last.amount) return;

        let formattedPayDate = last.date.replace(/\//g, "-");

        if (
            formattedPayDate === today &&
            (isAdmin || last.paid_by.toLowerCase() === logged.toLowerCase())
        ) {
            total += Number(last.amount);
            rows.push(`
                <tr>
                    <td>${sNo++}</td>
                    <td>${cust.customerNumber}</td>
                    <td>${cust.name}</td>
                    <td>₹${last.amount}</td>
                    <td>${last.date}</td>
                </tr>
            `);
        }
    });
});


tbody.innerHTML = rows.length
    ? rows.join("")
    : `<tr><td colspan="5" class="no-data">No collections today.</td></tr>`;

document.getElementById("totalAmount").innerText = "₹" + total;

});
}

</script>

</body>
</html>
