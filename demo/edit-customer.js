let currentCustomer = window.currentCustomer;

// ✅ Base amount from DB (customers.amount)
const baseAmount = currentCustomer.amount ? parseFloat(currentCustomer.amount) : 0;

document.addEventListener('DOMContentLoaded', () => {
    if (!currentCustomer) {
        window.location.href = dashboardUrl;
        return;
    }

    lucide.createIcons();
    initializeEventListeners();
    populateCustomerForm();

    // Ensure the structure for months (12 months)
    currentCustomer.weeksPaid = currentCustomer.weeksPaid || new Array(12).fill(false);
    currentCustomer.paymentDates = currentCustomer.paymentDates || new Array(12).fill(null);

    renderMonthlyGrid();
    updateProgress();
});

// ------------------- Initialize Event Listeners -------------------
function initializeEventListeners() {
    if (window.editMode === "edit") {
        document.getElementById('saveChangesBtn')
            ?.addEventListener('click', handleSaveChanges);
    }

    document.getElementById('downloadPDFBtn')?.addEventListener('click', handleDownloadPDF);

    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('input', handleFormInputChange);
    });
}

// ------------------- Populate Form -------------------
function populateCustomerForm() {
    document.getElementById('customerNumber').value = currentCustomer.customerNumber;
    document.getElementById('customerName').value = currentCustomer.name;
    if (document.getElementById('mobileNumber'))
        document.getElementById('mobileNumber').value = currentCustomer.mobileNumber || '';

    if (document.getElementById('place'))
        document.getElementById('place').value = currentCustomer.place || '';

    if (window.editMode === "payment") {
        document.getElementById('pageTitle').textContent =
            `Payment Details - ${currentCustomer.name}`;
    } else {
        document.getElementById('pageTitle').textContent =
            `Edit Customer - ${currentCustomer.name}`;
    }
}

// ------------------- Render Monthly Grid -------------------
function renderMonthlyGrid() {
    const months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    const container = document.getElementById('weeklyGrid');
    container.innerHTML = "";

    for (let i = 0; i < 12; i++) {
        const monthPayments = currentCustomer.paymentDates[i];
        let paid = false;
        let latestPayment = null;

        // ✅ handle multiple payments
        if (Array.isArray(monthPayments) && monthPayments.length > 0) {
            paid = true;
            latestPayment = monthPayments[monthPayments.length - 1]; // get latest
        } else if (monthPayments && typeof monthPayments === "object") {
            paid = true;
            latestPayment = monthPayments;
        }

        const card = document.createElement('div');
        card.className = `month-card ${paid ? 'paid' : ''}`;
        card.innerHTML = `
            <div class="month-header">${months[i]}</div>
            ${paid
                ? `<div class="month-info">₹${latestPayment.amount}<br>${latestPayment.date}</div>`
                : `<div class="month-info unpaid">UnPaid<br>₹${baseAmount}</div>`
            }
        `;

        if (window.editMode === "payment") {
            // allow editing
            card.addEventListener('click', () => openMonthPopup(i, months[i]));
        } else {
            // normal edit mode → monthly grid readonly
            card.classList.add("disabled-month");
        }

        container.appendChild(card);
    }
}

// ------------------- Popup to Enter Amount -------------------
function openMonthPopup(index, monthName) {

    const modal = document.createElement('div');
    modal.className = 'month-popup';
    modal.innerHTML = `
    <div class="popup-content">
        <h3 class="popup-title">Payment for ${monthName}</h3>

        <div class="popup-form">
            <div class="popup-field">
                <label>Box Number</label>
                <input type="text" value="${currentCustomer.customerNumber || '-'}" readonly>
            </div>

            <div class="popup-field">
                <label>Customer Name</label>
                <input type="text" value="${currentCustomer.name}" readonly>
            </div>

            <div class="popup-field">
                <label>Mobile Number</label>
                <input type="text" value="${currentCustomer.mobileNumber || '-'}" readonly>
            </div>

            <div class="popup-field">
                <label>Place</label>
                <input type="text" value="${currentCustomer.place || '-'}" readonly>
            </div>

            <div class="popup-field">
                <label>Base Amount<span style="color:red;">*</span></label>
               <input 
    type="number" 
    id="monthAmountInput" 
    value="${currentCustomer.amount || ''}"
    readonly
>
            </div>
            <div class="popup-field">
                <label>Change Amount</label>
                <input 
                    type="number" 
                    id="monthAmountChange" 
                    class="popup-input"
                    placeholder="Enter new amount (optional)"
                    min="0"
                >
            </div>
        </div>

        <div class="popup-actions">
            <button id="cancelMonth" class="cancel-btn">Cancel</button>
            <button id="saveMonth" class="save-btn">Save</button>
        </div>
    </div>
    `;

    document.body.appendChild(modal);

    // ✅ Default amount → baseAmount
    const mainAmountInput = document.getElementById("monthAmountInput");
    if (mainAmountInput && baseAmount > 0) {
        mainAmountInput.value = baseAmount;
    }

    document.getElementById('cancelMonth').addEventListener('click', () => modal.remove());

    // FINAL SAVE BUTTON
    document.getElementById('saveMonth').addEventListener('click', () => {

        const mainAmount = parseFloat(document.getElementById("monthAmountInput").value || 0);
        const changeAmount = parseFloat(document.getElementById("monthAmountChange").value || 0);

        let finalAmount = mainAmount;

        // If change amount entered → override main amount
        if (!isNaN(changeAmount) && changeAmount > 0) {
            finalAmount = changeAmount;
        }

        if (isNaN(finalAmount) || finalAmount <= 0) {
            showToast('Please enter a valid amount.', 'error');
            return;
        }

        const formattedDate = new Date().toLocaleDateString('en-GB');

        // Update JS structure
        currentCustomer.weeksPaid[index] = true;

        if (!Array.isArray(currentCustomer.paymentDates[index])) {
            currentCustomer.paymentDates[index] = [];
        }

        currentCustomer.paymentDates[index].push({
            month: monthName,
            amount: finalAmount,   // ✅ FIX: use finalAmount
            date: formattedDate,
            paid_by: window.appUser || window.appRole
        });
        currentCustomer.last_paid_by = window.appUser || window.appRole;

        // 🔥 IMPORTANT — CALCULATE totalPaid BEFORE SENDING TO DB
        let totalPaid = 0;

        currentCustomer.paymentDates.forEach(month => {
            if (Array.isArray(month) && month.length > 0) {
                totalPaid += parseFloat(month[month.length - 1].amount) || 0;
            } else if (month && typeof month === "object") {
                totalPaid += parseFloat(month.amount) || 0;
            }
        });

        // SAVE TO DB
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/demo_cable/admin/customers/update/${currentCustomer.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                customerNumber: currentCustomer.customerNumber,
                name: currentCustomer.name,
                mobileNumber: currentCustomer.mobileNumber,
                place: currentCustomer.place,
                weeksPaid: JSON.stringify(currentCustomer.weeksPaid),
                paymentDates: JSON.stringify(currentCustomer.paymentDates),
                totalAmount: totalPaid,
                year: document.getElementById("yearSelector")?.value
            }),
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    modal.remove();
                    showToast('Payment updated!', 'success');

                    renderMonthlyGrid();
                    updateProgress();

                    setTimeout(() => {
                        const params = new URLSearchParams(window.location.search);
                        const place = params.get("place") || "";
                        const search = params.get("search") || "";

                        if (window.appRole === "staff") {
                            window.location.href = `/demo_cable/admin/staff/search?search=${search}`;
                        } else {
                            window.location.href =
                                `/demo_cable/admin/dashboard?place=${place}&search=${search}`;
                        }
                    }, 1000);

                } else {
                    showToast('Update failed!', 'error');
                }

            })
            .catch(() => showToast('Server error!', 'error'));

    });
}


// ------------------- Update Progress -------------------
function updateProgress() {
    const paidCount = currentCustomer.weeksPaid.filter(Boolean).length;

    // only latest amount from each month
    let totalPaid = 0;

    currentCustomer.paymentDates.forEach(month => {
        if (Array.isArray(month) && month.length > 0) {
            const latestPayment = month[month.length - 1];
            totalPaid += parseFloat(latestPayment.amount) || 0;
        } else if (month && typeof month === "object") {
            totalPaid += parseFloat(month.amount) || 0;
        }
    });

    document.getElementById('weeksProgressText').textContent = `${paidCount} of 12 months paid`;
    document.getElementById('amountProgressText').textContent = `₹${totalPaid.toLocaleString()}`;
}

// ------------------- Save Changes via AJAX -------------------
function handleSaveChanges() {
    const url = `/demo_cable/admin/customers/update/${currentCustomer.id}`;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let totalPaid = 0;

    currentCustomer.paymentDates.forEach(month => {
        if (Array.isArray(month) && month.length > 0) {
            totalPaid += parseFloat(month[month.length - 1].amount) || 0;
        } else if (month && typeof month === "object") {
            totalPaid += parseFloat(month.amount) || 0;
        }
    });
    if (window.appRole === "admin") {
        currentCustomer.amount = document.getElementById("baseAmount")?.value;
    }


    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({
            customerNumber: currentCustomer.customerNumber,
            name: currentCustomer.name,
            mobileNumber: currentCustomer.mobileNumber,
            place: currentCustomer.place,
            weeksPaid: JSON.stringify(currentCustomer.weeksPaid),
            paymentDates: JSON.stringify(currentCustomer.paymentDates),
            totalAmount: totalPaid,
            baseAmount: currentCustomer.amount,
            year: document.getElementById("yearSelector")?.value
        }),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Customer updated successfully!', 'success');
                setTimeout(() => window.location.href = data.redirect, 1000);
            } else {
                showToast('Update failed.', 'error');
            }
        })
        .catch(() => showToast('Server error while saving.', 'error'));
}

// ------------------- PDF Download -------------------
function handleDownloadPDF() {
    showToast('Generating PDF...', 'info');
    const jsPDFConstructor = window.jspdf ? window.jspdf.jsPDF : null;
    if (!jsPDFConstructor) return showToast('jsPDF not loaded!', 'error');

    const container = document.getElementById('customerDetailsContainer');
    html2canvas(container, { scale: 2 }).then(canvas => {
        const pdf = new jsPDFConstructor('p', 'mm', 'a4');
        const imgData = canvas.toDataURL('image/png');
        const width = pdf.internal.pageSize.getWidth();
        const height = (canvas.height * width) / canvas.width;
        pdf.addImage(imgData, 'PNG', 0, 0, width, height);
        pdf.save(`${currentCustomer.customerNumber}_${currentCustomer.name}.pdf`);
        showToast('PDF downloaded successfully!', 'success');
    });
}

// ------------------- Toast -------------------
function showToast(msg, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => container.removeChild(toast), 300);
    }, 2500);
}

// ------------------- Handle Inline Form Edits -------------------
function handleFormInputChange(e) {
    const field = e.target.id;
    const value = e.target.value;

    switch (field) {
        case 'customerNumber':
            currentCustomer.customerNumber = value.toUpperCase();
            break;
        case 'customerName':
            currentCustomer.name = value;
            break;
        case 'mobileNumber':
            currentCustomer.mobileNumber = value;
            break;
        case 'place':
            currentCustomer.place = value;
            break;
    }
}
