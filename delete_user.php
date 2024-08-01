<?php
include 'connection.php';

$user_id = $_GET['id'] ?? '';

if ($user_id) {
    try {
        // Prepare and execute the delete query
        $sqlDeleteUser = "DELETE FROM user WHERE user_id = ?";
        $stmtDeleteUser = $conn->prepare($sqlDeleteUser);
        $stmtDeleteUser->bind_param("s", $user_id);

        if ($stmtDeleteUser->execute()) {
            // Redirect to index.php after successful deletion
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmtDeleteUser->error;
        }

        $stmtDeleteUser->close();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $conn->close();
    }
} else {
    echo "Error: No user ID provided.";
}
