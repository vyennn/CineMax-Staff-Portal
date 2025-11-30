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
        $stmt = $conn->query($sql);
        $movies = $stmt->fetchAll();
        
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
        
        $sql = "SELECT * FROM movies WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $movie = $stmt->fetch();
        
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
            
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create images directory']);
                    exit();
                }
            }
            
            if (!is_writable($upload_dir)) {
                echo json_encode(['success' => false, 'message' => 'Images directory is not writable']);
                exit();
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($file_extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }
            
            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File too large. Maximum 5MB']);
                exit();
            }
            
            $new_filename = 'movie_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'images/' . $new_filename;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit();
            }
        }
        
        // Insert into database
        $sql = "INSERT INTO movies (title, duration, genre, rating, image_path, status, created_at) 
                VALUES (:title, :duration, :genre, :rating, :image_path, :status, NOW())";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([
            'title' => $title,
            'duration' => $duration,
            'genre' => $genre,
            'rating' => $rating,
            'image_path' => $image_path,
            'status' => $status
        ])) {
            echo json_encode(['success' => true, 'message' => 'Movie added successfully!', 'id' => $conn->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add movie']);
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
                echo json_encode(['success' => false, 'message' => 'File too large']);
                exit();
            }
            
            $new_filename = 'movie_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Get old image
                $old_sql = "SELECT image_path FROM movies WHERE id = :id";
                $old_stmt = $conn->prepare($old_sql);
                $old_stmt->execute(['id' => $id]);
                $old_movie = $old_stmt->fetch();
                
                // Delete old image
                if ($old_movie && !empty($old_movie['image_path']) && $old_movie['image_path'] !== 'images/default-movie.jpg') {
                    $old_image_full_path = __DIR__ . '/' . $old_movie['image_path'];
                    if (file_exists($old_image_full_path)) {
                        @unlink($old_image_full_path);
                    }
                }
                
                $image_path = 'images/' . $new_filename;
                
                // Update with new image
                $sql = "UPDATE movies SET title = :title, duration = :duration, genre = :genre, 
                        rating = :rating, image_path = :image_path, status = :status, updated_at = NOW() 
                        WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([
                    'title' => $title,
                    'duration' => $duration,
                    'genre' => $genre,
                    'rating' => $rating,
                    'image_path' => $image_path,
                    'status' => $status,
                    'id' => $id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                exit();
            }
        } else {
            // Update without changing image
            $sql = "UPDATE movies SET title = :title, duration = :duration, genre = :genre, 
                    rating = :rating, status = :status, updated_at = NOW() WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                'title' => $title,
                'duration' => $duration,
                'genre' => $genre,
                'rating' => $rating,
                'status' => $status,
                'id' => $id
            ]);
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Movie updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update movie']);
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
        
        $sql = "UPDATE movies SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute(['status' => $status, 'id' => $id])) {
            $message = $status === 'showing' ? 'Movie is now showing' : 'Movie is now hidden';
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
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
        
        // Get movie details
        $sql = "SELECT image_path FROM movies WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $movie = $stmt->fetch();
        
        // Delete from database
        $delete_sql = "DELETE FROM movies WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_sql);
        
        if ($delete_stmt->execute(['id' => $id])) {
            // Delete image file
            if ($movie && !empty($movie['image_path']) && $movie['image_path'] !== 'images/default-movie.jpg') {
                $image_full_path = __DIR__ . '/' . $movie['image_path'];
                if (file_exists($image_full_path)) {
                    @unlink($image_full_path);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Movie deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete movie']);
        }
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    error_log('Movie operations error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>