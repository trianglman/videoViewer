<?php
namespace videoViewer\controllers;

use videoViewer as v;

/**
 * Controller for the listing of all episodes in a season
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class SeriesSeasonEpisodesController extends PageController {
    
    /**
     * The series being displayed
     * 
     * @var \videoViewer\Entities\Series
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
        $view = $this->_di->getView('SeriesSeasonEpisodes');
        $this->series = $this->_di['em']->find('videoViewer\Entities\Series',$this->_get['series']);
        $season = filter_var($this->_get['season'],\FILTER_SANITIZE_NUMBER_INT);
        if(!$this->_verifyAccess('viewSeries')){
            throw new v\PageRedirectException(403);
        }
        $view->seriesName = $this->series->getName();
        $view->selectedSeason = $season;
        $view->seriesImage = $this->series->getSrc();
        foreach($this->series->getEpisodes() as $episode)
        {
            if($episode->getSeasonNumber()==$season)
            {
                $view->addEpisode($episode);
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
            if(empty($this->_get['series']) || empty($this->_get['season']))
            {
                return false;
            }
        }
        elseif($action=='viewSeries')
        {
            return ($this->_user->isAdmin() || $this->_user->canAccessSeries($this->series));
        }
        return true;
    }

}

?>