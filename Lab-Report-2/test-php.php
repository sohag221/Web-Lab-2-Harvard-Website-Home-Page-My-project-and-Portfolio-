<?php
// PHP Test File - Check if PHP is working
echo "<h2>PHP Test Results</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=lab5;charset=utf8mb4", "root", "");
    echo "<p style='color: green;'><strong>Database Connection:</strong> SUCCESS ✅</p>";
    
    // Test if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Tables found:</strong> " . implode(", ", $tables) . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Database Connection:</strong> FAILED ❌</p>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Script:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
?>
