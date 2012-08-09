<?php

namespace videoViewer\controllers;

use videoViewer as v;

/**
 * Controller for the listing of all unwatched user videos
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class UserVideoListController extends PageController {

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
        $view = $this->_di->getView('UserVideoList');
        foreach($this->_user->getAuthorizedSeries() as $series){
            foreach($series->getEpisodes() as $vid){
                if(!$this->_user->getWatchedVideos()->contains($vid)){
                    $view->addVideo($vid);
                }
            }
        }
        return $view->render();
    }

    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost() {
        throw new v\PageRedirectException(501);
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
            if (is_null($this->_user)) {
                return false;
            }
        }
        return true;
    }

}

?>