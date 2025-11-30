<?php //seat_operations.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$require_login = true;
require_once 'secure_session.php';
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit();
}

try {
    $conn = getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $username = $_SESSION['username'];

    // Get booked seats for a specific movie/showtime
    if (isset($_GET['action']) && $_GET['action'] === 'get_booked') {
        $movieId = intval($_GET['movie_id'] ?? 0);
        $date = $_GET['date'] ?? '';
        $showtime = $_GET['showtime'] ?? '';
        
        if (empty($movieId) || empty($date) || empty($showtime)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit();
        }
        
        $sql = "SELECT seat_number FROM seat_bookings 
                WHERE movie_id = ? AND booking_date = ? AND showtime = ? AND status = 'booked'";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param("iss", $movieId, $date, $showtime);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookedSeats = [];
        while ($row = $result->fetch_assoc()) {
            $bookedSeats[] = $row['seat_number'];
        }
        
        echo json_encode(['success' => true, 'bookedSeats' => $bookedSeats]);
        exit();
    }

    // Get my bookings
    if (isset($_GET['action']) && $_GET['action'] === 'my_bookings') {
        $sql = "SELECT b.*, m.title as movie_title, m.image_path 
                FROM bookings b 
                LEFT JOIN movies m ON b.movie_id = m.id 
                WHERE b.user_id = ? 
                ORDER BY b.created_at DESC";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $bookings]);
        exit();
    }

    // Handle POST requests
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit();
    }
    
    $action = $data['action'] ?? '';

    // Book seats
    if ($action === 'book') {
        $movieId = intval($data['movie_id'] ?? 0);
        $movieTitle = trim($data['movie_title'] ?? '');
        $showtime = trim($data['showtime'] ?? '');
        $bookingDate = trim($data['booking_date'] ?? '');
        $seats = trim($data['seats'] ?? '');
        $totalAmount = floatval($data['total_amount'] ?? 0);
        
        // Validate input
        if (empty($movieId) || empty($movieTitle) || empty($showtime) || empty($bookingDate) || empty($seats)) {
            echo json_encode(['success' => false, 'message' => 'Missing required booking information']);
            exit();
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Check if seats are still available
            $seatArray = array_map('trim', explode(',', $seats));
            $placeholders = str_repeat('?,', count($seatArray) - 1) . '?';
            
            $checkSql = "SELECT seat_number FROM seat_bookings 
                        WHERE movie_id = ? AND showtime = ? AND booking_date = ? 
                        AND seat_number IN ($placeholders) AND status = 'booked'";
            
            $checkStmt = $conn->prepare($checkSql);
            
            $types = 'iss' . str_repeat('s', count($seatArray));
            $params = array_merge([$movieId, $showtime, $bookingDate], $seatArray);
            $checkStmt->bind_param($types, ...$params);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Some seats are already booked. Please select different seats.']);
                exit();
            }
            
            // Insert booking record
            $sql = "INSERT INTO bookings (user_id, movie_id, movie_title, showtime, booking_date, seats, total_amount, booking_status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed', NOW())";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Failed to prepare booking statement: ' . $conn->error);
            }
            
            $stmt->bind_param("sissssd", $username, $movieId, $movieTitle, $showtime, $bookingDate, $seats, $totalAmount);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to create booking: ' . $stmt->error);
            }
            
            $bookingId = $conn->insert_id;
            
            // Insert seat bookings
            foreach ($seatArray as $seat) {
                $seat = trim($seat);
                if (empty($seat)) continue;
                
                $seatSql = "INSERT INTO seat_bookings (booking_id, movie_id, showtime, booking_date, seat_number, status, booked_by, created_at) 
                            VALUES (?, ?, ?, ?, ?, 'booked', ?, NOW())
                            ON DUPLICATE KEY UPDATE status = 'booked', booked_by = ?, booking_id = ?";
                $seatStmt = $conn->prepare($seatSql);
                
                if (!$seatStmt) {
                    throw new Exception('Failed to prepare seat booking: ' . $conn->error);
                }
                
                $seatStmt->bind_param("iissssi", $bookingId, $movieId, $showtime, $bookingDate, $seat, $username, $username, $bookingId);
                
                if (!$seatStmt->execute()) {
                    throw new Exception('Failed to book seat ' . $seat . ': ' . $seatStmt->error);
                }
            }
            
            // Commit transaction
            $conn->commit();
            echo json_encode([
                'success' => true, 
                'message' => 'Booking confirmed successfully!',
                'booking_id' => $bookingId
            ]);
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log('Booking error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()]);
        }
        
        exit();
    }

    // Cancel booking
    if ($action === 'cancel') {
        $bookingId = intval($data['booking_id'] ?? 0);
        
        if (empty($bookingId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            exit();
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Get booking details
            $sql = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Failed to prepare query: ' . $conn->error);
            }
            
            $stmt->bind_param("is", $bookingId, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();
            
            if (!$booking) {
                throw new Exception('Booking not found or you do not have permission to cancel it');
            }
            
            // Check if booking is already cancelled
            if ($booking['booking_status'] === 'cancelled') {
                throw new Exception('This booking is already cancelled');
            }
            
            // Update booking status
            $updateSql = "UPDATE bookings SET booking_status = 'cancelled' WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            
            if (!$updateStmt) {
                throw new Exception('Failed to prepare update: ' . $conn->error);
            }
            
            $updateStmt->bind_param("i", $bookingId);
            
            if (!$updateStmt->execute()) {
                throw new Exception('Failed to update booking: ' . $updateStmt->error);
            }
            
            // Delete seat bookings (CASCADE will handle this, but we'll do it explicitly for clarity)
            $deleteSql = "DELETE FROM seat_bookings WHERE booking_id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            
            if (!$deleteStmt) {
                throw new Exception('Failed to prepare seat deletion: ' . $conn->error);
            }
            
            $deleteStmt->bind_param("i", $bookingId);
            
            if (!$deleteStmt->execute()) {
                throw new Exception('Failed to free seats: ' . $deleteStmt->error);
            }
            
            // Commit transaction
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log('Cancellation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        
        exit();
    }

    // Invalid action
    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    error_log('Seat operations error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>