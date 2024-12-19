<?php
include "./utils/functions.php"; // Ensure this file provides the `connect()` function

// Initialize the database connection
$conn = connect(); // Use the connect() function from functions.php

if ($conn && isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    try {
        // Prepare the SQL query
        $sql = "SELECT * FROM employee_files WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(1, $employee_id, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch the results
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Send the files as JSON response
        echo json_encode($files);
    } catch (Exception $e) {
        sendResponse(false, "Error fetching employee files: " . $e->getMessage());
    } finally {
        // Close the connection (optional for PDO but good practice)
        $conn = null;
    }
} else {
    sendResponse(false, "Invalid request or database connection failed.");
}
?>
