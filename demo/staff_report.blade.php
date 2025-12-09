<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Collection Report</title>
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
        body { background: #f4f6f9; font-family: Arial; }

        /* HEADER */
        .header-bar {
            background: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-title {
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-title img { height: 45px; }

        /* BUTTON */
        .back-btn {
            background: #0d6efd;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        .back-btn:hover { background: #0b5ed7; }

        /* PAGE */
        .container { padding: 25px; }
        .title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        /* TABLE */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }
        .report-table thead {
            background: #28a745;
            color: white;
            font-size: 17px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-size: 16px;
        }
        .report-table tr:nth-child(even) { background: #f6f6f6; }

        /* TOTAL */
        .grand-total {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            margin-top: 18px;
            margin-right: 5px;
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
    <button class="back-btn" onclick="window.location.href='{{ route('admin.dashboard') }}'">
        ← Back to dashboard
    </button>
</div>

<div class="container">

    <div class="title">Staff Collection Report</div>
    <form method="GET" action="{{ route('admin.staff.report') }}" style="margin-bottom:20px; text-align:center;">
    <label>From: </label>
    <input type="date" name="from" value="{{ $from ?? '' }}" required>

    &nbsp;&nbsp;

    <label>To: </label>
    <input type="date" name="to" value="{{ $to ?? '' }}" required>

    &nbsp;&nbsp;

    <button type="submit" style="padding:6px 14px; background:#28a745; color:#fff; border:none; border-radius:6px;">
        Filter
    </button>

    @if(request()->from)
    <a href="{{ route('admin.staff.report') }}" style="padding:6px 14px; background:#dc3545; color:#fff; border-radius:6px; text-decoration:none;">
        Reset
    </a>
    @endif
</form>


   <table class="report-table" id="reportTable">

        <thead>
            <tr>
                <th>S.No</th>
                <th>Staff Name</th>
                <th>Total Amount Collected</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($report as $name => $amount)
                @php $grandTotal += $amount; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-transform: capitalize;">{{ $name }}</td>
                    <td>₹{{ number_format($amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="grand-total">
        Total Collected: ₹{{ number_format($grandTotal) }}
    </div>

</div>
<script>
$(document).ready(function() {
    $('#reportTable').DataTable({
        pageLength: 10,
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });
});
</script>

</body>
</html>
