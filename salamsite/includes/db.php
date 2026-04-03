<?php
// MySQL Configuration from User
$host     = 'sql101.infinityfree.com';
$dbname   = 'if0_41566500_db'; // يرجى استبدال _db بالاسم الذي أنشأته في لوحة التحكم
$username = 'if0_41566500';
$password = '3seE3xFhEPk7';

// Ensure upload directories exist
$upload_dirs = [
    __DIR__ . '/../uploads',
    __DIR__ . '/../uploads/projects',
    __DIR__ . '/../uploads/services',
    __DIR__ . '/../uploads/team',
    __DIR__ . '/../uploads/about',
    __DIR__ . '/../uploads/slider'
];
foreach ($upload_dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If database connection fails, show a helpful message
    die("خطأ في الاتصال بقاعدة بيانات MySQL: " . $e->getMessage() . "<br>يرجى التأكد من اسم قاعدة البيانات وكلمة المرور في ملف includes/db.php");
}

// ─── TABLES ───────────────────────────────────────────────
// Note: Changed INTEGER PRIMARY KEY AUTOINCREMENT to INT AUTO_INCREMENT PRIMARY KEY for MySQL
$pdo->exec("CREATE TABLE IF NOT EXISTS slider_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT,
    subtitle TEXT,
    image_path TEXT NOT NULL,
    link TEXT,
    order_by INT DEFAULT 0,
    active INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location TEXT,
    status VARCHAR(50) DEFAULT 'قيد التنفيذ',
    image_path TEXT,
    gallery TEXT,
    description TEXT,
    price TEXT,
    area TEXT,
    added_date DATE DEFAULT (CURRENT_DATE),
    featured INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fa-building',
    image_path TEXT,
    order_by INT DEFAULT 0,
    active INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255),
    image_path TEXT,
    email VARCHAR(255),
    phone VARCHAR(50),
    order_by INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    subject TEXT,
    message TEXT,
    contact_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'جديد'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT,
    content TEXT,
    vision TEXT,
    mission TEXT,
    stats TEXT,
    image_path TEXT,
    years_exp INT DEFAULT 15,
    projects_count INT DEFAULT 200,
    clients_count INT DEFAULT 500,
    awards_count INT DEFAULT 30,
    features TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(255),
    whatsapp VARCHAR(50),
    social_links TEXT,
    working_hours TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Editor',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(255) DEFAULT 'السلام للعقارات',
    tagline TEXT,
    logo_path TEXT,
    primary_color VARCHAR(10) DEFAULT '#b8963e',
    meta_description TEXT,
    footer_text TEXT,
    cta_title TEXT,
    cta_subtitle TEXT,
    cta_btn1_text TEXT,
    cta_btn1_link TEXT,
    cta_btn2_text TEXT,
    cta_btn2_link TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS why_us_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) DEFAULT 'fa-star',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_by INT DEFAULT 0,
    active INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS home_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) UNIQUE NOT NULL,
    section_name VARCHAR(255) NOT NULL,
    active INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// ─── MIGRATIONS: add columns if they don't exist ───────────
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_title TEXT"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_subtitle TEXT"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn1_text TEXT"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn1_link TEXT"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn2_text TEXT"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn2_link TEXT"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE about_content ADD COLUMN features TEXT"); } catch(Exception $e) {}

// contact_info visibility columns
$cols = ['show_phone', 'show_whatsapp', 'show_email', 'show_address', 'show_hours', 'show_facebook', 'show_twitter', 'show_instagram', 'show_youtube', 'show_linkedin'];
foreach ($cols as $col) {
    try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN $col INT DEFAULT 1"); } catch(Exception $e) {}
}

// ─── SEEDS ────────────────────────────────────────────────
$check = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
if ($check == 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO admin_users (email, password_hash, role) VALUES (?, ?, 'Admin')")
        ->execute(['admin@salamsite1.kesug.com', $hash]);
}

$check = $pdo->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
if ($check == 0) {
    $pdo->exec("INSERT INTO site_settings (site_name, tagline, primary_color) VALUES ('السلام للعقارات', 'نحقق أحلامك العقارية', '#b8963e')");
}

$check = $pdo->query("SELECT COUNT(*) FROM about_content")->fetchColumn();
if ($check == 0) {
    $default_features = json_encode(['فريق متخصص ذو خبرة واسعة','شفافية كاملة في التعاملات','ضمان أعلى معايير الجودة','دعم ما بعد البيع']);
    $pdo->prepare("INSERT INTO about_content (title, content, vision, mission, years_exp, projects_count, clients_count, awards_count, features)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")
        ->execute([
            'من نحن - شركة السلام للعقارات',
            'شركة السلام للعقارات شركة رائدة في مجال التطوير العقاري، تأسست بهدف تقديم أفضل الحلول العقارية لعملائنا الكرام. نحن نؤمن بأن الحصول على منزل أحلامك يبدأ بالثقة والجودة.',
            'أن نكون الشركة العقارية الأولى في المنطقة من حيث الجودة والموثوقية وخدمة العملاء.',
            'تقديم خدمات عقارية متكاملة تجمع بين الاحترافية والأمانة لتحقيق رضا عملائنا.',
            15, 200, 500, 30, $default_features
        ]);
}

$check = $pdo->query("SELECT COUNT(*) FROM contact_info")->fetchColumn();
if ($check == 0) {
    $pdo->exec("INSERT INTO contact_info (address, phone, email, whatsapp, working_hours)
        VALUES ('المملكة العربية السعودية - الرياض', '+966 50 000 0000', 'info@salamsite1.kesug.com', '+966 50 000 0000', 'السبت - الخميس: 8 صباحاً - 6 مساءً')");
}

$check = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
if ($check == 0) {
    $services = [
        ['بيع العقارات', 'نقدم أفضل عروض البيع العقاري بأسعار تنافسية وخدمة متميزة', 'fa-home'],
        ['تأجير العقارات', 'خدمات تأجير شاملة للمساكن والمحلات التجارية والمكاتب', 'fa-key'],
        ['إدارة الأملاك', 'ندير أملاكك باحترافية لضمان أعلى عائد استثماري', 'fa-chart-line'],
        ['الاستشارات العقارية', 'فريق من الخبراء لتقديم أفضل الاستشارات والتوجيه', 'fa-handshake'],
        ['تقييم العقارات', 'تقييم دقيق وموثوق للعقارات وفق المعايير الدولية', 'fa-search-dollar'],
        ['التطوير العقاري', 'مشاريع تطوير عقاري متكاملة بأحدث التصاميم', 'fa-city'],
    ];
    $stmt = $pdo->prepare("INSERT INTO services (name, description, icon, order_by) VALUES (?, ?, ?, ?)");
    foreach ($services as $i => $s) $stmt->execute([$s[0], $s[1], $s[2], $i + 1]);
}

$check = $pdo->query("SELECT COUNT(*) FROM why_us_items")->fetchColumn();
if ($check == 0) {
    $items = [
        ['fa-shield-alt', 'الأمانة والمصداقية', 'نلتزم بأعلى معايير الأمانة والشفافية في جميع تعاملاتنا مع عملائنا', 1],
        ['fa-award',      'الجودة المضمونة',    'نقدم أعلى معايير الجودة في جميع مشاريعنا وخدماتنا العقارية', 2],
        ['fa-users',      'فريق متخصص',         'لدينا فريق من الخبراء المتخصصين في مجال العقارات لخدمتكم', 3],
        ['fa-headset',    'دعم مستمر',           'نوفر دعماً متواصلاً لعملائنا قبل وبعد إتمام الصفقة العقارية', 4],
    ];
    $stmt = $pdo->prepare("INSERT INTO why_us_items (icon,title,description,order_by) VALUES (?,?,?,?)");
    foreach ($items as $it) $stmt->execute($it);
}

$check = $pdo->query("SELECT COUNT(*) FROM home_sections")->fetchColumn();
if ($check == 0) {
    $sections = [
        ['slider',   'قسم السلايدر (الصور المتحركة)', 1],
        ['stats',    'قسم الإحصائيات والأرقام',        1],
        ['services', 'قسم الخدمات',                    1],
        ['projects', 'قسم المشاريع المميزة',            1],
        ['about',    'قسم من نحن (معاينة)',             1],
        ['whyus',    'قسم لماذا نحن',                   1],
        ['cta',      'قسم الدعوة للتواصل (CTA)',        1],
    ];
    $stmt = $pdo->prepare("INSERT INTO home_sections (section_key,section_name,active) VALUES (?,?,?)");
    foreach ($sections as $s) $stmt->execute($s);
}
