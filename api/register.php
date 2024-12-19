<?php

include "./utils/functions.php";

// Initialize response
$response = [
    'success' => false,
    'message' => 'An error occurred. Please try again.',
];

try {
    // Establish database connection
    $pdo = connect();

    // Process POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            $response['message'] = 'All fields are required.';
            echo json_encode($response);
            exit;
        }

        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $response['message'] = 'Email already registered.';
            echo json_encode($response);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
        ]);

        $response['success'] = true;
        $response['message'] = 'Registration successful.';
    }
} catch (\PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

// Output response as JSON
echo json_encode($response);
