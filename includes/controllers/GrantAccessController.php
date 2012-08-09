<?php

namespace videoViewer\controllers;

use videoViewer as v;

/**
 * Controller for the grant access page
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class GrantAccessController extends PageController {
    
    /**
     * The series being updated
     * @var \videoViewer\entities\Series
     */
    public $series=null;

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
        $view = $this->_di->getView('GrantAccess');
        if(is_null($this->series)){
            $this->series = $this->_di['em']->find('videoViewer\Entities\Series',$this->_get['series']);
        }
        $view->series = $this->series;
        $query = $this->_di['em']->createQuery("SELECT u FROM videoViewer\Entities\User u");
        foreach($query->getResult() as $user)
        {
            $view->addUser($user);
        }
        return $view->render();
    }

    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost() {
        $this->series = $this->_di['em']->find('videoViewer\Entities\Series',$this->_post['series']);
        
        $query = $this->_di['em']->createQuery("SELECT u FROM videoViewer\Entities\User u");
        foreach($query->getResult() as $user)
        {
            if(in_array($this->_post['authUser']))
            {
                $user->addAuthorizedSeries($this->series);
            }
            else
            {
                $user->removeAuthorizedSeries($this->series);
            }
        }
        $em->flush();
        throw new \videoViewer\PageRedirectException(303,'grantAccess.php?series='.$this->series->getId());
    }

    /**
     * Verifies that the logged in user has access to perform the supplied action
     * 
     * @param string $action [Optional] Defaults to checking if the user can load the page
     * 
     * @return boolean
     */
    protected function _verifyAccess($action='load') {
        if ($action == 'load') 
        {
            if (is_null($this->_user) || !$this->_user->isAdmin()) 
            {
                return false;
            }
            if(empty($this->_get['series']) && empty($this->_post['series']))
            {
                return false;
            }
        }
        return true;
    }

}

?>