// Authentication JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Registration form validation and submission
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match!', 'error');
                return false;
            }
            
            if (password.length < 6) {
                showMessage('Password must be at least 6 characters long!', 'error');
                return false;
            }
            
            submitForm(registerForm, 'register.php');
        });
    }
    
    // Login form validation and submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                showMessage('Please fill in all fields!', 'error');
                return false;
            }
            
            submitForm(loginForm, 'login.php');
        });
    }
});

// Submit form function
function submitForm(form, url) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('.btn-submit');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Please wait...';
    submitBtn.disabled = true;
    
    console.log('Submitting to:', url);
    console.log('Form data:', Object.fromEntries(formData));
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Check if the response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server did not return JSON. Content-Type: ' + contentType);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showMessage(data.message, 'success');
            
            // Store login info in sessionStorage
            if (data.user) {
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('userEmail', data.user.email);
                sessionStorage.setItem('userName', data.user.name);
            }
            
            // Redirect if specified
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Full error details:', error);
        
        let errorMessage = 'Network error. Please check:';
        errorMessage += '\n• Is your web server (Apache) running?';
        errorMessage += '\n• Are you accessing via http://localhost/?';
        errorMessage += '\n• Is PHP working? Test with test-php.php';
        
        showMessage(errorMessage, 'error');
    })
    .finally(() => {
        // Restore button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
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
    const form = document.querySelector('.auth-form');
    form.parentNode.insertBefore(messageDiv, form);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (messageDiv && messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}
