<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melcom Audit System - Corporate Portal</title>
    <!-- Browser Favicon using the Circle Logo -->
    <link rel="icon" type="image/png" href="IMG/logo_circle.png">
    
    <!-- Link to the clean, lightweight, separate vanilla CSS stylesheet with cache-busting -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo filemtime('css/style.css'); ?>">
    
    <!-- Load Outfit Google Font to match the template's premium geometric typography -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        
        #global-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        .loader-logo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: float-logo 3s ease-in-out infinite;
        }
        .loader-wordmark {
            width: 220px;
            object-fit: contain;
            margin-bottom: 0.75rem;
            filter: drop-shadow(0 0 15px rgba(79, 70, 229, 0.15));
        }
        .loader-text {
            color: #4f46e5;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .loader-glow-bar {
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #4f46e5, #9333ea, transparent);
            border-radius: 50%;
            animation: pulse-glow-bar 1.5s ease-in-out infinite;
            box-shadow: 0 0 10px 1px rgba(79, 70, 229, 0.5);
        }
        @keyframes float-logo {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        @keyframes pulse-glow-bar {
            0% { width: 60%; opacity: 0.5; }
            50% { width: 120%; opacity: 1; }
            100% { width: 60%; opacity: 0.5; }
        }
    </style>
</head>
<body>
    <!-- Global Loading Screen -->
    <div id="global-page-loader">
        <div class="loader-logo-wrapper">
            <img src="IMG/logo_wordmark.png" class="loader-wordmark" alt="Melcom Logo">
            <div class="loader-text">PLEASE WAIT...</div>
            <div class="loader-glow-bar"></div>
        </div>
    </div>
    <?php
        if (ob_get_level() > 0) ob_flush();
        flush();
    ?>
