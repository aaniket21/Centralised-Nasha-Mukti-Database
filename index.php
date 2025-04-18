<?php
require_once 'config/db.php';
require_once 'config/auth.php';

// Check if user is logged in
requireLogin();

// Fetch statistics
$stats = [
    'total_centers' => 0,
    'active_beneficiaries' => 0,
    'success_rate' => 0
];

// Get total centers
$sql = "SELECT COUNT(*) as count FROM centers";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $stats['total_centers'] = $row['count'];
}

// Get active beneficiaries
$sql = "SELECT COUNT(*) as count FROM beneficiaries WHERE status = 'Active'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $stats['active_beneficiaries'] = $row['count'];
}

// Calculate success rate
$sql = "SELECT 
    (COUNT(CASE WHEN status = 'Recovered' THEN 1 END) * 100.0 / COUNT(*)) as success_rate 
    FROM beneficiaries 
    WHERE status IN ('Recovered', 'Active', 'Discontinued')";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $stats['success_rate'] = round($row['success_rate']);
}

// Get state-wise distribution
$sql = "SELECT state, COUNT(*) as count FROM centers GROUP BY state ORDER BY count DESC LIMIT 6";
$state_data = $conn->query($sql);
$states = [];
$center_counts = [];
while ($row = $state_data->fetch_assoc()) {
    $states[] = $row['state'];
    $center_counts[] = $row['count'];
}

// Get addiction types distribution
$sql = "SELECT name, count FROM addiction_types ORDER BY count DESC";
$addiction_data = $conn->query($sql);
$addiction_types = [];
$addiction_counts = [];
while ($row = $addiction_data->fetch_assoc()) {
    $addiction_types[] = $row['name'];
    $addiction_counts[] = $row['count'];
}

// Get monthly admissions
$sql = "SELECT month, count FROM monthly_admissions WHERE year = 2024 ORDER BY id ASC LIMIT 6";
$monthly_data = $conn->query($sql);
$months = [];
$admission_counts = [];
while ($row = $monthly_data->fetch_assoc()) {
    $months[] = $row['month'];
    $admission_counts[] = $row['count'];
}

// Get recent admissions
$sql = "SELECT b.*, c.name as center_name, c.state 
        FROM beneficiaries b 
        JOIN centers c ON b.center_id = c.id 
        ORDER BY b.admission_date DESC 
        LIMIT 3";
$recent_admissions = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nasha Mukti - De-Addiction Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-card {
            background: rgba(17, 24, 39, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        /* Dark mode styles */
        .dark {
            background-color: #111827;
            color: #fff;
        }
        .dark .bg-white\/80 {
            background-color: rgba(17, 24, 39, 0.8);
        }
        .dark .bg-white {
            background-color: #1f2937;
        }
        .dark .text-gray-600 {
            color: #d1d5db;
        }
        .dark .text-gray-800 {
            color: #f3f4f6;
        }
        .dark .text-gray-500 {
            color: #9ca3af;
        }
        .dark .bg-gray-50 {
            background-color: #111827;
        }
        .dark .text-dark {
            color: #f3f4f6;
        }
        .dark table thead {
            background-color: #374151;
        }
        .dark table tbody {
            background-color: #1f2937;
        }
        .dark table tbody tr:hover {
            background-color: #374151;
        }
        .dark .bg-gray-100 {
            background-color: #374151;
        }
        .theme-toggle {
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .theme-toggle:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }
        .dark .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        /* Hero section styles */
        .hero-section {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            position: relative;
            overflow: hidden;
        }
        .dark .hero-section {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        }
        .hero-text {
            color: #ffffff;
        }
        .dark .hero-text {
            color: #e2e8f0;
        }
        .hero-description {
            color: #e2e8f0;
            opacity: 0.9;
        }
        .dark .hero-description {
            color: #94a3b8;
        }
        .hero-button-primary {
            background-color: #ffffff;
            color: #1e40af;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }
        .hero-button-primary::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: -100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        .hero-button-primary:hover::after {
            left: 100%;
        }
        .hero-button-primary:hover {
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .hero-button-primary:active {
            transform: translateY(0);
        }
        .dark .hero-button-primary {
            background-color: #3b82f6;
            color: #ffffff;
        }
        .dark .hero-button-primary:hover {
            background-color: #2563eb;
        }
        .hero-button-secondary {
            border: 2px solid #ffffff;
            color: #ffffff;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }
        .hero-button-secondary::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: -100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        .hero-button-secondary:hover::after {
            left: 100%;
        }
        .hero-button-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        .hero-button-secondary:active {
            transform: translateY(0);
        }
        .dark .hero-button-secondary {
            border-color: #3b82f6;
            color: #3b82f6;
        }
        .dark .hero-button-secondary:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        .button-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.8;
        }
        .button-loading::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid;
            border-color: currentColor currentColor currentColor transparent;
            border-radius: 50%;
            animation: button-loading-spinner 0.8s linear infinite;
            left: 50%;
            margin-left: -10px;
            top: 50%;
            margin-top: -10px;
            opacity: 0;
        }
        @keyframes button-loading-spinner {
            from {
                transform: rotate(0turn);
            }
            to {
                transform: rotate(1turn);
            }
        }
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
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

        // Theme toggle functionality
        function initTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.getElementById('theme-toggle-icon').classList.remove('fa-moon');
                document.getElementById('theme-toggle-icon').classList.add('fa-sun');
            } else {
                document.documentElement.classList.remove('dark');
                document.getElementById('theme-toggle-icon').classList.remove('fa-sun');
                document.getElementById('theme-toggle-icon').classList.add('fa-moon');
            }
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
                document.getElementById('theme-toggle-icon').classList.remove('fa-sun');
                document.getElementById('theme-toggle-icon').classList.add('fa-moon');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
                document.getElementById('theme-toggle-icon').classList.remove('fa-moon');
                document.getElementById('theme-toggle-icon').classList.add('fa-sun');
            }
            updateChartColors();
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', initTheme);
    </script>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="pt-20">
        <!-- Hero Section -->
        <div class="hero-section min-h-screen pt-16">
            <div class="max-w-7xl mx-auto px-4 py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center pb-32">
                    <div data-aos="fade-right" class="space-y-8">
                        <h1 class="text-5xl font-bold leading-tight hero-text">Break Free from <span class="text-white dark:text-blue-400">Addiction</span></h1>
                        <p class="text-xl hero-description">Empowering lives through comprehensive rehabilitation and support. Join us in building a healthier, addiction-free society.</p>
                        <!-- <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="add_beneficiary.php" onclick="handleButtonClick(this, event)" class="hero-button-primary px-8 py-3 rounded-full font-medium shadow-xl hover:shadow-2xl text-center inline-flex items-center justify-center group">
                                <i class="fas fa-user-plus mr-2 transform group-hover:scale-110 transition-transform"></i>
                                <span>Add Beneficiary</span>
                            </a>
                            <a href="statistics.php" onclick="handleButtonClick(this, event)" class="hero-button-secondary px-8 py-3 rounded-full font-medium text-center inline-flex items-center justify-center group">
                                <i class="fas fa-chart-bar mr-2 transform group-hover:scale-110 transition-transform"></i>
                                <span>View Statistics</span>
                            </a>
                        </div> -->
                    </div>
                    <div data-aos="fade-left" class="hidden lg:block relative z-10">
                        <img src="assets/images/hero.png" alt="Hero Image" class="w-full max-w-md mx-auto animate-float mb-12">
                    </div>
                </div>
            </div>
            <!-- Wave Shape -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full">
                    <path fill="currentColor" class="text-gray-50 dark:text-gray-900" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="max-w-7xl mx-auto px-4 -mt-32 relative z-10 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="add_center.php" data-aos="fade-up" data-aos-delay="0" class="glass-card rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all cursor-pointer">
                    <div class="flex items-center">
                        <div class="p-4 rounded-full bg-blue-100/50 text-primary">
                            <i class="fas fa-hospital text-3xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 font-medium">Total Centers</p>
                            <div class="flex items-baseline">
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $stats['total_centers']; ?></h3>
                                <span class="ml-2 text-green-500 text-sm"><i class="fas fa-arrow-up"></i> 12%</span>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="add_beneficiary.php" data-aos="fade-up" data-aos-delay="100" class="glass-card rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all cursor-pointer">
                    <div class="flex items-center">
                        <div class="p-4 rounded-full bg-green-100/50 text-green-600">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 font-medium">Active Beneficiaries</p>
                            <div class="flex items-baseline">
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $stats['active_beneficiaries']; ?></h3>
                                <span class="ml-2 text-green-500 text-sm"><i class="fas fa-arrow-up"></i> 8%</span>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="statistics.php" data-aos="fade-up" data-aos-delay="200" class="glass-card rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all cursor-pointer">
                    <div class="flex items-center">
                        <div class="p-4 rounded-full bg-purple-100/50 text-purple-600">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 font-medium">Success Rate</p>
                            <div class="flex items-baseline">
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $stats['success_rate']; ?>%</h3>
                                <span class="ml-2 text-green-500 text-sm"><i class="fas fa-arrow-up"></i> 5%</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-8 my-8" data-aos="fade-up">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-dark mb-2">Our Commitment to a Healthier India</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Nasha Mukti Kendra is dedicated to building a substance-free nation by providing comprehensive support, rehabilitation, and awareness programs. We track progress, analyze trends, and empower communities.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-4">
                    <div class="text-primary mb-4 text-4xl">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Data-Driven Insights</h3>
                    <p class="text-gray-600">
                        Leveraging real-time data to understand addiction trends and measure the effectiveness of rehabilitation efforts.
                    </p>
                </div>
                <div class="p-4">
                    <div class="text-primary mb-4 text-4xl">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Nationwide Network</h3>
                    <p class="text-gray-600">
                        Connecting and monitoring de-addiction centers across all states to ensure standardized care and support.
                    </p>
                </div>
                <div class="p-4">
                    <div class="text-primary mb-4 text-4xl">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Empowering Recovery</h3>
                    <p class="text-gray-600">
                        Focusing on beneficiary success through accessible resources, progress tracking, and community integration.
                    </p>
                </div>
            </div>
        </div>

        <!-- How We Help & Our Impact Section -->
        <div class="bg-gray-100 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-3xl font-bold text-dark mb-3">Driving Change: How This Platform Helps</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        This centralized dashboard is more than just data; it's a strategic tool designed to enhance the effectiveness of the Nasha Mukti initiative nationwide.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1: What it Does -->
                    <div class="bg-white p-6 rounded-xl shadow-md card-hover" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-secondary mb-4 text-3xl">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Centralized Monitoring</h3>
                        <p class="text-gray-600">
                            Provides a single platform to track key performance indicators, beneficiary progress, and center activities across all participating Kendras.
                        </p>
                    </div>
                    <!-- Feature 2: How it Helps -->
                    <div class="bg-white p-6 rounded-xl shadow-md card-hover" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-secondary mb-4 text-3xl">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Informed Decision Making</h3>
                        <p class="text-gray-600">
                            Offers insights into addiction trends, program effectiveness, and resource allocation needs, enabling data-driven strategies for better outcomes.
                        </p>
                    </div>
                    <!-- Feature 3: Our Programs (Briefly) -->
                    <div class="bg-white p-6 rounded-xl shadow-md card-hover" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-secondary mb-4 text-3xl">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Supporting Diverse Programs</h3>
                        <p class="text-gray-600">
                            Supports various Kendra programs including counseling, therapy, medical aid, and rehabilitation by tracking their impact and reach effectively.
                        </p>
                    </div>
                    <!-- Feature 4: Benefits/Why Choose Us -->
                    <div class="bg-white p-6 rounded-xl shadow-md card-hover" data-aos="fade-up" data-aos-delay="400">
                        <div class="text-secondary mb-4 text-3xl">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Enhanced Accountability</h3>
                        <p class="text-gray-600">
                            Increases transparency and accountability in the de-addiction process, ensuring quality standards and effective resource utilization across the network.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">Why Use This Platform?</h2>
                    <p class="text-lg text-gray-600 max-w-4xl mx-auto">
                        Nasha Mukti Kendra provides a robust, centralized system for managing and monitoring de-addiction efforts across India. Our platform offers comprehensive tools and data insights to support centers, track beneficiary progress, and inform national strategy for a substance-free future.
                    </p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 text-center">
                    <!-- Feature 1: High Success Rate Monitoring -->
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="0">
                        <div class="bg-gray-100 rounded-full p-5 mb-4 w-24 h-24 flex items-center justify-center shadow-inner">
                            <i class="fas fa-star text-orange-500 text-4xl"></i>
                        </div>
                        <h3 class="text-md font-semibold text-gray-700">Success Rate Tracking</h3>
                    </div>
                    <!-- Feature 2: Comprehensive Support -->
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="100">
                        <div class="bg-gray-100 rounded-full p-5 mb-4 w-24 h-24 flex items-center justify-center shadow-inner">
                            <i class="fas fa-users-cog text-blue-600 text-4xl"></i>
                        </div>
                        <h3 class="text-md font-semibold text-gray-700">Center & Beneficiary Mgmt</h3>
                    </div>
                    <!-- Feature 3: In/Out Patient Care -->
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="bg-gray-100 rounded-full p-5 mb-4 w-24 h-24 flex items-center justify-center shadow-inner">
                            <i class="fas fa-procedures text-teal-500 text-4xl"></i>
                        </div>
                        <h3 class="text-md font-semibold text-gray-700">Care Status Monitoring</h3>
                    </div>
                    <!-- Feature 4: Medical Checkups/Monitoring -->
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="bg-gray-100 rounded-full p-5 mb-4 w-24 h-24 flex items-center justify-center shadow-inner">
                            <i class="fas fa-clipboard-list text-red-500 text-4xl"></i>
                        </div>
                        <h3 class="text-md font-semibold text-gray-700">Detailed Record Keeping</h3>
                    </div>
                    <!-- Feature 5: 24x7 Support -->
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="400">
                        <div class="bg-gray-100 rounded-full p-5 mb-4 w-24 h-24 flex items-center justify-center shadow-inner">
                            <i class="fas fa-headset text-purple-600 text-4xl"></i>
                        </div>
                        <h3 class="text-md font-semibold text-gray-700">Nationwide Accessibility</h3>
                    </div>
                    <!-- Feature 6: Data Integration -->
                    <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="500">
                        <div class="bg-gray-100 rounded-full p-5 mb-4 w-24 h-24 flex items-center justify-center shadow-inner">
                            <i class="fas fa-database text-green-600 text-4xl"></i>
                        </div>
                        <h3 class="text-md font-semibold text-gray-700">Centralized Data Insights</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- What We Do Section -->
        <div class="bg-white rounded-xl shadow-md p-8 my-8" data-aos="fade-up">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- State Distribution Chart -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden p-6 card-hover">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-dark">State-wise Center Distribution</h2>
                        <div class="relative">
                            <select class="appearance-none bg-gray-100 border-0 pl-3 pr-8 py-2 rounded-lg text-sm focus:ring-2 focus:ring-primary/50">
                                <option>Last 6 Months</option>
                                <option>Last Year</option>
                                <option>All Time</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="stateDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Addiction Types -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 card-hover">
                    <h2 class="text-xl font-semibold text-dark mb-6">Addiction Types</h2>
                    <div class="h-80">
                        <canvas id="addictionTypeChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Admissions -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 card-hover">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-dark">Monthly Admissions</h2>
                        <button class="text-primary text-sm font-medium hover:text-primary/80"><a href="reports.php">View Report</a></button>
                    </div>
                    <div class="h-80">
                        <canvas id="monthlyAdmissionsChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden p-6 card-hover">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-dark">Recent Admissions</h2>
                        <button class="text-primary text-sm font-medium hover:text-primary/80"><a href="records.php">View All</a></button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beneficiary</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Center</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Addiction</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($row = $recent_admissions->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                                                <div class="text-sm text-gray-500">Age: <?php echo $row['age']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-dark"><?php echo htmlspecialchars($row['center_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['state']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <?php echo htmlspecialchars($row['addiction_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $row['status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($row['admission_date'])); ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
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
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="add_center.php" class="text-gray-400 hover:text-white transition-colors">Add Center</a></li>
                        <li><a href="add_beneficiary.php" class="text-gray-400 hover:text-white transition-colors">Add Beneficiary</a></li>
                        <li><a href="statistics.php" class="text-gray-400 hover:text-white transition-colors">Statistics</a></li>
                    </ul>
                </div>
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
        AOS.init({
            duration: 800,
            once: true
        });

        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts
            const stateCtx = document.getElementById('stateDistributionChart').getContext('2d');
            const addictionCtx = document.getElementById('addictionTypeChart').getContext('2d');
            const monthlyCtx = document.getElementById('monthlyAdmissionsChart').getContext('2d');

            // State Distribution Chart
            const stateChart = new Chart(stateCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($states); ?>,
                    datasets: [{
                        label: 'Centers',
                        data: <?php echo json_encode($center_counts); ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                            labels: {
                                color: getThemeColor('text')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: getThemeColor('grid')
                            },
                            ticks: {
                                color: getThemeColor('text')
                            }
                        },
                        x: {
                            grid: {
                                color: getThemeColor('grid')
                            },
                            ticks: {
                                color: getThemeColor('text')
                            }
                        }
                    }
                }
            });

            // Addiction Type Chart
            const addictionChart = new Chart(addictionCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($addiction_types); ?>,
                    datasets: [{
                        data: <?php echo json_encode($addiction_counts); ?>,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(139, 92, 246, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: getThemeColor('text')
                            }
                        }
                    }
                }
            });

            // Monthly Admissions Chart
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($months); ?>,
                    datasets: [{
                        label: 'Admissions',
                        data: <?php echo json_encode($admission_counts); ?>,
                        fill: true,
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                            labels: {
                                color: getThemeColor('text')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: getThemeColor('grid')
                            },
                            ticks: {
                                color: getThemeColor('text')
                            }
                        },
                        x: {
                            grid: {
                                color: getThemeColor('grid')
                            },
                            ticks: {
                                color: getThemeColor('text')
                            }
                        }
                    }
                }
            });

            // Make charts available globally for theme updates
            window.stateChart = stateChart;
            window.addictionChart = addictionChart;
            window.monthlyChart = monthlyChart;
        });

        // Helper function to get theme-specific colors
        function getThemeColor(type) {
            const isDark = document.documentElement.classList.contains('dark');
            const colors = {
                text: isDark ? '#f3f4f6' : '#1f2937',
                grid: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
            };
            return colors[type];
        }

        // Update chart colors when theme changes
        function updateChartColors() {
            const textColor = getThemeColor('text');
            const gridColor = getThemeColor('grid');

            // Update State Distribution Chart
            if (window.stateChart) {
                window.stateChart.options.scales.x.ticks.color = textColor;
                window.stateChart.options.scales.y.ticks.color = textColor;
                window.stateChart.options.scales.x.grid.color = gridColor;
                window.stateChart.options.scales.y.grid.color = gridColor;
                window.stateChart.options.plugins.legend.labels.color = textColor;
                window.stateChart.update('none'); // Use 'none' to prevent animation
            }

            // Update Addiction Type Chart
            if (window.addictionChart) {
                window.addictionChart.options.plugins.legend.labels.color = textColor;
                window.addictionChart.update('none');
            }

            // Update Monthly Admissions Chart
            if (window.monthlyChart) {
                window.monthlyChart.options.scales.x.ticks.color = textColor;
                window.monthlyChart.options.scales.y.ticks.color = textColor;
                window.monthlyChart.options.scales.x.grid.color = gridColor;
                window.monthlyChart.options.scales.y.grid.color = gridColor;
                window.monthlyChart.options.plugins.legend.labels.color = textColor;
                window.monthlyChart.update('none');
            }
        }

        function handleButtonClick(button, event) {
            // Prevent default action
            event.preventDefault();
            
            // Add loading state
            button.classList.add('button-loading');
            const buttonText = button.querySelector('span');
            const originalText = buttonText.textContent;
            buttonText.style.opacity = '0';
            
            // Simulate loading (you can remove setTimeout in production)
            setTimeout(() => {
                // Remove loading state
                button.classList.remove('button-loading');
                buttonText.style.opacity = '1';
                
                // Navigate to the link
                window.location.href = button.href;
            }, 500); // Short delay for better UX
        }
    </script>
</body>
</html> 