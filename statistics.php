<?php
require_once 'config/db.php';

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

// Get monthly trends (last 12 months)
$sql = "SELECT DATE_FORMAT(admission_date, '%Y-%m') as month, COUNT(*) as count 
        FROM beneficiaries 
        WHERE admission_date >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
        GROUP BY month 
        ORDER BY month";
$result = $conn->query($sql);
$monthly_labels = [];
$monthly_counts = [];
while($row = $result->fetch_assoc()) {
    $monthly_labels[] = date('M Y', strtotime($row['month'].'-01'));
    $monthly_counts[] = $row['count'];
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
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-lg shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="index.php" class="text-2xl font-bold gradient-text">Nasha Mukti</a>
                    </div>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-6">
                            <a href="index.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium transition-all">Dashboard</a>
                            <a href="add_center.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium">Add Center</a>
                            <a href="add_beneficiary.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium">Add Beneficiary</a>
                            <a href="about.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium transition-all">About</a>
                            <a href="records.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium transition-all">Records</a>
                            <a href="statistics.php" class="text-primary border-b-2 border-primary px-3 py-2 text-sm font-medium">Statistics</a>
                        </div>
                    </div>
                </div>
                <div class="md:hidden">
                    <button type="button" class="text-gray-600 hover:text-primary" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="index.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Dashboard</a>
                <a href="add_center.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Add Center</a>
                <a href="add_beneficiary.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Add Beneficiary</a>
                <a href="about.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">About</a>
                <a href="records.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Records</a>
                <a href="statistics.php" class="block px-3 py-2 text-base font-medium text-primary bg-gray-50 rounded-md">Statistics</a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen pt-16 pb-12">
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
                <!-- Monthly Trends -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Monthly Admission Trends</h3>
                    <canvas id="trendChart"></canvas>
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
    </div>

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
                <p>&copy; 2023 Nasha Mukti Kendra. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Monthly Trends Chart
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthly_labels); ?>,
                datasets: [{
                    label: 'New Admissions',
                    data: <?php echo json_encode($monthly_counts); ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
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

        // Addiction Types Chart
        new Chart(document.getElementById('addictionChart'), {
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
    </script>
</body>
</html> 