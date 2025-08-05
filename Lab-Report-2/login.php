<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get and validate input
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Check user credentials
    $stmt = $pdo->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Log the user in
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['full_name'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful!',
        'redirect' => 'protected-bio.html',
        'user' => [
            'name' => $user['full_name'],
            'email' => $user['email']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during login']);
}
?>
