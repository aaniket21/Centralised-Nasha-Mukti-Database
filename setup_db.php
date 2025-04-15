<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Nasha Mukti</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Nasha Mukti - Database Setup</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <?php
            require_once 'config/db.php';

            // Create addiction_types table
            $sql = "CREATE TABLE IF NOT EXISTS addiction_types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                count INT DEFAULT 0
            )";
            $conn->query($sql);

            // Create monthly_admissions table
            $sql = "CREATE TABLE IF NOT EXISTS monthly_admissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                month VARCHAR(20),
                year INT,
                count INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $conn->query($sql);

            // Insert sample addiction types data
            $addiction_types = [
                ['Alcohol', 45],
                ['Tobacco', 30],
                ['Drugs', 20],
                ['Others', 5]
            ];

            foreach ($addiction_types as $type) {
                $sql = "INSERT INTO addiction_types (name, count) 
                        SELECT * FROM (SELECT '{$type[0]}', {$type[1]}) AS tmp 
                        WHERE NOT EXISTS (SELECT name FROM addiction_types WHERE name = '{$type[0]}')";
                $conn->query($sql);
            }

            // Insert sample monthly admissions data
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $admissions = [1250, 1900, 1700, 2100, 2300, 1950];

            foreach ($months as $i => $month) {
                $sql = "INSERT INTO monthly_admissions (month, year, count) 
                        SELECT * FROM (SELECT '$month', 2024, {$admissions[$i]}) AS tmp 
                        WHERE NOT EXISTS (
                            SELECT month FROM monthly_admissions 
                            WHERE month = '$month' AND year = 2024
                        )";
                $conn->query($sql);
            }

            // Insert sample state distribution data
            $states = [
                ['Maharashtra', 215],
                ['Delhi', 180],
                ['Karnataka', 145],
                ['Tamil Nadu', 132],
                ['Uttar Pradesh', 128],
                ['West Bengal', 98]
            ];

            foreach ($states as $state) {
                $sql = "UPDATE centers SET state = '{$state[0]}' LIMIT {$state[1]}";
                $conn->query($sql);
            }

            echo "Additional tables and sample data created successfully!";
            ?>

            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <h2 class="text-xl font-semibold text-blue-800 mb-2">Setup Complete!</h2>
                <p class="text-blue-600">Your database has been set up successfully. You can now:</p>
                <ul class="list-disc list-inside mt-2 text-blue-600">
                    <li>Add new rehabilitation centers</li>
                    <li>Register beneficiaries</li>
                    <li>Track interventions</li>
                </ul>
                <div class="mt-4">
                    <a href="index.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Go to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 