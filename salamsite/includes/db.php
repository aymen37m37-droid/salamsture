<?php
$db_path = __DIR__ . '/../data/database.sqlite';
if (!is_dir(dirname($db_path))) mkdir(dirname($db_path), 0755, true);
try {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    $pdo->exec('PRAGMA journal_mode = WAL;');
} catch (PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}

// ─── TABLES ───────────────────────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS slider_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    subtitle TEXT,
    image_path TEXT NOT NULL,
    link TEXT,
    order_by INTEGER DEFAULT 0,
    active INTEGER DEFAULT 1
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    location TEXT,
    status TEXT DEFAULT 'قيد التنفيذ',
    image_path TEXT,
    gallery TEXT,
    description TEXT,
    price TEXT,
    area TEXT,
    added_date TEXT DEFAULT (date('now')),
    featured INTEGER DEFAULT 0
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    icon TEXT DEFAULT 'fa-building',
    image_path TEXT,
    order_by INTEGER DEFAULT 0,
    active INTEGER DEFAULT 1
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS team (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    position TEXT,
    image_path TEXT,
    email TEXT,
    phone TEXT,
    order_by INTEGER DEFAULT 0
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT,
    phone TEXT,
    subject TEXT,
    message TEXT,
    contact_date TEXT DEFAULT (datetime('now')),
    status TEXT DEFAULT 'جديد'
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS about_content (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    content TEXT,
    vision TEXT,
    mission TEXT,
    stats TEXT,
    image_path TEXT,
    years_exp INTEGER DEFAULT 15,
    projects_count INTEGER DEFAULT 200,
    clients_count INTEGER DEFAULT 500,
    awards_count INTEGER DEFAULT 30,
    features TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS contact_info (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    address TEXT,
    phone TEXT,
    email TEXT,
    whatsapp TEXT,
    social_links TEXT,
    working_hours TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT DEFAULT 'Editor',
    created_at TEXT DEFAULT (datetime('now'))
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    site_name TEXT DEFAULT 'السلام للعقارات',
    tagline TEXT DEFAULT 'نحقق أحلامك العقارية',
    logo_path TEXT,
    primary_color TEXT DEFAULT '#b8963e',
    meta_description TEXT,
    footer_text TEXT,
    cta_title TEXT DEFAULT 'هل أنت مستعد لامتلاك عقار أحلامك؟',
    cta_subtitle TEXT DEFAULT 'تواصل معنا الآن واحصل على استشارة مجانية من فريق خبرائنا',
    cta_btn1_text TEXT DEFAULT 'تواصل معنا الآن',
    cta_btn1_link TEXT DEFAULT '/contact.php',
    cta_btn2_text TEXT DEFAULT 'استعرض المشاريع',
    cta_btn2_link TEXT DEFAULT '/projects.php'
)");

// Why Us items
$pdo->exec("CREATE TABLE IF NOT EXISTS why_us_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    icon TEXT DEFAULT 'fa-star',
    title TEXT NOT NULL,
    description TEXT,
    order_by INTEGER DEFAULT 0,
    active INTEGER DEFAULT 1
)");

// Home sections visibility
$pdo->exec("CREATE TABLE IF NOT EXISTS home_sections (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section_key TEXT UNIQUE NOT NULL,
    section_name TEXT NOT NULL,
    active INTEGER DEFAULT 1
)");

// ─── MIGRATIONS: add columns if they don't exist ───────────
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_title TEXT DEFAULT 'هل أنت مستعد لامتلاك عقار أحلامك؟'"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_subtitle TEXT DEFAULT 'تواصل معنا الآن واحصل على استشارة مجانية من فريق خبرائنا'"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn1_text TEXT DEFAULT 'تواصل معنا الآن'"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn1_link TEXT DEFAULT '/contact.php'"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn2_text TEXT DEFAULT 'استعرض المشاريع'"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE site_settings ADD COLUMN cta_btn2_link TEXT DEFAULT '/projects.php'"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE about_content ADD COLUMN features TEXT"); } catch(Exception $e) {}

// contact_info visibility columns
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_phone INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_whatsapp INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_email INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_address INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_hours INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_facebook INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_twitter INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_instagram INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_youtube INTEGER DEFAULT 1"); } catch(Exception $e) {}
try { $pdo->exec("ALTER TABLE contact_info ADD COLUMN show_linkedin INTEGER DEFAULT 1"); } catch(Exception $e) {}

// ─── SEEDS ────────────────────────────────────────────────
$check = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
if ($check == 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO admin_users (email, password_hash, role) VALUES (?, ?, 'Admin')")
        ->execute(['admin@salam.com', $hash]);
}

$check = $pdo->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
if ($check == 0) {
    $pdo->exec("INSERT INTO site_settings (site_name, tagline, primary_color) VALUES ('السلام للعقارات', 'نحقق أحلامك العقارية', '#b8963e')");
}

$check = $pdo->query("SELECT COUNT(*) FROM about_content")->fetchColumn();
if ($check == 0) {
    $default_features = json_encode(['فريق متخصص ذو خبرة واسعة','شفافية كاملة في التعاملات','ضمان أعلى معايير الجودة','دعم ما بعد البيع']);
    $pdo->exec("INSERT INTO about_content (title, content, vision, mission, years_exp, projects_count, clients_count, awards_count, features)
        VALUES (
            'من نحن - شركة السلام للعقارات',
            'شركة السلام للعقارات شركة رائدة في مجال التطوير العقاري، تأسست بهدف تقديم أفضل الحلول العقارية لعملائنا الكرام. نحن نؤمن بأن الحصول على منزل أحلامك يبدأ بالثقة والجودة.',
            'أن نكون الشركة العقارية الأولى في المنطقة من حيث الجودة والموثوقية وخدمة العملاء.',
            'تقديم خدمات عقارية متكاملة تجمع بين الاحترافية والأمانة لتحقيق رضا عملائنا.',
            15, 200, 500, 30, '" . $default_features . "'
        )");
}

$check = $pdo->query("SELECT COUNT(*) FROM contact_info")->fetchColumn();
if ($check == 0) {
    $pdo->exec("INSERT INTO contact_info (address, phone, email, whatsapp, working_hours)
        VALUES ('المملكة العربية السعودية - الرياض', '+966 50 000 0000', 'info@salam-realestate.com', '+966 50 000 0000', 'السبت - الخميس: 8 صباحاً - 6 مساءً')");
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
