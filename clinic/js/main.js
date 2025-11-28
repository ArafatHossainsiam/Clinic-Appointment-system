// ============================================
// THEME MANAGEMENT
// ============================================

const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

// Load saved theme from localStorage
const savedTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', savedTheme);
updateThemeIcon(savedTheme);

// Theme toggle event listener
if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });
}

function updateThemeIcon(theme) {
    const icon = themeToggle ? themeToggle.querySelector('i') : null;
    if (icon) {
        icon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    }
}

// ============================================
// MODAL MANAGEMENT
// ============================================

function openModal(modalId, role = null) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        if (role && modalId === 'loginModal') {
            const loginRoleInput = document.getElementById('loginRole');
            if (loginRoleInput) {
                loginRoleInput.value = role;
            }
            
            const roleNames = {
                'assistant': 'Assistant',
                'doctor': 'Doctor',
                'patient': 'Patient'
            };
            
            const modalTitle = modal.querySelector('h2');
            if (modalTitle) {
                modalTitle.innerHTML = `<i class="fas fa-sign-in-alt"></i> ${roleNames[role]} Login`;
            }
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Re-enable background scrolling
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});

// ============================================
// NOTIFICATION SYSTEM
// ============================================

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    const bgColor = type === 'success' ? '#66bb6a' : '#ef5350';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 10px;
        background: ${bgColor};
        color: white;
        z-index: 3000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        font-weight: 500;
        max-width: 350px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations for notifications
const notificationStyle = document.createElement('style');
notificationStyle.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(notificationStyle);

// ============================================
// DATE & TIME FORMATTING
// ============================================

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function formatTime(timeString) {
    if (!timeString) return '';
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

function getCurrentDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// ============================================
// FORM VALIDATION
// ============================================

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#ef5350';
            isValid = false;
        } else {
            input.style.borderColor = 'var(--border-color)';
        }
    });
    
    return isValid;
}

// Reset form validation styles
function resetFormValidation(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.style.borderColor = 'var(--border-color)';
    });
}

// ============================================
// AJAX HELPER FUNCTIONS
// ============================================

async function fetchAPI(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        throw error;
    }
}

// ============================================
// CONFIRM ACTIONS
// ============================================

function confirmAction(message) {
    return confirm(message);
}

// ============================================
// FORM SUBMISSIONS - LOGIN
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // Login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!validateForm('loginForm')) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            const formData = new FormData(loginForm);
            
            try {
                const response = await fetch('login_process.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    showNotification(result.message || 'Login failed', 'error');
                }
            } catch (error) {
                console.error('Login error:', error);
                showNotification('Login failed. Please try again.', 'error');
            }
        });
    }
    
    // ============================================
    // APPOINTMENT FORM SUBMISSION
    // ============================================
    
    const appointmentForm = document.getElementById('appointmentForm');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!validateForm('appointmentForm')) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            const formData = new FormData(appointmentForm);
            
            try {
                const response = await fetch('appointment_request.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Appointment request submitted successfully!', 'success');
                    closeModal('appointmentModal');
                    appointmentForm.reset();
                    resetFormValidation('appointmentForm');
                } else {
                    showNotification(result.message || 'Submission failed', 'error');
                }
            } catch (error) {
                console.error('Appointment submission error:', error);
                showNotification('Submission failed. Please try again.', 'error');
            }
        });
    }
    
    // ============================================
    // SET MINIMUM DATE FOR APPOINTMENT
    // ============================================
    
    const appointmentDateInput = document.querySelector('input[name="appointment_date"]');
    if (appointmentDateInput) {
        appointmentDateInput.min = getCurrentDate();
    }
});

// ============================================
// LOAD DOCTORS FOR APPOINTMENT FORM
// ============================================

async function loadDoctors() {
    try {
        const response = await fetch('api/get_doctors.php');
        const doctors = await response.json();
        const select = document.getElementById('doctorSelect');
        
        if (select && doctors.length > 0) {
            doctors.forEach(doc => {
                const option = document.createElement('option');
                option.value = doc.doctor_id;
                option.textContent = `${doc.name} - ${doc.specialty}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading doctors:', error);
    }
}

// Auto-load doctors if select exists
if (document.getElementById('doctorSelect')) {
    loadDoctors();
}

// ============================================
// TABLE SEARCH/FILTER FUNCTIONALITY
// ============================================

function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = input.value.toUpperCase();
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            let txtValue = rows[i].textContent || rows[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    });
}

// ============================================
// SMOOTH SCROLL TO SECTION
// ============================================

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ============================================
// PRINT FUNCTIONALITY
// ============================================

function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<style>body{font-family:Arial;padding:20px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(element.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// ============================================
// LOADING SPINNER
// ============================================

function showLoader() {
    const loader = document.createElement('div');
    loader.id = 'loader';
    loader.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;
    loader.innerHTML = '<div style="border: 5px solid #f3f3f3; border-top: 5px solid #4a90e2; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite;"></div>';
    
    const spinStyle = document.createElement('style');
    spinStyle.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
    document.head.appendChild(spinStyle);
    
    document.body.appendChild(loader);
}

function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.remove();
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('Failed to copy', 'error');
    });
}

// ============================================
// MOBILE MENU TOGGLE (if needed)
// ============================================

const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const navMenu = document.querySelector('.nav-menu');

if (mobileMenuToggle && navMenu) {
    mobileMenuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// ============================================
// AUTO-HIDE ALERTS
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// ============================================
// PREVENT DOUBLE FORM SUBMISSION
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                setTimeout(() => {
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    });
});

// ============================================
// CONSOLE LOG (Development Only)
// ============================================

console.log('HealthCare Clinic Management System - Initialized ✓');
console.log('Current Theme:', localStorage.getItem('theme') || 'light');