<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer - Customer Management</title>
    <link rel="stylesheet" href="{{ asset('public/css/edit-customer.css') }}">
   
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
     <script>
    window.appRole = "{{ session('role') }}";
</script>
    <script src="{{ asset('public/js/edit-customer.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $mode = request()->get('mode', 'edit');   /* <-- ADDED */
    @endphp
    <style>
   /* CLEAN & MODERN YEAR DROPDOWN */
.year-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;

    width: 170px;
    font-size: 16px;
    padding: 10px 15px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;

    background-color: #f8faff;
    border: 1.8px solid #d4dcee;
    color: #1f2f50;

    box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
    transition: .25s ease;

    margin: 18px 0 25px 0;

    background-image: url("data:image/svg+xml;utf8,<svg fill='%231f2f50' viewBox='0 0 16 16' height='18' width='18' xmlns='http://www.w3.org/2000/svg'><path d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 40px;
}

.year-select:hover {
    border-color: #9bb3e9;
    background-color: #f0f4ff;
}

.year-select:focus {
    border-color: #4a7dff;
    background-color: #eef4ff;
    box-shadow: 0 0 0 4px rgba(74, 125, 255, 0.18);
}

</style>
</head>
<body>
@php
    $role = session('role');
    $staffName = session('username');
@endphp

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Back Button + Page Title -->
    <header class="header">
        <div class="header-container">
            <div class="header-content" style="display:flex; justify-content: space-between; align-items: center;">
                
                <!-- Page Title -->
                <h1 id="pageTitle" class="page-title">Edit Customer</h1>
                
                <!-- Back Button -->
                <button id="backBtn" class="back-btn" onclick="window.location='{{ url('admin/dashboard') }}'">
                    <i data-lucide="arrow-left" class="back-icon"></i>
                    <span>Back to Dashboard</span>
                </button>

            </div>
        </div>
    </header>

  <!-- Main Content -->
    <div class="main-content">
        <div id="customerDetailsContainer">
              
            {{-- STAFF VIEW HEADER --}}
          @if($role === 'staff')
<div class="staff-info-box">
      <div class="header-top" style="display: flex; justify-content: space-between; align-items: center;">
    <h2 class="welcome-text">Customer Details</h2>

    <a href="{{ route('staff.search') }}" class="back-btn" 
       style="padding: 6px 12px; background:#007bff; color:#fff; border-radius:6px; text-decoration:none;">
        ← Back
    </a>
</div>

    <div class="staff-details">
        <div><strong>Year:</strong> <span id="staffYearText"></span></div>
        <div><strong>Box Number:</strong> {{ $customer->customerNumber }}</div>
        <div><strong>Customer Name:</strong> {{ $customer->name }}</div>
    </div>
</div>
@endif


            <!-- Customer Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
                </div>

                <div class="card-content">
                    <form id="customerForm" class="customer-form"  method="POST" action="{{ route('customers.update', $customer->id) }}">
                        @csrf

                        <div class="form-group">
                            <label for="customerNumber" class="form-label">Box Number</label>
                            <input id="customerNumber" type="text" name="customerNumber"
       class="form-input"
       value="{{ $customer->customerNumber }}"
       oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9]/g,'').slice(0,16)"
       @if($mode === 'payment') readonly @endif>

                        </div>

                        <div class="form-group">
                            <label for="customerName" class="form-label">Customer Name</label>
                            <input id="customerName" type="text" name="name"
                                   class="form-input"
                                   value="{{ $customer->name }}"
                                   required pattern="[A-Za-z\s]+"
                                   @if($mode === 'payment') readonly @endif>
                        </div>

                        <div class="form-group">
                            <label for="mobileNumber" class="form-label">Mobile Number</label>
                            <input id="mobileNumber" type="text" name="mobileNumber"
                                   class="form-input"
                                   value="{{ $customer->mobileNumber }}"
                                   required maxlength="10"
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                                   title="Only digits allowed"
                                   @if($mode === 'payment') readonly @endif>
                        </div>

                        <div class="form-group">
                            <label for="place" class="form-label">Place</label>
                            <input id="place" type="text" name="place"
                                   class="form-input"
                                   value="{{ $customer->place }}"
                                   required
                                   oninput="this.value=this.value.replace(/[^A-Za-z\s]/g,'')"
                                   title="Only letters and spaces allowed"
                                   @if($mode === 'payment') readonly @endif>
                        </div>
@if(session('role') === 'admin')
<div class="form-group">
    <label for="baseAmount" class="form-label">Base Amount</label>
    <input 
        id="baseAmount" 
        type="number" 
        name="baseAmount" 
        class="form-input"
        value="{{ $customer->amount ?? '' }}" 
        placeholder="Enter base amount"
        min="1"
     @if($mode === 'payment') readonly @endif>
</div>
@endif

                    </form>
                </div>
            </div>
<select id="yearSelector" class="year-select"></select>
            <!-- Monthly Payment Tracking -->
            <div class="card" style="margin-top:20px;">
                <div class="card-header">
                    <div class="header-row">
                        <h3 class="card-title">Monthly Payment Tracking</h3>

                        <div class="header-actions">

                           @if($role !== 'staff')
<button id="downloadPDFBtn" class="download-btn">
    <i data-lucide="file-down" class="download-icon"></i>
    <span>Download PDF</span>
</button>
@endif
                            @if($mode !== 'payment') 
                            <!-- Save button hidden only in payment mode -->
                            <button id="saveChangesBtn" class="save-btn">
                                <i data-lucide="save" class="save-icon"></i>
                                <span>Save Changes</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div id="weeklyGrid" class="weekly-grid"></div>
                    <!-- Progress -->
                    <div class="progress-section">
                        <div class="progress-grid">

                            <div class="progress-card">
                                <label class="progress-label">Number of Months Paid</label>
                                <div class="progress-value weeks-progress">
                                    <span id="weeksProgressText">0 of 12 months</span>
                                </div>
                            </div>

                            <div class="progress-card">
                                <label class="progress-label">Total Amount Paid</label>
                                <div class="progress-value amount-progress">
                                    <span id="amountProgressText">₹0</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Pass Laravel values to JS -->
    <script>
        window.currentCustomer = @json($customer);
        const dashboardUrl = "{{ url('admin/dashboard') }}";
     window.editMode = "{{ trim($mode) }}";
   // <-- ADDED FOR JS
    </script>
   
   <script>
        window.appLogoUrl = "{{ asset('public/img/logo.png') }}";
        window.appTitle = "DAYAA SATVISION";
    </script>
<script>
document.getElementById("yearSelector").addEventListener("change", function () {
    const year = this.value;
    window.location.search = "?year=" + year + "&mode=" + window.editMode;
});
</script>
@if($role === 'staff')
<style>
    /* Page title and back button hide */
    #pageTitle,
    #backBtn {
        display: none !important;
    }

   .card:nth-of-type(2) {
    display: none !important;
}

    /* Save Changes button hide (staff payment only) */
    #saveChangesBtn {
        display: none !important;
    }

    /* Year dropdown hide – we show text in staff card */
    #yearSelector {
        display: none !important;
    }

    /* Progress section (months paid, total amount) hide */
    .progress-section {
        display: none !important;
    }

    /* Extra safety – disable inputs if somehow visible */
    .customer-form input {
        pointer-events: none !important;
        background: #f0f0f0 !important;
        color: #555 !important;
    }

    /* Make layout little closer to top */
    .main-content {
        margin-top: 10px !important;
    }
    .staff-info-box {
    background: #ffffff;
    padding: 25px 30px;
    border-radius: 14px;
    margin-bottom: 25px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    border-left: 6px solid #16a34ad1; /* nice blue accent */
}

.staff-title {
    margin: 0;
    font-size: 24px;
    font-weight: 800;
    color: #1a1a1a;
}

.staff-details {
    margin-top: 12px;
    font-size: 17px;
    line-height: 1.8;
}

.staff-details strong {
    color: #333;
    font-weight: 700;
}
.back-btn {
    display: inline-block;
    margin-bottom: 12px;
    padding: 5px 12px;
    background: #f0f0f0;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    color: #333;
    border: 1px solid #ccc;
    transition: 0.2s;
}

.back-btn:hover {
    background: #e0e0e0;
    color: #000;
}

</style>
@endif
<script>
document.addEventListener("DOMContentLoaded", function () {
    const yearSelector = document.getElementById("yearSelector");

    // Manual years you want always listed
    const manualYears = [2025, 2026];

    // Auto future years
    const currentYear = new Date().getFullYear();
    const futureYears = [];
    for (let y = currentYear; y <= currentYear + 1; y++) {
        futureYears.push(y);
    }

    // Merge + sort without duplicates
    const allYears = [...new Set([...manualYears, ...futureYears])].sort((a, b) => a - b);

    const selectedYear = new URLSearchParams(window.location.search).get("year") || currentYear;

    // Populate dropdown
    yearSelector.innerHTML = "";
    allYears.forEach(y => {
        const opt = document.createElement("option");
        opt.value = y;
        opt.textContent = y;
        if (parseInt(selectedYear) === y) opt.selected = true;
        yearSelector.appendChild(opt);
    });

    // Change event redirect
    yearSelector.addEventListener("change", function () {
        const year = this.value;
        window.location.search = "?year=" + year + "&mode=" + window.editMode;
    });

    // Staff screen — show year text
    const yearTextSpan = document.getElementById("staffYearText");
    if (yearTextSpan) {
        yearTextSpan.textContent = yearSelector.value;
    }
});
</script>

<!--
<script>
document.addEventListener('DOMContentLoaded', function () {
    var yearSelect = document.getElementById('yearSelector');
    var yearTextSpan = document.getElementById('staffYearText');

    if (yearSelect && yearTextSpan) {
        yearTextSpan.textContent = yearSelect.value;
    }
});
</script>
-->

</body>
</html>
