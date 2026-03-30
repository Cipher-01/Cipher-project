<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $first_name = trim($_POST['firstName']);
        $last_name = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $gender = $_POST['gender'];
        $course = $_POST['course'];
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirmPassword'];
        $address = trim($_POST['address']);
        
        // Basic validation
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $error = "Please fill all required fields";
        } elseif ($password != $confirm_password) {
            $error = "Passwords do not match";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long";
        } else {
            // Check if email exists - using prepared statement for security
            $check = "SELECT id FROM students WHERE email = ?";
            $stmt = $pdo->prepare($check);
            $stmt->execute([$email]);
            $result = $stmt->rowCount();
            
            if ($result > 0) {
                $error = "Email already registered";
            } else {
                // Create username
                $username = strtolower($first_name . '.' . $last_name);
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user - using prepared statement for security
                $sql = "INSERT INTO students (username, first_name, last_name, email, gender, course, phone, password, address, registration_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $first_name, $last_name, $email, $gender, $course, $phone, $hashed_password, $address]);
                
                if ($stmt->rowCount() > 0) {
                    $_SESSION['success'] = "Registration successful! Username: $username";
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Registration failed: No rows inserted";
                }
            }
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch(Exception $e) {
        $error = "General error: " . $e->getMessage();
    }
}

// Display session messages
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Evergreen High School</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Evergreen High School</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="academics.html">Academics</a></li>
                    <li><a href="science.html">Science Dept</a></li>
                    <li><a href="register.php" class="active">Registration</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="welcome-section">
            <h2>Student Registration</h2>
            <div class="moving-line"></div>
            <p>Join Excellence High School and start your journey to success!</p>
            
            <?php if ($error): ?>
                <div class="error-message" style="background: #dc3545; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message" style="background: #28a745; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form id="registrationForm" action="register.php" method="POST">
                    <div class="form-row" style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="firstName">First Name *</label>
                            <input 
                                type="text" 
                                id="firstName" 
                                name="firstName" 
                                placeholder="Enter your first name"
                                value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>"
                                required
                                onclick="showFieldInfo('firstName')"
                                onblur="validateFirstName()"
                                onkeyup="checkNameLength(this)"
                            >
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="lastName">Last Name *</label>
                            <input 
                                type="text" 
                                id="lastName" 
                                name="lastName" 
                                placeholder="Enter your last name"
                                value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>"
                                required
                                onclick="showFieldInfo('lastName')"
                                onblur="validateLastName()"
                                onkeyup="checkNameLength(this)"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email address"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required
                            onchange="validateEmail()"
                            onblur="validateEmail()"
                            onkeyup="checkEmailFormat(this)"
                        >
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="gender">Gender *</label>
                            <select 
                                id="gender" 
                                name="gender"
                                required
                                onchange="updateGenderInfo()"
                                onclick="showGenderInfo()"
                            >
                                <option value="">Select Gender</option>
                                <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="course">Course/Class *</label>
                            <select 
                                id="course" 
                                name="course"
                                required
                                onchange="updateCourseInfo()"
                                onclick="showCourseInfo()"
                            >
                                <option value="">Select Course</option>
                                <option value="grade9" <?php echo (isset($_POST['course']) && $_POST['course'] == 'grade9') ? 'selected' : ''; ?>>Grade 9</option>
                                <option value="grade10" <?php echo (isset($_POST['course']) && $_POST['course'] == 'grade10') ? 'selected' : ''; ?>>Grade 10</option>
                                <option value="grade11" <?php echo (isset($_POST['course']) && $_POST['course'] == 'grade11') ? 'selected' : ''; ?>>Grade 11</option>
                                <option value="grade12" <?php echo (isset($_POST['course']) && $_POST['course'] == 'grade12') ? 'selected' : ''; ?>>Grade 12</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            placeholder="Enter your phone number"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                            onkeyup="formatPhone(this)"
                            onblur="validatePhone()"
                        >
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex: 1;">
                            <label for="password">Password *</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Create a password"
                                required
                                onkeyup="checkPasswordStrength(this)"
                                onblur="validatePassword()"
                            >
                            <small style="color: #666; font-size: 0.9em;">Password must be at least 8 characters long</small>
                        </div>
                        
                        <div class="form-group" style="flex: 1;">
                            <label for="confirmPassword">Confirm Password *</label>
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                name="confirmPassword" 
                                placeholder="Confirm your password"
                                required
                                onkeyup="checkPasswordMatch()"
                                onblur="validatePasswordMatch()"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea 
                            id="address" 
                            name="address" 
                            placeholder="Enter your home address"
                            rows="3"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; resize: vertical;"
                            onkeyup="countCharacters(this)"
                            onblur="validateAddress()"
                        ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="remember-me">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the Terms and Conditions and Privacy Policy</label>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button type="submit" class="btn" onmouseover="highlightButton(this)" onmouseout="unhighlightButton(this)">Register Now</button>
                        <button type="reset" class="btn" style="background-color: #6c757d;" onclick="resetForm()">Reset Form</button>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px; text-align: center;">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Evergreen High School. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // JavaScript functions for form validation and interactivity
        function showMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            messageDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
                z-index: 1000;
                background: ${type === 'success' ? '#28a745' : '#dc3545'};
                max-width: 300px;
            `;
            
            document.body.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }

        function showFieldInfo(fieldName) {
            const messages = {
                'firstName': 'Please enter your legal first name as it appears on official documents.',
                'lastName': 'Please enter your legal last name as it appears on official documents.'
            };
            if (messages[fieldName]) {
                showMessage(messages[fieldName], 'info');
            }
        }

        function validateFirstName() {
            const firstName = document.getElementById('firstName').value.trim();
            if (firstName.length < 2) {
                showMessage('First name must be at least 2 characters long.', 'error');
                return false;
            }
            return true;
        }

        function validateLastName() {
            const lastName = document.getElementById('lastName').value.trim();
            if (lastName.length < 2) {
                showMessage('Last name must be at least 2 characters long.', 'error');
                return false;
            }
            return true;
        }

        function checkNameLength(input) {
            if (input.value.length > 50) {
                showMessage('Name cannot exceed 50 characters.', 'error');
                input.value = input.value.substring(0, 50);
            }
        }

        function checkEmailFormat(input) {
            const email = input.value;
            if (email.includes('@') && email.includes('.')) {
                input.style.borderColor = '#28a745';
            } else {
                input.style.borderColor = '#ddd';
            }
        }

        function validateEmail() {
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                showMessage('Please enter a valid email address.', 'error');
                return false;
            }
            return true;
        }

        function showGenderInfo() {
            showMessage('Select your gender for school records.', 'info');
        }

        function updateGenderInfo() {
            const gender = document.getElementById('gender').value;
            if (gender) {
                showMessage(`Gender selected: ${gender}`, 'info');
            }
        }

        function showCourseInfo() {
            showMessage('Select your current grade level.', 'info');
        }

        function updateCourseInfo() {
            const course = document.getElementById('course').value;
            if (course) {
                const courseNames = {
                    'grade9': 'Grade 9 - Foundation Year',
                    'grade10': 'Grade 10 - Intermediate', 
                    'grade11': 'Grade 11 - Advanced',
                    'grade12': 'Grade 12 - Senior Year'
                };
                showMessage(courseNames[course] || course, 'info');
            }
        }

        function formatPhone(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            } else if (value.length > 3) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            }
            input.value = value;
        }

        function validatePhone() {
            const phone = document.getElementById('phone').value;
            if (phone && !/^\d{3}-\d{3}-\d{4}$/.test(phone)) {
                showMessage('Please enter a valid phone number (XXX-XXX-XXXX).', 'error');
                return false;
            }
            return true;
        }

        function checkPasswordStrength(input) {
            const password = input.value;
            const strengthDiv = document.getElementById('passwordStrength') || createPasswordStrengthDiv();
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'][strength];
            const strengthColor = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#007bff'][strength];
            
            strengthDiv.textContent = `Password Strength: ${strengthText}`;
            strengthDiv.style.color = strengthColor;
        }

        function createPasswordStrengthDiv() {
            const div = document.createElement('div');
            div.id = 'passwordStrength';
            div.style.cssText = 'margin-top: 5px; font-size: 0.9em; font-weight: bold;';
            document.getElementById('password').parentNode.appendChild(div);
            return div;
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                showMessage('Password must be at least 8 characters long.', 'error');
                return false;
            }
            return true;
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const confirmInput = document.getElementById('confirmPassword');
            
            if (confirmPassword && password !== confirmPassword) {
                confirmInput.style.borderColor = '#dc3545';
            } else if (confirmPassword && password === confirmPassword) {
                confirmInput.style.borderColor = '#28a745';
            }
        }

        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match.', 'error');
                return false;
            }
            return true;
        }

        function countCharacters(textarea) {
            const remaining = 200 - textarea.value.length;
            if (remaining < 20) {
                showMessage(`${remaining} characters remaining`, 'info');
            }
        }

        function validateAddress() {
            const address = document.getElementById('address').value.trim();
            if (address && address.length < 10) {
                showMessage('Address must be at least 10 characters long if provided.', 'error');
                return false;
            }
            return true;
        }

        function highlightButton(button) {
            button.style.transform = 'scale(1.05)';
            button.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.3)';
        }

        function unhighlightButton(button) {
            button.style.transform = 'scale(1)';
            button.style.boxShadow = '';
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
                document.getElementById('registrationForm').reset();
                // Reset styles
                document.querySelectorAll('input, select, textarea').forEach(element => {
                    element.style.borderColor = '';
                });
                showMessage('Form has been reset.', 'info');
            }
        }

        // Form validation on submit
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // Additional client-side validation before form submission
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                showMessage('Passwords do not match. Please correct before submitting.', 'error');
                return false;
            }
            
            if (!document.getElementById('terms').checked) {
                e.preventDefault();
                showMessage('You must agree to the Terms and Conditions to register.', 'error');
                return false;
            }
        });
    </script>
</body>
</html>