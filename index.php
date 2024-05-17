<!DOCTYPE html>
<html>
<head>
    <title>Weather Forcasting App</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
.jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

<?php
include('navbar.php');
?>
<div class="jumbotron text-center">
    <h1>Welcome to Weather Forecasting Application</h1>
    <p>Stay Ahead of the Weather with Accurate Forecasts</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Get Started</a>
</div>

<div class="container mb-5">
    <h2>Search Location For Weather</h2>
    <form id="add-location-form" method="post" action="">
        <div class="form-group">
            <label for="location">Search Location:</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        <a class="btn btn-outline-dark" href="login.php">Search Location</a>
    </form>
    </div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 Weather Forcasting App. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
