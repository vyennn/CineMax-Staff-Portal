<?php //movie_operations.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$require_login = true;
require_once 'secure_session.php';
require_once 'config.php';

header('Content-Type: application/json');

try {
    $conn = getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // List all movies
    if ($action === 'list') {
        $sql = "SELECT * FROM movies ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        if (!$result) {
            throw new Exception('Query failed: ' . $conn->error);
        }
        
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $movies]);
        exit();
    }

    // Get single movie
    if ($action === 'get') {
        $id = intval($_GET['id'] ?? 0);
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Movie ID required']);
            exit();
        }
        
        $sql = "SELECT * FROM movies WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();
        
        if ($movie) {
            echo json_encode(['success' => true, 'data' => $movie]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Movie not found']);
        }
        exit();
    }

    // Add new movie
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $rating = floatval($_POST['rating'] ?? 0);
        $status = $_POST['status'] ?? 'showing';
        
        if (empty($title) || empty($duration) || empty($genre)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit();
        }
        
        // Default image path
        $image_path = 'images/default-movie.jpg';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/images/';
            
            // Create images directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create images directory']);
                    exit();
                }
            }
            
            // Check if directory is writable
            if (!is_writable($upload_dir)) {
                echo json_encode(['success' => false, 'message' => 'Images directory is not writable. Check permissions.']);
                exit();
            }
            
            // Validate file type
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp']);
                exit();
            }
            
            // Validate file size (max 5MB)
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
                exit();
            }
            
            // Generate unique filename
            $new_filename = 'movie_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'images/' . $new_filename; // Store relative path
            } else {
                $error_code = $_FILES['image']['error'];
                echo json_encode(['success' => false, 'message' => 'Failed to upload image. Error code: ' . $error_code]);
                exit();
            }
        }
        
        // Insert into database
        $sql = "INSERT INTO movies (title, duration, genre, rating, image_path, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
        }
        
        $stmt->bind_param("sssdss", $title, $duration, $genre, $rating, $image_path, $status);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Movie added successfully!', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add movie: ' . $stmt->error]);
        }
        exit();
    }

    // Update movie
    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $rating = floatval($_POST['rating'] ?? 0);
        $status = $_POST['status'] ?? 'showing';
        
        if (empty($title) || empty($duration) || empty($genre) || empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            exit();
        }
        
        // Check if new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/images/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }
            
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
                exit();
            }
            
            $new_filename = 'movie_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Get old image path to delete it
                $old_sql = "SELECT image_path FROM movies WHERE id = ?";
                $old_stmt = $conn->prepare($old_sql);
                $old_stmt->bind_param("i", $id);
                $old_stmt->execute();
                $old_result = $old_stmt->get_result();
                $old_movie = $old_result->fetch_assoc();
                
                // Delete old image if it exists and is not the default
                if ($old_movie && !empty($old_movie['image_path']) && $old_movie['image_path'] !== 'images/default-movie.jpg') {
                    $old_image_full_path = __DIR__ . '/' . $old_movie['image_path'];
                    if (file_exists($old_image_full_path)) {
                        @unlink($old_image_full_path);
                    }
                }
                
                $image_path = 'images/' . $new_filename;
                
                // Update with new image
                $sql = "UPDATE movies SET title = ?, duration = ?, genre = ?, rating = ?, image_path = ?, status = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssdssi", $title, $duration, $genre, $rating, $image_path, $status, $id);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit();
            }
        } else {
            // Update without changing image
            $sql = "UPDATE movies SET title = ?, duration = ?, genre = ?, rating = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdsi", $title, $duration, $genre, $rating, $status, $id);
        }
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
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
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'showing';
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Movie ID required']);
            exit();
        }
        
        $sql = "UPDATE movies SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
        }
        
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
        $id = intval($_POST['id'] ?? 0);
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
            exit();
        }
        
        // Get movie details first
        $sql = "SELECT image_path FROM movies WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();
        
        // Delete from database
        $delete_sql = "DELETE FROM movies WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // Delete image file if it exists and is not the default
            if ($movie && !empty($movie['image_path']) && $movie['image_path'] !== 'images/default-movie.jpg') {
                $image_full_path = __DIR__ . '/' . $movie['image_path'];
                if (file_exists($image_full_path)) {
                    @unlink($image_full_path);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Movie deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete movie: ' . $delete_stmt->error]);
        }
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    error_log('Movie operations error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

if (isset($conn)) {
    $conn->close();
}
?>