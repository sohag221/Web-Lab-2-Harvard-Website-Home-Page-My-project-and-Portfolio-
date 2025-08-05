<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Destroy the session
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'redirect' => 'login.html'
    ]);
    
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during logout']);
}
?>
