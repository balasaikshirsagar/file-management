<?php
include "./utils/functions.php"; // Ensure this file is included for database connection

// Initialize the database connection
$conn = connect(); // Use the connect() function from functions.php

if ($conn) {
    try {
        // Fetch employees data
        $sql = "SELECT * FROM employees";
        $stmt = $conn->query($sql);

        $employees = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $employees[] = $row;
        }

        // Send employees data as JSON response
        echo json_encode($employees);
    } catch (Exception $e) {
        sendResponse(false, "Error fetching employees: " . $e->getMessage());
    } finally {
        // Close the connection (optional with PDO, but added for clarity)
        $conn = null;
    }
} else {
    sendResponse(false, "Database connection failed.");
}
?>
