<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer Management</title>
    <link rel="stylesheet" href="{{asset('public/css/dashboard.css')}}">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Include Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

 <style>

/* ✅ Main modal overlay */
#changePasswordModal {
    display: none; /* Hidden initially */
    position: fixed;
    z-index: 9999;
    inset: 0; /* shorthand for top:0; right:0; bottom:0; left:0 */
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center; /* center horizontally */
    align-items: center;     /* center vertically */
}

/* ✅ When modal is visible */
#changePasswordModal.show {
    display: flex !important; /* enable flex to center */
}

/* ✅ Modal box styling */
#changePasswordModal .modal-content {
    background: #fff;
    border-radius: 10px;
    width: 400px;
    max-width: 90%;
    padding: 25px 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    animation: fadeInUp 0.3s ease-out;
}

/* Header */
#changePasswordModal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

/* Actions area */
#changePasswordModal .modal-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

/* Cancel / Submit buttons */
.cancel-btn, .submit-btn {
    padding: 8px 15px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}
.cancel-btn {
    background: #ddd;
    color: #333;
}
.submit-btn {
    background: #007bff;
    color: #fff;
}

/* ✅ Small animation */
@keyframes fadeInUp {
    from {
        transform: translateY(40px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}


    .password-input-wrapper {
    position: relative;
}

.password-toggle-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    cursor: pointer;
    color: #666;
}

.eye-icon {
    font-size: 30px;
}

.form-input {
    padding-right: 30px; /* To give space for the eye icon */
}
/* Ensure SweetAlert is displayed above all other UI elements */
.swal2-container {
    z-index: 9999 !important;
}
  /** PADDING REDUCE FIX START **/
    .summary-stats {
        padding: 8px 12px !important;
        margin-top: 10px !important;
        margin-bottom: 10px !important;
    }

    .stat-card {
        padding: 10px 18px !important;
        width:250px;
    }

    .stats-row {
        gap: 0.6rem !important;
       
    }

    .summary-title {
        margin-bottom: 6px !important;
    }
    /** PADDING REDUCE FIX END **/
.select-wrapper.paid-icon i {
    color: #28a745 !important; /* green */
}

.select-wrapper.unpaid-icon i {
    color: #dc3545 !important; /* red */
}

.select-wrapper.all-icon i {
    color: #6c757d !important; /* grey */
}
.top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    width: 100%;
    flex-wrap: nowrap;
    padding: 10px 0;
}

/* Show dropdown small & aligned */
.entries-control {
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

/* Buttons aligned */
.controls-right {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}

.download-btn,
.add-customer-btn {
    height: 50px;
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: center;
    white-space: nowrap;
}
/* Fix layout: title left & cards right inline */
.summary-stats {
    display: flex;
    align-items: center;
    width: 40% ;
    padding: 8px;
    flex-wrap: nowrap;
    gap: 40px; /* works now */
    justify-content: flex-start; 
     background: none;      /* remove gradient box */
    border: none;          /* remove border */
    box-shadow: none;      /* remove shadow */
    padding: 0;
}

/* Title placed left, not center */
.summary-stats .summary-title {
    display: none;
}

.stat-card {
    padding: 4px;
  * 8px → 4px */
}
/* REMOVE FIXED WIDTH (THIS CAUSED BREAKING) */
.summary-stats {
    width: auto !important;
    flex-shrink: 0;
  
    padding: 10px !important;
}

/* Buttons perfect alignment */
.controls-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Box 1: Total Customers → BLUE */
#overallStats .stat-card:nth-child(1) {
       border-color: #54545442 !important;
    color: #1e90ff !important;
    gap: 5px
}

/* Box 2: Amount Collected → GREEN */
#overallStats .stat-card:nth-child(2) {
    border-color: #54545442 !important;
    color: #28a745 !important;
      gap: 5px

}

/* Label */
#overallStats .stat-label {
    font-size: 16px;
}

/* Value */
#overallStats .stat-value {
    font-size: 20px;
    font-weight: 800;
}

/* Inline alignment */
#overallStats {
    display: flex !important;
    gap: 20px !important;
}

/* Prevent line break inside the stat box */
#overallStats .stat-card {
    white-space: nowrap !important;
}


.controls-right .add-customer-btn,
.controls-right .download-btn {
    height: 45px !important;
    padding: 0 18px !important;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-radius: 8px !important;
}

/* ⭐ Make all items same height & perfectly centered */
.controls-section {
   
   
    gap: 0px !important;
    width: 100%;
   
}

/* ⭐ Entries (show 300) height fix */
.entries-control select {
    height: 35px !important;
    display: flex;
    align-items: center;
}

/* ⭐ Summary cards same height as buttons */
#overallStats .stat-card {
    height: 45px !important;
    display: flex;
    align-items: center !important;
    padding: 0 20px !important;
    margin: 0 !important;
      border-radius: 8px !important;
}

/* ⭐ Label + Value alignment */
#overallStats .stat-label,
#overallStats .stat-value {
    line-height: 1 !important;
    display: flex;
    align-items: center;
}

/* ⭐ Buttons same height */
.controls-right button {
    height: 45px !important;
    padding: 0 20px !important;
    display: flex;
    align-items: center !important;
    border-radius: 8px !important;
}

/* ⭐ Remove extra padding from summary wrapper */




 
 
/* Mobile Responsive */
@media(max-width: 768px) {
 
.summary-stats {
    display: flex !important;
    align-items: center !important;
    gap: 20px !important;
    margin: 0 !important;
    padding: 0 !important;
    height: 145px !important;
}
}

 </style>
</head>
<body>
   @php
    $role = session('role');
@endphp

@if($role === 'staff')
<style>
    /* Hide Add Staff */
    a.btnprimary[href*="staff"] {
        display:none;
    }

    /* Hide Change Password */
    #changePasswordBtn {
        display:none;
    }

    /* Hide Add Customer */
    #addCustomerBtn {
        display:none;
    }

    /* Hide Download buttons */
    #downloadBtn,
    #downloadTodayBtn {
        display:none;
    }

    /* Hide entries dropdown */
    .entries-control {
        display:none !important;
    }

    /* Hide edit & delete actions */
    .edit,
    .delete {
        display:none !important;
    }

    /* Only rupee button visible */
    .rupee {
        display:inline-flex !important;
    }
    .btnprimary {
    background: #28a745 !important;
    color: white !important;
    border-radius: 6px;
    padding: 8px 15px;
    font-size: 15px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 2.35rem;
}

</style>
@endif
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="header-content">
                <div class="header-left">
                        @if(session('company_logo'))
        <img src="{{ asset('public/uploads/company_logos/' . session('company_logo')) }}" 
             alt="Icon" width="auto" height="40px" style="object-fit:contain;">
    @else
        <img src="{{ asset('public/img/new_logo.png') }}" 
             alt="Icon" width="auto" height="40px">
    @endif
                    <h1 class="header-title"> {{ session('company_name') }}</h1>
                </div>
                <div class="header-right">           
                    <div class="user-info">
                        <i data-lucide="user" class="user-icon"></i>
                     <span class="user-name">Admin User</span>

   <a href="{{ route('admin.staff_list') }}?openModal=1" 
   style="display:inline-flex;align-items:center;gap:6px;background:#28a745;color:white;padding:8px 15px;border-radius:6px;text-decoration:none;font-size:15px;  height: 2.35rem;">
    <i data-lucide="plus"></i> Add Staff
</a>
<a href="{{ route('admin.staff.report') }}" 
   style="display:inline-flex;align-items:center;gap:6px;background:#17a2b8;color:white;
          padding:8px 15px;border-radius:6px;text-decoration:none;font-size:15px; height: 2.35rem;">
    <i data-lucide="trending-up"></i> Staff Report
</a>


                        <!-- Button to trigger Change Password modal -->
<button id="changePasswordBtn" class="btnprimary">Change Password</button>

                  <!-- Change Password Modal -->
<div id="changePasswordModal" class="password">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Change Password</h3>
            <button type="button" id="closeChangePasswordModal" class="modal-close">
                <i data-lucide="x" class="close-icon"></i>
            </button>
        </div>

        <form id="changePasswordForm" method="POST" action="{{ route('change.password') }}">
            @csrf
            <div class="form-grid">
               <div class="form-group">
    <label for="currentPassword" class="form-label">Current Password</label>
    <div class="password-input-wrapper">
      <input id="currentPassword" type="password" name="current_password" class="form-input" required>
        <button type="button" id="toggleCurrentPassword" class="password-toggle-btn">
            <i data-lucide="eye-off" id="currentPasswordEyeIcon" class="eye-icon"></i>
        </button>
    </div>
</div>

<div class="form-group">
    <label for="newPassword" class="form-label">New Password</label>
    <div class="password-input-wrapper">
        <input id="newPassword" type="password" name="new_password" class="form-input" required>
        <button type="button" id="toggleNewPassword" class="password-toggle-btn">
            <i data-lucide="eye-off" id="newPasswordEyeIcon" class="eye-icon"></i>
        </button>
    </div>
</div>

<div class="form-group">
    <label for="confirmPassword" class="form-label">Confirm New Password</label>
    <div class="password-input-wrapper">
        <input id="confirmPassword" type="password" name="new_password_confirmation" class="form-input" required>
        <button type="button" id="toggleConfirmPassword" class="password-toggle-btn">
            <i data-lucide="eye-off" id="confirmPasswordEyeIcon" class="eye-icon"></i>
        </button>
    </div>
</div>
            </div>
            <div class="modal-actions">
                <button type="button" id="cancelChangePassword" class="cancel-btn">Cancel</button>
                <button type="submit" class="submit-btn">Update Password</button>
            </div>
        </form>
    </div>
</div>
                    <button id="logoutBtn" class="logout-btn" onclick="window.location='{{ route('logout') }}'">
                        <i data-lucide="log-out" class="logout-icon"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </header>
 
    <!-- Filter Section -->
<div class="filter-section">
    <div class="filter-container">
        <div class="filter-grid">
            <!-- Search Field -->
            <div class="filter-field">
                <label for="nameSearch" class="filter-label">Search Customer</label>
                <div class="search-input-wrapper">
                    <!-- Search Icon -->
                    <i data-lucide="search" class="search-icon"></i>
                    <!-- Search Input -->
                    <input
                        id="nameSearch"
                        type="text"
                        class="search-input"
                        placeholder="Search by name or customer Box number...">
                </div>
            </div>
            
            <!-- From Date Field -->
            <div class="filter-field">
                <label for="fromDate" class="filter-label">From Date</label>
                <div class="date-input-wrapper">
                    <i data-lucide="calendar" class="calendar-icon"></i>
                    <input
                        id="fromDate"
                        type="date"
                        class="date-input">
                </div>
            </div>
            
            <!-- To Date Field -->
            <div class="filter-field">
                <label for="toDate" class="filter-label">To Date</label>
                <div class="date-input-wrapper">
                    <i data-lucide="calendar" class="calendar-icon"></i>
                    <input
                        id="toDate"
                        type="date"
                        class="date-input">
                </div>
            </div>

            <!-- Place Filter Field -->
            <div class="filter-field">
                <label for="placeFilter" class="filter-label">Place</label>
                <div class="select-wrapper">
                    <!-- Map Pin Icon -->
                    <i data-lucide="map-pin" class="place-icon"></i>
                    <select id="placeFilter" class="search-input">
                        <option value="">All PLACES</option>
                        <option value="Pannapatti" selected>PANNAPATTI</option>
                        <option value="Karuppanampatti">KARUPPANAMPATTI</option>
                    </select>
                </div>
            </div>
        <div class="filter-field">
    <label class="filter-label">Payment Status</label>
    <div class="select-wrapper">
        <i id="statusIcon" data-lucide="check-circle" class="place-icon"></i>
        <select id="paymentStatusFilter" class="search-input">
            <option value="" selected>All</option>
            <option value="paid">Paid</option>
            <option value="unpaid">Unpaid</option>
        </select>
    </div>
</div>
<div class="filter-field">
    <label for="staffFilter" class="filter-label">Staff</label>
    <div class="select-wrapper">
        <i data-lucide="user-check" class="place-icon"></i>
        <select id="staffFilter" class="search-input">
            <option value="" selected>All Staff</option>
            <option value="admin">Admin</option>
          
            <!-- Add others if needed -->
        </select>
    </div>
        </div>
    </div>
</div>

  <hr style="border: 0; height: 1px; background: #c9c7c789;">

    <!-- Main Content -->
    <div class="main-content">
        <!-- Controls -->
        <div class="controls-section">
           <div class="controls-left">
                <div class="entries-control">
                    <label for="entriesSelect" class="entries-label">Show</label>
                    <select id="entriesSelect" class="entries-select">
                        <option value="250">250</option>
                         <option value="300" selected>300</option>             
                    </select>
                    <span class="entries-text">entries</span>
                </div>
            </div>
             <!-- Overall Summary -->
<div class="summary-stats">
    <div class="summary-title">Overall Statistics</div>
    <div id="overallStats"></div>
</div>
<div class="filter-field">
    <label class="filter-label">Year</label>
    <div class="select-wrapper">
        <i data-lucide="calendar-range" class="place-icon"></i>
        <select id="yearSelector" class="search-input">
            @php
               $currentYear = date('Y');
for ($y = 2025; $y <= $currentYear + 5; $y++) {
    $selected = ($y == $currentYear) ? 'selected' : '';
    echo "<option value='$y' $selected>$y</option>";
}
            @endphp
        </select>
    </div>
</div>

             <div class="controls-right">
        <button id="downloadBtn" class="download-btn">
            <i data-lucide="file-down" class="download-icon"></i>
            <span>Download List</span>
        </button>

        <button id="downloadTodayBtn" class="download-btn">
            <i data-lucide="file-text" class="download-icon"></i>
            <span>Download Box Numbers</span>
        </button>

        <button id="addCustomerBtn" class="add-customer-btn">
            <i data-lucide="plus" class="add-icon"></i>
            <span>Add Customer</span>
        </button>
    </div>
        </div>


 
        <!-- Customer Table -->
        <div class="table-container">
            <table class="customer-table">
                <thead>
                    <tr class="table-header-row">
                        <th class="table-header serial-header">S.No</th>
                        <th class="table-header">Box Number</th>
                        <th class="table-header">Customer Name</th>
                        <th class="table-header">Month Progress</th>
                        <th class="table-header">Total Amount</th>
       @if(strtolower(session('role')) === 'admin')
        <th class="table-header">Staff</th>
    @endif

                        <th class="table-header actions-header">Actions</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    <!-- Customer rows will be populated by JavaScript -->
                </tbody>
            </table>
           
            <div id="noCustomersMessage" class="no-customers" style="display: none;">
                No customers found. Add your first customer to get started.
            </div>
        </div>
 
        <!-- Filtered Summary Stats -->
        <div id="filteredStats" class="summary-stats" style="display: none;">
            <!-- Filtered stats will be populated by JavaScript -->
        </div>
 
        <!-- Pagination -->
        <div class="pagination-section">
            <div id="paginationInfo" class="pagination-info">
                Showing 1 to 10 of 2 customers
            </div>
            <div id="paginationControls" class="pagination-controls">
                <!-- Pagination buttons will be populated by JavaScript -->
            </div>
        </div>
    </div>
 
    <!-- Add Customer Modal -->
<div id="addCustomerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Customer</h3>
          <button type="button" id="closeAddModal" class="modal-close">
    <i data-lucide="x" class="close-icon"></i>
</button>

        </div>
    <form id="addCustomerForm" class="modal-form" method="POST" action="#">

            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="newCustomerNumber" class="form-label">Box Number</label>
                  <input
    id="newCustomerNumber"
    type="text"
    name="customerNumber"
    class="form-input"
    placeholder="Enter Box Number"
    maxlength="16"
    required
    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0,16)"
>
@error('customerNumber')
    <div style="color:red;font-size:14px;margin-top:5px">{{ $message }}</div>
@enderror

                    @error('customerNumber')
                            <div style="color:red;font-size:14px;margin-top:5px">{{ $message }}</div>
                        @enderror
                </div>
                <div class="form-group">
                    <label for="newCustomerName" class="form-label">Customer Name</label>
                    <input id="newCustomerName" type="text" name="name" class="form-input" placeholder="Enter customer name" required value="{{ old('name') }}">
                     @error('name')
                            <div style="color:red;font-size:14px;margin-top:5px">{{ $message }}</div>
                        @enderror
                </div>
                 <div class="form-group">
                <label for="newMobileNumber" class="form-label">Mobile Number</label>
    <input
        id="newMobileNumber"
        type="text"
        name="mobileNumber"
        class="form-input @error('mobileNumber') is-invalid @enderror"
        placeholder="Enter 10-digit mobile number"
        value="{{ old('mobileNumber') }}"
        maxlength="10"
        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)"
        required
    >
    @error('mobileNumber')
                            <div style="color:red;font-size:14px;margin-top:5px">{{ $message }}</div>
                        @enderror
   
</div>
                <div class="form-group">
                    <label for="newPlace" class="form-label">Place</label>
                     <select id="newPlace" name="place" class="form-input" required>
        <option value="">-- Select Place --</option>
        <option value="Pannapatti" selected>Pannapatti</option>
        <option value="Karuppanampatti">Karuppanampatti</option>
      
    </select>

    @error('place')
        <div style="color:red;font-size:14px;margin-top:5px">{{ $message }}</div>
    @enderror
                </div>
              <div class="form-group" style="grid-column: span 2;">
    <label for="newAmount" class="form-label">Enter Amount<span style="color:red;">*</label>
    <input
        id="newAmount"
        type="number"
        name="amount"
        class="form-input"
        placeholder="Enter amount"
        min="0"
        required
    >
</div>


               
            <div class="modal-actions">
               <button type="button" id="cancelAddCustomer" class="cancel-btn">Cancel</button>
              
            </div>
 </form><div class="modal-actions">
  <button type="submit" class="submit-btn">Add Customer</button> </div>
    </div>
</div>
 
      
 
    <!-- Toast Container -->

    <div id="toastContainer" class="toast-container"></div>
    @php
    $customerRoutes = [
        'list'    => route('admin.customers.list'),   // your route name for listing customers
        'store'   => route('admin.customers.store'),  // your route name for storing new customer
        'destroy' => url('admin/customers/destroy'),  // base URL, append /id in JS
        'edit'    => url('admin/customers/edit'),     // base URL, append /id in JS
    ];
@endphp
  <script>
       const customerRoutes = @json($customerRoutes);
</script>

<!-- Add SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ asset('public/js/dashboard.js') }}" defer></script>
 <script>
    const destroyRouteTemplate = "{{ route('admin.customers.destroy', ':id') }}";
    
    // Function to get the delete URL with the customer ID
    function getDestroyUrl(id) {
        return destroyRouteTemplate.replace(':id', id);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Password visibility toggle functionality
        const passwordFields = [
            { fieldId: 'currentPassword', iconId: 'currentPasswordEyeIcon', toggleBtnId: 'toggleCurrentPassword' },
            { fieldId: 'newPassword', iconId: 'newPasswordEyeIcon', toggleBtnId: 'toggleNewPassword' },
            { fieldId: 'confirmPassword', iconId: 'confirmPasswordEyeIcon', toggleBtnId: 'toggleConfirmPassword' }
        ];

        passwordFields.forEach(({ fieldId, iconId, toggleBtnId }) => {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(iconId);
            const toggleBtn = document.getElementById(toggleBtnId);

            toggleBtn.addEventListener('click', () => {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    eyeIcon.setAttribute('data-lucide', 'eye');  // Change icon to 'eye'
                } else {
                    passwordField.type = "password";
                    eyeIcon.setAttribute('data-lucide', 'eye-off');  // Change icon to 'eye-off'
                }
            });
        });

        // Change Password Modal functionality
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const changePasswordModal = document.getElementById('changePasswordModal');
        const closeChangePasswordModal = document.getElementById('closeChangePasswordModal');
        const cancelChangePassword = document.getElementById('cancelChangePassword');
        const changePasswordForm = document.getElementById('changePasswordForm');

        // Show the Change Password Modal when the button is clicked
        changePasswordBtn.addEventListener('click', () => {
            changePasswordModal.classList.add('show');
        });

        // Close Modal and reset form when Cancel or Close button is clicked
        [closeChangePasswordModal, cancelChangePassword].forEach(btn => {
            btn.addEventListener('click', () => {
                changePasswordModal.classList.remove('show');
                changePasswordForm.reset();
            });
        });

       // Handle Change Password Form Submission with Validation
changePasswordForm.addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(changePasswordForm);
    const currentPassword = formData.get('current_password');
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('new_password_confirmation');

    // Check if new password matches the current password
    if (currentPassword === newPassword) {
        Swal.fire('Invalid Password', 'New password cannot be the same as the current password.', 'warning');
        return;
    }

    // Check if new password and confirm password match
    if (newPassword !== confirmPassword) {
        Swal.fire('Password Mismatch', 'New and confirm password do not match.', 'warning');
        return;
    }

    try {
        const response = await fetch(changePasswordForm.action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        // 🔥 SESSION EXPIRED HANDLING
        if (response.status === 401 || response.status === 419) {
            Swal.fire({
                icon: 'warning',
                title: 'Session Expired',
                text: 'Please login again.',
            }).then(() => {
                window.location.href = "{{ route('login') }}";
            });
            return;
        }

        const data = await response.json();

        if (data.status === 'success') {
            Swal.fire('Success', data.message, 'success');
            changePasswordModal.classList.remove('show');
            changePasswordForm.reset();
        } else {
            Swal.fire('Error', data.message, 'error');
        }

    } catch (err) {
        Swal.fire('Error', 'Something went wrong. Please try again later.', 'error');
        console.error(err);
    }
});
      
  document.addEventListener("DOMContentLoaded", () => {

    const paymentFilter = document.getElementById("paymentStatusFilter");
    const statusIcon = document.getElementById("statusIcon");
    const wrapper = statusIcon.closest(".select-wrapper");

    function updateStatusIcon() {
        const status = paymentFilter.value;

        wrapper.classList.remove("paid-icon", "unpaid-icon", "all-icon");

        if (status === "paid") {
            statusIcon.setAttribute("data-lucide", "check-circle");
            wrapper.classList.add("paid-icon");
        } 
        else if (status === "unpaid") {
            statusIcon.setAttribute("data-lucide", "x-circle");
            wrapper.classList.add("unpaid-icon");
        } 
        else {
            statusIcon.setAttribute("data-lucide", "circle");
            wrapper.classList.add("all-icon");
        }

        // Re-render lucide ICONS after update
        lucide.createIcons();
    }

    updateStatusIcon(); // load once
    paymentFilter.addEventListener("change", updateStatusIcon);
});
    });
</script>
<script>
    
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const place = urlParams.get('place');
    const placeFilter = document.getElementById('placeFilter');
    
    if (place && placeFilter) {
        placeFilter.value = place; // auto-select the place from URL
    }
document.addEventListener("DOMContentLoaded", () => {
    const statusFilter = document.getElementById("paymentStatusFilter");
    const wrapper = statusFilter.closest(".select-wrapper");

    function updateIconColor() {
        wrapper.classList.remove("paid-icon", "unpaid-icon", "all-icon");

        if (statusFilter.value === "paid") {
            wrapper.classList.add("paid-icon");
        } else if (statusFilter.value === "unpaid") {
            wrapper.classList.add("unpaid-icon");
        } else {
            wrapper.classList.add("all-icon");
        }
    }

    statusFilter.addEventListener("change", updateIconColor);

    updateIconColor(); // Initial load
});

   
});
</script>
<script>
document.addEventListener("DOMContentLoaded", async () => {

    const staffFilter = document.getElementById("staffFilter");
    if (!staffFilter) return;

    async function loadStaff() {
        try {
            const response = await fetch("{{ route('admin.staff.list') }}");
            const staffList = await response.json();

            let html = `
                <option value="">All Staff</option>
                <option value="admin">Admin</option>
            `;

            staffList.forEach(staff => {
                html += `<option value="${staff.username}">${staff.name}</option>`;
            });

            staffFilter.innerHTML = html;

            // Always show All Staff on page load
            staffFilter.value = "";

        } catch (err) {
            console.error("Failed loading staff:", err);
        }
    }

    loadStaff();
});
</script>

<script>
    window.appRole = "{{ strtolower(session('role')) }}";
    window.appUser = "{{ session('username') ?? session('name') ?? session('user') ?? 'admin' }}";
</script>


</body>
</html>
 