<?php

include "./utils/functions.php"; // Ensure this file provides the `connect()` function

// Initialize the database connection
$conn = connect(); // Use the connect() function from functions.php

if ($conn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Delete files associated with the employee
        $fileSql = "SELECT filename FROM employee_files WHERE employee_id = ?";
        $fileStmt = $conn->prepare($fileSql);
        $fileStmt->bindParam(1, $id, PDO::PARAM_INT);
        $fileStmt->execute();

        while ($fileRow = $fileStmt->fetch(PDO::FETCH_ASSOC)) {
            $filePath = '../uploads/' . $fileRow['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete records from `employee_files` table
        $deleteFilesSql = "DELETE FROM employee_files WHERE employee_id = ?";
        $deleteFilesStmt = $conn->prepare($deleteFilesSql);
        $deleteFilesStmt->bindParam(1, $id, PDO::PARAM_INT);
        $deleteFilesStmt->execute();

        // Delete the employee record
        $sql = "DELETE FROM employees WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Commit transaction
            $conn->commit();
            echo "Employee deleted successfully.";
        } else {
            throw new Exception("Failed to delete employee.");
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    } finally {
        // Close statements
        if (isset($fileStmt)) $fileStmt = null;
        if (isset($deleteFilesStmt)) $deleteFilesStmt = null;
        if (isset($stmt)) $stmt = null;

        // Close the connection
        $conn = null;
    }
} else {
    echo "Invalid request or database connection failed.";
}
?>
