<?php
/**
 * ControllerAbstract.php
 */

namespace SoftnCMS\util\controller;

use SoftnCMS\classes\constants\OptionConstants;
use SoftnCMS\controllers\ViewController;
use SoftnCMS\models\managers\OptionsManager;
use SoftnCMS\rute\Request;
use SoftnCMS\rute\Router;
use SoftnCMS\util\Arrays;
use SoftnCMS\util\database\DBInterface;
use SoftnCMS\util\form\builders\InputAlphabeticBuilder;
use SoftnCMS\util\form\builders\InputBooleanBuilder;
use SoftnCMS\util\form\builders\InputIntegerBuilder;
use SoftnCMS\util\form\Form;
use SoftnCMS\util\Pagination;
use SoftnCMS\util\Util;

/**
 * Class ControllerAbstract
 * @author Nicolás Marulanda P.
 */
abstract class ControllerAbstract implements ControllerInterface {
    
    /** @var array */
    private $inputs;
    
    /** @var array */
    private $formObjects;
    
    /** @var bool */
    private $cancelView;
    
    /** @var DBInterface */
    private $connectionDB;
    
    /** @var Request */
    private $request;
    
    /** @var Router */
    private $router;
    
    /**
     * ControllerAbstract constructor.
     */
    public function __construct() {
        $this->formObjects  = [];
        $this->inputs       = [];
        $this->cancelView   = FALSE;
        $this->connectionDB = NULL;
        $this->router       = NULL;
        $this->request      = NULL;
    }
    
    public function reload() {
        $view             = InputAlphabeticBuilder::init('view')
                                                  ->setMethod($_GET)
                                                  ->build()
                                                  ->filter();
        $action           = InputAlphabeticBuilder::init('action')
                                                  ->setMethod($_GET)
                                                  ->build()
                                                  ->filter();
        $param            = InputIntegerBuilder::init('param')
                                               ->setMethod($_GET)
                                               ->build()
                                               ->filter();
        $this->cancelView = TRUE;
        
        if (method_exists($this, $action)) {
            call_user_func([
                $this,
                $action,
            ], $param);
            $this->singleView($view);
        } else {
            echo "ERROR";
        }
    }
    
    /**
     * @param string $viewName
     */
    protected function singleView($viewName = '') {
        ViewController::singleView($this->getViewName($viewName));
    }
    
    /**
     * @param string $viewName
     *
     * @return string
     */
    private function getViewName($viewName = '') {
        if (empty($viewName)) {
            //TODO: Buscar el primer método que este fuera del namespace "SoftnCMS\util\controller\ControllerAbstract" y quitar el "2".
            $backtrace = Arrays::get(debug_backtrace(), 2);
            $viewName  = Arrays::get($backtrace, 'function');
            
            if ($viewName === FALSE) {
                $viewName = '';
            }
        }
        
        return $viewName;
    }
    
    public function messages() {
        ViewController::singleViewByDirectory('messages');
    }
    
    /**
     * @return DBInterface
     */
    public function getConnectionDB() {
        return $this->connectionDB;
    }
    
    /**
     * @param DBInterface $connectionDB
     */
    public function setConnectionDB($connectionDB) {
        $this->connectionDB = $connectionDB;
    }
    
    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }
    
    /**
     * @param Request $request
     */
    public function setRequest($request) {
        $this->request = $request;
    }
    
    /**
     * @return Router
     */
    public function getRouter() {
        return $this->router;
    }
    
    /**
     * @param Router $router
     */
    public function setRouter($router) {
        $this->router = $router;
    }
    
    /**
     * @return array
     */
    public function getInputs() {
        return $this->inputs;
    }
    
    /**
     * @param string $actionName
     * @param array  $parametersValues
     */
    protected function redirectToAction($actionName, $parametersValues = []) {
        $this->redirectControllerToAction($this->request->getRoute()
                                                        ->getControllerName(), $actionName, $parametersValues);
    }
    
    /**
     * @param string $controllerName
     * @param string $actionName
     * @param array  $parametersValues
     */
    protected function redirectControllerToAction($controllerName, $actionName, $parametersValues = []) {
        $this->redirectRoute($this->request->getRoute()
                                           ->getControllerDirectoryName(), $controllerName, $actionName, $parametersValues);
    }
    
    /**
     * @param string $routeName
     * @param string $controllerName
     * @param string $actionName
     * @param array  $parametersValues
     */
    protected function redirectRoute($routeName, $controllerName, $actionName, $parametersValues = []) {
        $routeName .= empty($routeName) ? '' : '/';
        $this->redirect(sprintf('%1$s%2$s/%3$s', $routeName, $controllerName, $actionName), $parametersValues);
    }
    
    /**
     * @param string $route
     * @param array  $parametersValues
     */
    protected function redirect($route = '', $parametersValues = []) {
        if ($this->isCanRedirect()) {
            $parameters = array_map(function($value, $key) {
                return "$key=$value";
            }, $parametersValues, array_keys($parametersValues));
            $parameters = implode('&', $parameters);
            $parameters = empty($parameters) ? '' : "?$parameters";
            Util::redirect($this->request->getSiteUrl(), sprintf('%1$s%2$s', $route, $parameters));
        }
    }
    
    private function isCanRedirect() {
        if (Arrays::keyExists([
            $_POST,
            $_GET,
        ], 'redirect', TRUE)) {
            return InputBooleanBuilder::init('redirect')
                                      ->build()
                                      ->filter();
        }
        
        //Si "redirect" no existe, TRUE.
        return TRUE;
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    protected function checkSubmit($name) {
        return Form::submit($name);
    }
    
    protected function view($viewName = '') {
        if (!$this->cancelView) {
            ViewController::view($this->getViewName($viewName));
        }
    }
    
    /**
     * @param array $data
     */
    protected function sendDataView($data) {
        array_walk($data, function($value, $key) {
            ViewController::sendViewData($key, $value);
        });
    }
    
    /**
     * @param string $formDataName
     *
     * @return mixed|bool
     */
    protected function getForm($formDataName) {
        $this->inputFilter();
        
        return Arrays::get($this->formObjects, $formDataName);
    }
    
    private function inputFilter() {
        if (empty($this->formObjects)) {
            Form::setInput($this->formInputsBuilders());
            $this->inputs = Form::inputFilter();
            
            if (!empty($this->inputs)) {
                $this->formObjects = $this->formToObject();
            }
        }
    }
    
    /**
     * @return array
     */
    protected function formInputsBuilders() {
        return [];
    }
    
    /**
     * @return array|bool
     */
    protected function formToObject() {
        return [];
    }
    
    protected function isValidForm() {
        $this->inputFilter();
        
        return !empty($this->formObjects);
    }
    
    /**
     * @param string $name
     *
     * @return mixed|null
     */
    protected function getInput($name) {
        if (Arrays::keyExists($this->inputs, $name)) {
            return Arrays::get($this->inputs, $name);
        }
        
        return NULL;
    }
    
    /**
     * @param int $count
     *
     * @return string
     */
    protected function rowsPages($count) {
        $optionsManager = new OptionsManager($this->connectionDB);
        $optionPaged    = $optionsManager->searchByName(OptionConstants::PAGED);
        $siteUrl        = $optionsManager->getSiteUrl();
        $paged          = InputIntegerBuilder::init('paged')
                                             ->setMethod($_GET)
                                             ->build()
                                             ->filter();
        $rowCount       = 0;
        
        if ($optionPaged !== FALSE) {
            $rowCount = $optionPaged->getOptionValue();
        }
        
        $pagination = new Pagination($paged, $count, $rowCount, $siteUrl);
        
        if ($pagination->isShowPagination()) {
            ViewController::sendViewData('pagination', $pagination);
            
            return $pagination->getBeginRow() . ',' . $pagination->getRowCount();
        }
        
        return '';
    }
    
}
