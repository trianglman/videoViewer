<?php
namespace videoViewer\controllers;
use videoViewer as v;

/**
 * Description of UserHomeController
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class UserHomeController extends PageController{
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
        $view = $this->_di->getView('UserHome');
        $view->name = $this->_user->getName();
        $view->admin = $this->_user->isAdmin();
        $view->hasRoku = file_exists(dirname(__FILE__).'/../..'.
                $this->_user->getRokuXML());
        $view->rokuUrl = $this->_user->getRokuXML();
        $view->setSeries($this->_user->getAuthorizedSeries(),$this->_user);
        return $view->render();
    }
    
    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost(){
        throw new v\PageRedirectException(501);
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
            if(is_null($this->_user)){
                throw new v\PageRedirectException(403);
            }
        }
        return true;
    }
}

?>
