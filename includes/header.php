<?php
// Shared header and navigation bar for authenticated USIAMS pages.

if (PHP_SAPI !== 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}

$title = isset($page_title) && $page_title !== '' ? $page_title : 'USIAMS';
$current_page = isset($active_page) && $active_page !== ''
    ? $active_page
    : basename($_SERVER['PHP_SELF'] ?? '');
$load_validation_js = !empty($include_validation_js);

$nav_items = [
    ['href' => 'dashboard.php', 'label' => 'Dashboard', 'class' => 'nav-link'],
    ['href' => 'add_student.php', 'label' => 'Add Student', 'class' => 'nav-link'],
    ['href' => 'view_students.php', 'label' => 'View Students', 'class' => 'nav-link'],
    ['href' => 'xml_view.php', 'label' => 'View XML Records', 'class' => 'nav-link'],
    ['href' => 'logout.php', 'label' => 'Logout', 'class' => 'nav-link logout'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($title); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <?php if ($load_validation_js): ?>
        <script src="js/validation.js" defer></script>
    <?php endif; ?>
</head>
<body>
    <header class="site-header">
        <nav class="navbar" aria-label="Main Navigation">
            <div class="nav-brand-group">
                <a class="nav-brand" href="dashboard.php">USIAMS</a>
                <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== ''): ?>
                    <span class="nav-user"><?php echo esc((string) $_SESSION['username']); ?></span>
                <?php endif; ?>
            </div>

            <ul class="nav-links">
                <?php foreach ($nav_items as $item): ?>
                    <?php $is_active = ($current_page === $item['href']); ?>
                    <li>
                        <a
                            href="<?php echo esc($item['href']); ?>"
                            class="<?php echo esc($item['class'] . ($is_active ? ' active' : '')); ?>"
                            <?php echo $is_active ? 'aria-current="page"' : ''; ?>
                        ><?php echo esc($item['label']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>
    <main class="container">
