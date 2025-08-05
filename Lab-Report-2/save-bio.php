<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    // Get form data
    $user_email = $_SESSION['user_email'];
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $education = $_POST['education'] ?? '';
    $bio = trim($_POST['bio'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $terms = isset($_POST['terms']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($first_name)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($last_name)) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($date_of_birth)) {
        $errors[] = 'Date of birth is required';
    } else {
        $birthDate = new DateTime($date_of_birth);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        
        if ($age < 13) {
            $errors[] = 'You must be at least 13 years old';
        }
        
        if ($birthDate > $today) {
            $errors[] = 'Birth date cannot be in the future';
        }
    }
    
    if (empty($gender)) {
        $errors[] = 'Gender is required';
    }
    
    if (!$terms) {
        $errors[] = 'You must agree to the terms and conditions';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Handle file upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $file_name = uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
                $profile_picture = $file_name;
            }
        }
    }
    
    // Check if bio data already exists for this user
    $stmt = $pdo->prepare("SELECT id FROM bio_data WHERE user_email = ?");
    $stmt->execute([$user_email]);
    $existing_bio = $stmt->fetch();
    
    if ($existing_bio) {
        // Update existing bio data
        $sql = "UPDATE bio_data SET 
                first_name = ?, last_name = ?, email = ?, phone = ?, 
                date_of_birth = ?, gender = ?, address = ?, city = ?, 
                country = ?, occupation = ?, education = ?, bio = ?, 
                newsletter = ?, terms = ?, updated_at = CURRENT_TIMESTAMP";
        
        $params = [
            $first_name, $last_name, $email, $phone, $date_of_birth, 
            $gender, $address, $city, $country, $occupation, $education, 
            $bio, $newsletter, $terms
        ];
        
        if ($profile_picture) {
            $sql .= ", profile_picture = ?";
            $params[] = $profile_picture;
        }
        
        $sql .= " WHERE user_email = ?";
        $params[] = $user_email;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $message = 'Bio data updated successfully!';
    } else {
        // Insert new bio data
        $sql = "INSERT INTO bio_data (
                user_email, first_name, last_name, email, phone, 
                date_of_birth, gender, address, city, country, 
                occupation, education, bio, profile_picture, 
                newsletter, terms
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $user_email, $first_name, $last_name, $email, $phone,
            $date_of_birth, $gender, $address, $city, $country,
            $occupation, $education, $bio, $profile_picture,
            $newsletter, $terms
        ];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $message = 'Bio data saved successfully!';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'bio_id' => $existing_bio ? $existing_bio['id'] : $pdo->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    error_log("Save bio error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Save bio error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while saving bio data']);
}
?>
