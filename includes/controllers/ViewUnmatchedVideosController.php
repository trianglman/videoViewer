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
class ViewUnmatchedVideosController extends PageController {

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
        $view = $this->_di->getView('ViewUnmatchedVideos');
        $query = $this->_di['em']->createQuery("SELECT v FROM videoViewer\Entities\Video v
                    JOIN v.series s WHERE s.name=:name");
        $query->setParameter('name', 'Unmatched Videos');
        foreach($query->getResult() as $video){
            $view->addVideo($video);
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
            if (is_null($this->_user) || !$this->_user->isAdmin()) {
                return false;
            }
        }
        return true;
    }

}

?>