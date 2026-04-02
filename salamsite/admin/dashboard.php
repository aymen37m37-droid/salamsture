<?php
// Admin Dashboard Layout
require_once '../config.php';
require_once '../database.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch admin data
$admin_stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
$admin_stmt->execute([$_SESSION['admin_id']]);
$admin = $admin_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - شركة السلام للعقارات</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .dashboard-header h1 {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .dashboard-nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        
        .dashboard-nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .dashboard-nav a:hover {
            background-color: #34495e;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3498db;
        }
        
        .main-content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .chart-container {
            height: 300px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>شعار شركة السلام</h1>
        <nav class="dashboard-nav">
            <ul>
                <li><a href="dashboard.php">لوحة التحكم</a></li>
                <li><a href="projects.php">إدارة المشاريع</a></li>
                <li><a href="services.php">إدارة الخدمات</a></li>
                <li><a href="team.php">إدارة الفريق</a></li>
                <li><a href="about.php">إدارة المحتوى</a></li>
                <li><a href="contact.php">إدارة الرسائل</a></li>
                <li><a href="settings.php">الإعدادات</a></li>
                <li><a href="logout.php">تسجيل الخروج</a></li>
            </ul>
        </nav>
    </div>
    
    <main>
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>المشاريع</h3>
                <p>عدد المشاريع: <?php echo get_project_count(); ?></p>
            </div>
            <div class="stat-card">
                <h3>الخدمات</h3>
                <p>عدد الخدمات: <?php echo get_service_count(); ?></p>
            </div>
            <div class="stat-card">
                <h3>الفريق</h3>
                <p>عدد الأعضاء: <?php echo get_team_count(); ?></p>
            </div>
            <div class="stat-card">
                <h3>الرسائل</h3>
                <p>الرسائل الجديدة: <?php echo get_new_message_count(); ?></p>
            </div>
        </div>
        
        <div class="main-content">
            <h3>إحصائيات الموقع</h3>
            <p>عدد الزوار: <span id="visitor-count">جاري التحديث...</span></p>
            <div class="chart-container">
                سيتم تم تحميل المخطط بعداً</div>
        </div>
    </main>
    
    <footer>
        <p>© 2026 شركة السلام للعقارات. جميع الحقوق محفوظة.</p>
    </footer>
    
    <script src="../assets/js/main.js"></script>
    <script>
        // Example: Update visitor count
        document.getElementById('visitor-count').textContent = '1,234';
    </script>
</body>
</html>

<?php
// Helper functions
function get_project_count() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
}

function get_service_count() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
}

function get_team_count() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM team")->fetchColumn();
}

function get_new_message_count() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'New'")->fetchColumn();
}
?>>