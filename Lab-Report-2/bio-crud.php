<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    $user_email = $_SESSION['user_email'];

    switch ($method) {
        case 'POST':
            if ($action === 'create') {
                createBioData($pdo, $user_email);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action for POST']);
            }
            break;

        case 'GET':
            if ($action === 'read') {
                readBioData($pdo, $user_email);
            } elseif ($action === 'list') {
                listAllBioData($pdo, $user_email);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action for GET']);
            }
            break;

        case 'PUT':
        case 'POST':
            if ($action === 'update' || ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update')) {
                updateBioData($pdo, $user_email);
            } elseif ($action === 'create') {
                createBioData($pdo, $user_email);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
            break;

        case 'DELETE':
            if ($action === 'delete') {
                deleteBioData($pdo, $user_email);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action for DELETE']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (PDOException $e) {
    error_log("CRUD error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("CRUD error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}

// CREATE operation
function createBioData($pdo, $user_email) {
    // Get data from POST request
    $input = getInputData();
    
    // Validate required fields
    $errors = validateBioData($input);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
        return;
    }

    // Check if bio data already exists
    $stmt = $pdo->prepare("SELECT id FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Bio data already exists. Use update instead.']);
        return;
    }

    // Handle file upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $profile_picture = handleFileUpload($_FILES['profile_picture']);
        if (!$profile_picture) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile picture']);
            return;
        }
    }

    // Insert new bio data
    $sql = "INSERT INTO bio_data (user_email, first_name, last_name, email, phone, date_of_birth, 
            gender, address, city, country, occupation, education, bio, profile_picture, 
            newsletter, terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $user_email,
        $input['first_name'],
        $input['last_name'],
        $input['email'],
        $input['phone'],
        $input['date_of_birth'],
        $input['gender'],
        $input['address'],
        $input['city'],
        $input['country'],
        $input['occupation'],
        $input['education'],
        $input['bio'],
        $profile_picture,
        $input['newsletter'] ? 1 : 0,
        $input['terms'] ? 1 : 0
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Bio data created successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

// READ operation (single record)
function readBioData($pdo, $user_email) {
    $stmt = $pdo->prepare("SELECT * FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    $bio_data = $stmt->fetch();
    
    if ($bio_data) {
        // Remove sensitive data
        unset($bio_data['user_email']);
        
        echo json_encode([
            'success' => true,
            'data' => $bio_data
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Bio data not found'
        ]);
    }
}

// READ operation (list all records - for admin or extended functionality)
function listAllBioData($pdo, $user_email) {
    // For now, only return current user's data
    // Can be extended for admin functionality
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, created_at, updated_at FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    $bio_data_list = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $bio_data_list,
        'count' => count($bio_data_list)
    ]);
}

// UPDATE operation
function updateBioData($pdo, $user_email) {
    // Get data from PUT request
    $input = getInputData();
    
    // Validate required fields
    $errors = validateBioData($input);
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
        return;
    }

    // Check if bio data exists
    $stmt = $pdo->prepare("SELECT id, profile_picture FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    $existing = $stmt->fetch();
    
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Bio data not found. Create it first.']);
        return;
    }

    // Handle file upload
    $profile_picture = $existing['profile_picture']; // Keep existing picture by default
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $new_picture = handleFileUpload($_FILES['profile_picture']);
        if ($new_picture) {
            // Delete old picture if it exists
            if ($profile_picture && file_exists("uploads/" . $profile_picture)) {
                unlink("uploads/" . $profile_picture);
            }
            $profile_picture = $new_picture;
        }
    }

    // Update bio data
    $sql = "UPDATE bio_data SET first_name = ?, last_name = ?, email = ?, phone = ?, 
            date_of_birth = ?, gender = ?, address = ?, city = ?, country = ?, 
            occupation = ?, education = ?, bio = ?, profile_picture = ?, 
            newsletter = ?, terms = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE user_email = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $input['first_name'],
        $input['last_name'],
        $input['email'],
        $input['phone'],
        $input['date_of_birth'],
        $input['gender'],
        $input['address'],
        $input['city'],
        $input['country'],
        $input['occupation'],
        $input['education'],
        $input['bio'],
        $profile_picture,
        $input['newsletter'] ? 1 : 0,
        $input['terms'] ? 1 : 0,
        $user_email
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Bio data updated successfully'
    ]);
}

// DELETE operation
function deleteBioData($pdo, $user_email) {
    // Get the bio data to delete associated files
    $stmt = $pdo->prepare("SELECT profile_picture FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    $bio_data = $stmt->fetch();
    
    if (!$bio_data) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Bio data not found']);
        return;
    }

    // Delete the record
    $stmt = $pdo->prepare("DELETE FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    
    // Delete associated profile picture
    if ($bio_data['profile_picture'] && file_exists("uploads/" . $bio_data['profile_picture'])) {
        unlink("uploads/" . $bio_data['profile_picture']);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Bio data deleted successfully'
    ]);
}

// Helper function to get input data
function getInputData() {
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // For PUT requests, get JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return $data ?: [];
    } else {
        // For POST requests, use $_POST
        return $_POST;
    }
}

// Validation function
function validateBioData($data) {
    $errors = [];
    
    if (empty(trim($data['first_name'] ?? ''))) {
        $errors[] = 'First name is required';
    }
    
    if (empty(trim($data['last_name'] ?? ''))) {
        $errors[] = 'Last name is required';
    }
    
    if (empty(trim($data['email'] ?? ''))) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($data['date_of_birth'] ?? '')) {
        $errors[] = 'Date of birth is required';
    }
    
    if (empty($data['gender'] ?? '')) {
        $errors[] = 'Gender is required';
    } elseif (!in_array($data['gender'], ['male', 'female', 'other'])) {
        $errors[] = 'Invalid gender value';
    }
    
    if (!empty($data['education']) && !in_array($data['education'], ['high_school', 'bachelor', 'master', 'phd', 'other'])) {
        $errors[] = 'Invalid education value';
    }
    
    return $errors;
}

// File upload handler
function handleFileUpload($file) {
    $upload_dir = 'uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > $max_size) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}
?>
