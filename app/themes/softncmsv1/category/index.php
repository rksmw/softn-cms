<?php
use SoftnCMS\controllers\ViewController;

$posts    = ViewController::getViewData('posts');
$category = ViewController::getViewData('category');
?>
<main>
    <div class="alert alert-info clearfix">
        <h2>Categoría: <?php echo $category->getCategoryName(); ?></h2>
    </div>
    <?php foreach ($posts as $postTemplate) {
        $siteUrl            = $postTemplate->getSiteUrl();
        $urlPost            = $siteUrl . 'post/';
        $urlCategory        = $siteUrl . 'category/';
        $urlTerm            = $siteUrl . 'term/';
        $urlUser            = $siteUrl . 'user/';
        $post               = $postTemplate->getPost();
        $user               = $postTemplate->getUserTemplate()
                                           ->getUser();
        $termsTemplate      = $postTemplate->getTermsTemplate();
        $categoriesTemplate = $postTemplate->getCategoriesTemplate();
        $postId             = $post->getId();
        ?>
    <article id="post-<?php echo $postId; ?>" class="bg-grey">
        <header class="clearfix">
            <div class="post-title clearfix">
                <h2 class="h3">
                    <a href="<?php echo $urlPost . $postId; ?>">
                        <?php echo $post->getPostTitle(); ?>
                    </a>
                </h2>
            </div>
            <p class="meta">
                <time class="label label-primary" datetime="2015/01/22"><span class="glyphicon glyphicon-time"></span> <?php echo $post->getPostDate(); ?></time>
                <span class="glyphicon glyphicon-user"></span> Publicado por
                <a href="<?php echo $urlUser . $user->getId(); ?>"><?php echo $user->getUserName(); ?></a>/
                <span class=" glyphicon glyphicon-folder-open"></span> Archivado en
                <?php foreach ($categoriesTemplate as $categoryTemplate) {
                    $category = $categoryTemplate->getCategory();
                    ?>
                    <a class="label label-default" href="<?php echo $urlCategory . $category->getId(); ?>">
                        <?php echo $category->getCategoryName(); ?>
                    </a>
                <?php } ?>
            </p>
        </header>
        <section><?php echo $post->getPostContents(); ?></section>
        <footer>
            <p>
                Etiquetas:
                <?php foreach ($termsTemplate as $termTemplate) {
                    $term = $termTemplate->getTerm();
                    ?>
                    <a class="label label-default" href="<?php echo $urlTerm . $term->getId(); ?>">
                        <?php echo $term->getTermName(); ?>
                    </a>
                <?php } ?>
            </p>
        </footer>
    </article>
        <?php } ?>
</main>
<?php
ViewController::singleViewDirectoryViews('pagination');

