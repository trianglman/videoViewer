<?php
namespace videoViewer\controllers;
use videoViewer as v;

/**
 * Description of UserHomeController
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class CreateAliasController extends PageController{
    
    /**
     * The series requested
     * @var \videoViewer\Entities\Video
     */
    protected $_video=null;
    
    /**
     * An object that parses a file name into pieces
     * @var \videoViewer\FileNameParser
     */
    public $fileNameParser = null;
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
        if(is_null($this->_video)){
            $this->_video = $this->_di['em']->find('videoViewer\Entities\Video',
                    $this->_get['videoId']);
        }
        try{
            $this->fileNameParser->parseFileName($this->_video->getFileName('mp4'));
        }
        catch(\Exception $e){
            $errorMessage = 'Video file name could not be parsed.';
        }
        $seriesOpt = $this->_di['em']
                ->createQuery("SELECT s FROM videoViewer\Entities\Series s")
                ->getResult();

        $view = $this->_di->getView('CreateAlias');
        $view->video = $this->_video->__toString();
        $view->videoId = $this->_video->getId();
        if(!empty($errorMessage)){
            $view->error = $errorMessage;
        }
        $view->defaultSeries = $this->_video->getSeries()->getId();
        $view->setSeries($seriesOpt);
        $view->defaultAlias = $this->fileNameParser->series;
        $view->seriesHasAlias = $this->_video
                ->getSeries()->hasAlias($this->fileNameParser->series);
        
        return $view->render();
    }
    
    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost(){
        //Load the video and series
        $this->_video = $this->_di['em']->find('videoViewer\Entities\Video',
                $this->_post['videoId']);
        $series = $this->_di['em']->find('videoViewer\Entities\Series',
                $this->_post['series']);
        //if the video doesn't have a series, set the video series to the selected series
        if($this->_video->getSeries()!=$series){
            $this->_video->setSeries($series);
        }
        //create a new series alias with the supplied alias
        $alias = $this->_di->getEntity('SeriesAlias');
        $alias->setAlias($this->_post['alias']);
        //if the selected series doesn't have this alias, add it
        $found = false;
        foreach($series->getAliases() as $setAlias){
            if($setAlias->getAlias()==$alias->getAlias()){
                $found=true;
                break;
            }
        }
        if(!$found){
            $series->addAlias($alias);
            $this->_di['em']->persist($alias);
        }
        $this->_di['em']->flush();
        
        throw new \videoViewer\PageRedirectException(303, 'seriesList.php');
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
                return false;
            }
            if(!isset($this->_get['videoId']) && !isset($this->_post['videoId'])){
                throw new v\PageRedirectException(303,'seriesList.php');
            }
        }
        return true;
    }
}

?>
