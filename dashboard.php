<?php //dashboard.php
$require_login = true;
require_once 'secure_session.php';
require_once 'db.php';

$username = $_SESSION['username'];

$conn = getConnection();
$sql = "SELECT * FROM movies WHERE status = 'showing' ORDER BY created_at DESC";
$result = $conn->query($sql);
$movies = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CineMax</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Header */
        .header {
            background: rgba(0, 0, 0, 0.95);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(234, 88, 12, 0.3);
            border-bottom: 2px solid #ea580c;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .menu-toggle {
            display: none;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span {
            color: #ea580c;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome-text {
            font-size: 1.1rem;
        }

        .welcome-text .username {
            color: #ea580c;
            font-weight: bold;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(234, 88, 12, 0.5);
        }

        /* Container for sidebar and content */
        .container {
            display: flex;
            flex: 1;
            position: relative;
            overflow: hidden;
            height: calc(100vh - 82px - 66px);
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: rgba(0, 0, 0, 0.95);
            border-right: 2px solid #ea580c;
            padding: 20px 0;
            transition: all 0.3s;
            overflow-y: auto;
        }

        .sidebar.hidden {
            margin-left: -260px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 30px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover {
            background: rgba(234, 88, 12, 0.1);
            border-left-color: #ea580c;
        }

        .sidebar-menu a.active {
            background: rgba(234, 88, 12, 0.2);
            border-left-color: #ea580c;
            color: #ea580c;
        }

        .sidebar-menu a i {
            font-size: 1.2rem;
            width: 25px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .section-title {
            color: white;
            font-size: 2rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 10px rgba(234, 88, 12, 0.5);
        }

        /* Dashboard - Now Showing Movies */
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .movie-poster {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            cursor: pointer;
        }

        .movie-poster:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(234, 88, 12, 0.5);
        }

        .movie-image {
            width: 100%;
            height: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4.5rem;
            position: relative;
            overflow: hidden;
        }

        .movie-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .movie-info {
            padding: 15px;
        }

        .movie-info h3 {
            color: #1a1a2e;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .movie-details {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .movie-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #ea580c;
            font-weight: bold;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .book-btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .book-btn:hover {
            transform: scale(1.02);
        }

        /* Manage Movies Section */
        .manage-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .add-movie-btn {
            padding: 12px 25px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .add-movie-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.5);
        }

        .movies-table {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #1a1a2e;
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .movie-thumb {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-showing {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-hidden {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 5px;
            transition: all 0.3s;
        }

        .edit-btn {
            background: #dbeafe;
            color: #2563eb;
        }

        .edit-btn:hover {
            background: #2563eb;
            color: white;
        }

        .delete-btn {
            background: #fee2e2;
            color: #dc2626;
        }

        .delete-btn:hover {
            background: #dc2626;
            color: white;
        }

        .toggle-btn {
            background: #fef3c7;
            color: #d97706;
        }

        .toggle-btn:hover {
            background: #d97706;
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            overflow: auto;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: #1a1a2e;
        }

        .close-btn {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ea580c;
        }

        .save-btn {
            padding: 12px 40px;
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            width: 100%;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(234, 88, 12, 0.5);
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.95);
            color: white;
            text-align: center;
            padding: 15px;
            border-top: 2px solid #ea580c;
        }

        .footer p {
            margin: 3px 0;
            font-size: 13px;
        }

        .footer a {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
        }

         /* Booking Container */
.booking-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 30px;
    max-width: 600px;
    margin: 0 auto;
}

.booking-container h2 {
    color: #1a1a2e;
    margin-bottom: 25px;
    text-align: center;
}

/* Seat Selection Container */
.seat-selection-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Booking Info Card */
.booking-info-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 25px;
    height: fit-content;
    position: sticky;
    top: 20px;
}

.booking-info-card h3 {
    color: #1a1a2e;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ea580c;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-label {
    color: #666;
    font-weight: 600;
}

.info-value {
    color: #1a1a2e;
    font-weight: 500;
    text-align: right;
}

.total-row {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #ea580c;
    border-bottom: none;
}

.total-row .info-label {
    font-size: 1.2rem;
    color: #1a1a2e;
}

.total-row .info-value {
    font-size: 1.3rem;
    color: #ea580c;
    font-weight: bold;
}

.back-btn {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: #6b7280;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.back-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.cinema-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 30px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}
/* Screen */
.screen {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
    text-align: center;
    padding: 15px;
    border-radius: 10px 10px 50% 50%;
    margin-bottom: 30px;
    font-weight: bold;
    font-size: 1.2rem;
    letter-spacing: 3px;
    box-shadow: 0 5px 20px rgba(234, 88, 12, 0.3);
}

/* Seat Legend */
.seat-legend {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 10px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #1a1a2e;
    font-weight: 500;
}

.available-demo,
.selected-demo,
.booked-demo {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 2px solid #ddd;
}

.available-demo {
    background: white;
    border-color: #10b981;
}

.selected-demo {
    background: #ea580c;
    border-color: #ea580c;
}

.booked-demo {
    background: #9ca3af;
    border-color: #6b7280;
    cursor: not-allowed;
}

/* Seats Grid */
.seats-grid {
    margin: 20px 0;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.row-label {
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: #1a1a2e;
    font-size: 1.1rem;
}

.seat {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid #ddd;
    background: white;
}

.seat.available {
    border-color: #10b981;
    color: #10b981;
}

.seat.available:hover {
    background: #dcfce7;
    transform: scale(1.1);
}

.seat.selected {
    background: #ea580c;
    border-color: #ea580c;
    color: white;
    transform: scale(1.05);
}

.seat.booked {
    background: #9ca3af;
    border-color: #6b7280;
    color: white;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Confirm Booking Button */
.confirm-booking-btn {
    width: 100%;
    padding: 15px;
    margin-top: 25px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.confirm-booking-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
}

.confirm-booking-btn:disabled {
    background: #d1d5db;
    cursor: not-allowed;
    opacity: 0.6;
}

/* My Bookings Section */
.bookings-container {
    max-width: 1200px;
    margin: 0 auto;
}

.bookings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
}

.booking-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.booking-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(234, 88, 12, 0.3);
}

.booking-card.cancelled {
    opacity: 0.7;
    border: 2px solid #dc2626;
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e5e7eb;
}

.booking-header h3 {
    color: #1a1a2e;
    margin: 0;
    font-size: 1.3rem;
}

.booking-status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.booking-status.confirmed {
    background: #dcfce7;
    color: #16a34a;
}

.booking-status.cancelled {
    background: #fee2e2;
    color: #dc2626;
}

.booking-details {
    margin: 15px 0;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    color: #1a1a2e;
}

.detail-row i {
    color: #ea580c;
    width: 20px;
    font-size: 1.1rem;
}

.cancel-booking-btn {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.cancel-booking-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(220, 38, 38, 0.5);
}

/* Showtimes Grid */
.showtimes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.showtime-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.showtime-card h3 {
    color: #1a1a2e;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ea580c;
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.time-slot {
    background: #f3f4f6;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    color: #1a1a2e;
    border: 2px solid transparent;
    transition: all 0.3s;
    cursor: pointer;
}

.time-slot:hover {
    background: #ea580c;
    color: white;
    border-color: #ea580c;
    transform: scale(1.05);
}

/* Profile Container */
.profile-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 30px;
    max-width: 700px;
    margin: 0 auto;
}

.profile-container h2 {
    color: #1a1a2e;
    margin-bottom: 25px;
    text-align: center;
}

        /* Responsive */
        @media (max-width: 968px) {
            .menu-toggle {
                display: block;
            }

           

            .sidebar {
                position: fixed;
                left: 0;
                top: 82px;
                z-index: 99;
                margin-left: -260px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                padding: 20px;
            }

            table {
                font-size: 0.85rem;
            }

            .movie-thumb {
                width: 40px;
                height: 60px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }

            .welcome-text {
                display: none;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <span>🎬</span> CineMax<span> Portal</span>
            </div>
        </div>
        <div class="user-section">
            <div class="welcome-text">
                Welcome, <span class="username"><?php echo htmlspecialchars($username); ?></span>!
            </div>
            <button class="logout-btn" onclick="confirmLogout(); return false;">
    <i class="fas fa-sign-out-alt"></i> Logout
</button>
        </div>
    </header>

    <!-- Container -->
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="active" onclick="showSection('dashboard'); return false;">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('manage-movies'); return false;">
                        <i class="fas fa-film"></i>
                        <span>Manage Movies</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('book-tickets'; return false;">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Book Tickets</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('showtimes'); return false;">
                        <i class="fas fa-clock"></i>
                        <span>Showtimes</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('my-bookings'); return false;">
                        <i class="fas fa-receipt"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <h1 class="section-title">🎬 Now Showing</h1>
                <div class="movies-grid">
                    <?php
                    if (count($movies) > 0) {
                        foreach($movies as $movie) {
                            echo '
                            <div class="movie-poster">
                                <div class="movie-image">
                                    <img src="' . htmlspecialchars($movie['image_path']) . '" alt="' . htmlspecialchars($movie['title']) . '">
                                </div>
                                <div class="movie-info">
                                    <h3>' . htmlspecialchars($movie['title']) . '</h3>
                                    <div class="movie-details">⏱️ ' . htmlspecialchars($movie['duration']) . ' | 🎭 ' . htmlspecialchars($movie['genre']) . '</div>
                                    <div class="movie-rating">⭐ ' . htmlspecialchars($movie['rating']) . '/10</div>
                                    <button class="book-btn" onclick="showSection(\'book-tickets\')">Book Now</button>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p style="color: white;">No movies currently showing.</p>';
                    }
                    ?>
                </div>
            </section>

            <!-- Manage Movies Section -->
            <section id="manage-movies" class="content-section">
                <div class="manage-header">
                    <h1 class="section-title">🎬 Manage Movies</h1>
                    <button class="add-movie-btn" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Movie
                    </button>
                </div>
                <div class="movies-table" id="moviesTableContainer">
                    <!-- Table will be loaded here -->
                </div>
            </section>

            <!-- Book Tickets Section -->
<section id="book-tickets" class="content-section">
    <h1 class="section-title">🎟️ Book Tickets</h1>
    <div class="booking-container">
        <h2>Select Your Movie</h2>
        <form id="bookingForm">
            <div class="form-group">
                <label>Choose Movie</label>
                <select id="movieSelect" name="movie_id" required onchange="updateMovieInfo()">
                    <option value="">-- Select Movie --</option>
                    <?php
                    $result->data_seek(0);
                    while($movie = $result->fetch_assoc()) {
                        echo '<option value="' . $movie['id'] . '" 
                              data-title="' . htmlspecialchars($movie['title']) . '">' 
                              . htmlspecialchars($movie['title']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Date</label>
                <input type="date" id="bookingDate" name="booking_date" required onchange="checkSeats()">
            </div>
            <div class="form-group">
                <label>Select Showtime</label>
                <select id="showtime" name="showtime" required onchange="checkSeats()">
                    <option value="">-- Select Time --</option>
                    <option value="10:00 AM">10:00 AM</option>
                    <option value="1:00 PM">1:00 PM</option>
                    <option value="4:00 PM">4:00 PM</option>
                    <option value="7:00 PM">7:00 PM</option>
                    <option value="10:00 PM">10:00 PM</option>
                </select>
            </div>
            <button type="button" class="save-btn" onclick="proceedToSeats()">Proceed to Seat Selection</button>
        </form>
    </div>
</section>

<!-- Seat Selection Section -->
<section id="seat-selection" class="content-section">
    <h1 class="section-title">🎬 Select Your Seats</h1>
    
    <div class="seat-selection-container">
        <!-- Booking Info -->
        <div class="booking-info-card">
            <h3>Booking Details</h3>
            <div class="info-row">
                <span class="info-label">Movie:</span>
                <span class="info-value" id="selectedMovie">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value" id="selectedDate">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Showtime:</span>
                <span class="info-value" id="selectedShowtime">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Selected Seats:</span>
                <span class="info-value" id="selectedSeatsDisplay">None</span>
            </div>
            <div class="info-row total-row">
                <span class="info-label">Total Amount:</span>
                <span class="info-value" id="totalAmount">₱0.00</span>
            </div>
            <button class="back-btn" onclick="backToBooking()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        </div>

        <!-- Cinema Screen -->
        <div class="cinema-container">
            <div class="screen">SCREEN</div>
            
            <!-- Seat Legend -->
            <div class="seat-legend">
                <div class="legend-item">
                    <div class="seat available-demo"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="seat selected-demo"></div>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <div class="seat booked-demo"></div>
                    <span>Taken</span>
                </div>
            </div>

            <!-- Seats Grid -->
            <div class="seats-grid" id="seatsGrid">
                <!-- Seats will be generated here by JavaScript -->
            </div>

            <button class="confirm-booking-btn" id="confirmBookingBtn" onclick="confirmBooking()" disabled>
                <i class="fas fa-check-circle"></i> Confirm Booking
            </button>
        </div>
    </div>
</section>

<!-- My Bookings Section -->
<section id="my-bookings" class="content-section">
    <h1 class="section-title">🎫 Bookings</h1>
    <div class="bookings-container" id="bookingsContainer">
        <!-- Bookings will be loaded here -->
    </div>
</section>

            <!-- Showtimes Section -->
            <section id="showtimes" class="content-section">
                <h1 class="section-title">⏰ Today's Showtimes</h1>
                <div class="showtimes-grid" id="showtimesContainer">
                    <!-- Showtimes will be loaded here -->
                </div>
            </section>

            <!-- Profile Settings Section -->
            <section id="profile" class="content-section">
                <h1 class="section-title">⚙️ Profile Settings</h1>
                <div class="profile-container">
                    <h2>Update Your Information</h2>
                    <form>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($username); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" placeholder="your.email@example.com">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" placeholder="+1 234 567 8900">
                        </div>
                        <div class="form-group">
                            <label>Change Password</label>
                            <input type="password" placeholder="New Password">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" placeholder="Confirm New Password">
                        </div>
                        <button type="submit" class="save-btn">Save Changes</button>
                    </form>
                </div>
            </section>
        </main>
    </div>

    

    <!-- Add/Edit Movie Modal -->
    <div id="movieModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Movie</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="movieForm" enctype="multipart/form-data">
                <input type="hidden" id="movieId" name="id">
                <div class="form-group">
                    <label>Movie Title</label>
                    <input type="text" id="movieTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" id="movieDuration" name="duration" placeholder="e.g., 2h 25min" required>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" id="movieGenre" name="genre" placeholder="e.g., Action, Adventure" required>
                </div>
                <div class="form-group">
                    <label>Rating</label>
                    <input type="number" id="movieRating" name="rating" step="0.1" min="0" max="10" required>
                </div>
                <div class="form-group">
                    <label>Movie Poster Image</label>
                    <input type="file" id="movieImage" name="image" accept="image/*">
                    <small style="color: #666;">Leave empty to keep current image (for edit)</small>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="movieStatus" name="status">
                        <option value="showing">Showing</option>
                        <option value="not_showing">Not Showing</option>
                    </select>
                </div>
                <button type="submit" class="save-btn">Save Movie</button>
            </form>
        </div>
    </div>

    <script>
        // Global variables for booking
        let selectedSeats = [];
        let seatPrice = 250; // Price per seat in PHP pesos
        let currentBookingData = {};

        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('bookingDate');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute('min', today);
            }
        });

        // Update movie info when movie is selected
        function updateMovieInfo() {
            const movieSelect = document.getElementById('movieSelect');
            const selectedOption = movieSelect.options[movieSelect.selectedIndex];
            
            if (movieSelect.value) {
                console.log('Selected movie:', selectedOption.getAttribute('data-title'));
            }
        }

        // Check available seats
        function checkSeats() {
            const movieId = document.getElementById('movieSelect').value;
            const date = document.getElementById('bookingDate').value;
            const showtime = document.getElementById('showtime').value;
            
            if (movieId && date && showtime) {
                console.log('Checking seats for:', {movieId, date, showtime});
            }
        }

        // Proceed to seat selection
        function proceedToSeats() {
            const movieSelect = document.getElementById('movieSelect');
            const dateInput = document.getElementById('bookingDate');
            const showtimeSelect = document.getElementById('showtime');
            
            if (!movieSelect.value) {
                alert('Please select a movie');
                return;
            }
            
            if (!dateInput.value) {
                alert('Please select a date');
                return;
            }
            
            if (!showtimeSelect.value) {
                alert('Please select a showtime');
                return;
            }
            
            currentBookingData = {
                movieId: movieSelect.value,
                movieTitle: movieSelect.options[movieSelect.selectedIndex].getAttribute('data-title'),
                date: dateInput.value,
                showtime: showtimeSelect.value
            };
            
            document.getElementById('selectedMovie').textContent = currentBookingData.movieTitle;
            document.getElementById('selectedDate').textContent = formatDate(currentBookingData.date);
            document.getElementById('selectedShowtime').textContent = currentBookingData.showtime;
            
            loadBookedSeats();
            showSection('seat-selection');
        }

        // Format date for display
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Load booked seats from backend
        function loadBookedSeats() {
            fetch(`seat_operations.php?action=get_booked&movie_id=${currentBookingData.movieId}&date=${currentBookingData.date}&showtime=${encodeURIComponent(currentBookingData.showtime)}`)
                .then(response => response.json())
                .then(data => {
                    generateSeats(data.bookedSeats || []);
                })
                .catch(error => {
                    console.error('Error loading booked seats:', error);
                    generateSeats([]);
                });
        }

        // Generate seats grid
        function generateSeats(bookedSeats) {
            const seatsGrid = document.getElementById('seatsGrid');
            seatsGrid.innerHTML = '';
            selectedSeats = [];
            updateSelectedSeatsDisplay();
            
            const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            const seatsPerRow = 10;
            
            rows.forEach(row => {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'seat-row';
                
                const rowLabel = document.createElement('div');
                rowLabel.className = 'row-label';
                rowLabel.textContent = row;
                rowDiv.appendChild(rowLabel);
                
                for (let i = 1; i <= seatsPerRow; i++) {
                    const seatNumber = `${row}${i}`;
                    const seat = document.createElement('div');
                    seat.className = 'seat';
                    seat.textContent = i;
                    seat.setAttribute('data-seat', seatNumber);
                    
                    if (bookedSeats.includes(seatNumber)) {
                        seat.classList.add('booked');
                    } else {
                        seat.classList.add('available');
                        seat.addEventListener('click', function() {
                            toggleSeat(this);
                        });
                    }
                    
                    rowDiv.appendChild(seat);
                }
                
                seatsGrid.appendChild(rowDiv);
            });
        }

        // Toggle seat selection
        function toggleSeat(seatElement) {
            const seatNumber = seatElement.getAttribute('data-seat');
            
            if (seatElement.classList.contains('selected')) {
                seatElement.classList.remove('selected');
                selectedSeats = selectedSeats.filter(s => s !== seatNumber);
            } else {
                if (selectedSeats.length >= 10) {
                    alert('Maximum 10 seats can be selected');
                    return;
                }
                seatElement.classList.add('selected');
                selectedSeats.push(seatNumber);
            }
            
            updateSelectedSeatsDisplay();
        }

        // Update selected seats display
        function updateSelectedSeatsDisplay() {
            const display = document.getElementById('selectedSeatsDisplay');
            const totalAmount = document.getElementById('totalAmount');
            const confirmBtn = document.getElementById('confirmBookingBtn');
            
            if (selectedSeats.length > 0) {
                display.textContent = selectedSeats.sort().join(', ');
                totalAmount.textContent = `₱${(selectedSeats.length * seatPrice).toFixed(2)}`;
                confirmBtn.disabled = false;
            } else {
                display.textContent = 'None';
                totalAmount.textContent = '₱0.00';
                confirmBtn.disabled = true;
            }
        }

        // Back to booking form
        function backToBooking() {
            if (selectedSeats.length > 0) {
                if (!confirm('You have selected seats. Are you sure you want to go back?')) {
                    return;
                }
            }
            showSection('book-tickets');
        }

        // Confirm booking
        function confirmBooking() {
            if (selectedSeats.length === 0) {
                alert('Please select at least one seat');
                return;
            }
            
            if (!confirm(`Confirm booking for ${selectedSeats.length} seat(s)?\nTotal: ₱${(selectedSeats.length * seatPrice).toFixed(2)}`)) {
                return;
            }
            
            const bookingData = {
                action: 'book',
                movie_id: currentBookingData.movieId,
                movie_title: currentBookingData.movieTitle,
                showtime: currentBookingData.showtime,
                booking_date: currentBookingData.date,
                seats: selectedSeats.join(','),
                total_amount: selectedSeats.length * seatPrice
            };
            
            const confirmBtn = document.getElementById('confirmBookingBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            fetch('seat_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(bookingData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking confirmed successfully!');
                    showSection('my-bookings');
                    loadMyBookings();
                } else {
                    alert('Booking failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Booking failed. Please try again.');
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
            });
        }

        // Load my bookings
        function loadMyBookings() {
            fetch('seat_operations.php?action=my_bookings')
                .then(response => response.json())
                .then(bookings => {
                    const container = document.getElementById('bookingsContainer');
                    
                    if (bookings.length === 0) {
                        container.innerHTML = '<p style="color: white; text-align: center;">No bookings found.</p>';
                        return;
                    }
                    
                    let html = '<div class="bookings-grid">';
                    bookings.forEach(booking => {
                        const status = booking.booking_status || 'confirmed';
                        const statusClass = status === 'cancelled' ? 'cancelled' : 'confirmed';
                        
                        html += `
                            <div class="booking-card ${statusClass}">
                                <div class="booking-header">
                                    <h3>${booking.movie_title}</h3>
                                    <span class="booking-status ${statusClass}">${status.toUpperCase()}</span>
                                </div>
                                <div class="booking-details">
                                    <div class="detail-row">
                                        <i class="fas fa-calendar"></i>
                                        <span>${formatDate(booking.booking_date)}</span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-clock"></i>
                                        <span>${booking.showtime}</span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-chair"></i>
                                        <span>Seats: ${booking.seats}</span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-money-bill"></i>
                                        <span>Total: ₱${parseFloat(booking.total_amount).toFixed(2)}</span>
                                    </div>
                                </div>
                                ${status !== 'cancelled' ? `
                                    <button class="cancel-booking-btn" onclick="cancelBooking(${booking.id})">
                                        <i class="fas fa-times-circle"></i> Cancel Booking
                                    </button>
                                ` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';
                    
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading bookings:', error);
                    document.getElementById('bookingsContainer').innerHTML = 
                        '<p style="color: white; text-align: center;">Error loading bookings.</p>';
                });
        }

        // Cancel booking
        function cancelBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this booking?')) {
                return;
            }
            
            fetch('seat_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'cancel',
                    booking_id: bookingId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking cancelled successfully!');
                    loadMyBookings();
                } else {
                    alert('Cancellation failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Cancellation failed. Please try again.');
            });
        }

        // Load movies table
        function loadMoviesTable() {
            fetch('movie_operations.php?action=list')
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <table>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Duration</th>
                                    <th>Genre</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    if (data.length > 0) {
                        data.forEach(movie => {
                            html += `
                                <tr>
                                    <td><img src="${movie.image_path}" alt="${movie.title}" class="movie-thumb"></td>
                                    <td>${movie.title}</td>
                                    <td>${movie.duration}</td>
                                    <td>${movie.genre}</td>
                                    <td>⭐ ${movie.rating}/10</td>
                                    <td><span class="status-badge ${movie.status === 'showing' ? 'status-showing' : 'status-hidden'}">${movie.status === 'showing' ? 'Showing' : 'Hidden'}</span></td>
                                    <td>
                                        <button class="action-btn edit-btn" onclick="editMovie(${movie.id})"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="action-btn toggle-btn" onclick="toggleMovie(${movie.id}, '${movie.status}')">
                                            <i class="fas fa-eye${movie.status === 'showing' ? '-slash' : ''}"></i> ${movie.status === 'showing' ? 'Hide' : 'Show'}
                                        </button>
                                        <button class="action-btn delete-btn" onclick="deleteMovie(${movie.id})"><i class="fas fa-trash"></i> Delete</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        html += '<tr><td colspan="7" style="text-align: center;">No movies found</td></tr>';
                    }
                    
                    html += '</tbody></table>';
                    document.getElementById('moviesTableContainer').innerHTML = html;
                });
        }

        // Load showtimes
        function loadShowtimes() {
            fetch('movie_operations.php?action=list')
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    data.forEach(movie => {
                        if (movie.status === 'showing') {
                            html += `
                                <div class="showtime-card">
                                    <h3>${movie.title}</h3>
                                    <div class="time-slots">
                                        <div class="time-slot">10:00 AM</div>
                                        <div class="time-slot">1:00 PM</div>
                                        <div class="time-slot">4:00 PM</div>
                                        <div class="time-slot">7:00 PM</div>
                                        <div class="time-slot">10:00 PM</div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                    document.getElementById('showtimesContainer').innerHTML = html || '<p style="color: white;">No showtimes available.</p>';
                });
        }

        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Movie';
            document.getElementById('movieForm').reset();
            document.getElementById('movieId').value = '';
            document.getElementById('movieModal').classList.add('active');
        }

        // Edit movie
        function editMovie(id) {
            fetch(`movie_operations.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(movie => {
                    document.getElementById('modalTitle').textContent = 'Edit Movie';
                    document.getElementById('movieId').value = movie.id;
                    document.getElementById('movieTitle').value = movie.title;
                    document.getElementById('movieDuration').value = movie.duration;
                    document.getElementById('movieGenre').value = movie.genre;
                    document.getElementById('movieRating').value = movie.rating;
                    document.getElementById('movieStatus').value = movie.status;
                    document.getElementById('movieModal').classList.add('active');
                });
        }

        // Close modal
        function closeModal() {
            document.getElementById('movieModal').classList.remove('active');
        }

        // Submit movie form
        document.getElementById('movieForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = document.getElementById('movieId').value ? 'update' : 'add';
            formData.append('action', action);
            
            const submitBtn = this.querySelector('.save-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            fetch('movie_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(text => {
                console.log('Response:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert(data.message);
                        closeModal();
                        loadMoviesTable();
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    alert('Error: Invalid response from server. Check console for details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message + '. Check console for details.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Toggle movie status
        function toggleMovie(id, currentStatus) {
            const newStatus = currentStatus === 'showing' ? 'not_showing' : 'showing';
            
            fetch('movie_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=toggle&id=${id}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    loadMoviesTable();
                    location.reload(); // Reload to update dashboard
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Delete movie
        function deleteMovie(id) {
            if (confirm('Are you sure you want to delete this movie?')) {
                fetch('movie_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        loadMoviesTable();
                        location.reload(); // Reload to update dashboard
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
            return false;
        }

        function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });

    // Remove active class from all menu items
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.classList.remove('active');
    });

    // Show selected section
    document.getElementById(sectionId).classList.add('active');

    // Add active class to clicked menu item - FIXED
    const clickedLink = document.querySelector(`.sidebar-menu a[onclick*="${sectionId}"]`);
    if (clickedLink) {
        clickedLink.classList.add('active');
    }

    // Load data for specific sections
    if (sectionId === 'manage-movies') {
        loadMoviesTable();
    } else if (sectionId === 'showtimes') {
        loadShowtimes();
    } else if (sectionId === 'my-bookings') {
        loadMyBookings();
    }

    // Close sidebar on mobile after selection
    if (window.innerWidth <= 968) {
        document.getElementById('sidebar').classList.remove('active');
    }
}

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 968 && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Close modal when clicking outside
        document.getElementById('movieModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>

<?php $conn = null(); ?>