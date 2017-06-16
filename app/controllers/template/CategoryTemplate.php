<?php
/**
 * CategoryTemplate.php
 */

namespace SoftnCMS\controllers\template;

use SoftnCMS\controllers\Template;
use SoftnCMS\models\managers\CategoriesManager;
use SoftnCMS\models\managers\PostsManager;
use SoftnCMS\models\tables\Category;
use SoftnCMS\models\tables\Post;

/**
 * Class CategoryTemplate
 * @author Nicolás Marulanda P.
 */
class CategoryTemplate extends Template {
    
    /** @var Category */
    private $category;
    
    /** @var array */
    private $posts;
    
    /**
     * CategoryTemplate constructor.
     *
     * @param Category $category
     * @param bool     $initRelationShip
     */
    public function __construct(Category $category = NULL, $initRelationShip = FALSE) {
        $this->category = $category;
        $this->posts    = [];
        
        if ($initRelationShip) {
            $this->initRelationship();
        }
    }
    
    public function initRelationship() {
        $this->initPosts();
    }
    
    private function initPosts() {
        $postsManager = new PostsManager();
        $this->posts  = $postsManager->searchByCategoryId($this->category->getId());
        $this->posts  = array_map(function(Post $post) {
            return new PostTemplate($post);
        }, $this->posts);
    }
    
    public function initCategory($categoryId) {
        $categoriesManager = new CategoriesManager();
        $this->category    = $categoriesManager->searchById($categoryId);
        
        if (empty($this->category)) {
            throw new \Exception("La categoría no existe.");
        }
    }
    
    /**
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }
    
    /**
     * @param Category $category
     */
    public function setCategory($category) {
        $this->category = $category;
    }
    
    /**
     * @return array
     */
    public function getPosts() {
        return $this->posts;
    }
    
}
