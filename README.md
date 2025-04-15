# Nasha Mukti Kendra - De-Addiction Management System

## Overview

Nasha Mukti Kendra is a web-based dashboard designed to manage and monitor de-addiction centers and beneficiaries across India. It provides a centralized platform for tracking key statistics, visualizing data, and managing center/beneficiary information effectively.

The platform aims to support the Nasha Mukti initiative by offering data-driven insights, enhancing accountability, and facilitating informed decision-making for a substance-free nation.

## Key Features

*   **Dashboard:** Displays real-time statistics (Total Centers, Active Beneficiaries, Success Rate) and data visualizations (State Distribution, Addiction Types, Monthly Admissions).
*   **Center Management:** Allows adding new de-addiction centers with details like name, state, capacity, etc.
*   **Beneficiary Management:** Enables adding new beneficiaries, associating them with centers, and tracking their status (Active, Recovered, Discontinued), age, addiction type, and admission date.
*   **Records Page:** Provides a comprehensive, filterable, and searchable table view of all beneficiary records.
*   **Statistics Page:** Offers more detailed statistical analysis and charts (potentially more advanced than the dashboard).
*   **About Page:** Information about the Nasha Mukti initiative, mission, vision, and contact details.
*   **Responsive Design:** Adapts to various screen sizes (desktops, tablets, mobiles).
*   **Dark/Light Theme:** Supports user preference for light or dark mode interface.
*   **Data Visualization:** Utilizes Chart.js for interactive charts.
*   **Animations:** Subtle animations using AOS (Animate On Scroll) for enhanced user experience.

## Technology Stack

*   **Frontend:** HTML, Tailwind CSS, JavaScript, Chart.js, AOS, Font Awesome
*   **Backend:** PHP
*   **Database:** MySQL (or compatible, connection configured via `config/db.php`)
*   **Web Server:** Apache/Nginx (or any server that runs PHP)

## Setup and Installation

1.  **Prerequisites:**
    *   A web server (like Apache, Nginx) with PHP support.
    *   A MySQL database server.
    *   A web browser.

2.  **Clone the Repository (if applicable):**
    ```bash
    git clone <repository-url>
    cd nasha-mukti-kendra
    ```
    Or download the project files and place them in your web server's document root (e.g., `htdocs` for XAMPP, `www` for WAMP/MAMP).

3.  **Database Setup:**
    *   Create a new database (e.g., `nasha_mukti`).
    *   Import the provided `.sql` database schema file (if available) or manually create the necessary tables (`centers`, `beneficiaries`, `addiction_types`, `monthly_admissions`, etc.).
    *   Update the database connection details in `config/db.php`:
        ```php
        <?php
        $servername = "localhost"; // Your database host
        $username = "root"; // Your database username
        $password = ""; // Your database password
        $dbname = "nasha_mukti"; // Your database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        ?>
        ```

4.  **File Structure:** Ensure the directory structure is maintained, especially the `assets` (for CSS/JS/Images), `config`, and `includes` folders.

5.  **Access the Application:** Open your web browser and navigate to the project's URL (e.g., `http://localhost/nashamukti/`).

## Usage

*   **Index (`index.php`):** Main dashboard view.
*   **Add Center (`add_center.php`):** Form to add new centers.
*   **Add Beneficiary (`add_beneficiary.php`):** Form to add new beneficiaries.
*   **Records (`records.php`):** View and filter beneficiary data.
*   **Statistics (`statistics.php`):** View detailed statistics.
*   **About (`about.php`):** Learn more about the initiative.

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

## License

This project is licensed under the MIT License - see the LICENSE file for details (Optional: Add a LICENSE file if desired). 