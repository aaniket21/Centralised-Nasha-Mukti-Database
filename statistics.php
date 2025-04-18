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
    <title>Statistics Dashboard - Nasha Mukti</title>
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
        .dark {
            background-color: #111827;
            color: #fff;
        }
    </style>
    <script>
        tailwind.config = {
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
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Statistics Dashboard</h1>
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-primary">
                            <i class="fas fa-hospital text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Centers</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo $total_centers; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Total Beneficiaries</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo $total_beneficiaries; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-user-check text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500">Active Cases</p>
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo $active_beneficiaries; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Changed: Intervention Types Distribution (was Monthly Trends) -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Intervention Types Distribution</h3>
                    <canvas id="interventionTypeChart"></canvas>
                </div>

                <!-- Addiction Types -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Addiction Types Distribution</h3>
                    <canvas id="addictionChart"></canvas>
                </div>

                <!-- State Distribution -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">State-wise Center Distribution</h3>
                    <canvas id="stateChart"></canvas>
                </div>

                <!-- Gender Distribution -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Gender Distribution</h3>
                    <canvas id="genderChart"></canvas>
                </div>

                <!-- Age Distribution -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Age Group Distribution</h3>
                    <canvas id="ageChart"></canvas>
                </div>

                <!-- Center Capacity -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Center Capacity Utilization</h3>
                    <canvas id="capacityChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Nasha Mukti</h3>
                    <p class="text-gray-400">Comprehensive tracking and analysis of de-addiction centers across India.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="add_center.php" class="text-gray-400 hover:text-white transition-colors">Add Center</a></li>
                        <li><a href="add_beneficiary.php" class="text-gray-400 hover:text-white transition-colors">Add Beneficiary</a></li>
                        <li><a href="statistics.php" class="text-gray-400 hover:text-white transition-colors">Statistics</a></li>
                    </ul>
                </div>
                <!-- <div>
                    <h3 class="text-lg font-semibold mb-4">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                    </ul>
                </div> -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center text-gray-400"><i class="fas fa-envelope mr-2"></i> support@nashamukti.gov.in</li>
                        <li class="flex items-center text-gray-400"><i class="fas fa-phone mr-2"></i> 1800-123-4567</li>
                        <li class="flex items-center text-gray-400"><i class="fas fa-map-marker-alt mr-2"></i> New Delhi, India</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Nasha Mukti Kendra. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Addiction Types Chart
            const addictionCtx = document.getElementById('addictionChart').getContext('2d');
            new Chart(addictionCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($addiction_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($addiction_counts); ?>,
                        backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Intervention Types Chart (Doughnut)
            const interventionTypeCtx = document.getElementById('interventionTypeChart').getContext('2d');
            new Chart(interventionTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($intervention_type_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($intervention_type_counts); ?>,
                        backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#64748b'] // Add more colors if needed
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // State Distribution Chart
            new Chart(document.getElementById('stateChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($state_labels); ?>,
                    datasets: [{
                        label: 'Number of Centers',
                        data: <?php echo json_encode($state_counts); ?>,
                        backgroundColor: '#3b82f6'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Gender Distribution Chart
            new Chart(document.getElementById('genderChart'), {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($gender_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($gender_counts); ?>,
                        backgroundColor: ['#3b82f6', '#f472b6', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Age Distribution Chart
            new Chart(document.getElementById('ageChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($age_labels); ?>,
                    datasets: [{
                        label: 'Number of Beneficiaries',
                        data: <?php echo json_encode($age_counts); ?>,
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Center Capacity Chart
            new Chart(document.getElementById('capacityChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($center_labels); ?>,
                    datasets: [{
                        label: 'Capacity',
                        data: <?php echo json_encode($center_capacity); ?>,
                        backgroundColor: '#3b82f6'
                    }, {
                        label: 'Current Occupancy',
                        data: <?php echo json_encode($center_occupancy); ?>,
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html> 