# Real-time Air Quality Monitoring Dashboard for Colombo

A web-based dashboard that provides real-time air quality information for the Colombo Metropolitan Area. The dashboard visualizes data from simulated air quality sensors, allowing users to monitor Air Quality Index (AQI) levels across Colombo, explore historical trends, and understand the spatial distribution of air quality.

## Features

- **Interactive Map View**: Displays sensor locations on a map of Colombo with color-coded markers representing current AQI levels
- **Real-time Data Visualization**: Shows the latest air quality readings from multiple sensors
- **Historical Trends**: View historical AQI data over different time periods (24 hours, week, month)
- **Weather Integration**: Displays current weather conditions for Colombo using OpenWeather API
- **Administrative Panel**: Manage sensors, view system status, and control data simulation
- **Data Simulation**: Generate realistic air quality data for testing and demonstration purposes
- **Alert Thresholds**: Configure thresholds for different air quality categories and notification settings

## User Roles

### Public User
- View the public dashboard showing real-time air quality data
- Interact with the map to see sensor details
- View historical AQI trends
- Check current weather conditions

### Monitoring Admin
- All public user capabilities
- Secure login to admin panel
- Manage sensor details (add, edit, view, deactivate)
- Configure data simulation parameters
- Set alert thresholds for AQI levels

### System Administrator
- All Monitoring Admin capabilities
- Manage user accounts
- Advanced system configuration

## Technology Stack

- **Backend**: Laravel PHP Framework
- **Database**: MySQL/SQLite
- **Frontend**: HTML, CSS, JavaScript, Tailwind CSS
- **Mapping**: Leaflet.js
- **Charts**: Chart.js
- **Weather Data**: OpenWeather API

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or SQLite

### Setup Steps

1. Clone the repository:
   ```
   git clone <repository-url>
   cd air-quality-dashboard
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Create .env file from example:
   ```
   cp .env.example .env
   ```

5. Generate application key:
   ```
   php artisan key:generate
   ```

6. Configure database in .env:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. Add OpenWeather API key in .env:
   ```
   OPENWEATHER_API_KEY=your_api_key
   ```

8. Run database migrations and seeders:
   ```
   php artisan migrate --seed
   ```

9. Seed sample data (optional):
   ```
   php artisan seed:sample-data
   ```

10. Build frontend assets:
    ```
    npm run build
    ```

11. Start the development server:
    ```
    php artisan serve
    ```

12. Visit http://localhost:8000 in your browser

## Default Users

After seeding, the following users are available:

- **Admin User**:
  - Email: admin@example.com
  - Password: password

- **System Admin User**:
  - Email: system@example.com
  - Password: password

## License

This project is a coursework assignment for PUSL2020 and is intended for educational purposes only.

## Contributors

- [Your Name] - [Your Role]
- [Team Member 1] - [Role]
- [Team Member 2] - [Role]
- [Team Member 3] - [Role]
- [Team Member 4] - [Role]
- [Team Member 5] - [Role]
# weather-dashboard-demo
