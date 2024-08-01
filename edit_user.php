<?php
include 'connection.php';

$user_id = $_GET['id'] ?? '';
$userName = '';
$userRole = '';
$userEmail = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUserName = $_POST['user_name'];
    $inputUserRole = $_POST['user_role'];
    $inputUserEmail = $_POST['user_email'];

    // Validate email
    if (!filter_var($inputUserEmail, FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid email format.";
        exit();
    }

    try {
        // Update user data
        $sqlUpdateUser = "UPDATE user SET user_name = ?, user_role = ?, user_email = ? WHERE user_id = ?";
        $stmtUpdateUser = $conn->prepare($sqlUpdateUser);
        $stmtUpdateUser->bind_param("ssss", $inputUserName, $inputUserRole, $inputUserEmail, $user_id);

        if ($stmtUpdateUser->execute()) {
            header("Location: index.php"); // Redirect after successful update
            exit();
        } else {
            echo "Error: " . $stmtUpdateUser->error;
        }

        $stmtUpdateUser->close();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $conn->close();
    }
} else {
    // Fetch user data for the edit form
    if ($user_id) {
        $sql = "SELECT user_name, user_role, user_email FROM user WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->bind_result($userName, $userRole, $userEmail);
        $stmt->fetch();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            min-width: 400px;
            padding: 1rem;
        }

        .form-container h2 {
            margin-bottom: 1rem;
            color: #333;
        }

        .form-container .input_form {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .form-container .input_form input[type="text"],
        .form-container .input_form input[type="email"],
        .form-container .input_form select {
            padding: 0.5rem;
            margin: 0.5rem 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 0.5rem;
            width: 100%;
            border-radius: 4px;
            text-transform: capitalize;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Edit User</h2>
        <form method="post">
            <div class="input_form">
                <label for="user_name">User Name:</label>
                <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($userName); ?>" required>
            </div>

            <div class="input_form">
                <label for="user_role">User Role:</label>
                <select name="user_role" id="user_role" required>
                    <option value="user" <?php echo $userRole == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $userRole == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="super_admin" <?php echo $userRole == 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                </select>
            </div>

            <div class="input_form">
                <label for="user_email">User Email:</label>
                <input type="email" id="user_email" name="user_email" value="<?php echo htmlspecialchars($userEmail); ?>" required>
            </div>

            <button type="submit">Update User</button>
        </form>
    </div>
</body>

</html>