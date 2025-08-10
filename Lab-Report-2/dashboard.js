// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    checkAuthentication();
    
    // Set up tabs
    setupTabs();
    
    // Load user data
    loadUserData();
    
    // Set up update form
    setupUpdateForm();
    
    // Set up delete functionality
    setupDeleteFunction();
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

// Set up tabs functionality
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.getAttribute('data-tab');
            
            // Update active button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Update active content
            tabContents.forEach(content => {
                if (content.id === tab) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
            
            // Special handling for update tab
            if (tab === 'update') {
                loadUpdateForm();
            }
        });
    });
}

// Load user data for display
function loadUserData() {
    const userEmail = sessionStorage.getItem('userEmail');
    if (!userEmail) return;
    
    const loadingIndicator = document.querySelector('.loading-indicator');
    const bioDataDisplay = document.querySelector('.bio-data-display');
    
    loadingIndicator.style.display = 'block';
    bioDataDisplay.style.display = 'none';
    
    fetch(`get-bio.php?email=${encodeURIComponent(userEmail)}`)
        .then(response => response.json())
        .then(data => {
            loadingIndicator.style.display = 'none';
            
            if (data.success && data.bio) {
                displayBioData(data.bio);
                bioDataDisplay.classList.add('loaded');
            } else {
                bioDataDisplay.innerHTML = `
                    <div class="no-data">
                        <p>No bio data found. Please complete the bio form first.</p>
                        <a href="protected-bio.html" class="btn-primary" style="display: inline-block; margin-top: 15px;">Go to Bio Form</a>
                    </div>
                `;
                bioDataDisplay.classList.add('loaded');
            }
        })
        .catch(error => {
            loadingIndicator.style.display = 'none';
            bioDataDisplay.innerHTML = `
                <div class="no-data">
                    <p>Error loading data. Please try again later.</p>
                </div>
            `;
            bioDataDisplay.classList.add('loaded');
            console.error('Error loading bio data:', error);
        });
}

// Display bio data
function displayBioData(bioData) {
    const bioDataDisplay = document.querySelector('.bio-data-display');
    
    // Format fields for display
    const formattedData = {
        'Full Name': `${bioData.first_name} ${bioData.last_name}`,
        'Email': bioData.email,
        'Phone': bioData.phone || 'Not provided',
        'Date of Birth': formatDate(bioData.date_of_birth),
        'Gender': capitalizeFirstLetter(bioData.gender),
        'Address': bioData.address || 'Not provided',
        'City': bioData.city || 'Not provided',
        'Country': bioData.country || 'Not provided',
        'Occupation': bioData.occupation || 'Not provided',
        'Education': formatEducation(bioData.education),
        'About': bioData.bio || 'Not provided',
        'Newsletter': bioData.newsletter == 1 ? 'Subscribed' : 'Not subscribed',
    };
    
    let html = '';
    
    // Add profile picture if exists
    if (bioData.profile_picture) {
        html += `
            <div class="data-row">
                <div class="data-label">Profile Picture</div>
                <div class="data-value">
                    <img src="uploads/${bioData.profile_picture}" alt="Profile Picture" class="profile-picture-preview">
                </div>
            </div>
        `;
    }
    
    // Add other fields
    for (const [label, value] of Object.entries(formattedData)) {
        html += `
            <div class="data-row">
                <div class="data-label">${label}</div>
                <div class="data-value">${value}</div>
            </div>
        `;
    }
    
    bioDataDisplay.innerHTML = html;
}

// Setup update form
function setupUpdateForm() {
    const updateBtn = document.querySelector('.tab-btn[data-tab="update"]');
    
    updateBtn.addEventListener('click', () => {
        loadUpdateForm();
    });
}

// Load update form with current data
function loadUpdateForm() {
    const userEmail = sessionStorage.getItem('userEmail');
    if (!userEmail) return;
    
    const updateFormContainer = document.getElementById('updateBioForm');
    updateFormContainer.innerHTML = '<div class="loading-indicator">Loading your data...</div>';
    
    fetch(`get-bio.php?email=${encodeURIComponent(userEmail)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.bio) {
                createUpdateForm(updateFormContainer, data.bio);
            } else {
                updateFormContainer.innerHTML = `
                    <div class="no-data">
                        <p>No bio data found to update. Please complete the bio form first.</p>
                        <a href="protected-bio.html" class="btn-primary" style="display: inline-block; margin-top: 15px;">Go to Bio Form</a>
                    </div>
                `;
            }
        })
        .catch(error => {
            updateFormContainer.innerHTML = `
                <div class="no-data">
                    <p>Error loading data. Please try again later.</p>
                </div>
            `;
            console.error('Error loading bio data for update:', error);
        });
}

// Create update form with existing data
function createUpdateForm(container, bioData) {
    let html = `
        <div class="form-row">
            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="first_name" required value="${bioData.first_name}">
            </div>
            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="last_name" required value="${bioData.last_name}">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required value="${bioData.email}" readonly>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="${bioData.phone || ''}">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="dateOfBirth">Date of Birth *</label>
                <input type="date" id="dateOfBirth" name="date_of_birth" required value="${bioData.date_of_birth}">
            </div>
            <div class="form-group">
                <label for="gender">Gender *</label>
                <select id="gender" name="gender" required>
                    <option value="male" ${bioData.gender === 'male' ? 'selected' : ''}>Male</option>
                    <option value="female" ${bioData.gender === 'female' ? 'selected' : ''}>Female</option>
                    <option value="other" ${bioData.gender === 'other' ? 'selected' : ''}>Other</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" placeholder="Enter your full address">${bioData.address || ''}</textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="${bioData.city || ''}">
            </div>
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" value="${bioData.country || ''}">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="occupation">Occupation</label>
                <input type="text" id="occupation" name="occupation" value="${bioData.occupation || ''}">
            </div>
            <div class="form-group">
                <label for="education">Education Level</label>
                <select id="education" name="education">
                    <option value="" ${!bioData.education ? 'selected' : ''}>Select Education</option>
                    <option value="high_school" ${bioData.education === 'high_school' ? 'selected' : ''}>High School</option>
                    <option value="bachelor" ${bioData.education === 'bachelor' ? 'selected' : ''}>Bachelor's Degree</option>
                    <option value="master" ${bioData.education === 'master' ? 'selected' : ''}>Master's Degree</option>
                    <option value="phd" ${bioData.education === 'phd' ? 'selected' : ''}>PhD</option>
                    <option value="other" ${bioData.education === 'other' ? 'selected' : ''}>Other</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="bio">About Yourself</label>
            <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself...">${bioData.bio || ''}</textarea>
        </div>
        
        <div class="form-group">
            <label for="profilePicture">Profile Picture (Leave blank to keep current)</label>
            <input type="file" id="profilePicture" name="profile_picture" accept="image/*">
        </div>
        
        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="newsletter" value="1" ${bioData.newsletter == 1 ? 'checked' : ''}>
                <span class="checkmark"></span>
                Subscribe to newsletter
            </label>
        </div>
        
        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="terms" value="1" required checked>
                <span class="checkmark"></span>
                I agree to the terms and conditions *
            </label>
        </div>
        
        <div class="form-actions">
            <button type="button" class="btn-reset" onclick="resetUpdateForm()">Reset Changes</button>
            <button type="button" class="btn-submit" onclick="updateBioData()">Update Bio Data</button>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Store original data for reset functionality
    window.originalBioData = bioData;
}

// Reset update form to original values
function resetUpdateForm() {
    if (window.originalBioData) {
        createUpdateForm(document.getElementById('updateBioForm'), window.originalBioData);
    }
}

// Update bio data
function updateBioData() {
    const userEmail = sessionStorage.getItem('userEmail');
    if (!userEmail) return;
    
    const updateForm = document.getElementById('updateBioForm');
    const formData = new FormData(updateForm);
    
    // Add user email
    formData.append('user_email', userEmail);
    formData.append('is_update', '1');
    
    // Show loading state
    const submitBtn = updateForm.querySelector('.btn-submit');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Updating...';
    submitBtn.disabled = true;
    updateForm.classList.add('loading');
    
    fetch('update-bio.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Bio data updated successfully!', 'success');
            
            // Refresh data displays
            setTimeout(() => {
                loadUserData();
            }, 1000);
        } else {
            showMessage(data.message || 'Failed to update data', 'error');
        }
    })
    .catch(error => {
        showMessage('Error updating data. Please try again.', 'error');
        console.error('Update error:', error);
    })
    .finally(() => {
        // Restore button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        updateForm.classList.remove('loading');
    });
}

// Set up delete functionality
function setupDeleteFunction() {
    const confirmDeleteCheckbox = document.getElementById('confirmDelete');
    const deleteButton = document.getElementById('deleteBioBtn');
    
    confirmDeleteCheckbox.addEventListener('change', function() {
        deleteButton.disabled = !this.checked;
    });
    
    deleteButton.addEventListener('click', deleteBioData);
}

// Delete bio data
function deleteBioData() {
    const userEmail = sessionStorage.getItem('userEmail');
    if (!userEmail) return;
    
    const deleteBtn = document.getElementById('deleteBioBtn');
    const originalText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true;
    
    fetch('delete-bio.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `user_email=${encodeURIComponent(userEmail)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Bio data deleted successfully!', 'success');
            
            // Reset the checkbox
            document.getElementById('confirmDelete').checked = false;
            
            // Refresh data after short delay
            setTimeout(() => {
                loadUserData();
                
                // Switch back to view tab
                document.querySelector('.tab-btn[data-tab="view"]').click();
            }, 1500);
        } else {
            showMessage(data.message || 'Failed to delete data', 'error');
        }
    })
    .catch(error => {
        showMessage('Error deleting data. Please try again.', 'error');
        console.error('Delete error:', error);
    })
    .finally(() => {
        // Restore button state
        deleteBtn.textContent = originalText;
        deleteBtn.disabled = !document.getElementById('confirmDelete').checked;
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
    
    // Insert message at the top
    const container = document.querySelector('.dashboard-container');
    const header = container.querySelector('.dashboard-header');
    container.insertBefore(messageDiv, header.nextSibling);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (messageDiv && messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

// Helper functions
function formatDate(dateString) {
    if (!dateString) return 'Not provided';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function capitalizeFirstLetter(string) {
    if (!string) return 'Not provided';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function formatEducation(education) {
    if (!education) return 'Not provided';
    
    const educationMap = {
        'high_school': 'High School',
        'bachelor': 'Bachelor\'s Degree',
        'master': 'Master\'s Degree',
        'phd': 'PhD',
        'other': 'Other'
    };
    
    return educationMap[education] || education;
}

// Logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        fetch('logout.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            sessionStorage.clear();
            window.location.href = 'login.html';
        })
        .catch(error => {
            console.error('Logout error:', error);
            // Fallback logout
            sessionStorage.clear();
            window.location.href = 'login.html';
        });
    }
}
