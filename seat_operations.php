<?php //seat_operations.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getConnection();
$username = $_SESSION['username'];

// Get booked seats
if (isset($_GET['action']) && $_GET['action'] === 'get_booked') {
    $movieId = $_GET['movie_id'];
    $date = $_GET['date'];
    $showtime = $_GET['showtime'];
    
    $sql = "SELECT seat_number FROM seat_bookings 
            WHERE movie_id = ? AND booking_date = ? AND showtime = ? AND status = 'booked'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $movieId, $date, $showtime);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookedSeats = [];
    while ($row = $result->fetch_assoc()) {
        $bookedSeats[] = $row['seat_number'];
    }
    
    echo json_encode(['bookedSeats' => $bookedSeats]);
    exit();
}

// Get my bookings
if (isset($_GET['action']) && $_GET['action'] === 'my_bookings') {
    $sql = "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    echo json_encode($bookings);
    exit();
}

// Handle POST requests
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

// Book seats
if ($action === 'book') {
    $movieId = $data['movie_id'];
    $movieTitle = $data['movie_title'];
    $showtime = $data['showtime'];
    $bookingDate = $data['booking_date'];
    $seats = $data['seats'];
    $totalAmount = $data['total_amount'];
    
    $conn->begin_transaction();
    
    try {
        $sql = "INSERT INTO bookings (user_id, movie_id, movie_title, showtime, booking_date, seats, total_amount, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissssd", $username, $movieId, $movieTitle, $showtime, $bookingDate, $seats, $totalAmount);
        $stmt->execute();
        
        $seatArray = explode(',', $seats);
        foreach ($seatArray as $seat) {
            $seat = trim($seat);
            $sql = "INSERT INTO seat_bookings (movie_id, showtime, booking_date, seat_number, status, booked_by) 
                    VALUES (?, ?, ?, ?, 'booked', ?)
                    ON DUPLICATE KEY UPDATE status = 'booked', booked_by = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssss", $movieId, $showtime, $bookingDate, $seat, $username, $username);
            $stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Booking confirmed!']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()]);
    }
    
    exit();
}

// Cancel booking
if ($action === 'cancel') {
    $bookingId = $data['booking_id'];
    
    $conn->begin_transaction();
    
    try {
        $sql = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $bookingId, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();
        
        if (!$booking) {
            throw new Exception('Booking not found');
        }
        
        $sql = "UPDATE bookings SET booking_status = 'cancelled' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        $seatArray = explode(',', $booking['seats']);
        foreach ($seatArray as $seat) {
            $seat = trim($seat);
            $sql = "DELETE FROM seat_bookings 
                    WHERE movie_id = ? AND showtime = ? AND booking_date = ? AND seat_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $booking['movie_id'], $booking['showtime'], $booking['booking_date'], $seat);
            $stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Cancellation failed: ' . $e->getMessage()]);
    }
    
    exit();
}

$conn->close();
?>