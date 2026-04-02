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
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
    $success = 'تم حذف الرسالة بنجاح';
}

// Handle status update
if (isset($_GET['status']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $newStatus = $_GET['status'] == 'Answered' ? 'Answered' : 'In Review';
    $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);
    $success = 'تم تحديث حالة الرسالة';
}

// Fetch all contact messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY contact_date DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الرسائل - لوحة التحكم</title>
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
        
        .btn-view {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-view:hover {
            background-color: #229954;
        }
        
        .messages-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .messages-table th,
        .messages-table td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        
        .messages-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }
        
        .messages-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: bold;
        }
        
        .status-new {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-review {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-answered {
            background-color: #d4edda;
            color: #155724;
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
            .messages-table {
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
                <li><a href="about.php">إدارة المحتوى</a></li>
                <li><a href="contact.php" style="background-color: #34495e;">إدارة الرسائل</a></li>
                <li><a href="settings.php">الإعدادات</a></li>
                <li><a href="logout.php">تسجيل الخروج</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="admin-container">
        <div class="page-header">
            <h2>إدارة الرسائل</h2>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (count($messages) > 0): ?>
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الموضوع</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $index => $message): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($message['contact_date'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php 
                                    echo $message['status'] == 'New' ? 'new' : 
                                        ($message['status'] == 'In Review' ? 'review' : 'answered'); 
                                ?>">
                                    <?php echo $message['status'] == 'New' ? 'جديد' : 
                                        ($message['status'] == 'In Review' ? 'قيد المراجعة' : 'مُجابة'); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="contact-view.php?id=<?php echo $message['id']; ?>" class="btn btn-view">عرض</a>
                                <a href="contact.php?status=Answered&id=<?php echo $message['id']; ?>" class="btn btn-edit">تحديد كمُجابة</a>
                                <a href="contact.php?delete=<?php echo $message['id']; ?>" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>لا توجد رسائل حالياً.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; 2026 شركة السلام للعقارات. جميع الحقوق محفوظة.</p>
    </footer>
</body>
</html>