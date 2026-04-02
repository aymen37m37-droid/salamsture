<?php
session_start();
require_once '../config.php';
require_once '../database.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle delete action for content items
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM about_content WHERE id = ?");
    $stmt->execute([$id]);
    $success = 'تم حذف المحتوى بنجاح';
}

// Fetch all about content items
$stmt = $pdo->query("SELECT * FROM about_content ORDER BY id DESC");
$abouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المحتوى - لوحة التحكم</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-edit {
            background-color: #f39c12;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #d68910;
        }
        
        .about-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .about-table th,
        .about-table td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        
        .about-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }
        
        .about-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .about-table {
                display: block;
                overflow-x: auto;
            }
            
            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>شركة السلام للعقارات</h1>
        <nav class="dashboard-nav">
            <ul>
                <li><a href="dashboard.php">لوحة التحكم</a></li>
                <li><a href="projects.php">إدارة المشاريع</a></li>
                <li><a href="services.php">إدارة الخدمات</a></li>
                <li><a href="team.php">إدارة الفريق</a></li>
                <li><a href="about.php" style="background-color: #34495e;">إدارة المحتوى</a></li>
                <li><a href="contact.php">إدارة الرسائل</a></li>
                <li><a href="settings.php">الإعدادات</a></li>
                <li><a href="logout.php">تسجيل الخروج</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="admin-container">
        <div class="page-header">
            <h2>إدارة المحتوى</h2>
            <a href="about-form.php" class="btn btn-primary">إضافة محتوى جديد</a>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (count($abouts) > 0): ?>
            <table class="about-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($abouts as $index => $about): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($about['title']); ?></td>
                            <td class="actions">
                                <a href="about-form.php?id=<?php echo $about['id']; ?>" class="btn btn-edit">تعديل</a>
                                <a href="about.php?delete=<?php echo $about['id']; ?>" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المحتوى؟')">حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>لا توجد محتويات حالياً. ابدأ بإضافة محتوى جديد.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; 2026 شركة السلام للعقارات. جميع الحقوق محفوظة.</p>
    </footer>
</body>
</html>