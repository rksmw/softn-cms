<?php

use SoftnCMS\controllers\ViewController;

ViewController::registerScript('pagination');
ViewController::registerScript('delete-data');
$siteUrl = \SoftnCMS\rute\Router::getSiteURL() . "admin/user/";
?>
<div class="page-container" data-menu-collapse-id="user" data-url="<?php echo $siteUrl; ?>">
    <div>
        <h1>Usuarios <a href="<?php echo $siteUrl . 'create'; ?>" class="btn btn-success">Nuevo usuario</a></h1>
    </div>
    <div id="data-container">
        <?php ViewController::singleView('data'); ?>
    </div>
</div>