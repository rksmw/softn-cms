<?php
/**
 * PageController.php
 */

namespace SoftnCMS\controllers\theme;

use SoftnCMS\classes\constants\Constants;
use SoftnCMS\models\template\PageTemplate;
use SoftnCMS\models\managers\CommentsManager;
use SoftnCMS\models\managers\LoginManager;
use SoftnCMS\models\managers\PagesManager;
use SoftnCMS\models\managers\UsersManager;
use SoftnCMS\models\tables\Comment;
use SoftnCMS\util\controller\ThemeControllerAbstract;
use SoftnCMS\util\form\builders\InputAlphanumericBuilder;
use SoftnCMS\util\form\builders\InputEmailBuilder;
use SoftnCMS\util\form\builders\InputIntegerBuilder;
use SoftnCMS\util\Util;

/**
 * Class PageController
 * @author Nicolás Marulanda P.
 */
class PageController extends ThemeControllerAbstract {
    
    public function index($id) {
        $pagesManager = new PagesManager($this->getConnectionDB());
        $page         = $pagesManager->searchByIdAndStatus($id, TRUE);
        
        if (empty($page)) {
            $this->redirect();
        }
        
        $this->comment();
        $this->sendDataView(['page' => new PageTemplate($page, TRUE)]);
        $this->view();
    }
    
    private function comment() {
        if ($this->checkSubmit(Constants::FORM_SUBMIT) && $this->isValidForm()) {
            $commentsManager = new CommentsManager($this->getConnectionDB());
            $comment         = $this->getForm('comment');
            
            if ($commentsManager->create($comment)) {
                //TODO: mensaje
            }
        }
    }
    
    protected function formToObject() {
        $comment = new Comment();
        $comment->setCommentContents($this->getInput(CommentsManager::COMMENT_CONTENTS));
        $comment->setCommentAuthorEmail($this->getInput(CommentsManager::COMMENT_AUTHOR_EMAIL));
        $comment->setCommentAuthor($this->getInput(CommentsManager::COMMENT_AUTHOR));
        $comment->setCommentUserId($this->getInput(CommentsManager::COMMENT_USER_ID));
        $comment->setCommentStatus(0);
        $comment->setCommentDate(Util::dateNow());
        $comment->setPostId($this->getInput(CommentsManager::POST_ID));
        
        if (LoginManager::isLogin()) {
            $usersManager = new UsersManager($this->getConnectionDB());
            $user         = $usersManager->searchById(LoginManager::getUserId());
            $comment->setCommentAuthorEmail($user->getUserEmail());
            $comment->setCommentAuthor($user->getUserName());
        }
        
        return ['comment' => $comment];
    }
    
    protected function formInputsBuilders() {
        $isRequire = !LoginManager::isLogin();
        
        return [
            InputAlphanumericBuilder::init(CommentsManager::COMMENT_AUTHOR)
                                    ->setRequire($isRequire)
                                    ->build(),
            //commentUserID Corresponde al ID del usuario de la sesión.
            InputIntegerBuilder::init(CommentsManager::COMMENT_USER_ID)
                               ->setRequire(!$isRequire)
                               ->build(),
            InputEmailBuilder::init(CommentsManager::COMMENT_AUTHOR_EMAIL)
                             ->setRequire($isRequire)
                             ->build(),
            InputAlphanumericBuilder::init(CommentsManager::COMMENT_CONTENTS)
                                    ->build(),
            InputIntegerBuilder::init(CommentsManager::POST_ID)
                               ->build(),
        ];
    }
    
}
