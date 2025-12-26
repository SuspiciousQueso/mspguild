/**
 * MSP Portal - Main JavaScript
 */

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-info)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Password visibility toggle (if password fields exist)
    const passwordToggles = document.querySelectorAll('[data-password-toggle]');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-password-toggle');
            const passwordField = document.getElementById(targetId);
            
            if (passwordField) {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                }
            }
        });
    });
    
    // Character count for textarea
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counterId = textarea.id + '-counter';
        
        // Create counter element if it doesn't exist
        if (!document.getElementById(counterId)) {
            const counter = document.createElement('div');
            counter.id = counterId;
            counter.className = 'form-text text-end';
            counter.innerHTML = `<span class="char-count">0</span> / ${maxLength} characters`;
            textarea.parentNode.appendChild(counter);
        }
        
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            const charCount = document.querySelector(`#${counterId} .char-count`);
            if (charCount) {
                charCount.textContent = currentLength;
                
                // Change color if approaching limit
                if (currentLength > maxLength * 0.9) {
                    charCount.classList.add('text-danger');
                } else if (currentLength > maxLength * 0.7) {
                    charCount.classList.add('text-warning');
                    charCount.classList.remove('text-danger');
                } else {
                    charCount.classList.remove('text-warning', 'text-danger');
                }
            }
        });
    });
    
    // Phone number formatting (basic US format)
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 10) value = value.substr(0, 10);
            
            if (value.length >= 6) {
                this.value = `(${value.substr(0,3)}) ${value.substr(3,3)}-${value.substr(6)}`;
            } else if (value.length >= 3) {
                this.value = `(${value.substr(0,3)}) ${value.substr(3)}`;
            } else if (value.length > 0) {
                this.value = `(${value}`;
            }
        });
    });
    
    // Confirmation dialogs for destructive actions
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Session timeout warning (optional - customize as needed)
    const sessionTimeout = 3600000; // 1 hour in milliseconds
    const warningTime = 300000; // 5 minutes before timeout
    
    setTimeout(() => {
        if (document.body.classList.contains('logged-in')) {
            const warning = confirm('Your session will expire in 5 minutes. Would you like to stay logged in?');
            if (warning) {
                // Refresh session by making a simple request
                fetch(window.location.href, { method: 'HEAD' })
                    .then(() => console.log('Session refreshed'))
                    .catch(err => console.error('Session refresh failed', err));
            }
        }
    }, sessionTimeout - warningTime);
    
    // Add loading state to buttons on form submit
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && this.checkValidity()) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 10000);
            }
        });
    });
    
    // Initialize tooltips (if Bootstrap tooltips are used)
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers (if Bootstrap popovers are used)
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    console.log('MSP Portal initialized successfully');
});

// Utility function: Show toast notification (for future use)
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Utility function: Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Utility function: Format date
function formatDate(date) {
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
}
