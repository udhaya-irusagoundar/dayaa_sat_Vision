/* Dashboard Page Styles */

:root {
--font-size: 16px;
--background: #fafbff;
--foreground: #1e293b;
--card: #ffffff;
--card-foreground: #1e293b;
--primary: #2563eb;
--primary-foreground: #ffffff;
--secondary: #f1f5f9;
--secondary-foreground: #334155;
--muted: #f8fafc;
--muted-foreground: #64748b;
--border: #e2e8f0;
--input: transparent;
--input-background: #f8fafc;
--font-weight-medium: 500;
--font-weight-normal: 400;
--ring: #3b82f6;
--radius: 0.625rem;
--destructive: #dc2626;
--destructive-foreground: #ffffff;
}

* {
box-sizing: border-box;
margin: 0;
padding: 0;
}

html {
font-size: var(--font-size);
}

body {
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
line-height: 1.5;
color: var(--foreground);
background-color: #f9fafb;
min-height: 100vh;
}

/* Header Styles */
.header {
background-color: var(--card);
border-bottom: 1px solid var(--border);
box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
}

.header-container {
max-width: 80rem;
margin: 0 auto;
padding: 0 1rem;
}

.header-content {
display: flex;
align-items: center;
justify-content: space-between;
padding: 1rem 0;
}

.header-left {
display: flex;
align-items: center;
gap: 0.5rem;
}

.header-title {
font-size: 1.25rem;
font-weight: var(--font-weight-medium);
color: var(--primary);
}

.header-right {
display: flex;
align-items: center;
gap: 1.5rem;
}

.user-info {
display: flex;
align-items: center;
gap: 0.5rem;
font-size: 0.875rem;
color: var(--muted-foreground);
}

.user-icon {
height: 1rem;
width: 1rem;
}

.logout-btn,
.btnprimary {
display: flex;
align-items: center;
gap: 0.5rem;
padding: 0.5rem 1rem;
border: 1px solid var(--border);
background-color: transparent;
color: var(--foreground);
border-radius: calc(var(--radius) - 2px);
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
cursor: pointer;
transition: all 0.2s ease-in-out;
height: 2.25rem;
}

.logout-btn:hover {
background-color: var(--muted);
}

.logout-icon {
height: 1rem;
width: 1rem;
}

/* Filter Section */
.filter-section {
background-color: var(--card);
border-bottom: 1px solid var(--border);
}

.filter-container {
max-width: 80rem;
margin: 0 auto;
padding: 1.5rem 1rem;
}

.filter-grid {
display: grid;
grid-template-columns: 1fr;
gap: 1.5rem;
}

@media (min-width: 768px) {
.filter-grid {
grid-template-columns: repeat(3, 1fr);
align-items: end;
}
}

.filter-field {
display: flex;
flex-direction: column;
gap: 0.5rem;
}

.filter-label {
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
color: var(--foreground);
}

.search-input-wrapper,
.date-input-wrapper {
position: relative;
}

.search-icon,
.calendar-icon {
position: absolute;
left: 0.75rem;
top: 50%;
transform: translateY(-50%);
height: 1rem;
width: 1rem;
color: var(--muted-foreground);
}

.search-input,
.date-input {
width: 100%;
height: 2.5rem;
padding: 0.5rem 0.75rem 0.5rem 2.5rem;
border: 1px solid var(--border);
border-radius: calc(var(--radius) - 2px);
background-color: var(--input-background);
font-size: 0.875rem;
transition: all 0.2s ease-in-out;
outline: none;
}

.search-input:focus,
.date-input:focus {
border-color: var(--ring);
box-shadow: 0 0 0 2px rgb(59 130 246 / 0.1);
}

.search-input::placeholder {
color: var(--muted-foreground);
}

/* Main Content */
.main-content {
max-width: 80rem;
margin: 0 auto;
padding: 1.5rem 1rem;
}

.controls-section {
display: flex;
align-items: center;
justify-content: space-between;
gap: 20px;
flex-wrap: wrap;
}

/* LEFT */
.controls-left {
justify-self: start;
display: flex;
align-items: center;
}

/* CENTER */
#overallStats {
text-align: center;
width: fit-content;
}


/* RIGHT */
.controls-right {
justify-self: end;
display: flex;
gap: 10px;
white-space: nowrap;
}

/* Mobile Responsive */
@media(max-width: 768px) {
.controls-section {
grid-template-columns: 1fr;
gap: 15px;
text-align: center;
}

.controls-left,
#overallStats,
.controls-right {
justify-self: center;
}
}

.entries-control {
display: flex;
align-items: center;
gap: 0.75rem;
}

.entries-label {
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
color: var(--foreground);
}

.entries-select {
width: 5rem;
height: 2.5rem;
padding: 0.5rem 0.75rem;
border: 1px solid var(--border);
border-radius: calc(var(--radius) - 2px);
background-color: var(--input-background);
font-size: 0.875rem;
cursor: pointer;
outline: none;
}

.entries-text {
font-size: 0.875rem;
color: var(--muted-foreground);
}

.controls-right {
display: flex;
align-items: center;
gap: 0.75rem;
}

.download-btn,
.add-customer-btn {
display: flex;
align-items: center;
gap: 0.5rem;
padding: 0.5rem 1rem;
border-radius: calc(var(--radius) - 2px);
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
cursor: pointer;
transition: all 0.2s ease-in-out;
border: none;
height: 2.5rem;
}

.download-btn {
border: 1px solid var(--border);
background-color: transparent;
color: var(--foreground);
}

.download-btn:hover {
background-color: var(--muted);
}

.add-customer-btn {
background-color: var(--primary);
color: var(--primary-foreground);
}

.add-customer-btn:hover {
background-color: rgb(37 99 235 / 0.9);
}

.download-icon,
.add-icon {
height: 1rem;
width: 1rem;
}

/* Summary Stats */
.summary-stats {
position: relative;
background: linear-gradient(to right, #eef2ff, #f0fdf4);
border: 1px solid #e5e7eb;
border-radius: 12px;
padding: 1.75rem 1.25rem;
margin: 1.25rem auto 1.5rem;
max-width: 78rem;
width: 95%;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);

display: flex;
justify-content: space-between;
align-items: center;
flex-direction: row;
/* ✅ Now 1 row */
}

.stats-wrapper {
display: flex;
flex-wrap: nowrap;
/* ✅ Prevent second row */
gap: 1.5rem;
}


.summary-title {
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
color: var(--muted-foreground);
margin-bottom: 1rem;
}

.stats-grid {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
gap: 1.5rem;
align-items: center;
}

.stat-card {
text-align: center;
}

.stat-value {
font-size: 1.5rem;
font-weight: bold;
margin-bottom: 0.25rem;
}

.stat-value.customers {
color: var(--primary);
}

.stat-value.collected {
color: rgb(34 197 94);
}

.stat-value.expected {
color: rgb(107 114 128);
}

.stat-label {
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
color: var(--muted-foreground);
}

/* Table Styles */
.table-container {
background-color: var(--card);
border-radius: var(--radius);
border: 1px solid var(--border);
overflow: hidden;
margin-bottom: 1.5rem;
}

.customer-table {
width: 100%;
border-collapse: collapse;
}

.table-header-row {
background-color: rgba(248, 250, 252, 0.5);
}

.table-header {
padding: 0.75rem 1rem;
text-align: left;
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
color: var(--foreground);
border-bottom: 1px solid var(--border);
}

.serial-header {
width: 4rem;
}

.actions-header {
width: 8rem;
}

.table-row {
border-bottom: 1px solid var(--border);
transition: background-color 0.2s ease-in-out;
}

.table-row:hover {
background-color: rgba(248, 250, 252, 0.3);
}

.table-cell {
padding: 1rem;
font-size: 0.875rem;
vertical-align: middle;
}

.customer-badge {
display: inline-flex;
align-items: center;
border-radius: calc(var(--radius) - 4px);
border: 1px solid rgb(147 197 253);
background-color: rgb(239 246 255);
color: rgb(29 78 216);
padding: 0.125rem 0.5rem;
font-size: 0.75rem;
font-weight: var(--font-weight-medium);
}

.customer-name-btn {
background: none;
border: none;
color: var(--foreground);
cursor: pointer;
transition: all 0.2s ease-in-out;
text-align: left;
font-size: 0.875rem;
}

.customer-name-btn:hover {
color: var(--primary);
text-decoration: underline;
}

.progress-container {
display: flex;
align-items: center;
gap: 0.75rem;
min-width: 11rem;
}

.progress-bar {
flex: 1;
background-color: rgb(229 231 235);
border-radius: 9999px;
height: 0.5rem;
min-width: 5rem;
}

.progress-fill {
height: 0.5rem;
border-radius: 9999px;
transition: all 0.3s ease-in-out;
}

.progress-fill.week {
background-color: rgb(34 197 94);
}

.progress-fill.amount {
background-color: var(--primary);
}

.progress-text {
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
white-space: nowrap;
}

.action-buttons {
display: flex;
align-items: center;
gap: 0.5rem;
}

.action-btn {
height: 2rem;
width: 2rem;
padding: 0;
border: none;
background: transparent;
border-radius: calc(var(--radius) - 2px);
display: flex;
align-items: center;
justify-content: center;
cursor: pointer;
transition: all 0.2s ease-in-out;
}

.action-btn.rupee {
color: rgb(6, 18, 129);
}

.action-btn.rupee:hover {
color: rgb(7, 21, 146);
background-color: rgb(240 253 244);
}

.action-btn.edit {
color: rgb(34 197 94);
}

.action-btn.edit:hover {
color: rgb(21 128 61);
background-color: rgb(240 253 244);
}

.action-btn.delete {
color: rgb(239 68 68);
}

.action-btn.delete:hover {
color: rgb(185 28 28);
background-color: rgb(254 242 242);
}

.action-btn svg {
height: 1rem;
width: 1rem;
}

.no-customers {
text-align: center;
padding: 2rem;
color: var(--muted-foreground);
}

/* Pagination */
.pagination-section {
display: flex;
justify-content: space-between;
align-items: center;
background-color: var(--card);
padding: 0.75rem 1rem;
border-radius: var(--radius);
border: 1px solid var(--border);
}

.pagination-info {
font-size: 0.875rem;
color: var(--muted-foreground);
}

.pagination-controls {
display: flex;
align-items: center;
gap: 0.5rem;
}

.pagination-btn {
height: 2rem;
min-width: 2rem;
padding: 0 0.5rem;
border: 1px solid var(--border);
background-color: transparent;
color: var(--foreground);
border-radius: calc(var(--radius) - 4px);
font-size: 0.875rem;
cursor: pointer;
transition: all 0.2s ease-in-out;
display: flex;
align-items: center;
justify-content: center;
}

.pagination-btn:hover:not(:disabled) {
background-color: var(--muted);
}

.pagination-btn.active {
background-color: var(--primary);
color: var(--primary-foreground);
border-color: var(--primary);
}

.pagination-btn:disabled {
opacity: 0.5;
cursor: not-allowed;
}

.pagination-btn svg {
height: 1rem;
width: 1rem;
}

/* Modal Styles */
.modal {
position: fixed;
inset: 0;
background-color: rgb(0 0 0 / 0.5);
display: flex;
align-items: center;
justify-content: center;
z-index: 1000;
opacity: 0;
visibility: hidden;
transition: all 0.3s ease-in-out;
}

.modal.show {
opacity: 1;
visibility: visible;
}

.modal-content {
background-color: var(--card);
border-radius: var(--radius);
box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
width: 100%;
max-width: 32rem;
margin: 1rem;
max-height: 90vh;
overflow-y: auto;
transform: scale(0.95);
transition: transform 0.3s ease-in-out;
}

.modal.show .modal-content {
transform: scale(1);
}

.modal-header {
display: flex;
align-items: center;
justify-content: space-between;
padding: 1.5rem 1.5rem 0;
}

.modal-title {
font-size: 1.125rem;
font-weight: var(--font-weight-medium);
color: var(--foreground);
}

.modal-close {
height: 2rem;
width: 2rem;
border: none;
background: transparent;
border-radius: calc(var(--radius) - 4px);
display: flex;
align-items: center;
justify-content: center;
cursor: pointer;
transition: background-color 0.2s ease-in-out;
}

.modal-close:hover {
background-color: var(--muted);
}

.close-icon {
height: 1rem;
width: 1rem;
}

.modal-form {
padding: 1.5rem;
}

.form-grid {
display: grid;
grid-template-columns: 1fr;
/* single column for small screens */
gap: 1rem;
margin-bottom: 1.5rem;
}

@media (min-width: 640px) {
.form-grid {
grid-template-columns: repeat(2, 1fr);
/* two equal columns */
align-items: start;
/* make sure top aligns correctly */
}
}

/* Each input block */
.form-group {
display: flex;
flex-direction: column;
gap: 0.5rem;
height: 100%;
/* ✅ ensures both columns stretch equally */
}

/* Fix for select input (Place) */
.form-group select.form-input {
height: 2.5rem;
/* same height as other inputs */
line-height: 2.5rem;
padding: 0 0.75rem;
}

.form-label {
font-size: 0.875rem;
font-weight: var(--font-weight-medium);
color: var(--foreground);
}

.form-input {
height: 2.5rem;
width: 100%;
padding: 0.5rem 0.75rem;
border: 1px solid var(--border);
border-radius: calc(var(--radius) - 2px);
background-color: var(--input-background);
font-size: 0.875rem;
transition: all 0.2s ease-in-out;
outline: none;
}

.form-input:focus {
border-color: var(--ring);
box-shadow: 0 0 0 2px rgb(59 130 246 / 0.1);
}

.form-input::placeholder {
color: var(--muted-foreground);
}

.form-input:read-only {
background-color: var(--muted);
cursor: not-allowed;
}

/* ✅ Add Customer Modal footer fix */
.modal-actions {
width: 100%;
margin-top: 1.5rem;
padding-top: 1.2rem;
border-top: 1px solid #e5e7eb;
/* line full width */

display: flex;
justify-content: center;
/* center buttons */
align-items: center;
gap: 1rem;
/* spacing between Cancel / Add */
}

.cancel-btn,
.submit-btn {
padding: 0.6rem 1.4rem;
border-radius: 8px;
font-size: 0.9rem;
font-weight: 600;
cursor: pointer;
transition: all 0.2s ease-in-out;
}

.cancel-btn {
background-color: #f9fafb;
color: #111827;
border: 1px solid #d1d5db;
}

.cancel-btn:hover {
background-color: #f3f4f6;
}

.submit-btn {
background-color: #2563eb;
color: #fff;
border: none;
}

.submit-btn:hover {
background-color: #1e40af;
}

/* Optional responsive tweak (mobile view) */
@media (max-width: 480px) {
.modal-actions {
flex-direction: column;
/* stack buttons vertically */
}

.cancel-btn,
.submit-btn {
width: 100%;
/* full width buttons on mobile */
}
}

/* Toast Notifications */
.toast-container {
position: fixed;
top: 1rem;
right: 1rem;
z-index: 1100;
display: flex;
flex-direction: column;
gap: 0.5rem;
}

.toast {
padding: 0.75rem 1rem;
border-radius: calc(var(--radius) - 2px);
color: white;
font-weight: var(--font-weight-medium);
font-size: 0.875rem;
box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
transform: translateX(100%);
transition: transform 0.3s ease-in-out;
max-width: 300px;
}

.toast.show {
transform: translateX(0);
}

.toast.success {
background-color: rgb(34 197 94);
}

.toast.error {
background-color: rgb(239 68 68);
}

.toast.info {
background-color: var(--primary);
}

/* Responsive Design */
@media (max-width: 640px) {
.header-content {
flex-direction: column;
gap: 1rem;
align-items: stretch;
}

.header-right {
justify-content: space-between;
}

.filter-grid {
grid-template-columns: 1fr;
}

.table-container {
overflow-x: auto;
}

.customer-table {
min-width: 600px;
}

.pagination-section {
flex-direction: column;
gap: 1rem;
align-items: stretch;
text-align: center;
}
}

/* Loading States */
.loading {
position: relative;
color: transparent !important;
}

.loading::after {
content: "";
position: absolute;
width: 1rem;
height: 1rem;
top: 50%;
left: 50%;
margin-left: -0.5rem;
margin-top: -0.5rem;
border: 2px solid transparent;
border-top-color: currentColor;
border-radius: 50%;
animation: spin 1s linear infinite;
}

@keyframes spin {
0% {
transform: rotate(0deg);
}

100% {
transform: rotate(360deg);
}
}

/* ---------- Summary Section Improvements ---------- */
/* 📌 FIX: Overall Stats Left title, Right values */
.summary-stats {
background: linear-gradient(to right, #eef2ff, #f0fdf4);
border: 1px solid #e5e7eb;
border-radius: 12px;
padding: 1.5rem;
margin: 1.25rem auto 1.5rem;
max-width: 78rem;
width: 95%;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);

display: flex;
align-items: center;
justify-content: space-between;
/* 👈 This is the important part */
}

.summary-stats .stats-row {
display: flex;
gap: 1rem;
justify-content: flex-end;
flex-wrap: wrap;
}

.summary-title {
font-size: 1.1rem;
font-weight: 600;
color: var(--foreground);
text-align: center;
margin-bottom: 1rem;
letter-spacing: 0.3px;
}

.stats-row {
display: flex;
justify-content: center;
gap: 3rem;
flex-wrap: wrap;
align-items: center;
}

.stat-card {
background: white;
border: 1px solid var(--border);
border-radius: 0.75rem;
padding: 1rem 1.5rem;
text-align: center;
width: 200px;
transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
transform: translateY(-3px);
}

.stat-value {
font-size: 1.5rem;
font-weight: 700;
margin-bottom: 0.3rem;
color: var(--primary);
}

.stat-label {
font-size: 0.9rem;
color: var(--muted-foreground);
}

/* Filtered Summary spacing fix */
#filteredStats {
margin-top: 1rem;
background: linear-gradient(to right, #fff7ed, #fefce8);
border: 1px solid var(--border);
border-radius: var(--radius);
box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

#filteredStats .stat-value {
color: #059669;
}

/* --------------------------- */
/* 📊 FIXED Overall Statistics */
/* --------------------------- */
.summary-stats {
position: relative;
background: linear-gradient(to right, #eef2ff, #f0fdf4);
border: 1px solid #e5e7eb;
border-radius: 12px;
padding: 1.75rem 1.25rem;
margin: 1.25rem auto 1.5rem;
max-width: 78rem;
width: 95%;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
display: flex;
justify-content: center;
align-items: center;
flex-direction: column;
}

.summary-title {
font-size: 1.1rem;
font-weight: 600;
color: #1e293b;
text-align: center;
margin-bottom: 1rem;
}

.stats-wrapper {
display: flex;
justify-content: center;
align-items: stretch;
gap: 2rem;
flex-wrap: wrap;
}

.stat-card {
background: #ffffff;
border: 1px solid #e2e8f0;
border-radius: 10px;
padding: 1.25rem 1.75rem;
text-align: center;
width: 220px;
transition: all 0.2s ease;
}

.stat-card:hover {
transform: translateY(-3px);
box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
}

.stat-value {
font-size: 1.75rem;
font-weight: 700;
color: #2563eb;
margin-bottom: 0.25rem;
}

.stat-card.collected .stat-value {
color: #16a34a;
}

.stat-label {
font-size: 0.9rem;
color: #64748b;
font-weight: 500;
}

/* 🟡 Filtered Summary same style */
#filteredStats {
background: linear-gradient(to right, #fff7ed, #fefce8);
border: 1px solid #e5e7eb;
border-radius: 12px;
padding: 1.5rem;
margin: 1rem auto;
max-width: 78rem;
width: 95%;
box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
}

/* --------------------------- */
/* 📋 Table Alignment Fix */
/* --------------------------- */

.table-container {
background-color: #fff;
border: 1px solid #e2e8f0;
border-radius: 10px;
overflow-x: auto;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
margin: 1.5rem auto;
width: 95%;
max-width: 78rem;
}

.customer-table {
width: 100%;
border-collapse: collapse;
text-align: left;
}

.table-header-row {
background-color: #f9fafb;
}

.table-header {
padding: 0.875rem 1.25rem;
text-align: left;
font-size: 0.9rem;
font-weight: 600;
color: #1e293b;
border-bottom: 1px solid #e2e8f0;
vertical-align: middle;
}

.table-cell {
padding: 0.875rem 1.25rem;
font-size: 0.9rem;
color: #374151;
border-bottom: 1px solid #f1f5f9;
vertical-align: middle;
}

.table-row:hover {
background-color: #f8fafc;
}

/* Align column widths visually */
.table-header:first-child,
.table-cell:first-child {
padding-left: 1.5rem;
}

.table-header:last-child,
.table-cell:last-child {
padding-right: 1.5rem;
}

/* Progress alignment */
.progress-container {
display: flex;
align-items: center;
gap: 0.75rem;
min-width: 10rem;
}

.select-wrapper {
position: relative;
width: 100%;
}

.place-icon {
position: absolute;
left: 10px;
top: 50%;
height: 1rem;
width: 1rem;
transform: translateY(-50%);
font-size: 18px;
color: #999;
}

#changePasswordModal {
display: none;
}

#changePasswordModal.show {
display: block;
}