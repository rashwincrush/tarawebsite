<?php
// Optimize error reporting for production
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

// Use output buffering for better performance
ob_start();

// Include configuration
require_once 'config.php';

// Define constants if not already defined in config.php
defined('SITE_URL') || define('SITE_URL', 'https://tarasdental.in');
defined('SESSION_TIMEOUT') || define('SESSION_TIMEOUT', 3600); // 1 hour

// Clear any existing session data on the login page
if (!isset($_POST['username'])) {
    session_start();
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 5) {
        if (time() - $_SESSION['last_attempt'] < 300) { // 5 minutes lockout
            $error = 'Too many login attempts. Please try again later.';
            error_log("Login blocked due to too many attempts from IP: " . $_SERVER['REMOTE_ADDR']);
            http_response_code(429);
            goto output;
        } else {
            $_SESSION['login_attempts'] = 0;
        }
    }

    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username === ADMIN_USERNAME && verifyPassword($password)) {
        session_start();
        // Set session variables with enhanced security
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['last_activity'] = time();
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Log successful login
        error_log("Admin login successful for user: " . $username . " from IP: " . $_SERVER['REMOTE_ADDR']);
        
        header('Location: dashboard.php');
        exit;
    } else {
        // Increment failed login attempts
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_attempt'] = time();
        
        $error = 'Invalid username or password';
        error_log("Failed login attempt for username: " . $username . " from IP: " . $_SERVER['REMOTE_ADDR']);
        
        // Add delay to prevent brute force attempts
        usleep(random_int(500000, 1500000)); // Random delay between 0.5 and 1.5 seconds
    }
}

// Check if already logged in
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

output:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - Taras Dental</title>
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" as="style">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Taras Dental Blog Admin</h1>
            <p class="text-gray-600">Please sign in to access the dashboard</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6" autocomplete="off" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       autocomplete="off"
                       pattern="[a-zA-Z0-9]+"
                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       autocomplete="off"
                       minlength="8"
                       class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Contact administrator for access</p>
        </div>
    </div>

    <script>
    // Use strict mode for better error catching
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Disable form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Clear password field on page load
        document.getElementById('password').value = '';
        
        // Add form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
            
            // Disable submit button to prevent double submission
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Signing in...';
        });
    });
    </script>
</body>
</html>
<?php
// Flush output buffer
ob_end_flush();
?>