<?php //movie_operations.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$require_login = true;
require_once 'secure_session.php';
require_once 'config.php';

header('Content-Type: application/json');

try {
    $conn = getConnection();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // List all movies
    if ($action === 'list') {
        $sql = "SELECT * FROM movies ORDER BY created_at DESC";
        $result = $conn->query($sql);
        $movies = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }
        }
        
        echo json_encode($movies);
        exit();
    }

    // Get single movie
    if ($action === 'get') {
        $id = $_GET['id'];
        $sql = "SELECT * FROM movies WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();
        
        echo json_encode($movie);
        exit();
    }

    // Add new movie
    if ($action === 'add') {
        $title = $_POST['title'] ?? '';
        $duration = $_POST['duration'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $rating = $_POST['rating'] ?? 0;
        $status = $_POST['status'] ?? 'showing';
        
        if (empty($title) || empty($duration) || empty($genre)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit();
        }
        
        $image_path = 'images/default-movie.jpg';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'images/';
            
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create images directory']);
                    exit();
                }
            }
            
            if (!is_writable($upload_dir)) {
                echo json_encode(['success' => false, 'message' => 'Images directory is not writable']);
                exit();
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }
            
            $new_filename = 'movie_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit();
            }
        }
        
        $sql = "INSERT INTO movies (title, duration, genre, rating, image_path, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
        }
        
        $stmt->bind_param("sssdss", $title, $duration, $genre, $rating, $image_path, $status);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Movie added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add movie: ' . $stmt->error]);
        }
        exit();
    }

    // Update movie
    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $duration = $_POST['duration'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $rating = $_POST['rating'] ?? 0;
        $status = $_POST['status'] ?? 'showing';
        
        if (empty($title) || empty($duration) || empty($genre) || empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit();
        }
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'images/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }
            
            $new_filename = 'movie_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $old_sql = "SELECT image_path FROM movies WHERE id = ?";
                $old_stmt = $conn->prepare($old_sql);
                $old_stmt->bind_param("i", $id);
                $old_stmt->execute();
                $old_result = $old_stmt->get_result();
                $old_movie = $old_result->fetch_assoc();
                
                if ($old_movie && file_exists($old_movie['image_path']) && $old_movie['image_path'] !== 'images/default-movie.jpg') {
                    @unlink($old_movie['image_path']);
                }
                
                $sql = "UPDATE movies SET title = ?, duration = ?, genre = ?, rating = ?, image_path = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssdssi", $title, $duration, $genre, $rating, $upload_path, $status, $id);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit();
            }
        } else {
            $sql = "UPDATE movies SET title = ?, duration = ?, genre = ?, rating = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdsi", $title, $duration, $genre, $rating, $status, $id);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Movie updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update movie: ' . $stmt->error]);
        }
        exit();
    }

    // Toggle movie status
    if ($action === 'toggle') {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 'showing';
        
        $sql = "UPDATE movies SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            $message = $status === 'showing' ? 'Movie is now showing' : 'Movie is now hidden';
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . $stmt->error]);
        }
        exit();
    }

    // Delete movie
    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
            exit();
        }
        
        $sql = "SELECT image_path FROM movies WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();
        
        $delete_sql = "DELETE FROM movies WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            if ($movie && file_exists($movie['image_path']) && $movie['image_path'] !== 'images/default-movie.jpg') {
                @unlink($movie['image_path']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Movie deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete movie: ' . $delete_stmt->error]);
        }
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

if (isset($conn)) {
    $conn->close();
}
?>