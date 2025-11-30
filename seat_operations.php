<?php //seat_operations.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$require_login = true;
require_once 'secure_session.php';
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $conn = getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $username = $_SESSION['username'];

    // Get booked seats
    if (isset($_GET['action']) && $_GET['action'] === 'get_booked') {
        $movieId = intval($_GET['movie_id'] ?? 0);
        $date = $_GET['date'] ?? '';
        $showtime = $_GET['showtime'] ?? '';
        
        if (empty($movieId) || empty($date) || empty($showtime)) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit();
        }
        
        $sql = "SELECT seat_number FROM seat_bookings 
                WHERE movie_id = :movie_id AND booking_date = :date AND showtime = :showtime AND status = 'booked'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'movie_id' => $movieId,
            'date' => $date,
            'showtime' => $showtime
        ]);
        
        $bookedSeats = [];
        while ($row = $stmt->fetch()) {
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
                WHERE b.user_id = :username 
                ORDER BY b.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['username' => $username]);
        
        $bookings = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $bookings]);
        exit();
    }

    // Handle POST requests
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
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
        
        if (empty($movieId) || empty($movieTitle) || empty($showtime) || empty($bookingDate) || empty($seats)) {
            echo json_encode(['success' => false, 'message' => 'Missing booking information']);
            exit();
        }
        
        $conn->beginTransaction();
        
        try {
            // Check if seats are available
            $seatArray = array_map('trim', explode(',', $seats));
            $placeholders = str_repeat('?,', count($seatArray) - 1) . '?';
            
            $checkSql = "SELECT seat_number FROM seat_bookings 
                        WHERE movie_id = ? AND showtime = ? AND booking_date = ? 
                        AND seat_number IN ($placeholders) AND status = 'booked'";
            
            $checkStmt = $conn->prepare($checkSql);
            $checkParams = array_merge([$movieId, $showtime, $bookingDate], $seatArray);
            $checkStmt->execute($checkParams);
            
            if ($checkStmt->rowCount() > 0) {
                $conn->rollBack();
                echo json_encode(['success' => false, 'message' => 'Some seats are already booked']);
                exit();
            }
            
            // Insert booking
            $sql = "INSERT INTO bookings (user_id, movie_id, movie_title, showtime, booking_date, seats, total_amount, booking_status, created_at) 
                    VALUES (:user_id, :movie_id, :movie_title, :showtime, :booking_date, :seats, :total_amount, 'confirmed', NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'user_id' => $username,
                'movie_id' => $movieId,
                'movie_title' => $movieTitle,
                'showtime' => $showtime,
                'booking_date' => $bookingDate,
                'seats' => $seats,
                'total_amount' => $totalAmount
            ]);
            
            $bookingId = $conn->lastInsertId();
            
            // Insert seat bookings
            foreach ($seatArray as $seat) {
                $seat = trim($seat);
                if (empty($seat)) continue;
                
                $seatSql = "INSERT INTO seat_bookings (booking_id, movie_id, showtime, booking_date, seat_number, status, booked_by, created_at) 
                            VALUES (:booking_id, :movie_id, :showtime, :booking_date, :seat_number, 'booked', :booked_by, NOW())";
                $seatStmt = $conn->prepare($seatSql);
                $seatStmt->execute([
                    'booking_id' => $bookingId,
                    'movie_id' => $movieId,
                    'showtime' => $showtime,
                    'booking_date' => $bookingDate,
                    'seat_number' => $seat,
                    'booked_by' => $username
                ]);
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Booking confirmed!', 'booking_id' => $bookingId]);
            
        } catch (Exception $e) {
            $conn->rollBack();
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
        
        $conn->beginTransaction();
        
        try {
            // Get booking details
            $sql = "SELECT * FROM bookings WHERE id = :id AND user_id = :username";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $bookingId, 'username' => $username]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                throw new Exception('Booking not found');
            }
            
            if ($booking['booking_status'] === 'cancelled') {
                throw new Exception('Booking already cancelled');
            }
            
            // Update booking status
            $updateSql = "UPDATE bookings SET booking_status = 'cancelled' WHERE id = :id";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute(['id' => $bookingId]);
            
            // Delete seat bookings
            $deleteSql = "DELETE FROM seat_bookings WHERE booking_id = :booking_id";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->execute(['booking_id' => $bookingId]);
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
            
        } catch (Exception $e) {
            $conn->rollBack();
            error_log('Cancellation error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    error_log('Seat operations error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>