<?php
require_once 'config/db.php';
require_once 'config/auth.php';

// Check if user is logged in
requireLogin();

// Get total centers
$sql = "SELECT COUNT(*) as total FROM centers";
$result = $conn->query($sql);
$total_centers = $result->fetch_assoc()['total'];

// Get total beneficiaries
$sql = "SELECT COUNT(*) as total FROM beneficiaries";
$result = $conn->query($sql);
$total_beneficiaries = $result->fetch_assoc()['total'];

// Get active beneficiaries
$sql = "SELECT COUNT(*) as total FROM beneficiaries WHERE status = 'Active'";
$result = $conn->query($sql);
$active_beneficiaries = $result->fetch_assoc()['total'];

// Get state-wise distribution
$sql = "SELECT state, COUNT(*) as count FROM centers GROUP BY state ORDER BY count DESC";
$result = $conn->query($sql);
$state_data = [];
$state_labels = [];
$state_counts = [];
while($row = $result->fetch_assoc()) {
    $state_labels[] = $row['state'];
    $state_counts[] = $row['count'];
}

// Get addiction types distribution
$sql = "SELECT addiction_type, COUNT(*) as count FROM beneficiaries GROUP BY addiction_type ORDER BY count DESC";
$result = $conn->query($sql);
$addiction_data = [];
$addiction_labels = [];
$addiction_counts = [];
while($row = $result->fetch_assoc()) {
    $addiction_labels[] = $row['addiction_type'];
    $addiction_counts[] = $row['count'];
}

// Get intervention type distribution
$sql = "SELECT intervention_type, COUNT(*) as count FROM interventions GROUP BY intervention_type ORDER BY count DESC";
$result = $conn->query($sql);
$intervention_type_labels = [];
$intervention_type_counts = [];
while($row = $result->fetch_assoc()) {
    if (!empty($row['intervention_type'])) { // Ensure type is not empty
        $intervention_type_labels[] = $row['intervention_type'];
        $intervention_type_counts[] = $row['count'];
    } 
}

// Get gender distribution
$sql = "SELECT gender, COUNT(*) as count FROM beneficiaries GROUP BY gender";
$result = $conn->query($sql);
$gender_labels = [];
$gender_counts = [];
while($row = $result->fetch_assoc()) {
    $gender_labels[] = $row['gender'];
    $gender_counts[] = $row['count'];
}

// Get age group distribution
$sql = "SELECT 
            CASE 
                WHEN age < 18 THEN 'Under 18'
                WHEN age BETWEEN 18 AND 25 THEN '18-25'
                WHEN age BETWEEN 26 AND 35 THEN '26-35'
                WHEN age BETWEEN 36 AND 45 THEN '36-45'
                ELSE 'Above 45'
            END as age_group,
            COUNT(*) as count
        FROM beneficiaries 
        GROUP BY age_group
        ORDER BY 
            CASE age_group
                WHEN 'Under 18' THEN 1
                WHEN '18-25' THEN 2
                WHEN '26-35' THEN 3
                WHEN '36-45' THEN 4
                ELSE 5
            END";
$result = $conn->query($sql);
$age_labels = [];
$age_counts = [];
while($row = $result->fetch_assoc()) {
    $age_labels[] = $row['age_group'];
    $age_counts[] = $row['count'];
}

// Get center capacity utilization
$sql = "SELECT c.name, c.capacity, COUNT(b.id) as current_occupancy
        FROM centers c
        LEFT JOIN beneficiaries b ON c.id = b.center_id AND b.status = 'Active'
        GROUP BY c.id
        ORDER BY current_occupancy DESC
        LIMIT 10";
$result = $conn->query($sql);
$center_labels = [];
$center_capacity = [];
$center_occupancy = [];
while($row = $result->fetch_assoc()) {
    $center_labels[] = $row['name'];
    $center_capacity[] = $row['capacity'];
    $center_occupancy[] = $row['current_occupancy'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Nasha Mukti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#60a5fa',
                        dark: '#1f2937'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <?php include 'includes/header.php'; ?>

    <main class="pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Overview Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <i class="fas fa-hospital text-blue-600 dark:text-blue-200 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Centers</h3>
                            <p class="text-3xl font-bold text-primary"><?php echo $total_centers; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                            <i class="fas fa-users text-green-600 dark:text-green-200 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Beneficiaries</h3>
                            <p class="text-3xl font-bold text-green-600"><?php echo $total_beneficiaries; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                            <i class="fas fa-user-check text-yellow-600 dark:text-yellow-200 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Cases</h3>
                            <p class="text-3xl font-bold text-yellow-600"><?php echo $active_beneficiaries; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- State Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">State-wise Distribution</h3>
                    <canvas id="stateChart"></canvas>
                </div>

                <!-- Addiction Types -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Addiction Types</h3>
                    <canvas id="addictionChart"></canvas>
                </div>

                <!-- Intervention Types -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Intervention Types</h3>
                    <canvas id="interventionChart"></canvas>
                </div>

                <!-- Gender Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Gender Distribution</h3>
                    <canvas id="genderChart"></canvas>
                </div>

                <!-- Age Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Age Distribution</h3>
                    <canvas id="ageChart"></canvas>
                </div>

                <!-- Center Capacity -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Center Capacity Utilization</h3>
                    <canvas id="capacityChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark dark:bg-gray-900 text-white py-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white dark:text-white">Nasha Mukti</h3>
                    <p class="text-gray-400 dark:text-gray-300">Comprehensive tracking and analysis of de-addiction centers across India.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white dark:text-white">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 dark:text-gray-300 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="add_center.php" class="text-gray-400 dark:text-gray-300 hover:text-white transition-colors">Add Center</a></li>
                        <li><a href="add_beneficiary.php" class="text-gray-400 dark:text-gray-300 hover:text-white transition-colors">Add Beneficiary</a></li>
                        <li><a href="statistics.php" class="text-gray-400 dark:text-gray-300 hover:text-white transition-colors">Statistics</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-white dark:text-white">Contact</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center text-gray-400 dark:text-gray-300"><i class="fas fa-envelope mr-2"></i> support@nashamukti.gov.in</li>
                        <li class="flex items-center text-gray-400 dark:text-gray-300"><i class="fas fa-phone mr-2"></i> 1800-123-4567</li>
                        <li class="flex items-center text-gray-400 dark:text-gray-300"><i class="fas fa-map-marker-alt mr-2"></i> New Delhi, India</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 dark:border-gray-700 mt-8 pt-8 text-center text-gray-400 dark:text-gray-300">
                <p>&copy; 2025 Nasha Mukti Kendra. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Store chart instances
        const charts = [];

        // Get theme colors based on current mode
        function getThemeColors() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                text: isDark ? '#fff' : '#111827',
                grid: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                background: isDark ? '#1f2937' : '#ffffff'
            };
        }

        // Chart.js global defaults for dark mode
        function updateChartsForTheme() {
            const colors = getThemeColors();
            charts.forEach(chart => {
                // Update axes
                if (chart.options.scales) {
                    Object.values(chart.options.scales).forEach(scale => {
                        scale.grid = scale.grid || {};
                        scale.grid.color = colors.grid;
                        scale.ticks = scale.ticks || {};
                        scale.ticks.color = colors.text;
                    });
                }
                // Update legend
                if (chart.options.plugins && chart.options.plugins.legend) {
                    chart.options.plugins.legend.labels = chart.options.plugins.legend.labels || {};
                    chart.options.plugins.legend.labels.color = colors.text;
                }
                // Update title
                if (chart.options.plugins && chart.options.plugins.title) {
                    chart.options.plugins.title.color = colors.text;
                }
                chart.update();
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize theme
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // State Distribution Chart
            charts.push(new Chart(document.getElementById('stateChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($state_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($state_counts); ?>,
                        backgroundColor: '#3b82f6', 
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                            labels: { color: '#3b82f6' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(120,120,120,0.15)' },
                            ticks: { color: '#3b82f6' }
                        },
                        x: {
                            grid: { color: 'rgba(120,120,120,0.15)' },
                            ticks: { color: '#3b82f6' }
                        }
                    }
                }
            }));

            // Addiction Types Chart
            charts.push(new Chart(document.getElementById('addictionChart'), {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($addiction_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($addiction_counts); ?>,
                        backgroundColor: [
                            '#3b82f6', 
                            '#22c55e', 
                            '#f59e42', 
                            '#ef4444', 
                            '#a855f7', 
                            '#eab308'  
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#3b82f6' }
                        }
                    }
                }
            }));

            // Intervention Types Chart
            charts.push(new Chart(document.getElementById('interventionChart'), {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($intervention_type_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($intervention_type_counts); ?>,
                        backgroundColor: [
                            '#3b82f6', 
                            '#22c55e', 
                            '#f59e42', 
                            '#ef4444', 
                            '#a855f7', 
                            '#eab308'  
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#3b82f6' }
                        }
                    }
                }
            }));

            // Gender Distribution Chart
            charts.push(new Chart(document.getElementById('genderChart'), {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($gender_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($gender_counts); ?>,
                        backgroundColor: [
                            '#3b82f6', 
                            '#ef4444', 
                            '#22c55e', 
                            '#f59e42', 
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#3b82f6' }
                        }
                    }
                }
            }));

            // Age Distribution Chart
            charts.push(new Chart(document.getElementById('ageChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($age_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($age_counts); ?>,
                        backgroundColor: '#f59e42', 
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                            labels: { color: '#3b82f6' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(120,120,120,0.15)' },
                            ticks: { color: '#3b82f6' }
                        },
                        x: {
                            grid: { color: 'rgba(120,120,120,0.15)' },
                            ticks: { color: '#3b82f6' }
                        }
                    }
                }
            }));

            // Center Capacity Chart
            charts.push(new Chart(document.getElementById('capacityChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($center_labels); ?>,
                    datasets: [
                        {
                            label: 'Current Occupancy',
                            data: <?php echo json_encode($center_occupancy); ?>,
                            backgroundColor: '#3b82f6', 
                            borderWidth: 0
                        },
                        {
                            label: 'Total Capacity',
                            data: <?php echo json_encode($center_capacity); ?>,
                            backgroundColor: '#22c55e', 
                            borderWidth: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#3b82f6' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            stacked: false,
                            grid: { color: 'rgba(120,120,120,0.15)' },
                            ticks: { color: '#3b82f6' }
                        },
                        x: {
                            stacked: false,
                            grid: { color: 'rgba(120,120,120,0.15)' },
                            ticks: { color: '#3b82f6' }
                        }
                    }
                }
            }));

            // Initial update for theme
            updateChartsForTheme();
        });

        // Listen for theme changes
        const observer = new MutationObserver(updateChartsForTheme);
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    </script>
    <script>
        // Expose chart update for theme globally
        window.updateChartsForTheme = updateChartsForTheme;
    </script>
</body>
</html>