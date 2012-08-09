<?php
namespace videoViewer\controllers;
use videoViewer as v;

/**
 * Description of UserHomeController
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class SeriesSeasonListingController extends PageController{
    
    /**
     * The series requested
     * @var \videoViewer\Entities\Series
     */
    protected $_series=null;
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
        $this->_series = $this->_di['em']->find('videoViewer\Entities\Series',
                $this->_get['series']);
        if(!$this->_verifyAccess('viewSeries')){
            throw new v\PageRedirectException(403);
        }
        $seasons = array();
        foreach($this->_series->getEpisodes() as $episode){
            if(!isset($seasons[$episode->getSeasonNumber()])){
                $seasons[$episode->getSeasonNumber()]=0;
            }
            $seasons[$episode->getSeasonNumber()]++;
        }
        ksort($seasons);

        $view = $this->_di->getView('SeriesSeasonListing');
        $view->seriesName = $this->_series->getName();
        $view->seriesId = $this->_series->getId();
        $view->bannerURL = $this->_series->getSrc();
        $view->seriesDesc = $this->_series->getDescription();
        $view->seasons = array();
        foreach($seasons as $seasonNo=>$epCount){
            $view->seasons[]=array(
                'seasonNumber'=>$seasonNo,
                'epCount'=>$epCount
                );
        }
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
            if(is_null($this->_user)){return false;}
            if(!isset($this->_get['series'])){
                throw new v\PageRedirectException(303,'userHome.php');
            }
        }
        elseif($action=='viewSeries'){
            if(is_null($this->_series)){return false;}
            if(!$this->_user->canAccessSeries($this->_series) && !$this->_user->isAdmin())
            {
                return false;
            }
        }
        return true;
    }
}

?>
