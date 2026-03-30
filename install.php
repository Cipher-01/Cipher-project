<?php
// Database Installation Script
echo "<h2>Evergreen High School - Database Installation</h2>";

// Check if setup.sql exists
if (!file_exists('setup.sql')) {
    echo "<p style='color: red;'>❌ setup.sql file not found!</p>";
    exit();
}

try {
    // Read and execute setup.sql
    $sql = file_get_contents('setup.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    require_once 'db.php';
    
    echo "<h3>Installing database...</h3>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "<p style='color: green;'>✅ Executed: " . substr($statement, 0, 50) . "...</p>";
            } catch(PDOException $e) {
                echo "<p style='color: orange;'>⚠️ Warning: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<h3>Installation Complete!</h3>";
    echo "<p style='color: green;'>✅ Database and tables created successfully!</p>";
    echo "<p>Sample test users created:</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> john.doe | <strong>Password:</strong> password</li>";
    echo "<li><strong>Username:</strong> jane.smith | <strong>Password:</strong> password</li>";
    echo "</ul>";
    
    echo "<p><a href='test_db.php'>Test Database Connection</a> | <a href='register.php'>Go to Registration</a> | <a href='login.php'>Go to Login</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Installation failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database credentials in db.php</p>";
}
?>
