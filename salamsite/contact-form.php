<?php
session_start();
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'الرجاء ملء جميع الحقول المطلوبة';
    } else {
        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, contact_date, status) VALUES (?,?, ?, ?,  NOW(), 'New')");
        $stmt->execute([$name, $email, $subject, $message]);
        
        // Send confirmation email
        $to = $email;
        $from = 'admin@salamsite.com';
        $email_subject = 'شكرًا لاستفسارك - شركة السلام للعقارات';
        $email_body = "
مرحبا بكم في شركة السلام للعقارات\n\nشكرًا لاستفسارك، سيتم الرد عليك قريبًا.\n\nالاسم: $name\nالبريد الإلكتروني: $email\nالموضوع: $subject\n\nالرسالة: $message\n\nشكرًا لاختياركم لنا."
        $headers = "From: $from\r\nReply-To: $from\r\nX-Mailer: PHP/" . phpversion();
        mail($to, $email_subject, $email_body, $headers);
        
        $success = 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اتصل بنا - شركة السلام للعقارات</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .contact-form {
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
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .contact-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="contact-form">
        <h2>نموذج الاتصال</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php else: ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">الاسم الكامل *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">البريد الإلكتروني *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="subject">الموضوع *</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            
            <div class="form-group">
                <label for="message">الرسالة *</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            
            <button type="submit" class="btn">إرسال الاستفسار</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html> ?, ?, ?, NOW(), 'New')");
        $stmt->execute([$name, $email, $subject, $message]);
        
        // Send confirmation email
        $to = $email;
        $from = 'admin@salamsite.com';
        $email_subject = 'شكرًا لاستفسارك - شركة السلام للعقارات';
        $email_body = "
مرحبا بكم في شركة السلام للعقارات\n\nشكرًا لاستفسارك، سيتم الرد عليك قريبًا.\n\nالاسم: $name\nالبريد الإلكتروني: $email\nالموضوع: $subject\n\nالرسالة: $message\n\nشكرًا لاختياركم لنا."
        $headers = "From: $from\r\nReply-To: $from\r\nX-Mailer: PHP/" . phpversion();
        mail($to, $email_subject, $email_body, $headers);
        
        $success = 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اتصل بنا - شركة السلام للعقارات</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .contact-form {
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
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .contact-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="contact-form">
        <h2>نموذج الاتصال</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php else: ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">الاسم الكامل *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">البريد الإلكتروني *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="subject">الموضوع *</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            
            <div class="form-group">
                <label for="message">الرسالة *</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            
            <button type="submit" class="btn">إرسال الاستفسار</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>