<?php
// Simple database test
header('Content-Type: text/html; charset=utf-8');
echo "<h2>🔧 Database Connection Test</h2>";

try {
    // Test connection to MySQL server first (without database)
    $pdo_server = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    echo "<p style='color: green;'>✅ MySQL Server Connection: SUCCESS</p>";
    
    // Check if lab5 database exists
    $stmt = $pdo_server->query("SHOW DATABASES LIKE 'lab5'");
    $db_exists = $stmt->fetch();
    
    if ($db_exists) {
        echo "<p style='color: green;'>✅ Database 'lab5': EXISTS</p>";
        
        // Now test connection to lab5 database
        $pdo = new PDO("mysql:host=localhost;dbname=lab5;charset=utf8mb4", "root", "");
        echo "<p style='color: green;'>✅ Database 'lab5' Connection: SUCCESS</p>";
        
        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<p style='color: green;'>✅ Tables found: " . implode(", ", $tables) . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No tables found. Creating tables...</p>";
            
            // Create tables
            $sql_users = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            $sql_bio = "CREATE TABLE IF NOT EXISTS bio_data (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_email VARCHAR(255) NOT NULL,
                first_name VARCHAR(255) NOT NULL,
                last_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                date_of_birth DATE NOT NULL,
                gender ENUM('male', 'female', 'other') NOT NULL,
                address TEXT,
                city VARCHAR(100),
                country VARCHAR(100),
                occupation VARCHAR(255),
                education ENUM('high_school', 'bachelor', 'master', 'phd', 'other'),
                bio TEXT,
                profile_picture VARCHAR(255),
                newsletter BOOLEAN DEFAULT FALSE,
                terms BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
            )";
            
            $pdo->exec($sql_users);
            $pdo->exec($sql_bio);
            
            echo "<p style='color: green;'>✅ Tables created successfully!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Database 'lab5': NOT FOUND</p>";
        echo "<p style='color: blue;'>Creating database 'lab5'...</p>";
        
        // Create database
        $pdo_server->exec("CREATE DATABASE lab5");
        echo "<p style='color: green;'>✅ Database 'lab5' created successfully!</p>";
        
        // Reload this page to continue setup
        echo "<p><a href='db-test.php'>Click here to continue setup</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: blue;'>💡 <strong>Solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP/WAMP/MAMP is running</li>";
    echo "<li>Start MySQL service</li>";
    echo "<li>Check if MySQL is running on port 3306</li>";
    echo "<li>Verify MySQL username/password in config.php</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>🚀 Next Steps:</h3>";
echo "<p>If everything above shows ✅, your database is ready!</p>";
echo "<p><strong>Test your project:</strong></p>";
echo "<ol>";
echo "<li><a href='portfolio.html' target='_blank'>Open Portfolio</a></li>";
echo "<li>Click on 'User Registration System' project</li>";
echo "<li>Try to register a new user</li>";
echo "<li>Login and fill the bio form</li>";
echo "</ol>";
?>
