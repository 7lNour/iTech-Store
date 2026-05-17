<?php

// Author: Taghreed Mashal Alhrabi

session_start();
include 'Database/db.php';
include 'Includes/header.php';

//  Handle the form submission 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //  Server-side username validation 
    if (!preg_match('/^[A-Za-z][A-Za-z0-9_ ]*$/', $username)) {

        $_SESSION['login_error'] =
        'Username must start with a letter.';

    }

    //  Server side password validation 
    else if (
        strlen($password) < 8 ||
        !preg_match('/[A-Za-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)
    ) {

        $_SESSION['login_error'] =
        'Password must contain letters, numbers, special characters and be at least 8 characters.';

    }

    else {

        //  Check if the username exists in the database 
        $stmt = $conn->prepare(
        "SELECT admin_id, username, password
         FROM admin
         WHERE username = ?");

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();

        //  If a result is found 
        if ($result->num_rows > 0) {

            $admin = $result->fetch_assoc();

            //  Validate password 
            if ($password === $admin['password']) {

                $_SESSION['is_admin'] = true;
                $_SESSION['username'] = $admin['username'];
                $_SESSION['admin_id'] = $admin['admin_id'];

                header("Location: admin_panel.php");
                exit();

            } else {

                $_SESSION['login_error'] =
                'Wrong admin password.';
            }

        } else {

            $_SESSION['login_error'] =
            'Admin account not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Admin Login - iTech Store</title>

    <link rel="stylesheet" href="Style/style.css">

</head>

<body class="auth-body">

<!-- Main Content for Admin Login -->
<main class="auth-container">

    <div class="form-card">

        <!-- Admin Login Form -->
        <h2 class="auth-title">
            Admin Login
        </h2>

        <p class="auth-subtitle">
            Only administrators can manage products.
        </p>

        <!-- Show server-side error -->
        <?php if (isset($_SESSION['login_error'])): ?>

            <div id="serverError"
                 class="error-msg"
                 class="error-msg error-msg--visible">

                <?php
                echo htmlspecialchars($_SESSION['login_error']);

                unset($_SESSION['login_error']);
                ?>

            </div>

        <?php endif; ?>

        <!-- Login Form -->
        <form action="login.php"
              method="POST"
              id="loginForm">

            <!-- Username -->
            <div class="input-group">

                <label>
                    Username
                </label>

                <input type="text"
                       name="username"
                       id="loginUsername"
                       required>

                <span class="error-msg"
                      id="usernameError"></span>

            </div>

            <!-- Password -->
            <div class="input-group">

                <label>
                    Password
                </label>

                <input type="password"
                       name="password"
                       id="loginPassword"
                       required>
                       
                <small class="password-note">
                Password must be at least 8 characters and include:
                letter, number, and special character.
                </small>

                <span class="error-msg"
                      id="passwordError"></span>

            </div>

            <!-- Submit -->
            <button type="submit"
                    class="btn-auth">

                Admin Login

            </button>

        </form>

    </div>

</main>

<script>

//  Validate the login form 
function validateLogin() {

    var valid = true;

    var username =
    document.getElementById('loginUsername').value.trim();

    var password =
    document.getElementById('loginPassword').value.trim();

    // Clear previous errors
    document.getElementById('usernameError').textContent = '';
    document.getElementById('passwordError').textContent = '';

    // Username validation
    if (username === '') {

        document.getElementById('usernameError').textContent =
        'Username is required.';

        valid = false;

    } else if (/^\d/.test(username)) {

        document.getElementById('usernameError').textContent =
        'Username must not start with a number.';

        valid = false;

    } else if (!/^[A-Za-z][A-Za-z0-9_ ]*$/.test(username)) {

        document.getElementById('usernameError').textContent =
        'Username must start with a letter.';

        valid = false;
    }

    // Password validation
    if (password === '') {

        document.getElementById('passwordError').textContent =
        'Password is required.';

        valid = false;

    } else if (password.length < 8) {

        document.getElementById('passwordError').textContent =
        'Password must be at least 8 characters.';

        valid = false;

    } else if (!/[A-Za-z]/.test(password)) {

        document.getElementById('passwordError').textContent =
        'Password must contain at least one letter.';

        valid = false;

    } else if (!/[0-9]/.test(password)) {

        document.getElementById('passwordError').textContent =
        'Password must contain at least one number.';

        valid = false;

    } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {

        document.getElementById('passwordError').textContent =
        'Password must contain at least one special character.';

        valid = false;
    }

    return valid;
}

// Attach submit event listener
document.getElementById('loginForm')
.addEventListener('submit', function(e) {

    if (!validateLogin()) {
        e.preventDefault();
    }

});

</script>

</body>
</html>