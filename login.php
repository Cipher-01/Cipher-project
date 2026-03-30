<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = "Please enter username and password";
        } else {
            $sql = "SELECT * FROM students WHERE username = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['logged_in'] = true;
                    
                    header("Location: index.html");
                    exit();
                } else {
                    $error = "Invalid username or password";
                }
            } else {
                $error = "Invalid username or password";
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
    <title>Login - Evergreen High School</title>
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
                    <li><a href="register.php">Registration</a></li>
                    <li><a href="login.php" class="active">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="welcome-section">
            <h2>Student Login</h2>
            <div class="moving-line"></div>
            <p>Access your student portal and resources</p>
            
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
                <form id="loginForm" action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Enter your username"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            required
                            onclick="showFieldInfo('username')"
                            onblur="validateUsername()"
                            onkeyup="checkUsernameFormat(this)"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password"
                            required
                            onclick="showFieldInfo('password')"
                            onblur="validatePassword()"
                            onkeyup="checkPasswordStrength(this)"
                        >
                    </div>
                    
                    <div class="form-group">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button type="submit" class="btn" onmouseover="highlightButton(this)" onmouseout="unhighlightButton(this)">Login</button>
                        <button type="button" class="btn" style="background-color: #6c757d;" onclick="resetForm()">Clear</button>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px; text-align: center;">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                        <p><a href="#" onclick="showForgotPassword()">Forgot Password?</a></p>
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
        // JavaScript functions for login form validation and interactivity
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
                'username': 'Enter your username (typically: firstname.lastname)',
                'password': 'Enter your password. It should be at least 8 characters long.'
            };
            if (messages[fieldName]) {
                showMessage(messages[fieldName], 'info');
            }
        }

        function validateUsername() {
            const username = document.getElementById('username').value.trim();
            if (username.length < 3) {
                showMessage('Username must be at least 3 characters long.', 'error');
                return false;
            }
            return true;
        }

        function checkUsernameFormat(input) {
            const username = input.value;
            // Basic username validation
            if (username.includes('.') && username.split('.').length >= 2) {
                input.style.borderColor = '#28a745';
            } else {
                input.style.borderColor = '#ddd';
            }
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                showMessage('Password must be at least 8 characters long.', 'error');
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

        function highlightButton(button) {
            button.style.transform = 'scale(1.05)';
            button.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.3)';
        }

        function unhighlightButton(button) {
            button.style.transform = 'scale(1)';
            button.style.boxShadow = '';
        }

        function resetForm() {
            if (confirm('Are you sure you want to clear the form?')) {
                document.getElementById('loginForm').reset();
                // Reset styles
                document.querySelectorAll('input').forEach(element => {
                    element.style.borderColor = '';
                });
                showMessage('Form has been cleared.', 'info');
            }
        }

        function showForgotPassword() {
            showMessage('Please contact the school administrator to reset your password.', 'info');
        }

        // Form validation on submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (username.length < 3) {
                e.preventDefault();
                showMessage('Username must be at least 3 characters long.', 'error');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                showMessage('Password must be at least 8 characters long.', 'error');
                return false;
            }
        });

        // Add enter key support for form fields
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });

        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });

        // Auto-focus username field on page load
        window.onload = function() {
            document.getElementById('username').focus();
        };
    </script>
</body>
</html>