<?php
// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include database connection
include "./utils/functions.php";

// Get the PDO connection
$pdo = connect();

// Initialize response
$response = [
    'success' => false,
    'message' => 'Invalid email or password.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and Password are required.';
        echo json_encode($response);
        exit;
    }

    // Query to check user credentials
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            $response['success'] = true;
            $response['message'] = 'Login successful.';

            // Optionally, set session variables
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
        }
    }

    $stmt->closeCursor();
}

echo json_encode($response);
