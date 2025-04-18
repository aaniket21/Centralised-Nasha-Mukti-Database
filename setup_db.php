<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Nasha Mukti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * {
            font-family: 'Inter', sans-serif;
        }
        .gradient-text {
            background: linear-gradient(45deg, #1e40af, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold gradient-text mb-8">Nasha Mukti - Database Setup</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <?php
            require_once 'config/db.php';
            require_once 'config/auth.php';

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

            echo "<div class='p-4 bg-green-100 text-green-700 rounded-lg mb-6'>
                    <i class='fas fa-check-circle mr-2'></i> Database tables created successfully!
                  </div>";
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
                    <a href="index.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-home mr-2"></i>Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 