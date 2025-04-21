<?php
require_once 'config/db.php';
require_once 'config/auth.php';

// Check if user is logged in
requireAdmin();

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $capacity = $_POST['capacity'] ?? 0;

    if (!empty($name) && !empty($address) && !empty($state) && !empty($city) && !empty($phone) && !empty($capacity)) {
        $sql = "INSERT INTO centers (name, address, state, city, contact_person, phone, email, capacity) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $capacityInt = (int)$capacity;
        $stmt->bind_param("sssssssi", $name, $address, $state, $city, $contact_person, $phone, $email, $capacityInt);
        
        if ($stmt->execute()) {
            $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative' role='alert'>Center added successfully!</div>";
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative' role='alert'>Please fill in all required fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Center - Nasha Mukti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
        /* Dark-mode styling for inputs and selects */
        .dark .input-group input,
        .dark .input-group select,
        .dark .input-group textarea {
            background-color: #374151 !important; /* gray-700 */
            color: #e5e7eb !important; /* gray-200 */
            border-color: #4b5563 !important; /* gray-600 */
        }
        .dark .input-group label {
            background-color: #374151;
            color: #e5e7eb; /* gray-200 */
            padding: 0 0.25rem;
        }
        .form-card {
            transition: all 0.3s ease;
        }
        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .input-group input, .input-group select, .input-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            outline: none;
            transition: all 0.3s ease;
        }
        .input-group input:focus, .input-group select:focus, .input-group textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .input-group label {
            position: absolute;
            left: 1rem;
            top: -0.5rem;
            background: white;
            padding: 0 0.25rem;
            color: #6b7280;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        .submit-button {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .submit-button:hover {
            transform: translateY(-2px);
        }
        .submit-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 60%);
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.5s ease;
        }
        .submit-button:hover::after {
            transform: translate(-50%, -50%) scale(2);
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .floating-icon {
            animation: float 3s ease-in-out infinite;
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
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <?php include 'includes/header.php'; ?>

    <main class="pt-20">
        <div class="min-h-screen pt-20 pb-12 bg-white dark:bg-gray-800 transition-colors duration-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8 animate__animated animate__fadeIn">
                    <h1 class="text-3xl font-bold gradient-text inline-block mb-2">Add New Center</h1>
                    <p class="text-gray-600 dark:text-gray-400">Register a new rehabilitation center to our network</p>
                </div>

                <div class="bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-2xl shadow-xl p-8 form-card animate__animated animate__fadeInUp transition-colors duration-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="text-center md:text-left">
                            <div class="floating-icon mb-6">
                                <i class="fas fa-hospital-alt text-6xl text-primary opacity-80"></i>
                            </div>
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Center Information</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">Add details about the rehabilitation center to help people find the right care.</p>
                            <div class="hidden md:block">
                                <div class="bg-blue-50 dark:bg-gray-700 rounded-lg p-4 mt-6">
                                    <h3 class="text-primary font-medium mb-2">Center Guidelines</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Ensure the center meets all regulatory requirements and has proper facilities for rehabilitation care.</p>
                                </div>
                            </div>
                        </div>

                        <form method="POST" class="space-y-6" id="centerForm">
                            <?php
                            if (!empty($message)) {
                                echo "<div class='mb-4 col-span-full'>" . $message . "</div>";
                            }
                            ?>
                            <div class="input-group">
                                <label for="name">Center Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="input-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" name="city" required>
                                </div>
                                <div class="input-group">
                                    <label for="state">State</label>
                                    <select id="state" name="state" required>
                                        <option value="">Select State</option>
                                        <!-- States -->
                                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                                        <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                        <option value="Assam">Assam</option>
                                        <option value="Bihar">Bihar</option>
                                        <option value="Chhattisgarh">Chhattisgarh</option>
                                        <option value="Goa">Goa</option>
                                        <option value="Gujarat">Gujarat</option>
                                        <option value="Haryana">Haryana</option>
                                        <option value="Himachal Pradesh">Himachal Pradesh</option>
                                        <option value="Jharkhand">Jharkhand</option>
                                        <option value="Karnataka">Karnataka</option>
                                        <option value="Kerala">Kerala</option>
                                        <option value="Madhya Pradesh">Madhya Pradesh</option>
                                        <option value="Maharashtra">Maharashtra</option>
                                        <option value="Manipur">Manipur</option>
                                        <option value="Meghalaya">Meghalaya</option>
                                        <option value="Mizoram">Mizoram</option>
                                        <option value="Nagaland">Nagaland</option>
                                        <option value="Odisha">Odisha</option>
                                        <option value="Punjab">Punjab</option>
                                        <option value="Rajasthan">Rajasthan</option>
                                        <option value="Sikkim">Sikkim</option>
                                        <option value="Tamil Nadu">Tamil Nadu</option>
                                        <option value="Telangana">Telangana</option>
                                        <option value="Tripura">Tripura</option>
                                        <option value="Uttar Pradesh">Uttar Pradesh</option>
                                        <option value="Uttarakhand">Uttarakhand</option>
                                        <option value="West Bengal">West Bengal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="address">Complete Address</label>
                                <textarea id="address" name="address" rows="3" required></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="input-group">
                                    <label for="phone">Contact Number</label>
                                    <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
                                </div>
                                <div class="input-group">
                                    <label for="capacity">Bed Capacity</label>
                                    <input type="number" id="capacity" name="capacity" min="1" required>
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="facilities">Available Facilities</label>
                                <textarea id="facilities" name="facilities" rows="3" required 
                                    placeholder="e.g., Medical Staff, Counseling, Recreation, etc."></textarea>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <button type="button" onclick="window.location.href='index.php'" 
                                    class="px-6 py-2 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-400 rounded-full hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" class="submit-button px-8 py-2 bg-primary text-white rounded-full hover:bg-secondary transition-all">
                                    Add Center
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark dark:bg-gray-900 text-white dark:text-gray-100 py-8 transition-colors duration-200">
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
                <!-- <div>
                    <h3 class="text-lg font-semibold mb-4">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                    </ul>
                </div> -->
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
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation and submission
            document.getElementById('centerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Add loading state to submit button
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
                submitBtn.disabled = true;

                // Simulate form submission delay for better UX
                setTimeout(() => {
                    this.submit();
                }, 500);
            });

            // Animate form fields on focus
            const formFields = document.querySelectorAll('.input-group input, .input-group select, .input-group textarea');
            formFields.forEach(field => {
                field.addEventListener('focus', function() {
                    this.parentElement.querySelector('label').classList.add('text-primary');
                });
                field.addEventListener('blur', function() {
                    this.parentElement.querySelector('label').classList.remove('text-primary');
                });
            });
        });
    </script>
</body>
</html>