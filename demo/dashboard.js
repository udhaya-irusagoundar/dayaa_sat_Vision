document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const tableBody = document.getElementById('customerTableBody');
    const addCustomerBtn = document.getElementById('addCustomerBtn');
    const addCustomerModal = document.getElementById('addCustomerModal');
    const addCustomerForm = document.getElementById('addCustomerForm');
    const toastContainer = document.getElementById('toastContainer');
    const noCustomersMessage = document.getElementById('noCustomersMessage');
    const downloadTodayBtn = document.getElementById('downloadTodayBtn');

    const listUrl = customerRoutes.list;
    const storeUrl = customerRoutes.store;

    let currentPage = 1;
    let allCustomers = [];

    // ------------------- Show Add Customer Modal -------------------
    addCustomerBtn.addEventListener('click', () => {
        document.getElementById('newCustomerNumber').value = '';

        // get selected place from dashboard filter
        const selectedPlace = document.getElementById('placeFilter').value;

        // if no place selected → set empty
        // else → set the selected place
        document.getElementById('newPlace').value = selectedPlace || "";

        addCustomerModal.classList.add('show');
    });


    // ------------------- Close Modal -------------------
    document.getElementById('closeAddModal').addEventListener('click', e => {
        e.preventDefault();
        addCustomerModal.classList.remove('show');
    });

    document.getElementById('cancelAddCustomer').addEventListener('click', e => {
        e.preventDefault();
        addCustomerModal.classList.remove('show');
    });

    // ------------------- Load Customers -------------------
    function loadCustomers() {
        let placeFilter = document.getElementById('placeFilter')?.value || '';
        if (!placeFilter) {
            const savedPlace = localStorage.getItem('selectedPlace');
            if (savedPlace) {
                placeFilter = savedPlace;
                document.getElementById('placeFilter').value = savedPlace;
            }
        }
        /*
                // ❗ DO NOT reset full filters — only reset name/date/payment
                document.getElementById('nameSearch').value = "";
                document.getElementById('fromDate').value = "";
                document.getElementById('toDate').value = "";
                document.getElementById('paymentStatusFilter').value = "";
                document.getElementById('staffFilter').value = "";
        */
        currentPage = 1;

        fetch(`${listUrl}?place=${encodeURIComponent(placeFilter)}`)
            .then(res => res.json())
            .then(response => {
                allCustomers = Array.isArray(response.customers) ? response.customers : [];

                const placeSelect = document.getElementById('placeFilter');
                if (placeSelect && placeSelect.value) {
                    placeFilter = placeSelect.value;
                } else if (placeSelect && response.selectedPlace) {
                    placeSelect.value = response.selectedPlace;
                    placeFilter = response.selectedPlace;
                }

                renderTable();
                renderOverallStats(allCustomers);

                const url = new URL(window.location);
                url.searchParams.delete('place');
                window.history.replaceState({}, document.title, url.pathname);
            })
            .catch(err => console.error(err));
    }

    document.getElementById('placeFilter').addEventListener('change', loadCustomers);
    // ---------- Staff Filter Smart Logic ----------
    const staffFilter = document.getElementById("staffFilter");

    // Detect refresh correctly (F5 / browser reload)
    const navEntry = performance.getEntriesByType("navigation")[0];
    const isRefresh = navEntry.type === "reload";

    // Saved staff before place change
    const savedTempStaff = localStorage.getItem("staffFilter_temp");

    // Auto filter when staff changed
    staffFilter.addEventListener("change", () => {
        renderTable();
    });

    function calculateLatestTotal(paymentDates) {
        if (!Array.isArray(paymentDates)) return 0;
        let total = 0;

        paymentDates.forEach(monthPayments => {
            if (Array.isArray(monthPayments) && monthPayments.length > 0) {
                const latest = monthPayments[monthPayments.length - 1];
                total += parseFloat(latest.amount) || 0;
            } else if (monthPayments && typeof monthPayments === "object") {
                total += parseFloat(monthPayments.amount) || 0;
            }
        });

        return total;
    }
    function hasPaidInSelectedRange(paymentDates, fromDateVal, toDateVal) {
        if (!paymentDates || !Array.isArray(paymentDates)) return false;
        if (!fromDateVal && !toDateVal) return false;

        const fromTime = fromDateVal ? new Date(fromDateVal + " 00:00:00").getTime() : null;
        const toTime = toDateVal ? new Date(toDateVal + " 23:59:59").getTime() : null;

        for (const monthPayments of paymentDates) {
            if (Array.isArray(monthPayments)) {
                for (const p of monthPayments) {
                    if (!p?.date) continue;

                    let d = p.date.trim();

                    if (d.includes("/")) {
                        const [dd, mm, yyyy] = d.split("/");
                        d = `${yyyy}-${mm}-${dd}`;
                    }

                    const payDate = new Date(d);
                    if (isNaN(payDate)) continue;

                    const payTime = payDate.getTime();

                    if ((!fromTime || payTime >= fromTime) &&
                        (!toTime || payTime <= toTime)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function isCustomerPaid(customer) {
        if (!customer.paymentDates || !Array.isArray(customer.paymentDates)) return false;

        // Check if ANY month has ANY payment
        return customer.paymentDates.some(monthPayments => {
            if (Array.isArray(monthPayments)) {
                return monthPayments.length > 0;
            } else if (monthPayments && monthPayments.amount) {
                return true;
            }
            return false;
        });
    }
    function thisMonthPaid(paymentDates) {
        if (!Array.isArray(paymentDates)) return false;

        const now = new Date();
        const currentMonth = now.getMonth(); // 0–11
        const currentYear = now.getFullYear();

        // Loop all payments for current month only
        for (const month of paymentDates) {
            if (!Array.isArray(month)) continue;

            for (const p of month) {
                if (!p?.date) continue;

                let d = p.date.trim();
                if (d.includes("/")) {
                    const [dd, mm, yyyy] = d.split("/");
                    d = `${yyyy}-${mm}-${dd}`;
                }

                const payDate = new Date(d);
                if (isNaN(payDate)) continue;

                if (
                    payDate.getMonth() === currentMonth &&
                    payDate.getFullYear() === currentYear
                ) {
                    return true;   // paid this month
                }
            }
        }

        return false;  // unpaid this month
    }
    function getLatestPaymentDate(paymentDates) {
        if (!Array.isArray(paymentDates)) return null;

        let latest = null;

        paymentDates.forEach(month => {
            if (Array.isArray(month)) {
                month.forEach(p => {
                    if (!p?.date) return;

                    let d = p.date.trim();

                    if (d.includes("/")) {
                        const [dd, mm, yyyy] = d.split("/");
                        d = `${yyyy}-${mm}-${dd}`;
                    }

                    const dateObj = new Date(d);
                    if (!isNaN(dateObj)) {
                        if (!latest || dateObj > latest) {
                            latest = dateObj;
                        }
                    }
                });
            }
        });

        return latest;
    }


    // ------------------- Render Table -------------------
    function renderTable() {
        const searchVal = document.getElementById('nameSearch').value.toLowerCase().trim();
        const fromDateVal = document.getElementById('fromDate').value;
        const toDateVal = document.getElementById('toDate').value;
        const entries = parseInt(document.getElementById('entriesSelect').value);
        const paymentStatus = document.getElementById('paymentStatusFilter').value;
        const staffFilter = document.getElementById('staffFilter').value.toLowerCase();


        const from = fromDateVal ? new Date(fromDateVal) : null;
        const to = toDateVal ? new Date(toDateVal) : null;

        let filtered = allCustomers.filter(c => {
            const nameStr = (c.name || '').toLowerCase();
            const numberStr = (c.customerNumber || '').toLowerCase();
            const placeStr = (c.place || '').toLowerCase();

            const nameMatch = !searchVal || nameStr.includes(searchVal) || numberStr.includes(searchVal) || placeStr.includes(searchVal);
            const placeMatch = !document.getElementById('placeFilter')?.value || placeStr === document.getElementById('placeFilter').value.toLowerCase();

            // ---- Payment Status Filter ----
            const currentMonth = new Date().getMonth() + 1; // 1–12
            // ---- Payment Status Filter ----
            function hasPaymentInRange(paymentDates) {
                if (!Array.isArray(paymentDates)) return false;

                for (const month of paymentDates) {
                    if (!Array.isArray(month)) continue;

                    for (const p of month) {
                        if (!p?.date) continue;
                        return true; // has at least one payment
                    }
                }
                return false; // no payments
            }

            // STAFF FILTER (paid_by)
            // STAFF FILTER FIXED
            /*   let staffMatch = true;
   
               if (staffFilter) {
                   staffMatch = false;
   
                   // Normalize staff names
                   const customerStaff = (c.last_paid_by || "").trim().toLowerCase();
   
                   // 1️⃣ Check direct match with last_paid_by column
                   if (customerStaff === staffFilter) {
                       staffMatch = true;
                   }
   
                   // 2️⃣ Look inside payment history
                   if (Array.isArray(c.paymentDates)) {
                       for (const month of c.paymentDates) {
                           if (Array.isArray(month)) {
                               for (const p of month) {
                                   if (p?.paid_by && p.paid_by.trim().toLowerCase() === staffFilter) {
                                       staffMatch = true;
                                       break;
                                   }
                               }
                           }
                       }
                   }
               }
                   */

            let staffMatch = true;

            if (staffFilter) {
                const lastPaid = (c.last_paid_by || "").trim().toLowerCase();
                staffMatch = lastPaid === staffFilter;
            }
            let dateMatch = true;

            if (from || to) {
                // For ALL filter → use latestPay logic
                if (paymentStatus === "") {
                    const latestPay = getLatestPaymentDate(c.paymentDates);

                    if (!latestPay) {
                        dateMatch = false;
                    } else {
                        const t = latestPay.getTime();
                        const fromTime = from ? new Date(from.setHours(0, 0, 0, 0)).getTime() : null;
                        const toTime = to ? new Date(to.setHours(23, 59, 59, 999)).getTime() : null;

                        dateMatch = (!fromTime || t >= fromTime) && (!toTime || t <= toTime);
                    }
                }
                // For PAID / UNPAID filter → use ANY payment inside date range
                else {
                    dateMatch = true; // keep simple, paymentMatch will decide
                }
            }

            let paymentMatch = true;
            if (paymentStatus === "paid") {
                const latestPay = getLatestPaymentDate(c.paymentDates);

                if (!latestPay) return false; // no payment → not paid

                const latest = latestPay.getTime();
                const fromT = fromDateVal ? new Date(fromDateVal + " 00:00:00").getTime() : null;
                const toT = toDateVal ? new Date(toDateVal + " 23:59:59").getTime() : null;

                // latest payment MUST fall inside selected date range
                paymentMatch =
                    (!fromT || latest >= fromT) &&
                    (!toT || latest <= toT);
            }

            if (paymentStatus === "unpaid") {
                const latestPay = getLatestPaymentDate(c.paymentDates);

                // No payment at all → unpaid
                if (!latestPay) return true;

                const latest = latestPay.getTime();
                const fromT = fromDateVal ? new Date(fromDateVal + " 00:00:00").getTime() : null;
                const toT = toDateVal ? new Date(toDateVal + " 23:59:59").getTime() : null;

                // latest payment must be OUTSIDE the selected date range
                paymentMatch =
                    (fromT && latest < fromT) ||
                    (toT && latest > toT);
            }
            // ALL → apply date filter normally
            // 🚀 If ALL selected → ignore date filter (show Paid + Unpaid)
            // ⭐ FIXED DATE FILTER FOR "ALL" STATUS

            // ALL = show everyone, ignore date filter completely
            // ⭐ Payment Status = ALL → ignore date filter completely
            if (paymentStatus === "") {
                return nameMatch && placeMatch && staffMatch;
            }
            // PAID or UNPAID follow date logic
            return nameMatch && placeMatch && paymentMatch && dateMatch && staffMatch;

        });

        const total = filtered.length;
        const totalPages = Math.ceil(total / entries);
        if (currentPage > totalPages) currentPage = totalPages || 1;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * entries;
        const endIndex = startIndex + entries;
        const paginated = filtered.slice(startIndex, endIndex);

        tableBody.innerHTML = '';
        if (paginated.length === 0) {
            noCustomersMessage.style.display = 'block';
        } else {
            noCustomersMessage.style.display = 'none';
            paginated.forEach((customer, index) => {
                const row = `
<tr class="table-row">
    <td class="table-cell">${startIndex + index + 1}</td>
    <td class="table-cell">${customer.customerNumber}</td>
    <td class="table-cell">${customer.name}</td>
    <td class="table-cell">
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill week" style="width: ${customer.week_progress}%"></div>
            </div>
            <span class="progress-text">${customer.paidWeeks} of 12 months</span>
        </div>
    </td>

    <td class="table-cell">₹${calculateLatestTotal(customer.paymentDates).toLocaleString()}</td>

    ${window.appRole === 'admin' ? `<td class="table-cell">${customer.last_paid_by ?? '-'}</td>` : ''}

    <td class="table-cell">
        <div class="action-buttons">
            <button class="action-btn rupee" data-id="${customer.id}">
                <i class="fas fa-rupee-sign"></i>
            </button>
            <button class="action-btn edit" data-id="${customer.id}">
                <i data-lucide="edit"></i>
            </button>
            <button class="action-btn delete" data-id="${customer.id}">
                <i data-lucide="trash-2"></i>
            </button>
        </div>
    </td>
</tr>`;

                tableBody.insertAdjacentHTML('beforeend', row);
            });
        }

        lucide.createIcons();
        attachActionListeners();
        function hasPaymentInRange(paymentDates, fromDate, toDate) {
            if (!Array.isArray(paymentDates)) return false;

            const from = fromDate ? new Date(fromDate + " 00:00:00").getTime() : null;
            const to = toDate ? new Date(toDate + " 23:59:59").getTime() : null;

            for (const month of paymentDates) {
                if (!Array.isArray(month)) continue;

                for (const p of month) {
                    if (!p?.date) continue;

                    let d = p.date.trim();

                    if (d.includes("/")) {
                        const [dd, mm, yyyy] = d.split("/");
                        d = `${yyyy}-${mm}-${dd}`;
                    }

                    const payTime = new Date(d).getTime();

                    if ((!from || payTime >= from) && (!to || payTime <= to)) {
                        return true;
                    }
                }
            }

            return false;
        }


        // ------------------- Pagination Info -------------------
        const paginationInfo = document.getElementById('paginationInfo');
        if (total === 0) {
            paginationInfo.textContent = 'No customers found';
        } else {
            const showingStart = total === 0 ? 0 : startIndex + 1;
            const showingEnd = Math.min(endIndex, total);
            paginationInfo.textContent = `Showing ${showingStart} to ${showingEnd} of ${total} customers`;
        }

        // ------------------- Pagination Controls -------------------
        const paginationControls = document.getElementById('paginationControls');
        paginationControls.innerHTML = '';

        if (totalPages > 1) {
            const createBtn = (text, page, disabled = false, isActive = false) => {
                const btn = document.createElement('button');
                btn.textContent = text;
                btn.classList.add('pagination-btn');
                if (disabled) btn.disabled = true;
                if (isActive) btn.classList.add('active');
                btn.addEventListener('click', () => {
                    if (page < 1 || page > totalPages) return;
                    currentPage = page;
                    renderTable();
                });
                return btn;
            };

            paginationControls.appendChild(createBtn('Prev', currentPage - 1, currentPage === 1));

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

            for (let i = startPage; i <= endPage; i++) {
                paginationControls.appendChild(createBtn(i, i, false, i === currentPage));
            }

            paginationControls.appendChild(createBtn('Next', currentPage + 1, currentPage === totalPages));
        }

        renderFilteredStats(filtered);
    }

    // ------------------- Stats Functions -------------------
    function renderOverallStats(customers) {
        const overallStatsDiv = document.getElementById('overallStats');
        if (!overallStatsDiv) return;

        if (customers.length === 0) {

            return;
        } else {

        }

        const totalCustomers = customers.length;

        // ✅ Calculate total only from latest payment of each month
        let totalCollected = 0;

        customers.forEach(c => {
            if (!Array.isArray(c.paymentDates)) return;

            c.paymentDates.forEach(monthPayments => {
                if (Array.isArray(monthPayments) && monthPayments.length > 0) {
                    const latest = monthPayments[monthPayments.length - 1];
                    totalCollected += parseFloat(latest.amount) || 0;
                } else if (monthPayments && typeof monthPayments === 'object') {
                    totalCollected += parseFloat(monthPayments.amount) || 0;
                }
            });
        });

        overallStatsDiv.innerHTML = `
    <div>
        <div class="stats-row" >
            <div class="stat-card">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value customers">${totalCustomers}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Amount Collected</div>
                <div class="stat-value collected">₹${totalCollected.toLocaleString()}</div>
            </div>
        </div>
    </div>
`;
    }


    function renderFilteredStats(filteredCustomers) {
        const filteredStatsDiv = document.getElementById('filteredStats');
        if (!filteredStatsDiv) return;

        const searchVal = document.getElementById('nameSearch').value.trim();
        const fromDateVal = document.getElementById('fromDate').value;
        const toDateVal = document.getElementById('toDate').value;

        if (!searchVal && !fromDateVal && !toDateVal) {
            filteredStatsDiv.style.display = 'none';
            return;
        }

        if (filteredCustomers.length === 0) {
            filteredStatsDiv.style.display = 'block';
            filteredStatsDiv.innerHTML = `
            <div class="summary-title">Filtered Summary</div>
            <div>No customers found in filtered results.</div>`;
            return;
        }

        filteredStatsDiv.style.display = 'block';

        const totalFiltered = filteredCustomers.length;

        // ✅ Calculate only latest payments per month
        let filteredCollected = 0;

        filteredCustomers.forEach(c => {
            if (!Array.isArray(c.paymentDates)) return;

            c.paymentDates.forEach(monthPayments => {
                if (Array.isArray(monthPayments) && monthPayments.length > 0) {
                    const latest = monthPayments[monthPayments.length - 1];
                    filteredCollected += parseFloat(latest.amount) || 0;
                } else if (monthPayments && typeof monthPayments === 'object') {
                    filteredCollected += parseFloat(monthPayments.amount) || 0;
                }
            });
        });

        filteredStatsDiv.innerHTML = `
        <div class="summary-title" >Filtered Summary</div>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value customers">${totalFiltered}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Amount Collected</div>
                <div class="stat-value collected">₹${filteredCollected.toLocaleString()}</div>
            </div>
        </div>
    `;
    }


    // ------------------- Filters -------------------
    document.getElementById('nameSearch').addEventListener('input', () => { currentPage = 1; renderTable(); });
    document.getElementById('fromDate').addEventListener('change', () => { currentPage = 1; renderTable(); });
    document.getElementById('toDate').addEventListener('change', () => { currentPage = 1; renderTable(); });
    document.getElementById('entriesSelect').addEventListener('change', () => { currentPage = 1; renderTable(); });
    // 🔥 Payment status change aana odane table refresh
    document.getElementById('paymentStatusFilter').addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });
    document.getElementById('staffFilter').addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });



    // ------------------- Action Buttons -------------------
    function attachActionListeners() {
        /* =============================
   EDIT BUTTON — NORMAL EDIT MODE
   ============================= */
        document.querySelectorAll('.edit').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.target.closest('.edit').dataset.id;
                const place = document.getElementById('placeFilter').value;
                window.location.href =
                    `/demo_cable/admin/customers/edit/${id}?place=${place}`;
            });
        });
        document.querySelectorAll('.rupee').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.target.closest('.rupee').dataset.id;

                // get current place filter
                const place = document.getElementById('placeFilter').value || "";

                window.location.href =
                    `/demo_cable/admin/customers/edit/${id}?mode=payment&place=${place}`;
            });
        });



        document.querySelectorAll('.delete').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.target.closest('.delete').dataset.id;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the customer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    fetch(`/demo_cable/admin/customers/destroy/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showToast('Customer deleted successfully', 'success');
                                loadCustomers();
                            } else {
                                showToast('Failed to delete', 'error');
                            }
                        })
                        .catch(() => showToast('Error deleting customer', 'error'));
                });
            });
        });
    }

    // ------------------- Toast -------------------
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast ${type} show`;
        toast.textContent = message;
        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            toast.remove();
        }, 3000);
    }

    // ------------------- Excel Download -------------------
    document.getElementById('downloadBtn').addEventListener('click', () => {
        if (!allCustomers.length) {
            showToast('No customers to download', 'error');
            return;
        }

        const exportData = allCustomers.map((c, index) => ({
            'S.No': index + 1,
            'Customer Number': c.customerNumber,
            'Customer Name': c.name,
            'Total Amount': calculateLatestTotal(c.paymentDates),
            'Staff': c.last_paid_by ?? '-'
        }));

        const ws = XLSX.utils.json_to_sheet(exportData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Customers');

        XLSX.writeFile(wb, 'customers_list.xlsx');
    });



    // ------------------- Download Today's Box Numbers -------------------
    if (downloadTodayBtn) {

        downloadTodayBtn.addEventListener('click', () => {

            const fromDateVal = document.getElementById('fromDate').value;

            const toDateVal = document.getElementById('toDate').value;

            const placeFilterVal = document.getElementById('placeFilter')?.value || '';

            const searchVal = document.getElementById('nameSearch').value.toLowerCase().trim();

            // ✅ flatten payments for each customer

            const filtered = allCustomers.filter(c => {

                const nameStr = (c.name || '').toLowerCase();

                const numberStr = (c.customerNumber || '').toLowerCase();

                const placeStr = (c.place || '').toLowerCase();

                const nameMatch =

                    !searchVal || nameStr.includes(searchVal) || numberStr.includes(searchVal);

                const placeMatch =

                    !placeFilterVal || placeStr === placeFilterVal.toLowerCase();

                // date filter

                const from = fromDateVal ? new Date(fromDateVal) : null;

                const to = toDateVal ? new Date(toDateVal) : null;

                let dateMatch = false;

                let paymentDates = [];

                if (Array.isArray(c.paymentDates)) {

                    c.paymentDates.forEach(monthPayments => {

                        if (Array.isArray(monthPayments)) {

                            monthPayments.forEach(p => p?.date && paymentDates.push(p.date));

                        } else if (monthPayments?.date) {

                            paymentDates.push(monthPayments.date);

                        }

                    });

                }

                if (!from && !to) {

                    dateMatch = true;

                } else {

                    const fromTime = from ? new Date(from.setHours(0, 0, 0, 0)).getTime() : null;

                    const toTime = to ? new Date(to.setHours(23, 59, 59, 999)).getTime() : null;

                    dateMatch = paymentDates.some(dateStr => {

                        if (!dateStr) return false;

                        let normalized = dateStr.trim();

                        if (normalized.includes("/")) {

                            const [dd, mm, yyyy] = normalized.split("/");

                            normalized = `${yyyy}-${mm}-${dd}`;

                        } else if (normalized.includes("-") && normalized.length === 10 && normalized.indexOf("-") === 2) {

                            const [dd, mm, yyyy] = normalized.split("-");

                            normalized = `${yyyy}-${mm}-${dd}`;

                        }

                        const paymentDate = new Date(normalized);

                        if (isNaN(paymentDate)) return false;

                        const paymentTime = paymentDate.getTime();

                        return (!fromTime || paymentTime >= fromTime) && (!toTime || paymentTime <= toTime);

                    });

                }
                // 🔥 STAFF FILTER (same like table)
                const staffFilterVal = document.getElementById('staffFilter').value.trim().toLowerCase();
                let staffMatch = true;
                if (staffFilterVal) {
                    const lastPaid = (c.last_paid_by || "").trim().toLowerCase();
                    staffMatch = lastPaid === staffFilterVal;
                }

                // ---- PAYMENT FILTER 🔥 ----
                const paymentStatusVal = document.getElementById('paymentStatusFilter').value;
                let paymentMatch = true;

                // ⭐ PAYMENT STATUS = ALL → DATE MUST NOT FILTER CUSTOMERS
                if (paymentStatusVal === "") {
                    return nameMatch && placeMatch && staffMatch;
                }

                // 🚀 UNPAID
                if (paymentStatusVal === "unpaid") {

                    const latestPay = getLatestPaymentDate(c.paymentDates);

                    // Never paid → always unpaid
                    if (!latestPay) {
                        return nameMatch && placeMatch && staffMatch;
                    }

                    const latest = latestPay.getTime();
                    const fromT = fromDateVal ? new Date(fromDateVal + " 00:00:00").getTime() : null;
                    const toT = toDateVal ? new Date(toDateVal + " 23:59:59").getTime() : null;

                    // Latest payment must be OUTSIDE selected range
                    const unpaidByDate =
                        (fromT && latest < fromT) ||
                        (toT && latest > toT);

                    return nameMatch && placeMatch && staffMatch && unpaidByDate;
                }


                // 🚀 PAID
                if (paymentStatusVal === "paid") {

                    const latestPay = getLatestPaymentDate(c.paymentDates);
                    if (!latestPay) return false;

                    const latest = latestPay.getTime();
                    const fromT = fromDateVal ? new Date(fromDateVal + " 00:00:00").getTime() : null;
                    const toT = toDateVal ? new Date(toDateVal + " 23:59:59").getTime() : null;

                    return nameMatch && placeMatch && staffMatch &&
                        (!fromT || latest >= fromT) &&
                        (!toT || latest <= toT);
                }


                // ALL
                // ⭐ ALL = ignore date filter completely
                // ALL
                // PAYMENT STATUS = ALL → date should NOT filter customers
                // ⭐ PAYMENT STATUS = ALL → DO NOT filter by date at all



                /*    if (paymentStatusVal === "") {
                        // ALL → if no date selected, show everyone
                        if (!fromDateVal && !toDateVal) {
                            return nameMatch && placeMatch && staffMatch;
                        }
    
                        // ALL + DATE → use latestPay logic (only include customers whose latest payment is inside range)
                        const latestPay = getLatestPaymentDate(c.paymentDates);
    
                        if (!latestPay) return false; // no payment at all -> exclude when date range is specified
    
                        const latest = latestPay.getTime();
                        const fromT = fromDateVal ? new Date(fromDateVal + " 00:00:00").getTime() : null;
                        const toT = toDateVal ? new Date(toDateVal + " 23:59:59").getTime() : null;
    
                        const insideRange =
                            (!fromT || latest >= fromT) &&
                            (!toT || latest <= toT);
    
                        return nameMatch && placeMatch && staffMatch && insideRange;
                    }
    
    */

                /*
                                if (paymentStatusVal === "unpaid") {
                                    paymentMatch = !hasPaidInSelectedRange(c.paymentDates, fromDateVal, toDateVal);
                                }
                
                                // 🚀 Final return
                                if (paymentStatusVal === "unpaid") {
                                    return nameMatch && placeMatch && staffMatch && paymentMatch;
                                }
                
                                return nameMatch && placeMatch && staffMatch && dateMatch && paymentMatch;
                */

            });

            if (!filtered.length) {

                showToast('No customers found for selected filters', 'info');

                return;

            }

            // ✅ Collect only box numbers

            const boxNumbers = filtered.map(c => c.customerNumber).join(',');

            const placeName = placeFilterVal ? placeFilterVal.replace(/\s+/g, '_') : 'All';

            const fileDate = new Date().toISOString().slice(0, 10);

            const blob = new Blob([boxNumbers], { type: 'text/plain' });

            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');

            link.href = url;

            link.download = `${placeName}_box_numbers_${fileDate}.txt`;

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);

            URL.revokeObjectURL(url);

            showToast(`Downloaded ${filtered.length} box numbers for ${placeName}`, 'success');

        });

    }


    // ------------------- Add Customer Submit -------------------
    addCustomerForm.addEventListener('submit', e => {
        e.preventDefault();

        const formData = new FormData(addCustomerForm);

        // Optional: quick client-side validation
        const customerNumber = formData.get('customerNumber')?.trim();
        const name = formData.get('name')?.trim();
        const mobileNumber = formData.get('mobileNumber')?.trim();
        const place = formData.get('place')?.trim();

        if (!customerNumber || !name || !mobileNumber || !place) {
            showToast('All fields are required', 'error');
            return;
        }

        fetch(customerRoutes.store, {
            method: 'POST',
            body: formData, // ✅ let browser handle content-type
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast('Customer added successfully!', 'success');
                    addCustomerModal.classList.remove('show');
                    addCustomerForm.reset();
                    loadCustomers();
                }
                else if (data.errors) {
                    // ✅ Show Laravel validation errors in toast
                    Object.values(data.errors).forEach(errArr => showToast(errArr[0], 'error'));
                }
                else {
                    showToast('Failed to add customer', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Server error while adding customer', 'error');
            });
    });

    // ------------------- Initialize -------------------
    loadCustomers();

});
