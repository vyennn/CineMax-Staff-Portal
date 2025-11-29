<?php
// home.php - Welcome page with auto-redirect for logged-in users
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMax - Movie Ticket Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            overflow-x: hidden;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.95);
            padding: 12px 0;
            border-bottom: 2px solid #ea580c;
            box-shadow: 0 2px 20px rgba(234, 88, 12, 0.3);
        }

        .navdiv {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            color: #ea580c;
        }

        .navdiv ul {
            list-style: none;
            display: flex;
            gap: 15px;
        }

        .navdiv button {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        .navdiv button a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            text-align: center;
            padding: 50px;
            position: relative;
            color: white;
        }

        .hero-section {
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 4rem;
            margin-bottom: 50px;
            text-shadow: 3px 3px 20px rgba(234, 88, 12, 0.5);
        }

        .hero-section .emoji {
            font-size: 4.5rem;
        }

        .hero-section p {
            font-size: 1.4rem;
            margin-bottom: 25px;
        }

        .tagline {
            color: #ea580c;
            font-weight: 600;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .cta-btn {
            padding: 14px 40px;
            font-size: 1rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .cta-btn.primary {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
        }

        .cta-btn.secondary {
            background: transparent;
            border: 2px solid #ea580c;
            color: white;
        }

        .features {
            display: flex;
            gap: 25px;
            justify-content: center;
        }

        .feature {
            background: rgba(255, 255, 255, 0.07);
            padding: 18px 25px;
            border-radius: 15px;
            border: 1px solid rgba(234, 88, 12, 0.3);
            width: 240px;
            backdrop-filter: blur(10px);
        }

        .feature-icon {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .feature h3 {
            font-size: 1rem;
            color: #ea580c;
            margin-bottom: 5px;
        }

        .feature p {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* ------------------ MOBILE RESPONSIVE ------------------ */
        @media (max-width: 768px) {

            .navdiv {
                flex-direction: column;
                gap: 15px;
                padding: 10px;
            }

            .hero-section h1 {
                font-size: 2.5rem;
            }

            .hero-section .emoji {
                font-size: 3rem;
            }

            .hero-section p {
                font-size: 1.1rem;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .cta-btn {
                width: 100%;
                padding: 12px;
            }

            .features {
                flex-direction: column;
                align-items: center;
                width: 100%;
                padding: 0 20px;
            }

            .feature {
                width: 100%;
            }
        }

        @media (max-width: 480px) {

            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section .emoji {
                font-size: 2.4rem;
            }

            .hero-section p {
                font-size: 1rem;
            }

            .cta-btn {
                padding: 10px 20px;
                font-size: 0.95rem;
            }
        }

    </style>
</head>

<body>

    <nav class="navbar">
        <div class="navdiv">
            <div class="logo">
                <a href="#"><span>🎬</span> CineMax <span> Staff Portal</span></a>
            </div>
            <ul>
                <button><a href="login.php">Login</a></button>
                <button><a href="registration.php">Register</a></button>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <div class="hero-section">
            <h1><span class="emoji">🍿</span> Welcome to CineMax Staff Portal</h1>
            <p>Your all-in-one system for managing movie reservations</p>
            <p class="tagline">Handle bookings • Manage schedules • Assist customers</p>

            <div class="cta-buttons">
                <a href="login.php" class="cta-btn primary">Proceed to Login</a>
                <a href="registration.php" class="cta-btn secondary">Create Staff Account</a>
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🎟️</div>
                    <h3>Fast Booking</h3>
                    <p>Quickly reserve seats for customers</p>
                </div>

                <div class="feature">
                    <div class="feature-icon">🎭</div>
                    <h3>Now Showing</h3>
                    <p>Check movies and available screening times</p>
                </div>

                <div class="feature">
                    <div class="feature-icon">🪑</div>
                    <h3>Seat Management</h3>
                    <p>View availability and assign seats instantly</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
