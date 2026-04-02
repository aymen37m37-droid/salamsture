<?php
session_start();
require_once '../config.php';
require_once '../database.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM team WHERE id = ?");
    $stmt->execute([$id]);
    $success = 'تم حذف العضو بنجاح';
}

// Fetch all team members
$stmt = $pdo->query("SELECT * FROM team ORDER BY name ASC");
$team = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الفريق - لوحة التحكم</title>
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
        
        .team-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .team-table th,
        .team-table td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        
        .team-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }
        
        .team-table tr:hover {
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
            .team-table {
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
                <li><a href="team.php" style="background-color: #34495e;">إدارة الفريق</a></li>
                <li><a href="about.php">إدارة المحتوى</a></li>
                <li><a href="contact.php">إدارة الرسائل</a></li>
                <li><a href="settings.php">الإعدادات</a></li>
                <li><a href="logout.php">تسجيل الخروج</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="admin-container">
        <div class="page-header">
            <h2>إدارة الفريق</h2>
            <a href="team-form.php" class="btn btn-primary">إضافة عضو جديد</a>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (count($team) > 0): ?>
            <table class="team-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم الكامل</th>
                        <th>الوظيفة</th>
                        <th>البريد الإلكتروني</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($team as $index => $member): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($member['name']); ?></td>
                            <td><?php echo htmlspecialchars($member['position']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td class="actions">
                                <a href="team-form.php?id=<?php echo $member['id']; ?>" class="btn btn-edit">تعديل</a>
                                <a href="team.php?delete=<?php echo $member['id']; ?>" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا العضو؟')">حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>لا توجد أعضاء في الفريق حالياً. ابدأ بإضافة عضو جديد.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; 2026 شركة السلام للعقارات. جميع الحقوق محفوظة.</p>
    </footer>
</body>
</html>