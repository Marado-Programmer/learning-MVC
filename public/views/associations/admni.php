<?php if (!defined('ROOT_PATH')) exit ?>

<main>

<header>
<h1>Association Admnistrator Panel</h1>
</header>

<?php
if (!UsersManager::getTools()->getPremissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_ADMNI_NEWS,
    false
))
    require VIEWS_PATH . '/associations/news.php';

if (!UsersManager::getTools()->getPremissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_ADMNI_EVENTS,
    false
))
    require VIEWS_PATH . '/associations/events.php';

if (!UsersManager::getTools()->getPremissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_ADMNI_IMAGES,
    false
))
    require VIEWS_PATH . '/associations/images.php';
?>

</main>
