<?php
include('config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"];

function getWeatherData($location) {
    $apiKey = '219ff162a58589430fc465f29dd1d386'; // Replace with your OpenWeatherMap API key
    $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q=$location&appid=$apiKey&units=metric";
    $response = file_get_contents($apiUrl);

    if ($response === FALSE) {
        return null; // API request failed
    }

    $weatherData = json_decode($response, true);

    if ($weatherData['cod'] != "200") {
        return null; // Invalid response from API
    }

    return $weatherData;
}

if (!isset($_GET['location'])) {
    echo "Location not specified.";
    exit;
}

$location = $_GET['location'];
$weatherData = getWeatherData($location);

if ($weatherData === null) {
    echo "Unable to fetch weather data.";
    exit;
}

$next24Hours = array_slice($weatherData['list'], 0, 8);
?>

<!DOCTYPE html>
<html>
<head>
    <title>24-Hour Weather Forecast for <?php echo htmlspecialchars($location); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .forecast-hourly ul {
            list-style: none;
            padding: 0;
        }
        .forecast-hourly li {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container">
    <h2>24-Hour Weather Forecast for <?php echo htmlspecialchars($location); ?></h2>
    <div class="forecast-hourly">
        <ul>
            <?php foreach ($next24Hours as $entry) { ?>
                <li>
                    <span><?php echo date('H:i', strtotime($entry['dt_txt'])); ?></span>
                    <span><?php echo round($entry['main']['temp']); ?>°C</span>
                    <span><?php echo ucfirst($entry['weather'][0]['description']); ?></span>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
