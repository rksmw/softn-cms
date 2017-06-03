<?php
/**
 * CategoriesManager.php
 */

namespace SoftnCMS\models\managers;

use SoftnCMS\models\CRUDManagerAbstract;
use SoftnCMS\models\tables\Category;
use SoftnCMS\util\Arrays;
use SoftnCMS\util\MySQL;

/**
 * Class CategoriesManager
 * @author Nicolás Marulanda P.
 */
class CategoriesManager extends CRUDManagerAbstract {
    
    const TABLE                = 'categories';
    
    const CATEGORY_NAME        = 'category_name';
    
    const CATEGORY_DESCRIPTION = 'category_description';
    
    const CATEGORY_COUNT       = 'category_count';
    
    public function searchByPostId($postId) {
        $table                = parent::getTableWithPrefix(self::TABLE);
        $tablePostsCategories = parent::getTableWithPrefix(PostsCategoriesManager::TABLE);
        $query                = 'SELECT * ';
        $query                .= "FROM $table ";
        $query                .= 'WHERE id IN (';
        $query                .= 'SELECT category_ID ';
        $query                .= "FROM $tablePostsCategories ";
        $query                .= 'WHERE post_ID = :post_ID)';
        $this->parameterQuery(PostsCategoriesManager::POST_ID, $postId, \PDO::PARAM_INT);
        
        return parent::readData($query);
    }
    
    /**
     * @param Category $object
     */
    protected function addParameterQuery($object) {
        parent::parameterQuery(self::CATEGORY_COUNT, $object->getCategoryCount(), \PDO::PARAM_INT);
        parent::parameterQuery(self::CATEGORY_NAME, $object->getCategoryName(), \PDO::PARAM_STR);
        parent::parameterQuery(self::CATEGORY_DESCRIPTION, $object->getCategoryDescription(), \PDO::PARAM_STR);
    }
    
    protected function getTable() {
        return self::TABLE;
    }
    
    protected function buildObjectTable($result) {
        parent::buildObjectTable($result);
        $category = new Category();
        $category->setId(Arrays::get($result, self::ID));
        $category->setCategoryCount(Arrays::get($result, self::CATEGORY_COUNT));
        $category->setCategoryDescription(Arrays::get($result, self::CATEGORY_DESCRIPTION));
        $category->setCategoryName(Arrays::get($result, self::CATEGORY_NAME));
        
        return $category;
    }
    
}
