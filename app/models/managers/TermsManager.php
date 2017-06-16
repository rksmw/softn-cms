<?php
/**
 * TermsManager.php
 */

namespace SoftnCMS\models\managers;

use SoftnCMS\models\CRUDManagerAbstract;
use SoftnCMS\models\tables\Post;
use SoftnCMS\models\tables\Term;
use SoftnCMS\util\Arrays;

/**
 * Class TermsManager
 * @author Nicolás Marulanda P.
 */
class TermsManager extends CRUDManagerAbstract {
    
    const TABLE            = 'terms';
    
    const TERM_NAME        = 'term_name';
    
    const TERM_DESCRIPTION = 'term_description';
    
    const TERM_COUNT       = 'term_count';
    
    /**
     * @param Term $object
     *
     * @return bool
     */
    public function update($object) {
        $object = $this->checkName($object);
        
        return parent::update($object);
    }
    
    /**
     * @param Term $object
     *
     * @return Term
     */
    private function checkName($object) {
        $name    = $object->getTermName();
        $id      = $object->getId();
        $newName = $name;
        $num     = 0;
        
        while ($this->nameExists($newName, $id)) {
            $newName = $name . ++$num;
        }
        
        $object->setTermName($newName);
        
        return $object;
    }
    
    /**
     * @param string $name
     * @param int    $id
     *
     * @return bool
     */
    private function nameExists($name, $id) {
        parent::parameterQuery(self::TERM_NAME, $name, \PDO::PARAM_STR);
        $result = parent::searchBy(self::TERM_NAME);
        
        //Si el "id" es el mismo, estamos actualizando.
        return $result !== FALSE && $result->getId() != $id;
    }
    
    /**
     * @param Term $object
     *
     * @return bool
     */
    public function create($object) {
        $object = $this->checkName($object);
        
        return parent::create($object);
    }
    
    /**
     * @param array $posts
     *
     * @return array
     */
    public function searchByPosts($posts) {
        $postsId = array_map(function(Post $post) {
            return $post->getId();
        }, $posts);
        
        $where = array_map(function($postId) {
            $columnPostId = PostsTermsManager::POST_ID;
            $param        = $columnPostId . "_$postId";
            parent::parameterQuery($param, $postId, \PDO::PARAM_INT);
            
            return "$columnPostId = :$param";
        }, $postsId);
        
        $tablePostsTerms = parent::getTableWithPrefix(PostsTermsManager::TABLE);
        $query           = 'SELECT * ';
        $query           .= 'FROM ' . parent::getTableWithPrefix();
        $query           .= ' WHERE ' . self::ID . ' IN ';
        $query           .= '(SELECT ' . PostsTermsManager::TERM_ID;
        $query           .= " FROM $tablePostsTerms ";
        $query           .= 'WHERE ' . implode(' OR ', $where);
        $query           .= ')';
        
        return parent::readData($query);
    }
    
    public function searchByPostId($postId) {
        $columnPostId    = PostsTermsManager::POST_ID;
        $tablePostsTerms = parent::getTableWithPrefix(PostsTermsManager::TABLE);
        $query           = 'SELECT * ';
        $query           .= 'FROM ' . parent::getTableWithPrefix();
        $query           .= ' WHERE ' . self::ID . ' IN ';
        $query           .= '(SELECT ' . PostsTermsManager::TERM_ID;
        $query           .= " FROM $tablePostsTerms ";
        $query           .= "WHERE $columnPostId = :$columnPostId)";
        $this->parameterQuery($columnPostId, $postId, \PDO::PARAM_INT);
        
        return parent::readData($query);
    }
    
    /**
     * @param Term $object
     */
    protected function addParameterQuery($object) {
        parent::parameterQuery(self::TERM_NAME, $object->getTermName(), \PDO::PARAM_STR);
        parent::parameterQuery(self::TERM_DESCRIPTION, $object->getTermDescription(), \PDO::PARAM_STR);
        parent::parameterQuery(self::TERM_COUNT, $object->getTermCount(), \PDO::PARAM_INT);
    }
    
    protected function getTable() {
        return self::TABLE;
    }
    
    protected function buildObjectTable($result) {
        parent::buildObjectTable($result);
        $term = new Term();
        $term->setId(Arrays::get($result, self::ID));
        $term->setTermName(Arrays::get($result, self::TERM_NAME));
        $term->setTermDescription(Arrays::get($result, self::TERM_DESCRIPTION));
        $term->setTermCount(Arrays::get($result, self::TERM_COUNT));
        
        return $term;
    }
    
}
