<?php
// error_handler.php - Custom error handling for production

// Log errors instead of displaying them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Set error reporting level
error_reporting(E_ALL);

// Custom error handler function
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = date('[Y-m-d H:i:s]') . " [ERROR] ";
    $error_message .= "Type: $errno | Message: $errstr | ";
    $error_message .= "File: $errfile | Line: $errline\n";
    
    // Log to file
    error_log($error_message, 3, __DIR__ . '/../logs/php_errors.log');
    
    // Don't execute PHP internal error handler
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    $error_message = date('[Y-m-d H:i:s]') . " [EXCEPTION] ";
    $error_message .= "Message: " . $exception->getMessage() . " | ";
    $error_message .= "File: " . $exception->getFile() . " | ";
    $error_message .= "Line: " . $exception->getLine() . "\n";
    
    // Log to file
    error_log($error_message, 3, __DIR__ . '/../logs/php_errors.log');
    
    // Display generic error to user
    if (!headers_sent()) {
        http_response_code(500);
    }
    
    // Show user-friendly error page
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Service Unavailable</title>
        <style>
            body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
            .error-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
            h1 { color: #e74c3c; }
            p { color: #666; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>⚠️ Service Temporarily Unavailable</h1>
            <p>We\'re experiencing technical difficulties. Please try again later.</p>
            <p><small>Error Reference: ' . uniqid() . '</small></p>
        </div>
    </body>
    </html>';
    
    exit;
}

// Register handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');
?>