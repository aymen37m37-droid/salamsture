<?php
session_start();
require_once '../config.php';
require_once '../database.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$isEdit = false;
$member = [
    'name' => '',
    'position' => '',
    'email' => '',
    'phone' => '',
    'bio' => '',
    'image_path' => ''
];

// Check if editing existing team member
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $isEdit = true;
    $stmt = $pdo->prepare('SELECT * FROM team WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$member) {
        header('Location: team.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    
    // Handle image upload
    $imagePath = $member['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../' . UPLOAD_DIR . 'team/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            $imagePath = UPLOAD_DIR . 'team/' . $fileName;
        }
    }
    
    if ($isEdit) {
        // Update existing member
        $stmt = $pdo->prepare('UPDATE team SET name = ?, position = ?, email = ?, phone = ?, bio = ?, image_path = ? WHERE id = ?');
        $stmt->execute([$name, $position, $email, $phone, $bio, $imagePath, $_GET['id']]);
        $success = 'تم تحديث بيانات العضو بنجاح';
    } else {
        // Insert new member
        $stmt = $pdo->prepare('INSERT INTO team (name, position, email, phone, bio, image_path) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $position, $email, $phone, $bio, $imagePath]);
        $success = 'تم إضافة العضو بنجاح';
        $isEdit = true;
        $_GET['id'] = $pdo->lastInsertId();
    }
    
    // Refresh member data
    $stmt = $pdo->prepare('SELECT * FROM team WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'تعديل' : 'إضافة'; ?> عضو فريق - لوحة التحكم</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .image-upload-area {
            border: 2px dashed #ddd;
            padding: 2rem;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .image-upload-area:hover {
            border-color: #3498db;
        }
        
        .current-image {
            margin-top: 1rem;
        }
        
        .current-image img {
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
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
    
    <div class="form-container">
        <h2><?php echo $isEdit ? 'تعديل بيانات' : 'إضافة'; ?> عضو فريق</h2>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">الاسم الكامل *</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="position">المنصب الوظيفي *</label>
                <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($member['position']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">رقم الهاتف</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>">
            </div>
            
            <div class="form-group">
                <label for="bio">نبذة مختصرة عن العضو</label>
                <textarea id="bio" name="bio"><?php echo htmlspecialchars($member['bio']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>صورة العضو</label>
                <div class="image-upload-area" onclick="document.getElementById('image').click()">
                    <p>اضغط هنا لرفع صورة العضو</p>
                    <p style="font-size: 0.875rem; color: #666;">صورة بحجم مناسب (مربعة مثالية)</p>
                    <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                </div>
                
                <?php if ($member['image_path']): ?>
                <div class="current-image">
                    <p>الصورة الحالية:</p>
                    <img src="../<?php echo htmlspecialchars($member['image_path']); ?>" alt="صورة العضو">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'تحديث' : 'إضافة'; ?> العضو</button>
                <a href="team.php" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
    
    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.current-image') || document.createElement('div');
                    preview.className = 'current-image';
                    preview.innerHTML = '<p>الصورة المختارة:</p><img src="' + e.target.result + '" alt="صورة العضو">';
                    document.querySelector('.form-container').appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>