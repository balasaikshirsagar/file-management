<?php
include "./utils/functions.php"; // Ensure this file correctly sets up the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Employee details
    $name = trim($_POST['name']);
    $designation = trim($_POST['designation']);
    $dob = trim($_POST['dob']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);

    // File upload handling
    if (isset($_FILES['files'])) {
        $fileInfo = [];
        foreach ($_FILES['files']['name'] as $key => $filename) {
            if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                $tempPath = $_FILES['files']['tmp_name'][$key];
                $uploadPath = "../uploads/" . basename($filename);

                if (move_uploaded_file($tempPath, $uploadPath)) {
                    $fileInfo[] = $filename; // Store filenames for database
                } else {
                    echo json_encode(['error' => 'Failed to upload file: ' . $filename]);
                    exit;
                }
            }
        }
    }

    // Basic validation
    if (empty($name) || empty($designation) || empty($dob) || empty($mobile) || empty($email) || empty($fileInfo)) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    // Establish the database connection
    $conn = connect(); // Use the PDO connection from the connect() function

    if ($conn) {
        // Begin database transaction
        $conn->beginTransaction();

        try {
            // Insert employee details into the database
            $sql = "INSERT INTO employees (name, designation, dob, mobile, email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $designation, $dob, $mobile, $email]);

            // Get the last inserted employee ID
            $employeeId = $conn->lastInsertId();

            // Insert file information into the database
            foreach ($fileInfo as $filename) {
                $fileSql = "INSERT INTO employee_files (employee_id, filename) VALUES (?, ?)";
                $fileStmt = $conn->prepare($fileSql);
                $fileStmt->execute([$employeeId, $filename]);
            }

            // Commit transaction
            $conn->commit();

            echo json_encode(['success' => 'Employee added successfully']);
        } catch (Exception $e) {
            // Rollback transaction in case of an error
            $conn->rollBack();
            echo json_encode(['error' => 'Failed to add employee: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Failed to connect to the database']);
    }
}
