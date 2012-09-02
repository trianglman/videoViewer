<?php

namespace videoViewer\controllers;

use videoViewer as v;

/**
 * Controller for the edit video page
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class EditVideoController extends PageController {
    
    /**
     * An error message to display to users on failed form submits
     * @var string
     */
    protected $_message='';
    
    /**
     *
     * The video being edited
     * @var \videoViewer\entities\Video
     */
    public $video=null;
    
    /**
     *
     * @var \videoViewer\TvDBApiConn
     */
    public $tvdbConn=null;
    
    /**
     *
     * @var \videoViewer\FileNameParser
     */
    public $filenameParser=null;

    /**
     * Processes an AJAX request this page knows how to handle
     * 
     * Should generally return a json string
     * 
     * @return string
     */
    protected function _processAjax() {
        $this->video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_get['id']);
        $authFuncs = array('checkSeries');
        if(empty($this->_get['req']) || !in_array($this->_get['req'], $authFuncs)){
            throw new \videoViewer\PageRedirectException(501);
        }
        switch($this->_get['req']){
            case 'checkSeries':
                return json_encode($this->_checkSeries());
                break;
        }
    }

    /**
     * Processes a standard GET request for the page
     * 
     * @return string
     */
    protected function _processGet() {
        $view = $this->_di->getView('EditVideo');
        if(is_null($this->video))
        {
            $this->video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_get['id']);
        }
        $view->video = $this->video;
        if(!empty($this->_message))
        {
            $view->hasError=true;
            $view->errorMessage = $this->_message;
        }
        $query = $this->_di['em']->createQuery("SELECT s FROM videoViewer\Entities\Series s");
        $seriesOpt = $query->getResult();
        foreach($seriesOpt as $series)
        {
            $view->addSeries($series);
        }
        return $view->render();
    }

    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost() {
        $this->video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_post['id']);
        //verify that the series exists
        $series = $this->_di['em']->find('videoViewer\Entities\Series',$this->_post['series']);
        if(is_null($series))
        {
            $this->_message = 'Invalid series';
            return $this->_processGet();
        }
        
        if(($status = $this->_validateForm())!==true)
        {
            $this->_message = $status;
            return $this->_processGet();
        }
        $this->video->setEpisodeName($this->_post['name']);
        $this->video->setSeasonNumber($this->_post['season']);
        $this->video->setEpisodeNumber($this->_post['episode']);
        $this->video->setAirDate(new \DateTime($this->_post['date']));
        $this->video->setDetails($this->_post['details']);
        $this->video->setNotes($this->_post['notes']);
        $series->addEpisode($this->video);
        $this->_di['em']->flush();
        throw new \videoViewer\PageRedirectException(303, 'viewVideoDetails.php?id='.$this->video->getId());
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
            if (is_null($this->_user)) 
            {
                return false;
            }
            if((empty($this->_get['id']) && empty($this->_post['id'])))
            {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Verifies that the supplied data is valid
     * 
     * @return string
     */
    protected function _validateForm(){
        $errors = array();
        if(empty($this->_post['name']))
        {
            $errors[]="You must supply an episode name.";
        }
        if(!empty($this->_post['date']) && strtotime($this->_post['date'])===false)
        {
            $errors[]='Date must be a valid date.';
        }
        if(count($errors)===0)
        {
            return true;
        }
        return implode(' ',$errors);
    }
    
    /**
     * Sets the TVDB API connection object
     * 
     * @param v\TvDBApiConn $conn 
     * 
     * @return void
     */
    public function setTvdbConn(v\TvDBApiConn $conn)
    {
        $this->tvdbConn = $conn;
    }
    
    /**
     * Sets the file name parser object
     * 
     * @param v\FileNameParser $parser 
     * 
     * @return void
     */
    public function setFileNameParser(v\FileNameParser $parser)
    {
        $this->filenameParser = $parser;
    }
    
    /**
     * Checks whether the selected series has episode information matching the video's filename
     * 
     * @return mixed
     */
    protected function _checkSeries(){
        //verify that the series exists
        $series = $this->_di['em']->find('videoViewer\Entities\Series',$this->_get['seriesId']);
        if(is_null($series))
        {
            return 'Invalid series';
        }
        //check for the episode details in the series
        $tvdbSeries = $this->tvdbConn->getFullSeriesInformation($series->getSeriesId());
        $this->filenameParser->parseFileName($this->video->getFileNameBase());
        if(!empty($this->filenameParser->episode))
        {
            $episode = $tvdbSeries->getEpisodeByEpisodeNumber($this->filenameParser->season,
                    $this->filenameParser->episode);
        }
        elseif(!empty($this->filenameParser->airDate))
        {
            $episode = $tvdbSeries->getEpisodeByAirDate($this->filenameParser->airDate);
        }
        //if the details are found, return them
        if(!empty($episode))
        {
            $returnArray = array();
            $returnArray['Season']=$episode->season;
            $returnArray['Episode']=$episode->episode;
            $returnArray['Date']=$episode->airDate->format('m/d/Y');
            $returnArray['Details']=$episode->desc;
            $returnArray['Name']=$episode->name;
            return $returnArray;
        }
        //otherwise return details not matched
        else
        {
            return 'Episode not matched';
        }
    }
}

?>