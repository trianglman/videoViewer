<?php

namespace videoViewer\controllers;

use videoViewer as v;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Controller
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class UserHistoryController extends PageController {

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
        $view = $this->_di->getView('UserHistory');
        foreach($this->_user->getWatchedVideos() as $vid){
            $view->addVideo($vid);
        }
        return $view->render();
    }

    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost() {
        if(!$this->_verifyAccess('edit') 
                || !$this->_verifyAccess($this->_post['action'])){
            throw new v\PageRedirectException(501);
        }
        $episode = $this->_di['em']->find('videoViewer\Entities\Video',
                $this->_post['episode']);
        if($this->_post['action']=='add'){$this->_user->addWatchedVideo($episode);}
        else{$this->_user->removeWatchedVideo($episode);}
        $this->_di['em']->flush();

        if($this->_post['action']=='add'){
            $goto = 'userVideoList.php';
        }
        else{
            $goto = 'userHistory.php';
        }
        throw new v\PageRedirectException(303,$goto);
    }

    /**
     * Verifies that the logged in user has access to perform the supplied action
     * 
     * @param string $action [Optional] Defaults to checking if the user can load the page
     * 
     * @return boolean
     */
    protected function _verifyAccess($action='load') {
        if ($action == 'load') {
            return !is_null($this->_user);
        }
        if($action=='edit'){
            if(!empty($this->_post['action']) 
                    && in_array($this->_post['action'],array('add','del'))){
                return true;
            }
        }
        if(in_array($action,array('add','del'))){
            return !empty($this->_post['episode']);
        }
        return false;
    }

}

?>