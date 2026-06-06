# weather_forecast_final

A lightweight PHP web application that provides real‑time weather forecasts, user authentication, and profile management. The project demonstrates clean MVC‑style organization, responsive UI, and integration with a public weather API.

---

## Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [License](#license)

---

## Overview
`weather_forecast_final` is a simple yet functional weather forecasting portal built with PHP. Users can register, log in, view current weather data for selected locations, and update their profile information. The application stores user data in a MySQL database and fetches weather information from an external API.

---

## Features
- **User Authentication** – Register, login, logout, and password management.  
- **Profile Management** – Update personal details and change password.  
- **Weather Forecast** – Display current conditions and a 5‑day forecast for any city.  
- **Responsive UI** – Clean layout with a navigation bar and CSS styling.  
- **Secure Configuration** – API keys and DB credentials are stored in `config.php` (replace placeholders with your own values).  

---

## Tech Stack
| Layer | Technology |
|-------|------------|
| Backend | PHP 7.4+ |
| Database | MySQL (see `Database/weather_db.sql`) |
| Front‑end | HTML5, CSS3 (`css/style.css`) |
| API | OpenWeatherMap (or any compatible weather API) |
| Server | Apache / Nginx (LAMP stack) |

---

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/weather_forecast_final.git
   cd weather_forecast_final
   ```

2. **Create the database**
   ```bash
   # Log into MySQL and run the script
   mysql -u root -p
   SOURCE Database/weather_db.sql;
   ```

3. **Configure the application**
   - Copy `config.php.example` to `config.php` (if an example file exists) or edit `config.php` directly.
   - Update the following placeholders:
     ```php
     // config.php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'weather_db');
     define('DB_USER', 'your_db_user');
     define('DB_PASS', 'your_db_password');

     // Weather API
     define('WEATHER_API_KEY', 'YOUR_OWN_API_KEY');
     ```
   - Ensure the web server has read/write access to the project directory.

4. **Set up the web server**
   - Place the project in your web root (e.g., `/var/www/html/weather_forecast_final`).
   - Configure Apache/Nginx to point to `index.php` as the entry point.
   - Restart the server:
     ```bash
     sudo systemctl restart apache2   # or nginx
     ```

5. **Optional: Composer dependencies**
   - If the project uses Composer (not required for core functionality), run:
     ```bash
     composer install
     ```

---

## Usage

1. Open your browser and navigate to the project URL, e.g., `http://localhost/weather_forecast_final/`.
2. **Register** a new account via the **Register** link.
3. **Log in** with your credentials.