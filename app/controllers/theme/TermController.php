<?php
/**
 * TermController.php
 */

namespace SoftnCMS\controllers\theme;

use SoftnCMS\models\template\PostTemplate;
use SoftnCMS\models\managers\PostsManager;
use SoftnCMS\models\managers\PostsTermsManager;
use SoftnCMS\models\managers\TermsManager;
use SoftnCMS\models\tables\Post;
use SoftnCMS\util\controller\ThemeControllerAbstract;

/**
 * Class TermController
 * @author Nicolás Marulanda P.
 */
class TermController extends ThemeControllerAbstract {
    
    public function index($id) {
        $termsManager = new TermsManager($this->getConnectionDB());
        $term         = $termsManager->searchById($id);
        
        if (empty($term)) {
            $this->redirect();
        }
        
        $postStatus        = TRUE;
        $postsManager      = new PostsManager($this->getConnectionDB());
        $postsTermsManager = new PostsTermsManager($this->getConnectionDB());
        $count             = $postsTermsManager->countPostsByTermIdAndPostStatus($id, $postStatus);
        $limit             = $this->rowsPages($count);
        $posts             = $postsManager->searchAllByTermIdAndStatus($term->getId(), $postStatus, $limit);
        $postsTemplate     = array_map(function(Post $post) {
            return new PostTemplate($post, TRUE, $this->getRequest()
                                                      ->getSiteUrl(), $this->getConnectionDB());
        }, $posts);
        
        $this->sendDataView([
            'posts' => $postsTemplate,
            'term'  => $term,
        ]);
        $this->view();
    }
    
}
