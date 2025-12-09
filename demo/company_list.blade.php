<!DOCTYPE html>
<html>
<head>
    <title>Company List</title>

<style>
/* GLOBAL */
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

/* CONTENT CONTAINER */
.container {
    width: 95%;
    margin: 15px auto !important;
    margin-left: 240px !important;
    margin-top: 80px !important;
  
    border-radius: 12px;
    padding: 18px 25px !important;
  
}

/* TITLE */
h2 {
    margin: 0 0 12px 0 !important;
    font-size: 24px;
    color: #1e3a8a;
    text-align: center;
    font-weight: 700;
}

/* SUCCESS BOX */
.success {
    padding: 10px !important;
    text-align: center;
    background: #d1fad1;
    border-left: 5px solid #1b8f1b;
    color: #145c14;
    border-radius: 6px;
    margin: 10px 0 15px 0 !important;
    font-size: 14px;
    font-weight: 500;
}

/* TOP ACTION BAR */
.top-actions {
    width: 100%;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 12px !important;
}

/* ADD BUTTON */
.add-btn {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    padding: 10px 18px !important;
    border-radius: 8px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: 0.25s;
}
.add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0px 4px 10px rgba(37,99,235,0.35);
}

/* TABLE */
table {
    width: 100%;
    margin-top: 5px !important;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
table th {
    background: #1e40af;
    color: white;
    padding: 10px !important;
    font-size: 14px;
}
table td {
    padding: 10px !important;
    font-size: 14px;
    color: #334155;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
    transition: 0.18s;
    text-align: center;
}
table tr:hover td {
    background: #f1f5ff;
}

/* ICONS */
table td a {
    text-decoration: none !important;
}
td i {
    transition: 0.2s ease;
    cursor: pointer;
}
td i:hover {
    transform: scale(1.18);
    opacity: .85;
}

/* POPUP OVERLAY */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(3px);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* POPUP BOX */
.modal-content {
    width: 280px;
    background: #fff;
    padding: 20px 20px 25px;
    border-radius: 16px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.25);
    animation: popup 0.35s ease-out;
    position: relative;
}

/* CLOSE BUTTON */
.modal-content .close {
    position: absolute;
    top: 8px;
    right: 12px;
    font-size: 26px;
    cursor: pointer;
    color: #444;
    transition: 0.2s;
}
.modal-content .close:hover {
    color: #e60000;
}

/* POPUP TITLE */
.modal-content h2 {
    text-align: center;
    font-size: 18px;
    margin-top: 5px;
    margin-bottom: 18px;
    font-weight: 600;
}

/* LABEL */
.modal-content label {
    font-size: 13px;
    font-weight: 600;
    margin-top: 6px;
    margin-bottom: 3px;
    display: block;
    color: #1f2937;
}

/* INPUT FIELD */
.modal-content input {
    width: 90%;
    padding: 7px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 13px;
    background: #f8fafc;
    margin-bottom: 10px;
    transition: 0.2s;
}
.modal-content input:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 4px rgba(37,99,235,0.25);
} /* ✅ DASHBOARD STYLE WHITE HEADER */
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


/* BUTTON */
.modal-content button {
    width: 95%;
    padding: 10px;
    background: #2563eb;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 8px;
    transition: 0.25s;
}
.modal-content button:hover {
    background: #1e40af;
    transform: translateY(-2px);
}
.error-text {
    color: red;
    font-size: 12px;
    display: block;
    margin-top: -5px;
    margin-bottom: 5px;
}


/* POPUP ANIMATION */
@keyframes popup {
    from { opacity: 0; transform: scale(0.85); }
    to { opacity: 1; transform: scale(1); }
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
</div>

<!-- ✅ CORRECT DASHBOARD HEADER -->
<div class="top-header">
    <h2>Companies</h2>

    <form action="{{ route('superadmin.logout') }}" method="POST">
        @csrf
        <button class="logout-top-btn">
            <i data-lucide="log-out"></i> Logout
        </button>
    </form>
</div>
<!-- MAIN CONTENT -->
<div class="container">
  

    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <div class="top-actions">
       <a href="javascript:void(0)" onclick="openModal()" class="add-btn">+ Add Company</a>

    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Company Name</th>
            <th>Company Code</th>
            <th>Logo</th>
            <th>DB Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        @foreach($companies as $c)
        <tr>
          <td>{{ $loop->iteration }}</td>
            <td>{{ $c->company_name }}</td>
            <td>{{ $c->company_code }}</td>
            <td>
    
        <img src="{{ asset('public/uploads/company_logos/' . $c->logo) }}" style="width:45px; height:45px; object-fit:contain;">
   
</td>

            <td>{{ $c->db_name }}</td>
        
            <td>
    @if($c->status == 1)
        <a href="#" class="status-toggle" data-url="{{ route('company.toggle', $c->id) }}">
            <i data-lucide="thumbs-up" style="color:#0a7d24; width:24px; height:24px;"></i>
        </a>
    @else
        <a href="#" class="status-toggle" data-url="{{ route('company.toggle', $c->id) }}">
            <i data-lucide="thumbs-down" style="color:#b30000; width:24px; height:24px;"></i>
        </a>
    @endif
</td>


            <td>
                <a href="{{ route('company.edit', $c->id) }}">
                    <i data-lucide="pencil" style="color:#2563eb; width:20px; height:20px;"></i>
                </a>

                <a href="#" class="delete-btn" data-url="{{ route('company.delete', $c->id) }}">
    <i data-lucide="trash-2" style="color:#d00000; width:20px; height:20px; margin-left:8px;"></i>
</a>

            </td>
        </tr>
        @endforeach
    </table>
</div>

<!-- ADD COMPANY POPUP -->
<div id="addCompanyModal" class="modal">
    <div class="modal-content">

        <span class="close" onclick="closeModal()">×</span>

        <h2>Create Company</h2>

       <form method="POST" action="{{ route('company.store') }}" enctype="multipart/form-data">

    @csrf

    <label>Company Name</label>
    <input type="text" name="company_name" value="{{ old('company_name') }}" required>
    @error('company_name')
        <span class="error-text">{{ $message }}</span>
    @enderror

   <label>Company Code</label>
<input type="text" id="company_code" name="company_code" maxlength="4" style="text-transform: uppercase;" required>
<span id="codeError" class="error-text" style="display:none"></span>
@error('company_code')
    <span class="error-text">{{ $message }}</span>
@enderror
<label>Company Logo</label>
<input type="file" name="logo" accept="image/*" onchange="previewLogo(event)">
@error('logo')
  <span class="error-text">{{ $message }}</span>
@enderror

<!-- Preview box -->
<img id="logoPreview" src="{{ asset('public/img/new_logo.png') }}" 
     style="width: 80px; height: 80px; object-fit: contain; margin-top: 8px; display:none; border:1px solid #ddd; padding:4px; border-radius:6px;">

    <button type="submit">Create Company</button>
</form>

    </div>
</div>
<script>
function openModal() {
    document.getElementById("addCompanyModal").style.display = "flex";
}
function closeModal() {
    document.getElementById("addCompanyModal").style.display = "none";
}
</script>
<script>
document.getElementById("company_code").addEventListener("input", function () {
    let value = this.value.toUpperCase();
    let errorSpan = document.getElementById("codeError");

    this.value = value.replace(/[^A-Z0-9]/g, '');  // allow only A-Z and 0-9

    if (!/^[A-Z]{2}[0-9]{2}$/.test(this.value)) {
        errorSpan.style.display = "block";
        errorSpan.innerText = "Code must be 2 letters + 2 numbers (Example: AB12)";
    } else {
        errorSpan.style.display = "none";
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".delete-btn");

    deleteButtons.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault(); // stop link
            
            let url = this.getAttribute("data-url");

            Swal.fire({
                title: "Are you sure?",
                text: "This company will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url; // redirect to delete route
                }
            });
        });
    });
});
document.addEventListener("DOMContentLoaded", function () {
    // STATUS TOGGLE CONFIRM
    const toggles = document.querySelectorAll(".status-toggle");

    toggles.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            let url = this.getAttribute("data-url");

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to change company status?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0a7d24",
                cancelButtonColor: "#b30000",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
@if ($errors->any())
<script>
    document.getElementById("addCompanyModal").style.display = "flex";
</script>
@endif

</body>
</html>
