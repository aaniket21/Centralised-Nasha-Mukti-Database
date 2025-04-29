<?php
require_once 'config/db.php';
require_once 'config/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    die('No record ID specified.');
}
$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $status = $conn->real_escape_string($_POST['status']);
    $addiction_type = $conn->real_escape_string($_POST['addiction_type']);
    // Add other fields as needed
    $sql = "UPDATE beneficiaries SET name='$name', status='$status', addiction_type='$addiction_type' WHERE id=$id";
    if ($conn->query($sql)) {
        header('Location: view_record.php?id=' . $id);
        exit;
    } else {
        $error = 'Update failed: ' . $conn->error;
    }
}
$sql = "SELECT * FROM beneficiaries WHERE id = $id";
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
    <title>Edit Beneficiary Record</title>
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
                <h1 class="text-3xl font-bold text-white" data-aos="fade-up">Edit Beneficiary Record</h1>
                <p class="mt-2 text-blue-100" data-aos="fade-up" data-aos-delay="100">Update the information for this beneficiary.</p>
            </div>
        </div>
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md p-8">
            <?php if (!empty($error)) echo '<div class="text-red-600 mb-4">' . htmlspecialchars($error) . '</div>'; ?>
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 px-3 py-2" required>
    <option value="Active" <?php echo $row['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
    <option value="Recovered" <?php echo $row['status'] === 'Recovered' ? 'selected' : ''; ?>>Recovered</option>
    <option value="Discharged" <?php echo $row['status'] === 'Discharged' ? 'selected' : ''; ?>>Discharged</option>
</select>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-1">Addiction Type</label>
                    <?php
// Fetch addiction types for dropdown
$addiction_types_query = "SELECT DISTINCT name FROM addiction_types ORDER BY name";
$addiction_types_result = $conn->query($addiction_types_query);
$addiction_types = [];
while ($type_row = $addiction_types_result->fetch_assoc()) {
    $addiction_types[] = $type_row['name'];
}
?>
<select name="addiction_type" class="w-full rounded-lg border dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 px-3 py-2" required>
    <?php foreach ($addiction_types as $type): ?>
        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $type === $row['addiction_type'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($type); ?></option>
    <?php endforeach; ?>
</select>
                </div>
                <!-- Add more fields as needed -->
                <div class="flex items-center space-x-4 mt-6">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors"><i class="fas fa-save mr-2"></i>Save Changes</button>
                    <a href="records.php" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"><i class="fas fa-times mr-2"></i>Cancel</a>
                </div>
            </form>
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
