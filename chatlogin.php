<?php
// login.php
// Array of valid username-password pairs
$valid_credentials = [
    ['username' => 'admin', 'password' => 'password123'],
    ['username' => 'steve', 'password' => 'steveonly'],
    ['username' => 'pankaj', 'password' => 'pankajonly']
];

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
        // Redirect to the target page
        header("Location: index.php");
        exit; // Ensures no further code is executed
    } else {
        // Redirect back to the same page with an error message
        header("Location: " . $_SERVER['PHP_SELF'] . "?login_failed=true");
        exit;
    }
}
?>

<?php
// If login failed, show an alert
if (isset($_GET['login_failed']) && $_GET['login_failed'] == 'true') {
    
    echo '
    <script
     type="text/javascript">alert("Invalid username or password. Please try again.");
     window.location.href = "login.html";
     </script>';
    }
?>

