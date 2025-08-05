<?php
// Database configuration for lab5 database
class Database {
    private $host = 'localhost';
    private $database = 'lab5';
    private $username = 'root'; // Change this to your MySQL username
    private $password = '';     // Change this to your MySQL password
    private $connection;
    
    public function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
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
