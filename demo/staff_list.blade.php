<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff List</title>
    <link rel="stylesheet" href="{{asset('public/css/dashboard.css')}}">
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" 
href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" 
href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- JSZip (Excel export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- PDFMake (PDF export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<style>
body {
    background: #f4f7fb;
    font-family: "Poppins", sans-serif;
}

/* HEADER */
.header-bar {
    background: #fff;
    padding: 18px 32px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.header-title {
    font-size: 24px;
    font-weight: 700;
    color: #2563eb;
    display: flex;
    align-items: center;
    gap: 12px;
}
.header-title img {
    height: 40px;
}

/* BUTTON AREA */
.button-row {
    display: flex;
    gap: 12px;
}
.btnprimary {
    background: #2563eb;
    color: white;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btnprimary:hover {
    background: #1e48c7;
}
.back-btn {
    background: #047857;
    color: white;
    padding: 9px 18px;
    border-radius: 8px;
    border: none;
    font-size: 14px;
    font-weight: 600;
}
.back-btn:hover {
    background: #036548;
}

/* TITLE */
h1 {
    font-size: 26px;
    font-weight: 700;
    color: #1e293b;
}

/* TABLE */
.staff-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
    margin-top: 15px;
}
.staff-table thead th {
    background: none;
    color: #1e293b;
    border: none;
    font-weight: 600;
    font-size: 15px;
}
.staff-table tbody tr {
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-radius: 8px;
}
.staff-table td {
    padding: 13px;
    border: none;
    text-align: center;
    font-size: 14px;
    color: #334155;
}
.staff-table tbody tr td:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}
.staff-table tbody tr td:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
}

/* ACTION BUTTONS */
.edit-btn, .delete-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
}
.edit-btn { color: #2563eb; }
.delete-btn { color: #dc2626; }

/* MODAL */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 999;
}
.modal.show { display: flex; }
.modal-content {
    background: white;
    width: 420px;
    padding: 32px;
    border-radius: 14px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.22);
    position: relative;
    animation: fadeIn .28s ease-out;
}
.modal-title {
    font-size: 22px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 10px;
    color: #1e293b;
}
.modal-close {
    position: absolute;
    right: -5px;
    top: -5px;
    border: none;
    background: #e2e8f0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
}

/* INPUTS */
.form-group label {
    font-size: 14px;
    font-weight: 600;
    color: #475569;
}
.form-input {
    width: 100%;
    padding: 11px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    margin: 4px 0 10px;
    font-size: 14px;
}
.form-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 4px rgba(37,99,235,0.3);
}
.cancel-btn, .submit-btn {
    padding: 9px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    border: none;
}
.cancel-btn {
    background: #e2e8f0;
}
.submit-btn {
    background: #2563eb;
    color: #fff;
}
.submit-btn:hover {
    background: #1e48c7;
}

</style>
</head>

<body>

<div class="header-bar">
    <div class="header-title">
      @if(session('company_logo'))
        <img src="{{ asset('public/uploads/company_logos/' . session('company_logo')) }}" 
             alt="Icon" width="auto" height="40px" style="object-fit:contain;">
    @else
        <img src="{{ asset('public/img/new_logo.png') }}" 
             alt="Icon" width="auto" height="40px">
    @endif

       {{ session('company_name') }}
    </div>
      <!-- Buttons Row -->
    <div class="button-row">
        <button id="addStaffBtn" class="btnprimary mb-4"> Add Staff
        </button>

        <button class="back-btn" onclick="window.location.href='/demo_cable/admin/dashboard'">
            ← Back to Dashboard
        </button>
    </div>
</div>

<div class="container mt-4 mb-5">

   <div style="text-align: center;">
    <h1 class="fw-bold mb-4">Staff Details</h1>
</div>

<div id="staffContainer" class="main-content">

    <!-- Add/Edit Staff Modal -->
    <div id="staffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add/Edit Staff</h3>
                <button type="button" id="closeStaffModal" class="modal-close">
                    <i data-lucide="x"></i>
                </button>
            </div>
            
            <form id="staffForm">
                @csrf
                <input type="hidden" id="staffId" name="staffId">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="staffName" class="form-input" required>
                    <div id="nameError" class="form-error"></div>
                </div>
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" name="mobile" id="staffMobile" class="form-input" maxlength="10" required>
                    <div id="mobileError" class="form-error"></div>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="staffUsername" class="form-input" required>
                    <div id="usernameError" class="form-error"></div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="staffPassword" class="form-input">
                    <div id="passwordError" class="form-error"></div>
                </div>
                <div class="modal-actions">
                    <button type="button" id="cancelStaff" class="cancel-btn">Cancel</button>
                    <button type="submit" class="submit-btn">Save Staff</button>
                </div>
            </form>
        </div>
    </div>

  

    <!-- Staff Table -->
    <table class="staff-table" id="staffDataTable">

        <thead>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="staffTableBody"></tbody>
    </table>

</div>

<!-- ✔ ADD THIS LINE: Lucide JS -->
<script src="https://unpkg.com/lucide@latest"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
      // 🆕 Add this function here
function updateStaffFilterDropdown(staffList) {
    // update the dropdown in dashboard page
    const staffFilter = window.opener?.document.getElementById("staffFilter");
    if (!staffFilter) return;

    const savedValue = localStorage.getItem("staffFilter") ?? staffFilter.value;

    staffFilter.innerHTML = `<option value="">All Staff</option><option value="admin">Admin</option>`;

    staffList.forEach(staff => {
        staffFilter.innerHTML += `<option value="${staff.username}">${staff.name}</option>`;
    });

    staffFilter.value = savedValue;
}
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons(); // initialize lucide globally

        const addStaffBtn = document.getElementById('addStaffBtn');
        const staffModal = document.getElementById('staffModal');
        const closeStaffModal = document.getElementById('closeStaffModal');
        const cancelStaffBtn = document.getElementById('cancelStaff');
        const staffForm = document.getElementById('staffForm');
        const staffTableBody = document.getElementById('staffTableBody');

        // Show modal for adding new staff
        addStaffBtn.addEventListener('click', () => {
            staffForm.reset();
            document.querySelectorAll('.form-error').forEach(e => e.textContent = "");
            document.querySelectorAll('.form-input').forEach(e => e.classList.remove('input-error'));
            document.getElementById('staffId').value = '';
            staffModal.classList.add('show');
        });

        // Close modal
        [closeStaffModal, cancelStaffBtn].forEach(btn => {
            btn.addEventListener('click', () => {
                staffModal.classList.remove('show');
            });
        });

        // Load staff list
        async function loadStaffTable() {
            try {
                const response = await fetch("{{ route('admin.staff.list') }}");
                const data = await response.json();
                staffTableBody.innerHTML = '';
                data.forEach((staff, index) => {
                    staffTableBody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${staff.name}</td>
                            <td>${staff.mobile}</td>
                            <td>${staff.username}</td>
                            <td>
                                <button class="edit-btn" data-id="${staff.id}">
                                    <i data-lucide="edit"></i>
                                </button>

                                <button class="delete-btn" data-id="${staff.id}">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                lucide.createIcons(); 
                updateStaffFilterDropdown(data);
                
   // ⭐ FIX : Only initialize first time
if (!$.fn.DataTable.isDataTable('#staffDataTable')) {
    $('#staffDataTable').DataTable({
        pageLength: 10,
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print'],
        ordering: false
    });
} else {
    // refresh table content only
    $('#staffDataTable').DataTable().ajax?.reload ?? null;
}

                // Edit button click
                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        const res = await fetch(`{{ url('admin/staff/edit') }}/${id}`);
                        const result = await res.json();
                        document.getElementById('staffId').value = result.id;
                        document.getElementById('staffName').value = result.name;
                        document.getElementById('staffMobile').value = result.mobile;
                        document.getElementById('staffUsername').value = result.username;
                        staffModal.classList.add('show');
                    });
                });

                // Delete button click
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-id');
                        const confirmed = await Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!'
                        });
                        if (confirmed.isConfirmed) {
                            const res = await fetch(`{{ url('admin/staff/destroy') }}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const result = await res.json();
                           if(result.status === 'success'){
    Swal.fire('Deleted!', result.message, 'success');

    // refresh table
    $('#staffDataTable').DataTable().clear().destroy();
    loadStaffTable();

} else {
    Swal.fire('Error', result.message, 'error');
}

                        }
                    });
                });

            } catch(err){
                console.error('Error loading staff:', err);
            }
        }

        loadStaffTable();
        

        // Form submit (add/edit staff)
        staffForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            document.querySelectorAll('.form-error').forEach(e => e.textContent = "");
            document.querySelectorAll('.form-input').forEach(e => e.classList.remove('input-error'));

            const formData = new FormData(staffForm);
            const staffId = document.getElementById('staffId').value;
            const url = staffId
                ? `{{ url('admin/staff/update') }}/${staffId}`
                : `{{ url('admin/staff/store') }}`;

            const method = 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const result = await res.json();

                if (result.status === 'error' && result.errors) {
                    if (result.errors.name) {
                        document.getElementById('nameError').textContent = result.errors.name[0];
                        document.getElementById('staffName').classList.add('input-error');
                    }
                    if (result.errors.mobile) {
                        document.getElementById('mobileError').textContent = result.errors.mobile[0];
                        document.getElementById('staffMobile').classList.add('input-error');
                    }
                    if (result.errors.username) {
                        document.getElementById('usernameError').textContent = result.errors.username[0];
                        document.getElementById('staffUsername').classList.add('input-error');
                    }
                    if (result.errors.password) {
                        document.getElementById('passwordError').textContent = result.errors.password[0];
                        document.getElementById('staffPassword').classList.add('input-error');
                    }
                    return;
                }

              if (result.status === 'success') {
    Swal.fire('Success', result.message, 'success');
    staffModal.classList.remove('show');
    staffForm.reset();

    // refresh table UI
    $('#staffDataTable').DataTable().clear().destroy();
    loadStaffTable();
}
else {
                    Swal.fire('Error', result.message, 'error');
                }

            } catch (err) {
                Swal.fire('Error', 'Something went wrong', 'error');
                console.error(err);
            }
        });

    });
    
</script>

</body>
</html>
