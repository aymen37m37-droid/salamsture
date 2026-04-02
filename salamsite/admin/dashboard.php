<?php
$admin_title = 'لوحة التحكم';
$admin_icon = 'tachometer-alt';
require_once __DIR__ . '/../includes/admin-check.php';

$cnt_projects  = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$cnt_services  = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$cnt_team      = $pdo->query("SELECT COUNT(*) FROM team")->fetchColumn();
$cnt_messages  = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status='جديد'")->fetchColumn();
$cnt_sliders   = $pdo->query("SELECT COUNT(*) FROM slider_images WHERE active=1")->fetchColumn();

$recent_msg = $pdo->query("SELECT * FROM contact_messages ORDER BY contact_date DESC LIMIT 5")->fetchAll();
$recent_projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC LIMIT 5")->fetchAll();
include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-icon gold"><i class="fas fa-images"></i></div>
        <div class="admin-stat-info"><strong><?php echo $cnt_sliders; ?></strong><span>عروض الرئيسية</span></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon blue"><i class="fas fa-building"></i></div>
        <div class="admin-stat-info"><strong><?php echo $cnt_projects; ?></strong><span>المشاريع</span></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon green"><i class="fas fa-concierge-bell"></i></div>
        <div class="admin-stat-info"><strong><?php echo $cnt_services; ?></strong><span>الخدمات</span></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon purple"><i class="fas fa-users"></i></div>
        <div class="admin-stat-info"><strong><?php echo $cnt_team; ?></strong><span>أعضاء الفريق</span></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon red"><i class="fas fa-envelope"></i></div>
        <div class="admin-stat-info"><strong><?php echo $cnt_messages; ?></strong><span>رسائل جديدة</span></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fas fa-envelope"></i> آخر الرسائل</h3>
        <a href="/admin/messages.php" class="btn btn-sm btn-gold">عرض الكل</a>
    </div>
    <div class="admin-card-body" style="padding:0;">
        <?php if (empty($recent_msg)): ?>
        <div class="empty-state"><i class="fas fa-inbox"></i><p>لا توجد رسائل</p></div>
        <?php else: ?>
        <table class="admin-table">
            <thead><tr><th>الاسم</th><th>الموضوع</th><th>التاريخ</th><th>الحالة</th></tr></thead>
            <tbody>
            <?php foreach ($recent_msg as $m): ?>
            <tr>
                <td><?php echo htmlspecialchars($m['name']); ?></td>
                <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($m['subject'] ?? '-'); ?></td>
                <td style="font-size:12px;"><?php echo substr($m['contact_date'],0,10); ?></td>
                <td><span class="badge <?php echo $m['status']=='جديد'?'badge-red':'badge-green'; ?>"><?php echo htmlspecialchars($m['status']); ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3><i class="fas fa-building"></i> آخر المشاريع</h3>
        <a href="/admin/projects.php" class="btn btn-sm btn-gold">عرض الكل</a>
    </div>
    <div class="admin-card-body" style="padding:0;">
        <?php if (empty($recent_projects)): ?>
        <div class="empty-state"><i class="fas fa-building"></i><p>لا توجد مشاريع</p></div>
        <?php else: ?>
        <table class="admin-table">
            <thead><tr><th>الصورة</th><th>الاسم</th><th>الموقع</th><th>الحالة</th></tr></thead>
            <tbody>
            <?php foreach ($recent_projects as $p): ?>
            <tr>
                <td><?php if (!empty($p['image_path']) && file_exists(__DIR__ . '/../' . $p['image_path'])): ?><img src="/<?php echo htmlspecialchars($p['image_path']); ?>" alt=""><?php else: ?><div style="width:50px;height:40px;background:#eee;border-radius:4px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-building" style="color:#ccc;"></i></div><?php endif; ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['location'] ?? '-'); ?></td>
                <td><span class="badge badge-gold"><?php echo htmlspecialchars($p['status']); ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

</div>

<div class="admin-card" style="margin-top:20px;">
    <div class="admin-card-header">
        <h3><i class="fas fa-rocket"></i> إجراءات سريعة</h3>
    </div>
    <div class="admin-card-body">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="/admin/slider.php?action=add" class="btn btn-gold"><i class="fas fa-plus"></i> إضافة عرض جديد</a>
            <a href="/admin/project-form.php" class="btn btn-dark"><i class="fas fa-plus"></i> إضافة مشروع</a>
            <a href="/admin/service-form.php" class="btn btn-blue"><i class="fas fa-plus"></i> إضافة خدمة</a>
            <a href="/admin/team-form.php" class="btn btn-green"><i class="fas fa-plus"></i> إضافة عضو فريق</a>
            <a href="/admin/messages.php" class="btn btn-red"><i class="fas fa-envelope"></i> رسائل التواصل</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
