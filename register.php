<?php
require_once 'config/db.php';
require_once 'config/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate fields
    if (!$username || !$email || !$password || !$confirm_password) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();  

        if ($result->num_rows > 0) {
            $error = 'Username or email already exists';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'client')");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Nasha Mukti</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .floating {
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 15px rgba(102, 126, 234, 0.6);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }
        
        
        .password-strength {
            height: 4px;
            transition: all 0.3s ease;
        }
        
        .strength-0 { width: 0%; background-color: #ef4444; }
        .strength-1 { width: 25%; background-color: #ef4444; }
        .strength-2 { width: 50%; background-color: #f59e0b; }
        .strength-3 { width: 75%; background-color: #3b82f6; }
        .strength-4 { width: 100%; background-color: #10b981; }
    </style>
</head>
<body class="gradient-bg min-h-screen flex flex-col">
    <?php include 'includes/header.php'; ?>

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="max-w-6xl w-full grid md:grid-cols-2 gap-12 items-center">
            <!-- Left side - Illustration and text -->
            <div class="hidden md:block text-center" data-aos="fade-right">
                <div class="relative inline-block">
                    <img src="https://img.freepik.com/free-vector/people-walking-sunny-day-park_74855-5278.jpg" alt="Recovery Illustration" 
                         class="w-full max-w-md floating mx-auto rounded-lg shadow-xl">
                    <div class="absolute -bottom-8 -left-8 w-24 h-24 rounded-full bg-purple-300 opacity-20 animate-pulse"></div>
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-blue-300 opacity-20 animate-pulse delay-1000"></div>
                </div>
                
                <h2 class="text-3xl font-bold text-white mt-8">Begin Your Recovery Journey</h2>
                <p class="text-white/80 mt-4 text-lg">Join our community and take the first step towards a healthier life</p>
                
                <div class="mt-8 flex justify-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center pulse">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center pulse" style="animation-delay: 0.5s">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center pulse" style="animation-delay: 1s">
                        <i class="fas fa-heartbeat text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Registration Form -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden" data-aos="fade-left">
                <div class="p-1 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">
                    <div class="bg-white p-8 sm:p-10 rounded-2xl">
                        <div class="text-center mb-8">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-user-plus text-white text-3xl"></i>
                            </div>
                            <h2 class="text-3xl font-extrabold text-gray-900">Create Account</h2>
                            <p class="mt-2 text-gray-600">Join our recovery community</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="animate__animated animate__shakeX mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="animate__animated animate__fadeIn mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form class="space-y-6" method="POST" id="registerForm">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input id="username" name="username" type="text" required 
                                        class="input-focus pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3" 
                                        placeholder="Choose your username">
                                </div>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input id="email" name="email" type="email" required 
                                        class="input-focus pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3" 
                                        placeholder="Enter your email">
                                </div>
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input id="password" name="password" type="password" required 
                                        class="input-focus pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3" 
                                        placeholder="Create a password"
                                        oninput="checkPasswordStrength(this.value)">
                                </div>
                                <div class="mt-2 flex space-x-1">
                                    <div id="strength-bar-1" class="password-strength strength-0 rounded-l"></div>
                                    <div id="strength-bar-2" class="password-strength strength-0"></div>
                                    <div id="strength-bar-3" class="password-strength strength-0"></div>
                                    <div id="strength-bar-4" class="password-strength strength-0 rounded-r"></div>
                                </div>
                                <p id="strength-text" class="text-xs mt-1 text-gray-500">Password strength: <span class="font-medium">Weak</span></p>
                            </div>
                            
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input id="confirm_password" name="confirm_password" type="password" required 
                                        class="input-focus pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3" 
                                        placeholder="Confirm your password">
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" required>
                                <label for="terms" class="ml-2 block text-sm text-gray-700">
                                    I agree to the <a href="#" class="font-medium text-purple-600 hover:text-purple-500">Terms of Service</a> and <a href="#" class="font-medium text-purple-600 hover:text-purple-500">Privacy Policy</a>
                                </label>
                            </div>

                            <div>
                                <button type="submit" 
                                    class="btn-glow w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-user-plus mr-2"></i>Create Account
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-8 text-center">
                            <p class="text-sm text-gray-600">
                                Already have an account? 
                                <a href="login.php" class="font-medium text-purple-600 hover:text-purple-500 transition-colors">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Add some interactive animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-purple-300', 'rounded-xl');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-purple-300', 'rounded-xl');
            });
        });
        
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Complexity checks
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            // Cap at 4 for our UI
            strength = Math.min(4, Math.floor(strength/2));
            
            // Update UI
            const strengthTexts = ['Very Weak', 'Weak', 'Moderate', 'Strong', 'Very Strong'];
            const strengthColors = ['text-red-500', 'text-orange-500', 'text-yellow-500', 'text-blue-500', 'text-green-500'];
            
            document.getElementById('strength-text').innerHTML = `Password strength: <span class="font-medium ${strengthColors[strength]}">${strengthTexts[strength]}</span>`;
            
            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById(`strength-bar-${i}`);
                bar.className = `password-strength rounded${i === 1 ? '-l' : i === 4 ? '-r' : ''} ${strength >= i ? `strength-${strength}` : 'strength-0'}`;
            }
        }
    </script>
</body>
</html>