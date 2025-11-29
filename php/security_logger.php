<?php
// security_logger.php

class SecurityLogger {
    private $log_file;
    
    public function __construct() {
        $this->log_file = __DIR__ . '/logs/security.log';
        
        // Create logs directory if it doesn't exist
        if (!is_dir(__DIR__ . '/logs')) {
            mkdir(__DIR__ . '/logs', 0755, true);
        }
    }
    
    public function logLoginAttempt($username, $success) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $status = $success ? 'SUCCESS' : 'FAILED';
        $timestamp = date('[Y-m-d H:i:s]');
        
        $message = "$timestamp [LOGIN_ATTEMPT] [User: $username] [IP: $ip] Login attempt $status for username: $username\n";
        
        error_log($message, 3, $this->log_file);
    }
    
    public function logSuspiciousActivity($description, $username = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $timestamp = date('[Y-m-d H:i:s]');
        $user_info = $username ? "[User: $username]" : "[User: Anonymous]";
        
        $message = "$timestamp [SUSPICIOUS] $user_info [IP: $ip] $description\n";
        
        error_log($message, 3, $this->log_file);
    }
    
    // ADD THIS METHOD
    public function logRegistration($username, $email) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $timestamp = date('[Y-m-d H:i:s]');
        
        $message = "$timestamp [REGISTRATION] [User: $username] [Email: $email] [IP: $ip] New user registered successfully\n";
        
        error_log($message, 3, $this->log_file);
    }
     // ADD THIS METHOD ← YOU NEED THIS!
     public function logLogout($username) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $timestamp = date('[Y-m-d H:i:s]');
        
        $message = "$timestamp [LOGOUT] [User: $username] [IP: $ip] User logged out\n";
        
        error_log($message, 3, $this->log_file);
    }
}
?>