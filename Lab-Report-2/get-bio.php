<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $user_email = $_SESSION['user_email'];
    
    // Get bio data for the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    $bio_data = $stmt->fetch();
    
    if ($bio_data) {
        // Remove sensitive data before sending
        unset($bio_data['id']);
        unset($bio_data['user_email']);
        
        echo json_encode([
            'success' => true,
            'bio' => $bio_data
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'bio' => null,
            'message' => 'No bio data found'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Get bio error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Get bio error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while retrieving bio data']);
}
?>
