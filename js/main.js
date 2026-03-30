// Handle session messages on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check for error or success messages in session
    // This would typically be passed from PHP, but for now we'll check URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const success = urlParams.get('success');
    
    if (error) {
        showMessage(error, 'error');
    }
    
    if (success) {
        showMessage(success, 'success');
    }
});

// Function to show messages
function showMessage(message, type) {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = type === 'error' ? 'alert-error' : 'alert-success';
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        padding: 15px 20px;
        border-radius: 5px;
        max-width: 300px;
        word-wrap: break-word;
    `;
    
    // Add to page
    document.body.appendChild(messageDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.parentNode.removeChild(messageDiv);
        }
    }, 5000);
}

// Form validation helpers
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePassword(password) {
    return password.length >= 8;
}

function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]+$/;
    return phone === '' || re.test(phone);
}
