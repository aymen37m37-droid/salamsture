<?php
// Application Configuration
define('APP_NAME', 'شركة السلام للعقارات');
define('APP_URL', 'https://salamsite.wuaze.com'); // Change to your actual domain
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Database configuration
$host = 'sql208.infinityfree.com';
$dbname = 'if0_41558062_salamsite_db';
$username = 'if0_41558062';
$password = 'QSHKuJjHBcT';
$port = '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Create tables if they don't exist
$tables = [
    "projects" => "CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        location TEXT NOT NULL,
        status ENUM('Pending', 'Completed') NOT NULL,
        images TEXT,
        description TEXT,
        price DECIMAL(10,2),
        added_date DATE
    ) ENGINE=InnoDB;",
    
    "services" => "CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        icon VARCHAR(255),
        order_by INT
    ) ENGINE=InnoDB;",
    
    "team" => "CREATE TABLE IF NOT EXISTS team (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        position VARCHAR(255),
        image_path VARCHAR(255),
        email VARCHAR(255)
    ) ENGINE=InnoDB;",
    
    "contact_messages" => "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255),
        message TEXT,
        contact_date DATETIME,
        status ENUM('New', 'In Review', 'Answered') DEFAULT 'New'
    ) ENGINE=InnoDB;",
    
    "about_content" => "CREATE TABLE IF NOT EXISTS about_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        content TEXT,
        stats TEXT,
        images TEXT
    ) ENGINE=InnoDB;",
    
    "contact_info" => "CREATE TABLE IF NOT EXISTS contact_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        address TEXT,
        phone VARCHAR(50),
        email VARCHAR(255),
        social_links TEXT
    ) ENGINE=InnoDB;",
    
    "admin_users" => "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('Admin', 'Editor') DEFAULT 'Editor',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;",
    
    "site_settings" => "CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        logo_path VARCHAR(255),
        primary_color VARCHAR(50),
        meta_description TEXT,
        keywords TEXT
    ) ENGINE=InnoDB;",
    
    "media_files" => "CREATE TABLE IF NOT EXISTS media_files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        file_path VARCHAR(255) NOT NULL,
        file_size INT,
        mime_type VARCHAR(50),
        upload_date DATETIME
    ) ENGINE=InnoDB;"
];

foreach ($tables as $tableName => $createSql) {
    $pdo->exec($createSql);
}

// Optional: Insert default admin user if not exists
$checkAdmin = $pdo->prepare("SELECT COUNT(1) FROM admin_users WHERE email = ?");
$checkAdmin->execute([ 'admin@example.com' ]);
if ($checkAdmin->fetchColumn() == 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO admin_users (email, password_hash, role) VALUES (?, ?, 'Admin')")
        ->execute(['admin@example.com', $hash]);
}

$pdo->close();
?>