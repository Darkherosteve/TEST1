<?php
session_start(); // Start the session

// Array of valid username-password pairs
$valid_credentials = [
    ['username' => 'admin', 'password' => 'password1234'],
    ['username' => 'steve', 'password' => 'steveson10'],
    ['username' => 'sajimon', 'password' => 'starasteve']
];

// Set the session timeout limit (in seconds)
$timeout_duration = 86400; // 24 hours (86400 seconds)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Initialize a flag for successful login
    $login_successful = false;

    // Loop through the array to check for valid credentials
    foreach ($valid_credentials as $credentials) {
        if ($credentials['username'] == $username && $credentials['password'] == $password) {
            $login_successful = true;
            break;
        }
    }

    // Check login status
    if ($login_successful) {
        // Set session variable on successful login
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time(); // Record the current time as last activity
        header("Location: index1.php"); // Redirect to aquatrans.php (no .php visible)
        exit; // Ensures no further code is executed
    } else {
        // Redirect back to the login page with an error message
        header("Location: " . $_SERVER['PHP_SELF'] . "?login_failed=true");
        exit;
    }
}

// If login failed, show an alert
if (isset($_GET['login_failed']) && $_GET['login_failed'] == 'true') {
    echo '
    <script type="text/javascript">
        alert("Invalid username or password. Please try again.");
        window.location.href = "login.html";
    </script>';
}
?>