<?php
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Nasha Mukti</title>
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
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 dark:from-[#1e2761] dark:to-[#2b3990] text-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4" data-aos="fade-up">About Nasha Mukti</h1>
                    <p class="text-xl text-blue-100 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                        Empowering lives through comprehensive rehabilitation and support services across India.
                    </p>
                </div>
            </div>
        </div>

        <!-- Mission & Vision -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8" data-aos="fade-right">
                    <div class="text-primary mb-4">
                        <i class="fas fa-bullseye text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Our Mission</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        To provide comprehensive support and rehabilitation services to individuals struggling with addiction, 
                        while promoting awareness and prevention in communities across India. We strive to create a society 
                        free from the harmful effects of substance abuse.
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8" data-aos="fade-left">
                    <div class="text-primary mb-4">
                        <i class="fas fa-eye text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Our Vision</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        To be the leading organization in addiction rehabilitation and recovery in India, setting standards 
                        for quality care and successful rehabilitation outcomes. We envision a future where every individual 
                        has access to the support they need to overcome addiction.
                    </p>
                </div>
            </div>
        </div>

        <!-- Key Features -->
        <div class="bg-gray-100 dark:bg-gray-800 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 dark:text-white" data-aos="fade-up">What We Offer</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-primary mb-4">
                            <i class="fas fa-hands-helping text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Professional Support</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Expert counselors and medical professionals providing comprehensive care and support.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-primary mb-4">
                            <i class="fas fa-home text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Rehabilitation Centers</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            State-of-the-art facilities across India providing safe and supportive environments.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-primary mb-4">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Community Support</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Building strong support networks and communities for sustainable recovery.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white" data-aos="fade-up">Get in Touch</h2>
                <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                    Have questions or need support? Our team is here to help you 24/7.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-primary mb-4">
                        <i class="fas fa-phone-alt text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Call Us</h3>
                    <p class="text-gray-600 dark:text-gray-300">1800-123-4567</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-primary mb-4">
                        <i class="fas fa-envelope text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Email Us</h3>
                    <p class="text-gray-600 dark:text-gray-300">support@nashamukti.gov.in</p>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-primary mb-4">
                        <i class="fas fa-map-marker-alt text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Visit Us</h3>
                    <p class="text-gray-600 dark:text-gray-300">New Delhi, India</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
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