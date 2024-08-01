<?php
include 'connection.php';

$recordCreated = false;
$isEmailExist = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari formulir
    $inputUserName = $_POST['user_name'];
    $inputUserRole = $_POST['user_role'];
    $inputUserEmail = $_POST['user_email'];


    // Validasi email
    if (!filter_var($inputUserEmail, FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid email format.";
        exit();
    }

    try {
        // Memeriksa apakah email sudah ada di database
        $sqlCheckEmail = "SELECT COUNT(*) FROM user WHERE user_email = ?";
        $stmtCheckEmail = $conn->prepare($sqlCheckEmail);
        $stmtCheckEmail->bind_param("s", $inputUserEmail);
        $stmtCheckEmail->execute();
        $stmtCheckEmail->bind_result($emailCount);
        $stmtCheckEmail->fetch();
        $stmtCheckEmail->close();

        if ($emailCount > 0) {
            $isEmailExist = true;
        } else {
            // Menyisipkan data jika email belum ada
            $sqlInsertUser = "INSERT INTO user (user_id, user_name, user_role, user_email) VALUES (UUID(), ?, ?, ?)";
            $stmtInsertUser = $conn->prepare($sqlInsertUser);
            $stmtInsertUser->bind_param("sss", $inputUserName, $inputUserRole, $inputUserEmail);

            if ($stmtInsertUser->execute()) {
                $recordCreated = true;

                // redirect to home page
                header("Location: index.php");
                exit();
            } else {
                echo "Error: " . $stmtInsertUser->error;
            }

            $stmtInsertUser->close();
        }
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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

        .error {
            color: red;
            margin-bottom: 1rem;
        }

        .success {
            color: green;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Add User</h2>
        <?php
        if ($isEmailExist) {
            echo '<div class="error">Error: Email already exists.</div>';
        } else if ($recordCreated) {
            echo '<div class="success">New record created successfully</div>';
        }
        ?>
        <form id="userForm" method="post">
            <div class="input_form">
                <label for="user_name">User Name:</label>
                <input type="text" id="user_name" name="user_name" required>
            </div>

            <div class="input_form">
                <label for="user_role">
                    User Role:
                </label>
                <select name="user_role" id="user_role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>

            <div class="input_form">
                <label for="user_email">
                    User Email:
                </label>
                <input type="email" id="user_email" name="user_email" required>
            </div>

            <button type="submit">add user</button>
        </form>
    </div>
</body>

</html>