<?php
namespace videoViewer\controllers;
use videoViewer as v;

/**
 * Description of IndexPageController
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class IndexPageController extends PageController{
    
    protected $_errorMessage='';
    /**
     * Processes an AJAX request this page knows how to handle
     * 
     * Should generally return a json string
     * 
     * @return string
     */
    protected function _processAjax(){
        throw new v\PageRedirectException(501);
    }
    
    /**
     * Processes a standard GET request for the page
     * 
     * @return string
     */
    protected function _processGet(){
        $view = $this->_di->getView('index');
        if(!empty($this->_errorMessage)){
            $view->hasError=true;
            $view->errorMessage = $this->_errorMessage;
        }
        return $view->render();
    }
    
    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost(){
        try{
            $user = $this->_di['em']->getRepository('videoViewer\Entities\User')
                    ->getUserByNameAndPassword(filter_var($this->_post['login'],FILTER_SANITIZE_STRING),
                            filter_var($this->_post['pass'],FILTER_UNSAFE_RAW));
            $this->_session['userId']=$user->getId();
            throw new v\PageRedirectException(303, 'userHome.php');
        }
        catch(\Doctrine\ORM\NoResultException $e){
            $this->_errorMessage = 'User name or Password not recognized.';
        }
        return $this->_processGet();
    }
    
    /**
     * Verifies that the logged in user has access to perform the supplied action
     * 
     * @param string $action [Optional] Defaults to checking if the user can load the page
     * 
     * @return boolean
     */
    protected function _verifyAccess($action='load'){
        if($action=='load'){
            if(!is_null($this->_user)){
                throw new v\PageRedirectException(303, 'userHome.php');
            }
        }
        return true;
    }
}

?>
