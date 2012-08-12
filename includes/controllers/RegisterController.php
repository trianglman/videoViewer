<?php

namespace videoViewer\controllers;

use videoViewer as v;

/**
 * Controller for the Register page
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class RegisterController extends PageController {
    
    /**
     * Form validation error messages
     * @var string
     */
    public $error='';

    /**
     * Processes an AJAX request this page knows how to handle
     * 
     * Should generally return a json string
     * 
     * @return string
     */
    protected function _processAjax() {
        throw new v\PageRedirectException(501);
    }

    /**
     * Processes a standard GET request for the page
     * 
     * @return string
     */
    protected function _processGet() {
        $view = $this->_di->getView('Register');
        if(!empty($this->error))
        {
            $view->hasError=true;
            $view->errorMessage = $this->error;
        }
        return $view->render();
    }

    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost() 
    {
        if(($error = $this->_validateForm())!==true)
        {
            $this->error = $error;
            return $this->_processGet();
        }
        $user = $this->_di->getEntity('User');
        $user->setName(filter_var($this->_post['name'],FILTER_SANITIZE_STRING));
        $user->hashAndSetPassword($this->_post['pass']);
        $user->setUserName(filter_var($this->_post['login'],FILTER_SANITIZE_STRING));
        try
        {
            $em->persist($user);
            $em->flush();
            $this->_session['userId'] = $user->getId();
            throw new videoViewer\PageRedirectException(303,'userHome.php');
        }
        catch(\Exception $e)
        {
            $this->error = $e->getMessage();
            return $this->_processGet();
        }
    }
    
    /**
     * Validates the supplied form and returns any errors found or true if there are none
     * 
     * @return mixed
     */
    protected function _validateForm(){
        $errors = array();
        if(strlen($this->_post['pass']) < 6)
        {
            $errors[]='You must set a password of at least six characters.';
        }
        elseif($this->_post['pass']!==$this->_post['pass2']){
            $errors[] = 'Passwords do not match.';
        }
        if(empty($this->_post['name']))
        {
            $errors[] = 'You must set a name.';
        }
        if(empty($this->_post['login']))
        {
            $errors[] = 'You must set a log in.';
        }
        if(count($errors)>0)
        {
            return implode(' ',$errors);
        }
        return true;
    }

    /**
     * Verifies that the logged in user has access to perform the supplied action
     * 
     * @param string $action [Optional] Defaults to checking if the user can load the page
     * 
     * @return boolean
     */
    protected function _verifyAccess($action='load') {
        return true;
    }

}

?>