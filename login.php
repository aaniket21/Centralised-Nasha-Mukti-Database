<?php
require_once 'config/db.php';
require_once 'config/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nasha Mukti</title>
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
        
    </style>
</head>
<body class="gradient-bg min-h-screen flex flex-col">
    <?php include 'includes/header.php'; ?>

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="max-w-6xl w-full grid md:grid-cols-2 gap-12 items-center">
            <!-- Left side - Illustration and text -->
            <div class="hidden md:block text-center" data-aos="fade-right">
                <div class="relative inline-block">
                    <img src="https://illustrations.popsy.co/amber/digital-nomad.svg" alt="Recovery Illustration" 
                         class="w-full max-w-md floating mx-auto">
                    <div class="absolute -bottom-8 -left-8 w-24 h-24 rounded-full bg-purple-300 opacity-20 animate-pulse"></div>
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-blue-300 opacity-20 animate-pulse delay-1000"></div>
                </div>
                
                <h2 class="text-3xl font-bold text-white mt-8">Welcome Back to Nasha Mukti</h2>
                <p class="text-white/80 mt-4 text-lg">Your journey to recovery starts with a single step</p>
                
                <div class="mt-8 flex justify-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center pulse">
                        <i class="fas fa-heart text-white text-xl"></i>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center pulse" style="animation-delay: 0.5s">
                        <i class="fas fa-hands-helping text-white text-xl"></i>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center pulse" style="animation-delay: 1s">
                        <i class="fas fa-smile-beam text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Login Form -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden" data-aos="fade-left">
                <div class="p-1 bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">
                    <div class="bg-white p-8 sm:p-10 rounded-2xl">
                        <div class="text-center mb-8">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-user-lock text-white text-3xl"></i>
                            </div>
                            <h2 class="text-3xl font-extrabold text-gray-900">Login</h2>
                            <p class="mt-2 text-gray-600">Access your recovery dashboard</p>
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
                        
                        <form class="space-y-6" method="POST">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input id="username" name="username" type="text" required 
                                        class="input-focus pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 py-3" 
                                        placeholder="Enter your username">
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
                                        placeholder="Enter your password">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                                </div>
                                <!-- <div class="text-sm">
                                    <a href="#" class="font-medium text-purple-600 hover:text-purple-500">Forgot password?</a>
                                </div> -->
                            </div>

                            <div>
                                <button type="submit" 
                                    class="btn-glow w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Sign in
                                </button>
                            </div>
                        </form>
                        
                        <!-- <div class="mt-8">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                                </div>
                            </div> -->

                            <!-- <div class="mt-6 grid grid-cols-2 gap-3">
                                <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all hover:-translate-y-1">
                                    <i class="fab fa-google text-red-500 mr-2"></i> Google
                                </a>
                                <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all hover:-translate-y-1">
                                    <i class="fab fa-facebook-f text-blue-600 mr-2"></i> Facebook
                                </a>
                            </div> -->
                            
                            <div class="mt-8 text-center">
                                <p class="text-sm text-gray-600">
                                    Don't have an account? 
                                    <a href="register.php" class="font-medium text-purple-600 hover:text-purple-500 transition-colors">
                                        Create one now
                                    </a>
                                </p>
                            </div>
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
    </script>
</body>
</html>