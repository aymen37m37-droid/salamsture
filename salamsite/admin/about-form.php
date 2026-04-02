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
$about = [
    'title' => '',
    'content' => '',
    'stats' => '',
    'images' => []
];

// Check if editing existing content
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $isEdit = true;
    $stmt = $pdo->prepare('SELECT * FROM about_content WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$about) {
        header('Location: about.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $stats = trim($_POST['stats']);
    
    // Handle image uploads
    $images = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadDir = '../' . UPLOAD_DIR . 'about/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    $images[] = UPLOAD_DIR . 'about/' . $fileName;
                }
            }
        }
    }
    
    if ($isEdit) {
        // Update existing content
        $existingImages = $about['images'] ? json_decode($about['images'], true) : [];
        $allImages = array_merge($existingImages, $images);
        
        $stmt = $pdo->prepare('UPDATE about_content SET title = ?, content = ?, stats = ?, images = ? WHERE id = ?');
        $stmt->execute([$title, $content, $stats, json_encode($allImages), $_GET['id']]);
        $success = 'تم تحديث المحتوى بنجاح';
    } else {
        // Insert new content
        $stmt = $pdo->prepare('INSERT INTO about_content (title, content, stats, images) VALUES (?,?, ?,  ?)');
        $stmt->execute([$title, $content, $stats, json_encode($images)]);
        $success = 'تم إضافة المحتوى بنجاح';
        $isEdit = true;
        $_GET['id'] = $pdo->lastInsertId();
    }
    
    // Refresh content data
    $stmt = $pdo->prepare('SELECT * FROM about_content WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'تعديل' : 'إضافة'; ?> محتوى - لوحة التحكم</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container {
            max-width: 1000px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-group textarea {
            min-height: 150px;
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
        
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .image-preview img {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            position: relative;
        }
        
        .image-preview .remove-img {
            position: absolute;
            top: 5px;
            left: 5px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 12px;
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
        
        .stats-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            margin-bottom: 0.5rem;
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
    
    <div class="form-container">
        <h2><?php echo $isEdit ? 'تعديل محتوى' : 'إضافة محتوى جديد'; ?></h2>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">العنوان *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($about['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">المحتوى *</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($about['content']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>الإحصائيات</label>
                <p style="font-size: 0.875rem; color: #666;">أدخل كل إحصائية في سطر منفصل (مثال: 50 مشروع منجز)</p>
                <textarea id="stats" name="stats" class="stats-input" required><?php echo htmlspecialchars($about['stats']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>صور المحتوى</label>
                <div class="image-upload-area" onclick="document.getElementById('images').click()">
                    <p>اضغط هنا لرفع صور المحتوى</p>
                    <p style="font-size: 0.875rem; color: #666;">يمكن رفع عدة صور في مرة واحدة</p>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" style="display: none;">
                </div>
                
                <?php 
                $existingImages = $about['images'] ? json_decode($about['images'], true) : [];
                if (count($existingImages) > 0): 
                ?>
                <div class="image-preview">
                    <?php foreach ($existingImages as $imgIndex => $imgPath): ?>
                        <div class="preview-item">
                            <img src="../<?php echo htmlspecialchars($imgPath); ?>" alt="صورة المحتوى">
                            <button type="button" class="remove-img" onclick="removeExistingImage(<?php echo $imgIndex; ?>)">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'تحديث' : 'إضافة'; ?> المحتوى</button>
                <a href="about.php" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
    
    <script>
        // Image upload preview
        document.getElementById('images').addEventListener('change', function(e) {
            const files = e.target.files;
            const preview = document.querySelector('.image-preview') || document.createElement('div');
            preview.className = 'image-preview';
            
            for (let file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                    document.querySelector('.form-container').appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
        
        function removeExistingImage(index) {
            if (confirm('هل تريد حذف هذه الصورة؟')) {
                // This would need AJAX implementation to remove from server
                alert('يجب تنفيذ حذف الصورة عبر AJAX');
            }
        }
    </script>
</body>
</html> ?, ?, ?)');
        $stmt->execute([$title, $content, $stats, json_encode($images)]);
        $success = 'تم إضافة المحتوى بنجاح';
        $isEdit = true;
        $_GET['id'] = $pdo->lastInsertId();
    }
    
    // Refresh content data
    $stmt = $pdo->prepare('SELECT * FROM about_content WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'تعديل' : 'إضافة'; ?> محتوى - لوحة التحكم</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container {
            max-width: 1000px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .form-group textarea {
            min-height: 150px;
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
        
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .image-preview img {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            position: relative;
        }
        
        .image-preview .remove-img {
            position: absolute;
            top: 5px;
            left: 5px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 12px;
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
        
        .stats-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            margin-bottom: 0.5rem;
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
    
    <div class="form-container">
        <h2><?php echo $isEdit ? 'تعديل محتوى' : 'إضافة محتوى جديد'; ?></h2>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">العنوان *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($about['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">المحتوى *</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($about['content']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>الإحصائيات</label>
                <p style="font-size: 0.875rem; color: #666;">أدخل كل إحصائية في سطر منفصل (مثال: 50 مشروع منجز)</p>
                <textarea id="stats" name="stats" class="stats-input" required><?php echo htmlspecialchars($about['stats']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>صور المحتوى</label>
                <div class="image-upload-area" onclick="document.getElementById('images').click()">
                    <p>اضغط هنا لرفع صور المحتوى</p>
                    <p style="font-size: 0.875rem; color: #666;">يمكن رفع عدة صور في مرة واحدة</p>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" style="display: none;">
                </div>
                
                <?php 
                $existingImages = $about['images'] ? json_decode($about['images'], true) : [];
                if (count($existingImages) > 0): 
                ?>
                <div class="image-preview">
                    <?php foreach ($existingImages as $imgIndex => $imgPath): ?>
                        <div class="preview-item">
                            <img src="../<?php echo htmlspecialchars($imgPath); ?>" alt="صورة المحتوى">
                            <button type="button" class="remove-img" onclick="removeExistingImage(<?php echo $imgIndex; ?>)">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'تحديث' : 'إضافة'; ?> المحتوى</button>
                <a href="about.php" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
    
    <script>
        // Image upload preview
        document.getElementById('images').addEventListener('change', function(e) {
            const files = e.target.files;
            const preview = document.querySelector('.image-preview') || document.createElement('div');
            preview.className = 'image-preview';
            
            for (let file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                    document.querySelector('.form-container').appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
        
        function removeExistingImage(index) {
            if (confirm('هل تريد حذف هذه الصورة؟')) {
                // This would need AJAX implementation to remove from server
                alert('يجب تنفيذ حذف الصورة عبر AJAX');
            }
        }
    </script>
</body>
</html>
