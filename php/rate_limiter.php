<?php
// rate_limiter.php - Prevent brute force attacks

class RateLimiter {
    private $max_requests = 5;
    private $time_window = 60; // seconds
    
    public function checkRateLimit($identifier) {
        $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
        
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            
            $current_time = time();
            $time_diff = $current_time - $data['start_time'];
            
            if ($time_diff < $this->time_window) {
                if ($data['count'] >= $this->max_requests) {
                    return false; // Rate limit exceeded
                }
                $data['count']++;
            } else {
                // Reset counter
                $data = ['start_time' => $current_time, 'count' => 1];
            }
        } else {
            $data = ['start_time' => time(), 'count' => 1];
        }
        
        file_put_contents($cache_file, json_encode($data));
        return true;
    }
}
?>
```

**Use in login.php:**

```php
<?php
include("rate_limiter.php");

$rate_limiter = new RateLimiter();
$ip_address = $_SERVER['REMOTE_ADDR'];

if (!$rate_limiter->checkRateLimit($ip_address)) {
    die("Too many requests. Please try again later.");
}
?>