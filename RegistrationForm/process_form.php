<?php
header('Content-Type: application/json');

// Database configuration (update with your cloud database credentials)
// For now, we'll save to a file

session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Collect and sanitize form data
    $formData = array(
        'fullName' => sanitizeInput($_POST['fullName'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'dob' => sanitizeInput($_POST['dob'] ?? ''),
        'gender' => sanitizeInput($_POST['gender'] ?? ''),
        'address' => sanitizeInput($_POST['address'] ?? ''),
        'city' => sanitizeInput($_POST['city'] ?? ''),
        'country' => sanitizeInput($_POST['country'] ?? ''),
        'education' => sanitizeInput($_POST['education'] ?? ''),
        'timestamp' => date('Y-m-d H:i:s')
    );

    // Validate form data
    $validation = validateFormData($formData);
    
    if (!$validation['valid']) {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validation['errors']
        ));
        exit;
    }

    // Save to file (alternative: save to database)
    $success = saveFormData($formData);

    if ($success) {
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Registration submitted successfully!',
            'data' => $formData
        ));
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Failed to save registration'
        ));
    }
    exit;
}

// Function to sanitize input
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

// Function to validate form data
function validateFormData($data) {
    $errors = array();
    
    // Validate Full Name
    if (empty($data['fullName']) || strlen($data['fullName']) < 3) {
        $errors['fullName'] = 'Full name is required and must be at least 3 characters';
    }
    if (!preg_match('/^[a-zA-Z\s]+$/', $data['fullName'])) {
        $errors['fullName'] = 'Full name must contain only letters';
    }
    
    // Validate Email
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email address is required';
    }
    
    // Validate Phone
    if (empty($data['phone']) || strlen(preg_replace('/[^0-9]/', '', $data['phone'])) < 10) {
        $errors['phone'] = 'Valid phone number is required (at least 10 digits)';
    }
    
    // Validate Date of Birth
    if (empty($data['dob'])) {
        $errors['dob'] = 'Date of birth is required';
    } else {
        $birthDate = new DateTime($data['dob']);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if ($age < 18 || $age > 120) {
            $errors['dob'] = 'You must be between 18 and 120 years old';
        }
    }
    
    // Validate Gender
    if (empty($data['gender'])) {
        $errors['gender'] = 'Gender is required';
    }
    
    // Validate Address
    if (empty($data['address']) || strlen($data['address']) < 5) {
        $errors['address'] = 'Address is required and must be at least 5 characters';
    }
    
    // Validate City
    if (empty($data['city']) || strlen($data['city']) < 2) {
        $errors['city'] = 'City is required and must be at least 2 characters';
    }
    
    // Validate Country
    if (empty($data['country']) || strlen($data['country']) < 2) {
        $errors['country'] = 'Country is required and must be at least 2 characters';
    }
    
    // Validate Education
    if (empty($data['education'])) {
        $errors['education'] = 'Education level is required';
    }
    
    return array(
        'valid' => count($errors) === 0,
        'errors' => $errors
    );
}

// Function to save form data to file
function saveFormData($data) {
    $registrationsDir = __DIR__ . '/registrations';
    
    // Create directory if it doesn't exist
    if (!is_dir($registrationsDir)) {
        mkdir($registrationsDir, 0755, true);
    }
    
    // Create a unique filename
    $filename = $registrationsDir . '/' . $data['email'] . '_' . time() . '.json';
    
    // Save to file
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($filename, $json) !== false;
}

// Function to save form data to database (MySQL example)
function saveFormDataToDatabase($data) {
    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "registration_form";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        return false;
    }
    
    // Prepare SQL query
    $sql = "INSERT INTO registrations (full_name, email, phone, dob, gender, address, city, country, education, timestamp) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssss",
        $data['fullName'],
        $data['email'],
        $data['phone'],
        $data['dob'],
        $data['gender'],
        $data['address'],
        $data['city'],
        $data['country'],
        $data['education'],
        $data['timestamp']
    );
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Display success message if form was submitted via GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['success'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Registration Successful</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }
            .success-message {
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                text-align: center;
            }
            h1 { color: #28a745; }
            a { color: #667eea; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="success-message">
            <h1>Registration Successful!</h1>
            <p>Thank you for registering. Your information has been submitted successfully.</p>
            <a href="index.html">Register Another User</a>
        </div>
    </body>
    </html>
    <?php
}
?>
