<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration for lab5 database
class Database {
    private $host = 'localhost';
    private $database = 'lab5';
    private $username = 'root'; // Change this to your MySQL username
    private $password = '';     // Change this to your MySQL password
    private $connection;
    
    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            error_log("Database connection successful");
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            
            // Return a more specific error message
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                die(json_encode([
                    'success' => false, 
                    'message' => 'Database "lab5" not found. Please create the database first.'
                ]));
            } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
                die(json_encode([
                    'success' => false, 
                    'message' => 'Database access denied. Please check username/password.'
                ]));
            } else {
                die(json_encode([
                    'success' => false, 
                    'message' => 'Database connection failed: ' . $e->getMessage()
                ]));
            }
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Create database and tables if they don't exist
    public function initializeDatabase() {
        try {
            // Create users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->connection->exec($sql);
            
            // Create bio_data table
            $sql = "CREATE TABLE IF NOT EXISTS bio_data (
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
            $this->connection->exec($sql);
            
            return true;
        } catch (PDOException $e) {
            error_log("Database initialization failed: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize database
$database = new Database();
$database->initializeDatabase();
$pdo = $database->getConnection();
?>
