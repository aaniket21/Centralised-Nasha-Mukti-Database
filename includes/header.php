<?php
require_once __DIR__ . '/../config/auth.php';

// Get current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
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
                        <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary'; ?> px-3 py-2 text-sm font-medium transition-all">Dashboard</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="add_beneficiary.php" class="<?php echo $current_page == 'add_beneficiary.php' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary'; ?> px-3 py-2 text-sm font-medium transition-all">Add Beneficiary</a>
                            <?php if (isAdmin()): ?>
                                <a href="add_center.php" class="<?php echo $current_page == 'add_center.php' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary'; ?> px-3 py-2 text-sm font-medium transition-all">Add Center</a>
                                <a href="records.php" class="<?php echo $current_page == 'records.php' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary'; ?> px-3 py-2 text-sm font-medium transition-all">Records</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="about.php" class="<?php echo $current_page == 'about.php' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary'; ?> px-3 py-2 text-sm font-medium transition-all">About</a>
                        <a href="statistics.php" class="<?php echo $current_page == 'statistics.php' ? 'text-primary border-b-2 border-primary' : 'text-gray-600 hover:text-primary hover:border-b-2 hover:border-primary'; ?> px-3 py-2 text-sm font-medium transition-all">Statistics</a>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="toggleTheme()" class="theme-toggle">
                    <i id="theme-toggle-icon" class="fas fa-moon w-6 h-6 text-gray-600 dark:text-gray-300"></i>
                </button>
                <?php if (isLoggedIn()): ?>
                    <span class="text-gray-600 mr-2">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-red-700 transition-all shadow-lg shadow-red-500/30">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="bg-primary text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="register.php" class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-green-700 transition-all shadow-lg shadow-green-500/30">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                <?php endif; ?>
                <div class="md:hidden">
                    <button type="button" class="text-gray-600 hover:text-primary" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="index.php" class="block px-3 py-2 text-base font-medium <?php echo $current_page == 'index.php' ? 'text-primary bg-blue-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?> rounded-md">Dashboard</a>
            <?php if (isLoggedIn()): ?>
                <a href="add_center.php" class="block px-3 py-2 text-base font-medium <?php echo $current_page == 'add_center.php' ? 'text-primary bg-blue-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?> rounded-md">Add Center</a>
                <a href="add_beneficiary.php" class="block px-3 py-2 text-base font-medium <?php echo $current_page == 'add_beneficiary.php' ? 'text-primary bg-blue-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?> rounded-md">Add Beneficiary</a>
                <?php if (isAdmin()): ?>
                    <a href="records.php" class="block px-3 py-2 text-base font-medium <?php echo $current_page == 'records.php' ? 'text-primary bg-blue-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?> rounded-md">Records</a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="about.php" class="block px-3 py-2 text-base font-medium <?php echo $current_page == 'about.php' ? 'text-primary bg-blue-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?> rounded-md">About</a>
            <a href="statistics.php" class="block px-3 py-2 text-base font-medium <?php echo $current_page == 'statistics.php' ? 'text-primary bg-blue-50' : 'text-gray-600 hover:text-primary hover:bg-gray-50'; ?> rounded-md">Statistics</a>
            <?php if (isLoggedIn()): ?>
                <a href="logout.php" class="block px-3 py-2 text-base font-medium text-red-600 hover:text-red-700 hover:bg-gray-50 rounded-md">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="block px-3 py-2 text-base font-medium text-blue-600 hover:text-blue-700 hover:bg-gray-50 rounded-md">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
                <a href="register.php" class="block px-3 py-2 text-base font-medium text-green-600 hover:text-green-700 hover:bg-gray-50 rounded-md">
                    <i class="fas fa-user-plus mr-2"></i>Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('hidden');
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
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', initTheme);
</script> 