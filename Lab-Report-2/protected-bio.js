// Protected Bio Form JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    checkAuthentication();
    
    // Set up form validation
    setupFormValidation();
    
    // Load user data if available
    loadUserData();
    
    // Auto-save functionality (optional)
    setupAutoSave();
});

// Check if user is authenticated
function checkAuthentication() {
    // In a real application, you would check session/token
    const isLoggedIn = sessionStorage.getItem('isLoggedIn');
    const userEmail = sessionStorage.getItem('userEmail');
    
    if (!isLoggedIn) {
        // Redirect to login if not authenticated
        window.location.href = 'login.html';
        return;
    }
    
    // Update welcome message
    if (userEmail) {
        const welcomeElement = document.getElementById('userWelcome');
        if (welcomeElement) {
            welcomeElement.textContent = `Welcome, ${userEmail}!`;
        }
    }
}

// Set up form validation
function setupFormValidation() {
    const bioForm = document.getElementById('bioForm');
    if (!bioForm) return;
    
    bioForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitForm();
        }
    });
    
    // Real-time validation
    const requiredFields = bioForm.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', validateField);
        field.addEventListener('input', clearFieldError);
    });
}

// Validate individual field
function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    
    clearFieldError(event);
    
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Please enter a valid email address');
            return false;
        }
    }
    
    // Phone validation
    if (field.type === 'tel' && value) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        if (!phoneRegex.test(value.replace(/[\s\-\(\)]/g, ''))) {
            showFieldError(field, 'Please enter a valid phone number');
            return false;
        }
    }
    
    // Date validation (minimum age check)
    if (field.type === 'date' && value) {
        const birthDate = new Date(value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        
        if (age < 13) {
            showFieldError(field, 'You must be at least 13 years old');
            return false;
        }
        
        if (birthDate > today) {
            showFieldError(field, 'Birth date cannot be in the future');
            return false;
        }
    }
    
    return true;
}

// Show field error
function showFieldError(field, message) {
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    errorElement.style.color = '#e53e3e';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '5px';
    
    field.style.borderColor = '#e53e3e';
    field.parentNode.appendChild(errorElement);
}

// Clear field error
function clearFieldError(event) {
    const field = event.target;
    const errorElement = field.parentNode.querySelector('.field-error');
    
    if (errorElement) {
        errorElement.remove();
    }
    
    field.style.borderColor = '#e2e8f0';
}

// Validate entire form
function validateForm() {
    const form = document.getElementById('bioForm');
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    // Clear previous messages
    const existingMessage = document.querySelector('.message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Validate each required field
    requiredFields.forEach(field => {
        if (!validateField({ target: field })) {
            isValid = false;
        }
    });
    
    // Check terms agreement
    const termsCheckbox = form.querySelector('input[name="terms"]');
    if (termsCheckbox && !termsCheckbox.checked) {
        showMessage('You must agree to the terms and conditions', 'error');
        isValid = false;
    }
    
    return isValid;
}

// Submit form
function submitForm() {
    const form = document.getElementById('bioForm');
    const submitBtn = form.querySelector('.btn-submit');
    
    // Show loading state
    submitBtn.textContent = 'Saving...';
    submitBtn.classList.add('loading');
    form.classList.add('loading');
    
    const formData = new FormData(form);
    
    // Add user ID to form data
    const userEmail = sessionStorage.getItem('userEmail');
    formData.append('user_email', userEmail);
    
    fetch('save-bio.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Bio data saved successfully!', 'success');
            // Optional: redirect to success page
            // setTimeout(() => window.location.href = 'success.html', 2000);
        } else {
            showMessage(data.message || 'An error occurred while saving', 'error');
        }
    })
    .catch(error => {
        showMessage('Network error. Please try again.', 'error');
        console.error('Error:', error);
    })
    .finally(() => {
        // Restore button state
        submitBtn.textContent = 'Save Bio Data';
        submitBtn.classList.remove('loading');
        form.classList.remove('loading');
    });
}

// Load user data
function loadUserData() {
    const userEmail = sessionStorage.getItem('userEmail');
    if (!userEmail) return;
    
    // Pre-fill email field
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.value = userEmail;
        emailField.readOnly = true; // Make email read-only
    }
    
    // Load existing bio data if available
    fetch(`get-bio.php?email=${encodeURIComponent(userEmail)}`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.bio) {
            populateForm(data.bio);
        }
    })
    .catch(error => {
        console.error('Error loading bio data:', error);
    });
}

// Populate form with existing data
function populateForm(bioData) {
    Object.keys(bioData).forEach(key => {
        const field = document.querySelector(`[name="${key}"]`);
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = bioData[key] == 1;
            } else {
                field.value = bioData[key];
            }
        }
    });
}

// Auto-save functionality
function setupAutoSave() {
    const form = document.getElementById('bioForm');
    let autoSaveTimeout;
    
    form.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            saveFormData();
        }, 30000); // Auto-save every 30 seconds
    });
}

// Save form data to localStorage
function saveFormData() {
    const form = document.getElementById('bioForm');
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    localStorage.setItem('bioFormData', JSON.stringify(data));
}

// Load form data from localStorage
function loadFormData() {
    const savedData = localStorage.getItem('bioFormData');
    if (savedData) {
        const data = JSON.parse(savedData);
        populateForm(data);
    }
}

// Logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        sessionStorage.clear();
        localStorage.removeItem('bioFormData');
        window.location.href = 'login.html';
    }
}

// Show message function
function showMessage(message, type) {
    // Remove existing messages
    const existingMessage = document.querySelector('.message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    // Insert message at the top of the form
    const formContainer = document.querySelector('.form-container');
    const formHeader = formContainer.querySelector('.form-header');
    formContainer.insertBefore(messageDiv, formHeader.nextSibling);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}
