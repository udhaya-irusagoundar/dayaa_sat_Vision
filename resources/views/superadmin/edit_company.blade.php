<!DOCTYPE html>
<html>
<head>
    <title>Edit Company</title>

<style>
/* GLOBAL */
body {
    background: #eef2f7;
    font-family: "Poppins", sans-serif;
    margin: 0;
    padding: 0;
}

/* CARD CONTAINER */
.container {
    width: 430px;
    margin: 60px auto;
    background: #ffffff;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0px 8px 22px rgba(0,0,0,0.12);
    animation: fadeIn 0.4s ease;
    position: relative;
}

/* HEADER */
h2 {
    text-align: center;
    color: #1e3a8a;
    margin-bottom: 10px;
    font-weight: 700;
    font-size: 26px;
}

.title-line {
    width: 60px;
    height: 4px;
    background: #2563eb;
    margin: 8px auto 25px auto;
    border-radius: 8px;
}

/* LABEL */
label {
    font-size: 14px;
    margin-top: 14px;
    display: block;
    font-weight: 600;
    color: #374151;
}

/* INPUT */
input {
    width: 100%;
    padding: 12px;
    margin-top: 6px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
    background: #f8fafc;
    transition: 0.25s;
}
input:focus {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.20);
    outline: none;
}

/* SUBMIT BUTTON */
button {
    width: 100%;
    margin-top: 25px;
    padding: 12px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.25s;
}
button:hover {
    transform: translateY(-2px);
    box-shadow: 0px 5px 12px rgba(37,99,235,0.35);
}

/* BACK BUTTON */
.top-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 12px;
}
.back-btn {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    padding: 9px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.25s;
}
.back-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0px 4px 10px rgba(37, 99, 235, 0.35);
}

.error-text {
    color: red;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0);   }
}
</style>

</head>
<body>

<div class="container">

    <div class="top-actions">
        <a href="{{ route('company.index') }}" class="back-btn">‹ Back</a>
    </div>

    <h2>Edit Company</h2>
    <div class="title-line"></div>

    <form action="{{ route('company.update', $company->id) }}" method="POST" enctype="multipart/form-data">

        @csrf

        <label>Company Name</label>
        <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}" required>
        @error('company_name')
            <span class="error-text">{{ $message }}</span>
        @enderror

        <label>Company Code</label>
        <input type="text" id="company_code" name="company_code" value="{{ old('company_code', $company->company_code) }}" maxlength="4" style="text-transform: uppercase;" required>
        <span id="codeError" class="error-text" style="display:none"></span>
        @error('company_code')
            <span class="error-text">{{ $message }}</span>
        @enderror
       <label>Change Logo</label>
<input type="file" name="logo" accept="image/*">

@error('logo')
    <span class="error-text" style="color:red; font-size:12px; display:block; margin-top:4px;">
        {{ $message }}
    </span>
@enderror

@if($company->logo)
    <img src="{{ asset('uploads/company_logos/' . $company->logo) }}" width="60px" style="margin-top:8px;">
@endif

        <button type="submit" id="updateBtn">Update Company</button>
    </form>

</div>

<script>
document.getElementById("company_code").addEventListener("input", function () {
    let value = this.value.toUpperCase();
    let errorSpan = document.getElementById("codeError");
    let submitBtn = document.getElementById("updateBtn");

    this.value = value.replace(/[^A-Z0-9]/g, '');

    const letters = (this.value.match(/[A-Z]/g) || []).length;
    const digits  = (this.value.match(/[0-9]/g) || []).length;

    if (this.value.length !== 4 || letters !== 2 || digits !== 2) {
        errorSpan.style.display = "block";
        errorSpan.innerText = "Code must contain exactly 2 letters and 2 numbers (AB12, 1A2B, A1B2 etc)";
        submitBtn.disabled = true;
    } else {
        errorSpan.style.display = "none";
        submitBtn.disabled = false;
    }
});
</script>

</body>
</html>
