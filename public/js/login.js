// Login Page JavaScript for Laravel

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lucide icons
    lucide.createIcons();

    // Toast container
    const toastContainer = document.getElementById('toastContainer');

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        toastContainer.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toastContainer.contains(toast)) toastContainer.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // Laravel flash messages
    const successMessage = document.querySelector('[data-flash-success]');
    if (successMessage) {
        showToast(successMessage.dataset.flashSuccess, 'success');
    }

    const errorMessage = document.querySelector('[data-flash-error]');
    if (errorMessage) {
        showToast(errorMessage.dataset.flashError, 'error');
    }

    // Auto-focus username
    const usernameInput = document.getElementById('username');
    if (usernameInput) usernameInput.focus();

    // Enter key moves focus to password
    usernameInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            document.getElementById('password').focus();
        }
    });

    // Form validation
    function validateForm(username, password) {
        if (!username.trim()) {
            showToast('Please enter your username', 'error');
            return false;
        }
        if (!password.trim()) {
            showToast('Please enter your password', 'error');
            return false;
        }
        return true;
    }

    const form = document.getElementById('loginForm');
    const submitButton = form.querySelector('button[type="submit"]');

    // Enable/disable submit button based on input
    form.addEventListener('input', () => {
        const username = usernameInput.value;
        const password = document.getElementById('password').value;

        if (username.trim() && password.trim()) {
            submitButton.style.opacity = '1';
            submitButton.disabled = false;
            submitButton.style.cursor = 'pointer';
        } else {
            submitButton.style.opacity = '0.7';
            submitButton.disabled = true;
            submitButton.style.cursor = 'not-allowed';
        }
    });

    // Visual feedback for input fields
    const formInputs = document.querySelectorAll('.form-input');
    formInputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.style.borderColor = '';
            input.style.boxShadow = '';
        });
        input.addEventListener('blur', () => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                input.style.borderColor = '#ef4444';
            }
        });
    });

    // Keyboard shortcuts: Alt+L focuses username
    document.addEventListener('keydown', (e) => {
        if (e.altKey && e.key.toLowerCase() === 'l') {
            e.preventDefault();
            usernameInput.focus();
        }
    });
    /*
    // ================= Change Password Modal =================
    // modal elements
const modal = document.getElementById("changePasswordModal");
const changeLink = document.getElementById("changePasswordLink");
const closeBtn = modal.querySelector(".close");
const changeForm = document.getElementById("changePasswordForm");
const message = document.getElementById("passwordMessage");

changeLink.onclick = e => { e.preventDefault(); modal.style.display = "flex"; message.innerText = ""; message.style.color = 'red'; };
closeBtn.onclick = () => modal.style.display = "none";
window.onclick = e => { if (e.target == modal) modal.style.display = "none"; };

changeForm.onsubmit = async e => {
    e.preventDefault();
    const username = changeForm.username.value.trim();
    const current = changeForm.current_password.value.trim();
    const newPass = changeForm.new_password.value.trim();
    const confirm = changeForm.confirm_password.value.trim();

    if (newPass.length < 8 || newPass.length > 15) { message.innerText = "Password must be 8–15 characters long."; return; }
    if (newPass === current) { message.innerText = "New password cannot be same as current password."; return; }
    if (newPass !== confirm) { message.innerText = "New password and confirm password do not match."; return; }

    const token = document.querySelector('input[name="_token"]').value;

    try {
        const res = await fetch("{{ route('change_password') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token },
            body: JSON.stringify({ username, current_password: current, new_password: newPass })
        });
        const data = await res.json();
        if (data.success) {
            message.style.color = "green";
            message.innerText = "Password updated successfully!";
            setTimeout(() => modal.style.display = "none", 1200);
        } else {
            message.style.color = "red";
            message.innerText = data.message || "Error occurred";
        }
    } catch (err) {
        message.style.color = "red";
        message.innerText = "Server error. Try again.";
    }
};
*/

});


