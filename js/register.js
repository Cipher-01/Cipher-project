// Registration form specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registrationForm');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const gender = document.getElementById('gender').value;
            const course = document.getElementById('course').value;
            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const address = document.getElementById('address').value.trim();
            const terms = document.getElementById('terms').checked;
            
            // Client-side validation
            if (!firstName || !lastName || !email || !gender || !course || !password || !confirmPassword) {
                showMessage('Please fill in all required fields.', 'error');
                return;
            }
            
            if (!validateEmail(email)) {
                showMessage('Please enter a valid email address.', 'error');
                return;
            }
            
            if (password.length < 8) {
                showMessage('Password must be at least 8 characters long.', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match.', 'error');
                return;
            }
            
            if (!terms) {
                showMessage('You must agree to the terms and conditions.', 'error');
                return;
            }
            
            // Submit form
            const formData = new FormData(this);
            
            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(data => {
                if (data) {
                    showMessage('Registration failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                showMessage('An error occurred. Please try again.', 'error');
            });
        });
    }
});

// Password visibility toggles
function toggleRegPasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.querySelector('#password + .password-input button i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
}

function toggleConfirmPasswordVisibility() {
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const toggleButton = document.querySelector('#confirmPassword + .password-input button i');
    
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        confirmPasswordInput.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
}
