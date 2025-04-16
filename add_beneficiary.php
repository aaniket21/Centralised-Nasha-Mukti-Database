<?php
require_once 'config/db.php';
require_once 'update_stats.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $center_id = $_POST['center_id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $addiction_type = $_POST['addiction_type'];
    $admission_date = $_POST['admission_date'];
    $status = 'Active';

    // Start transaction
    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO beneficiaries (center_id, name, age, gender, address, phone, addiction_type, admission_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssss", $center_id, $name, $age, $gender, $address, $phone, $addiction_type, $admission_date, $status);
        
        if ($stmt->execute()) {
            // Update statistics
            updateAllStats($addiction_type, $admission_date);
            
            // Commit transaction
            $conn->commit();
            $message = "Beneficiary added successfully!";
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}

// Get list of centers for dropdown
$centers = [];
$sql = "SELECT id, name, city FROM centers ORDER BY name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $centers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Beneficiary - Nasha Mukti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-minimal@4/minimal.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        .input-group input, .input-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            outline: none;
            transition: all 0.3s ease;
            background-color: white;
        }
        .input-group input:focus, .input-group select:focus {
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
            z-index: 1;
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
        .select2-container {
            width: 100% !important;
        }
        .select2-container .select2-selection--single {
            height: 45px !important;
            padding: 0.5rem !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            background-color: white !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px !important;
        }
        .select2-dropdown {
            border: 2px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            margin-top: 4px;
        }
        .hidden {
            display: none;
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
                            <a href="add_beneficiary.php" class="text-primary border-b-2 border-primary px-3 py-2 text-sm font-medium">Add Beneficiary</a>
                            <a href="about.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium transition-all">About</a>
                            <a href="records.php" class="text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary px-3 py-2 text-sm font-medium transition-all">Records</a>
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
                <a href="add_beneficiary.php" class="block px-3 py-2 text-base font-medium text-primary bg-gray-50 rounded-md">Add Beneficiary</a>
                <a href="about.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">About</a>
                <a href="records.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Records</a>
                <a href="statistics.php" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-primary hover:bg-gray-50 rounded-md">Statistics</a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen pt-20 pb-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 animate__animated animate__fadeIn">
                <h1 class="text-3xl font-bold gradient-text inline-block mb-2">Add New Beneficiary</h1>
                <p class="text-gray-600">Help someone start their journey to recovery</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 form-card animate__animated animate__fadeInUp">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="text-center md:text-left">
                        <div class="floating-icon mb-6">
                            <i class="fas fa-user-plus text-6xl text-primary opacity-80"></i>
                        </div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Beneficiary Details</h2>
                        <p class="text-gray-600 mb-4">Please fill in the details carefully. All information will be kept confidential.</p>
                        <div class="hidden md:block">
                            <div class="bg-blue-50 rounded-lg p-4 mt-6">
                                <h3 class="text-primary font-medium mb-2">Why this matters?</h3>
                                <p class="text-sm text-gray-600">Accurate information helps us provide better care and support to those in need. Your contribution makes a difference.</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" class="space-y-6" id="beneficiaryForm">
                        <div class="input-group">
                            <label for="center_id">Center</label>
                            <select name="center_id" id="center_id" class="select2" required>
                                <option value="">Select Center</option>
                                <?php
                                $sql = "SELECT id, name FROM centers ORDER BY name";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='".$row['id']."'>".$row['name']."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="input-group">
                                <label for="age">Age</label>
                                <input type="number" id="age" name="age" min="1" max="120" required>
                            </div>
                            <div class="input-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>

                        <div class="input-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
                        </div>

                        <div class="input-group">
                            <label for="addiction_type">Addiction Type</label>
                            <select id="addiction_type" name="addiction_type" required onchange="toggleOtherAddiction()">
                                <option value="">Select Type</option>
                                <option value="Alcohol">Alcohol</option>
                                <option value="Drugs">Drugs</option>
                                <option value="Tobacco">Tobacco</option>
                                <option value="Multiple">Multiple</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div id="other_addiction_group" class="input-group hidden">
                            <label for="other_addiction">Specify Addiction Type</label>
                            <input type="text" id="other_addiction" name="other_addiction" placeholder="Please specify the addiction type">
                        </div>

                        <div class="input-group">
                            <label for="admission_date">Admission Date</label>
                            <input type="date" id="admission_date" name="admission_date" required>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="window.location.href='index.php'" 
                                class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-full hover:bg-gray-50 transition-all">
                                Cancel
                            </button>
                            <button type="submit" class="submit-button px-8 py-2 bg-primary text-white rounded-full hover:bg-secondary transition-all">
                                Add Beneficiary
                            </button>
                        </div>
                    </form>
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

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
                width: '100%',
                dropdownParent: $('#beneficiaryForm')
            });

            // Form validation and submission
            $('#beneficiaryForm').on('submit', function(e) {
                e.preventDefault();
                
                // Check if Other addiction type is selected but not specified
                if ($('#addiction_type').val() === 'Other' && !$('#other_addiction').val().trim()) {
                    alert('Please specify the addiction type');
                    $('#other_addiction').focus();
                    return;
                }

                // Update addiction type if "Other" is selected
                if ($('#addiction_type').val() === 'Other') {
                    $('#addiction_type').val($('#other_addiction').val());
                }

                // Add loading state to submit button
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Adding...');
                submitBtn.prop('disabled', true);

                // Simulate form submission delay for better UX
                setTimeout(() => {
                    this.submit();
                }, 500);
            });

            // Animate form fields on focus
            $('.input-group input, .input-group select').on('focus', function() {
                $(this).parent().find('label').addClass('text-primary');
            }).on('blur', function() {
                $(this).parent().find('label').removeClass('text-primary');
            });
        });

        function toggleOtherAddiction() {
            const addictionType = document.getElementById('addiction_type');
            const otherAddictionGroup = document.getElementById('other_addiction_group');
            const otherAddictionInput = document.getElementById('other_addiction');

            if (addictionType.value === 'Other') {
                otherAddictionGroup.classList.remove('hidden');
                otherAddictionInput.required = true;
            } else {
                otherAddictionGroup.classList.add('hidden');
                otherAddictionInput.required = false;
                otherAddictionInput.value = '';
            }
        }

        <?php if($message): ?>
            Swal.fire({
                title: '<?php echo strpos($message, "Error") !== false ? "Error!" : "Success!" ?>',
                text: '<?php echo $message ?>',
                icon: '<?php echo strpos($message, "Error") !== false ? "error" : "success" ?>',
                confirmButtonColor: '#1e40af'
            });
        <?php endif; ?>
    </script>
</body>
</html> 