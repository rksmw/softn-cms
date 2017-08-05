<?php

use SoftnCMS\models\tables\Menu;
use SoftnCMS\controllers\ViewController;
use SoftnCMS\models\managers\MenusManager;

$siteUrlEditParentMenu = ViewController::getViewData('siteUrlEditParentMenu');
$parentMenuId          = ViewController::getViewData('parentMenuId');
$parentMenus           = ViewController::getViewData('parentMenus');
$title                 = ViewController::getViewData('title');
$menu                  = ViewController::getViewData('menu');
$method                = ViewController::getViewData('method');
$isUpdate              = $method === MenusManager::FORM_UPDATE;
?>
<div class="page-container">
    <div>
        <h1><?php echo $title;
    
            if ($isUpdate && !empty($siteUrlEditParentMenu)) {
                ?>
                <a class="btn btn-primary" href="<?php echo $siteUrlEditParentMenu; ?>" title="Volver"><span class="glyphicon glyphicon-arrow-left"></span></a>
            <?php } ?>
        </h1>
    </div>
    <div>
        <form role="form" method="post">
            <div id="content-left" class="col-sm-9">
                <div class="form-group">
                    <label class="control-label">Titulo</label>
                    <input class="form-control" type="text" name="<?php echo MenusManager::MENU_TITLE; ?>" placeholder="Escribe el título" value="<?php echo $menu->getMenuTitle(); ?>">
                </div>
                <div class="form-group">
                    <label class="control-label">Enlace</label>
                    <input class="form-control" type="url" name="<?php echo MenusManager::MENU_URL; ?>" placeholder="Enlace del menu" value="<?php echo $menu->getMenuUrl(); ?>"/>
                </div>
                <?php if (empty($parentMenus)) { ?>
                    <input type="hidden" name="<?php echo MenusManager::MENU_SUB; ?>" value="<?php echo $parentMenuId; ?>"/>
                <?php } else { ?>
                    <div class="form-group">
                        <label class="control-label">Seleccionar menu padre</label>
                        <select class="form-control" name="<?php echo MenusManager::MENU_SUB; ?>">
                            <option value="<?php echo MenusManager::MENU_SUB_PARENT; ?>">Sin padre</option>
                            <?php
                            $echo = '';
                            array_walk($parentMenus, function(Menu $parentMenu) use ($menu, &$echo) {
                                $selected = '';
                                $parentId = $parentMenu->getId();
        
                                if ($menu->getMenuSub() == $parentId) {
                                    $selected = 'selected';
                                }
        
                                $echo .= "<option value='$parentId' $selected>" . $parentMenu->getMenuTitle() . '</option>';
                            });
    
                            echo $echo;
                            ?>
                        </select>
                    </div>
                <?php } ?>
            </div>
            <div id="content-right" class="col-sm-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Publicación</div>
                    <div class="panel-body">
                        <?php if ($isUpdate) { ?>
                            <button class="btn btn-primary btn-block" type="submit" name="<?php echo MenusManager::FORM_UPDATE; ?>" value="<?php echo MenusManager::FORM_UPDATE; ?>">Actualizar</button>
                        <?php } else { ?>
                            <button class="btn btn-primary btn-block" type="submit" name="<?php echo MenusManager::FORM_CREATE; ?>" value="<?php echo MenusManager::FORM_CREATE; ?>">Publicar</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="<?php echo MenusManager::ID; ?>" value="<?php echo $menu->getId(); ?>"/>
        </form>
    </div>
</div>