<?php
include('config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"];

// Function to fetch user locations from the database
function getUserLocations($conn, $user_id) {
    $sql = "SELECT id, location FROM user_locations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $locations = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $locations;
}

// Function to fetch weather data from OpenWeatherMap API
function getWeatherData($location) {
    $apiKey = "YOUR_OWN_API_KEY"; // Replace with your OpenWeatherMap API key
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

// Function to process forecast data
function processForecastData($forecastData) {
    $days = [];
    foreach ($forecastData['list'] as $entry) {
        $date = explode(' ', $entry['dt_txt'])[0];
        if (!isset($days[$date])) {
            $days[$date] = [
                'temp' => [],
                'feels_like' => [],
                'pressure' => [],
                'humidity' => [],
                'weather' => $entry['weather'][0],
                'wind_speed' => [],
            ];
        }

        $days[$date]['temp'][] = $entry['main']['temp'];
        $days[$date]['feels_like'][] = $entry['main']['feels_like'];
        $days[$date]['pressure'][] = $entry['main']['pressure'];
        $days[$date]['humidity'][] = $entry['main']['humidity'];
        $days[$date]['wind_speed'][] = $entry['wind']['speed'];
    }

    $dailySummaries = [];
    foreach ($days as $date => $data) {
        $dailySummaries[$date] = [
            'temp' => round(array_sum($data['temp']) / count($data['temp']), 1),
            'feels_like' => round(array_sum($data['feels_like']) / count($data['feels_like']), 1),
            'pressure' => round(array_sum($data['pressure']) / count($data['pressure']), 1),
            'humidity' => round(array_sum($data['humidity']) / count($data['humidity']), 1),
            'weather' => $data['weather'],
            'wind_speed' => round(array_sum($data['wind_speed']) / count($data['wind_speed']), 1),
        ];
    }

    return $dailySummaries;
}

// Handle add location form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['location'])) {
        $location = $_POST['location'];
        $sql = "INSERT INTO user_locations (user_id, location) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $location);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['remove_location_id'])) {
        $location_id = $_POST['remove_location_id'];
        $sql = "DELETE FROM user_locations WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $location_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "success";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Weather Forecast System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .weather-card {
            margin: 20px 0;
        }
        .weather-card img {
            width: 50px;
            height: 50px;
        }
        .weather-card .card-header {
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container">
    <h2>Manage Your Locations</h2>
    <form id="add-location-form" method="post" action="">
        <div class="form-group">
            <label for="location">Add Location:</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Location</button>
    </form>
    
    <h3>Your Locations</h3>
    <div class="row">
        <?php
        $locations = getUserLocations($conn, $user_id);
        foreach ($locations as $loc) {
            $weatherData = getWeatherData($loc['location']);
            if ($weatherData) {
                $dailySummaries = processForecastData($weatherData);

                echo "<div class='col-md-4 weather-card'>";
                echo "<div class='card'>";
                echo "<div class='card-header'>{$loc['location']} <button class='remove-location btn btn-danger btn-sm float-right' data-id='{$loc['id']}'>Remove</button></div>";
                echo "<div class='card-body'>";
                echo "<a href='forecast.php?location={$loc['location']}' class='mb-2 btn btn-primary'>View 24-Hour Forecast</a>";

                $today = date('Y-m-d');
                $tomorrow = date('Y-m-d', strtotime('+1 day'));
                $dayAfterTomorrow = date('Y-m-d', strtotime('+2 days'));

                $getWeatherCard = function($data, $date) {
                    $icon = $data['weather']['icon'];
                    $description = ucfirst($data['weather']['description']);
                    $temp = $data['temp'];
                    $feels_like = $data['feels_like'];
                    $humidity = $data['humidity'];
                    $pressure = $data['pressure'];
                    $wind_speed = $data['wind_speed'];

                    return "
                    <div class='card mb-3'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$date}</h5>
                            <p class='card-text'><img src='https://openweathermap.org/img/wn/{$icon}.png' alt='{$description}'> {$description}</p>
                            <p class='card-text'>Temperature: {$temp}°C</p>
                            <p class='card-text'>Feels Like: {$feels_like}°C</p>
                            <p class='card-text'>Humidity: {$humidity}%</p>
                            <p class='card-text'>Pressure: {$pressure} hPa</p>
                            <p class='card-text'>Wind Speed: {$wind_speed} m/s</p>
                        </div>
                    </div>";
                };

                echo $getWeatherCard($dailySummaries[$today] ?? [], 'Today');
                echo $getWeatherCard($dailySummaries[$tomorrow] ?? [], 'Tomorrow');
                echo $getWeatherCard($dailySummaries[$dayAfterTomorrow] ?? [], 'Day After Tomorrow');

                echo "</div>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "
                <div class='col-md-4 weather-card'>
                    <div class='card'>
                        <div class='card-header'>{$loc['location']} <button class='remove-location btn btn-danger btn-sm float-right' data-id='{$loc['id']}'>Remove</button></div>
                        <div class='card-body'>
                            <p>Weather data not available</p>
                        </div>
                    </div>
                </div>";
            }
        }
        ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('.remove-location').click(function() {
            const locationId = $(this).data('id');
            $.post('', { remove_location_id: locationId }, function(response) {
                if (response === 'success') {
                    location.reload();
                }
            });
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
