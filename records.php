<?php
require_once 'config/db.php';
require_once 'config/auth.php';

// Check if user is logged in and is an admin
requireAdmin();

// Initialize filters
$state_filter = isset($_GET['state']) ? $_GET['state'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$addiction_filter = isset($_GET['addiction_type']) ? $_GET['addiction_type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get all states for filter
$states_query = "SELECT DISTINCT state FROM centers ORDER BY state";
$states_result = $conn->query($states_query);
$states = [];
while ($row = $states_result->fetch_assoc()) {
    $states[] = $row['state'];
}

// Get all addiction types for filter
$addiction_types_query = "SELECT DISTINCT name FROM addiction_types ORDER BY name";
$addiction_types_result = $conn->query($addiction_types_query);
$addiction_types = [];
while ($row = $addiction_types_result->fetch_assoc()) {
    $addiction_types[] = $row['name'];
}

// Build the query with filters
$query = "SELECT b.*, c.name as center_name, c.state 
          FROM beneficiaries b 
          JOIN centers c ON b.center_id = c.id 
          WHERE 1=1";

if ($state_filter) {
    $query .= " AND c.state = '" . $conn->real_escape_string($state_filter) . "'";
}
if ($status_filter) {
    $query .= " AND b.status = '" . $conn->real_escape_string($status_filter) . "'";
}
if ($addiction_filter) {
    $query .= " AND b.addiction_type = '" . $conn->real_escape_string($addiction_filter) . "'";
}
if ($search) {
    $query .= " AND (b.name LIKE '%" . $conn->real_escape_string($search) . "%' 
                OR c.name LIKE '%" . $conn->real_escape_string($search) . "%')";
}

$query .= " ORDER BY b.admission_date DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records - Nasha Mukti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 dark:from-[#1e2761] dark:to-[#2b3990] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold" data-aos="fade-up">Beneficiary Records</h1>
                <p class="mt-2 text-blue-100" data-aos="fade-up" data-aos-delay="100">
                    Comprehensive database of all beneficiaries across our centers.
                </p>
            </div>
        </div>

        <!-- Filters and Table -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
                <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State</label>
                        <select name="state" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All States</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?php echo htmlspecialchars($state); ?>" 
                                    <?php echo $state === $state_filter ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($state); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="Active" <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active</option>
                            <option value="Recovered" <?php echo $status_filter === 'Recovered' ? 'selected' : ''; ?>>Recovered</option>
                            <option value="Discontinued" <?php echo $status_filter === 'Discontinued' ? 'selected' : ''; ?>>Discontinued</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Addiction Type</label>
                        <select name="addiction_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <?php foreach ($addiction_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" 
                                    <?php echo $type === $addiction_filter ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Beneficiary</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Center</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Addiction Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Admission Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 dark:text-blue-200"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['name']); ?></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Age: <?php echo $row['age']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['center_name']); ?></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($row['state']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                        <?php echo htmlspecialchars($row['addiction_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php 
                                            switch($row['status']) {
                                                case 'Active':
                                                    echo 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200';
                                                    break;
                                                case 'Recovered':
                                                    echo 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200';
                                                    break;
                                                default:
                                                    echo 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200';
                                            }
                                        ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?php echo date('d M Y', strtotime($row['admission_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-blue-600 dark:text-blue-200 hover:text-blue-900 transition-colors">View</a>
                                    <a href="#" class="text-green-600 dark:text-green-200 hover:text-green-900 transition-colors">Edit</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
        AOS.init({
            duration: 800,
            once: true
        });

        // Initialize theme on page load
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>