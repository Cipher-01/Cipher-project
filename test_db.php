<?php
// Simple database test
require_once 'db.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test connection
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'high_school_db'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Database 'high_school_db' exists!</p>";
    } else {
        echo "<p style='color: red;'>❌ Database 'high_school_db' does not exist!</p>";
        echo "<p>Please run the setup.sql file first.</p>";
    }
    
    // Test if students table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'students'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Table 'students' exists!</p>";
        
        // Show table structure
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        
        $stmt = $pdo->query("DESCRIBE students");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        echo "<h3>Sample Data:</h3>";
        $stmt = $pdo->query("SELECT id, username, first_name, last_name, email FROM students LIMIT 5");
        if ($stmt->rowCount() > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ No data in students table.</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table 'students' does not exist!</p>";
        echo "<p>Please run the setup.sql file first.</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='register.php'>Go to Registration</a> | <a href='login.php'>Go to Login</a></p>";
?>
