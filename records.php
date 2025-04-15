<?php
require_once 'config/db.php';

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
                            <a href="records.php" class="text-primary border-b-2 border-primary px-3 py-2 text-sm font-medium">Records</a>
                            <a href="statistics.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium transition-all">Statistics</a>
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
                <a href="records.php" class="block px-3 py-2 text-base font-medium text-primary bg-gray-50 rounded-md">Records</a>
                <a href="statistics.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Statistics</a>
            </div>
        </div>
    </nav>

    <main class="pt-20">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4" data-aos="fade-up">Beneficiary Records</h1>
                    <p class="text-xl text-blue-100 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                        Comprehensive database of all beneficiaries and their recovery journey
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Search name or center...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <select name="state" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All States</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?php echo htmlspecialchars($state); ?>" 
                                        <?php echo $state_filter === $state ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($state); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="Active" <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active</option>
                            <option value="Recovered" <?php echo $status_filter === 'Recovered' ? 'selected' : ''; ?>>Recovered</option>
                            <option value="Discontinued" <?php echo $status_filter === 'Discontinued' ? 'selected' : ''; ?>>Discontinued</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Addiction Type</label>
                        <select name="addiction_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <?php foreach ($addiction_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>"
                                        <?php echo $addiction_filter === $type ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Records Table -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beneficiary</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Center</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Addiction Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['name']); ?></div>
                                            <div class="text-sm text-gray-500">Age: <?php echo $row['age']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($row['center_name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['state']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <?php echo htmlspecialchars($row['addiction_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $row['status'] == 'Active' ? 'bg-green-100 text-green-800' : 
                                            ($row['status'] == 'Recovered' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d M Y', strtotime($row['admission_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="#" class="text-green-600 hover:text-green-900">Edit</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
                <p>&copy; 2023 Nasha Mukti Kendra. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>