<?php //index.php - Welcome page with auto-redirect for logged-in users
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
            padding: 15px 0;
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
            font-size: 1.8rem;
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

        .navdiv ul li a {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
        }

        .navdiv ul li a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(234, 88, 12, 0.5);
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 50px 30px;
            position: relative;
            color: white;
        }

        .hero-section {
            z-index: 1;
            max-width: 1200px;
        }

        .hero-section h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 3px 3px 20px rgba(234, 88, 12, 0.5);
        }

        .hero-section .emoji {
            font-size: 4.5rem;
        }

        .hero-section p {
            font-size: 1.4rem;
            margin-bottom: 15px;
            opacity: 0.95;
        }

        .tagline {
            color: #ea580c;
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 40px;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 50px;
        }

        .cta-btn {
            padding: 16px 45px;
            font-size: 1.1rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .cta-btn.primary {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4);
        }

        .cta-btn.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(234, 88, 12, 0.6);
        }

        .cta-btn.secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #ea580c;
            color: white;
            backdrop-filter: blur(10px);
        }

        .cta-btn.secondary:hover {
            background: rgba(234, 88, 12, 0.2);
            transform: translateY(-3px);
        }

        .features {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .feature {
            background: rgba(255, 255, 255, 0.08);
            padding: 25px 30px;
            border-radius: 15px;
            border: 1px solid rgba(234, 88, 12, 0.3);
            width: 280px;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }

        .feature:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(234, 88, 12, 0.6);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .feature h3 {
            font-size: 1.2rem;
            color: #ea580c;
            margin-bottom: 10px;
        }

        .feature p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .footer {
            background: rgba(0, 0, 0, 0.95);
            color: white;
            text-align: center;
            padding: 15px;
            border-top: 2px solid #ea580c;
        }

        .footer p {
            margin: 5px 0;
            font-size: 14px;
        }

        .footer a {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* ------------------ MOBILE RESPONSIVE ------------------ */
        @media (max-width: 968px) {
            .navdiv {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .navdiv ul {
                flex-direction: row;
                gap: 10px;
            }

            .navdiv ul li a {
                padding: 8px 16px;
                font-size: 13px;
            }

            .hero-section h1 {
                font-size: 3rem;
            }

            .hero-section .emoji {
                font-size: 3.5rem;
            }

            .hero-section p {
                font-size: 1.2rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-btn {
                width: 100%;
                max-width: 350px;
                justify-content: center;
            }

            .features {
                flex-direction: column;
                align-items: center;
            }

            .feature {
                width: 100%;
                max-width: 350px;
            }
        }

        @media (max-width: 600px) {
            .hero-section h1 {
                font-size: 2.2rem;
            }

            .hero-section .emoji {
                font-size: 2.8rem;
            }

            .hero-section p {
                font-size: 1rem;
            }

            .tagline {
                font-size: 1.1rem;
            }

            .cta-btn {
                padding: 14px 35px;
                font-size: 1rem;
            }

            .main-content {
                padding: 30px 20px;
            }
        }

    </style>
</head>

<body>

    <nav class="navbar">
        <div class="navdiv">
            <div class="logo">
                <a href="index.php"><span>🎬</span> CineMax <span>Portal</span></a>
            </div>
            <ul>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="registration.php"><i class="fas fa-user-plus"></i> Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <div class="hero-section">
            <h1><span class="emoji">🍿</span> Welcome to CineMax Portal</h1>
            <p>Your all-in-one system for managing movie reservations</p>
            <p class="tagline">Handle bookings • Manage schedules • Assist customers</p>

            <div class="cta-buttons">
                <a href="login.php" class="cta-btn primary">
                    <i class="fas fa-sign-in-alt"></i> Proceed to Login
                </a>
                <a href="registration.php" class="cta-btn secondary">
                    <i class="fas fa-user-plus"></i> Create Staff Account
                </a>
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

    <div class="footer">
        <p>&copy; 2024 CineMax Portal. All Rights Reserved.</p>
        <p>Designed for seamless movie booking management</p>
    </div>

</body>
</html>