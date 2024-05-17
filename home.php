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
    $apiKey = '219ff162a58589430fc465f29dd1d386'; // Replace with your OpenWeatherMap API key
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$location&appid=$apiKey&units=metric";
    $response = file_get_contents($apiUrl);

    if ($response === FALSE) {
        return null; // API request failed
    }

    $weatherData = json_decode($response, true);

    if ($weatherData['cod'] != 200) {
        return null; // Invalid response from API
    }

    return $weatherData;
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
    <title>Welcome</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mb-5">
    <h2>Manage Your Locations</h2>
    <form id="add-location-form" method="post" action="">
        <div class="form-group">
            <label for="location">Add Location:</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Location</button>
    </form>
    
    <h3>Your Locations</h3>
    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Location</th>
                <th>Coordinates</th>
                <th>Weather</th>
                <th>Temperature (°C)</th>
                <th>Feels Like (°C)</th>
                <th>Min Temp (°C)</th>
                <th>Max Temp (°C)</th>
                <th>Pressure (hPa)</th>
                <th>Humidity (%)</th>
                <th>Visibility (m)</th>
                <th>Wind Speed (m/s)</th>
                <th>Wind Direction (°)</th>
                <th>Cloudiness (%)</th>
                <th>Sunrise</th>
                <th>Sunset</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $locations = getUserLocations($conn, $user_id);
            foreach ($locations as $loc) {
                $weatherData = getWeatherData($loc['location']);
                if ($weatherData) {
                    $coordinates = "Lon: " . $weatherData['coord']['lon'] . ", Lat: " . $weatherData['coord']['lat'];
                    $weather = $weatherData['weather'][0]['main'] . " (" . $weatherData['weather'][0]['description'] . ")";
                    $temp = $weatherData['main']['temp'];
                    $feels_like = $weatherData['main']['feels_like'];
                    $temp_min = $weatherData['main']['temp_min'];
                    $temp_max = $weatherData['main']['temp_max'];
                    $pressure = $weatherData['main']['pressure'];
                    $humidity = $weatherData['main']['humidity'];
                    $visibility = $weatherData['visibility'];
                    $wind_speed = $weatherData['wind']['speed'];
                    $wind_deg = $weatherData['wind']['deg'];
                    $cloudiness = $weatherData['clouds']['all'];
                    $sunrise = date("H:i:s", $weatherData['sys']['sunrise']);
                    $sunset = date("H:i:s", $weatherData['sys']['sunset']);
                    echo "<tr>
                            <td>{$loc['location']}</td>
                            <td>{$coordinates}</td>
                            <td>{$weather}</td>
                            <td>{$temp}</td>
                            <td>{$feels_like}</td>
                            <td>{$temp_min}</td>
                            <td>{$temp_max}</td>
                            <td>{$pressure}</td>
                            <td>{$humidity}</td>
                            <td>{$visibility}</td>
                            <td>{$wind_speed}</td>
                            <td>{$wind_deg}</td>
                            <td>{$cloudiness}</td>
                            <td>{$sunrise}</td>
                            <td>{$sunset}</td>
                            <td><button class='remove-location btn btn-danger' data-id='{$loc['id']}'>Remove</button></td>
                        </tr>";
                } else {
                    echo "<tr>
                            <td>{$loc['location']}</td>
                            <td colspan='15'>Weather data not available</td>
                        </tr>";
                }
            }
            ?>
        </tbody>
    </table>
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
