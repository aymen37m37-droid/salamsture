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
$project = [
    'name' => '',
    'location' => '',
    'status' => 'Pending',
    'description' => '',
    'price' => '',
    'added_date' => date('Y-m-d')
];

// Check if editing existing project
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $isEdit = true;
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        header('Location: projects.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $status = $_POST['status'];
    $description = trim($_POST['description']);
    $price = !empty($_POST['price']) ? (float)$_POST['price'] : null;
    $added_date = $_POST['added_date'];
    
    // Handle image uploads
    $images = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadDir = '../' . UPLOAD_DIR . 'projects/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    $images[] = UPLOAD_DIR . 'projects/' . $fileName;
                }
            }
        }
    }
    
    if ($isEdit) {
        // Update existing project
        $existingImages = $project['images'] ? json_decode($project['images'], true) : [];
        $allImages = array_merge($existingImages, $images);
        
        $stmt = $pdo->prepare("UPDATE projects SET name = ?, location = ?, status = ?, description = ?, price = ?, added_date = ?, images = ? WHERE id = ?");
        $stmt->execute([
            $name, $location, $status, $description, $price, $added_date, 
            json_encode($allImages), $_GET['id']
        ]);
        $success = 'تم تحديث المشروع بنجاح';
    } else {
        // Insert new project
        $stmt = $pdo->prepare("INSERT INTO projects (name, location, status, description, price, added_date, images) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $name, $location, $status, $description, $price, $added_date, 
            json_encode($images)
        ]);
        $success = 'تم إضافة المشروع بنجاح';
        $isEdit = true;
        $_GET['id'] = $pdo->lastInsertId();
    }
    
    // Refresh project data
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'تعديل' : 'إضافة'; ?> مشروع - لوحة التحكم</title>
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
                <li><a href="contact.php">إدارة الرسائل</a></li>
                <li><a href="settings.php">الإعدادات</a></li>
                <li><a href="logout.php">تسجيل الخروج</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="form-container">
        <h2><?php echo $isEdit ? 'تعديل مشروع' : 'إضافة مشروع جديد'; ?></h2>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">اسم المشروع *</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($project['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="location">الموقع *</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($project['location']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="status">الحالة *</label>
                <select id="status" name="status" required>
                    <option value="Pending" <?php echo $project['status'] == 'Pending' ? 'selected' : ''; ?>>قيد التنفيذ</option>
                    <option value="Completed" <?php echo $project['status'] == 'Completed' ? 'selected' : ''; ?>>مكتمل</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">السعر (ريال)</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $project['price']; ?>">
            </div>
            
            <div class="form-group">
                <label for="added_date">تاريخ الإضافة *</label>
                <input type="date" id="added_date" name="added_date" value="<?php echo $project['added_date']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">الوصف *</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($project['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>صور المشروع</label>
                <div class="image-upload-area" onclick="document.getElementById('images').click()">
                    <p>اضغط هنا لرفع صور المشروع</p>
                    <p style="font-size: 0.875rem; color: #666;">يمكن رفع عدة صور في مرة واحدة</p>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" style="display: none;">
                </div>
                
                <?php 
                $existingImages = $project['images'] ? json_decode($project['images'], true) : [];
                if (count($existingImages) > 0): 
                ?>
                <div class="image-preview">
                    <?php foreach ($existingImages as $imgIndex => $imgPath): ?>
                        <div class="preview-item">
                            <img src="../<?php echo htmlspecialchars($imgPath); ?>" alt="صورة المشروع">
                            <button type="button" class="remove-img" onclick="removeExistingImage(<?php echo $imgIndex; ?>)">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'تحديث' : 'إضافة'; ?> المشروع</button>
                <a href="projects.php" class="btn btn-secondary">إلغاء</a>
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