<?php
require_once 'config/db.php';
require_once 'config/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    die('No record ID specified.');
}
$id = intval($_GET['id']);
$sql = "SELECT b.*, c.name as center_name, c.state FROM beneficiaries b JOIN centers c ON b.center_id = c.id WHERE b.id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    die('Record not found.');
}
$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Beneficiary Record</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .gradient-text { background: linear-gradient(45deg, #1e40af, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
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
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 dark:from-[#1e2761] dark:to-[#2b3990] text-white py-10 mb-10">
            <div class="max-w-2xl mx-auto px-4">
                <h1 class="text-3xl font-bold text-white" data-aos="fade-up">Beneficiary Details</h1>
                <p class="mt-2 text-blue-100" data-aos="fade-up" data-aos-delay="100">Full details of the selected beneficiary.</p>
            </div>
        </div>
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md p-8">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-6">
                <tr><td class="py-2 font-semibold text-gray-700 dark:text-gray-200">Name:</td><td class="py-2 text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['name']); ?></td></tr>
                <tr><td class="py-2 font-semibold text-gray-700 dark:text-gray-200">Center:</td><td class="py-2 text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['center_name']); ?></td></tr>
                <tr><td class="py-2 font-semibold text-gray-700 dark:text-gray-200">State:</td><td class="py-2 text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['state']); ?></td></tr>
                <tr><td class="py-2 font-semibold text-gray-700 dark:text-gray-200">Status:</td><td class="py-2 text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['status']); ?></td></tr>
                <tr><td class="py-2 font-semibold text-gray-700 dark:text-gray-200">Addiction Type:</td><td class="py-2 text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['addiction_type']); ?></td></tr>
                <tr><td class="py-2 font-semibold text-gray-700 dark:text-gray-200">Admission Date:</td><td class="py-2 text-gray-900 dark:text-white"><?php echo htmlspecialchars($row['admission_date']); ?></td></tr>
                <!-- Add more fields as needed -->
            </table>
            <a href="records.php" class="inline-block bg-primary text-white px-5 py-2 rounded hover:bg-blue-700 transition-colors"><i class="fas fa-arrow-left mr-2"></i>Back to Records</a>
        </div>
    </main>
    <script>
        AOS.init({ duration: 800, once: true });
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>
