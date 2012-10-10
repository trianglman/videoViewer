<?php

namespace videoViewer\controllers;

use videoViewer as v;

/**
 * Page to view a selected video
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class ViewEpisodeController extends PageController {
    
    /**
     *
     * @var \videoViewer\Entities\Video
     */
    public $video=null;

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
        $view = $this->_di->getView('ViewEpisode');
        $this->video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_get['episode']);
        if(!$this->_verifyAccess('viewVideo')){
            throw new \videoViewer\PageRedirectException(403);
        }
        $view->episodeName = $this->video->getEpisodeName();
        $view->seriesName = $this->video->getSeries()->getName();
        $view->oggPath = $this->video->getWebPath('ogg');
        $view->mp4Path = $this->video->getWebPath('mp4');
        $view->isAdmin = $this->_user->isAdmin();
        $view->videoId = $this->video->getId();
        
        $this->_user->addWatchedVideo($this->video);
        $this->_di['em']->flush();
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
            if (is_null($this->_user) || (empty($this->_get['episode']) && empty($this->_post['episode']))) {
                return false;
            }
        }
        if($action=='viewVideo'){
            return ($this->_user->isAdmin() || $this->_user->canAccessSeries($this->video->getSeries()));
        }
        return true;
    }

}

?>