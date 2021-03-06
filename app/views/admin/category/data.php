<?php

use SoftnCMS\controllers\ViewController;
use SoftnCMS\models\tables\Category;

$categories        = ViewController::getViewData('categories');
$siteUrlUpdate     = ViewController::getViewData('siteUrl') . 'admin/category/update/';
$strTranslateName  = __('Nombre');
$strTranslatePosts = __('Entradas');
ViewController::singleViewByDirectory('pagination'); ?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th></th>
                <th><?php echo $strTranslateName; ?></th>
                <th><?php echo $strTranslatePosts; ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th></th>
                <th><?php echo $strTranslateName; ?></th>
                <th><?php echo $strTranslatePosts; ?></th>
            </tr>
        </tfoot>
        <tbody>
        <?php array_walk($categories, function(Category $category) use ($siteUrlUpdate) { ?>
            <tr>
                <td class="options">
                    <a class="btn-action-sm btn btn-primary" href="<?php echo $siteUrlUpdate . $category->getId(); ?>" title="Editar"><span class="glyphicon glyphicon-edit"></span></a>
                    <button type="button" class="btn-action-sm btn btn-danger" data-toggle="modal" data-target="#modal-data-delete" data-id="<?php echo $category->getId(); ?>" title="Borrar">
                        <span class="glyphicon glyphicon-remove-sign"></span>
                    </button>
                </td>
                <td><?php echo $category->getCategoryName(); ?></td>
                <td><?php echo $category->getCategoryPostCount(); ?></td>
            </tr>
        <?php }); ?>
        </tbody>
    </table>
</div>
<?php ViewController::singleViewByDirectory('pagination');
