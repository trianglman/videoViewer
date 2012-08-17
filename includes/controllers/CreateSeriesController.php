<?php
namespace videoViewer\controllers;
use videoViewer as v;

/**
 * Description of UserHomeController
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class CreateSeriesController extends PageController{
    
    /**
     * The object used to talk to TVDB
     * 
     * @var \videoViewer\TvDBApiConn
     */
    public $tvdbConn=null;
    /**
     * The object used to parse a file name into suggested pieces
     * 
     * @var \videoViewer\FileNameParser
     */
    public $fileParser=null;
    
    /**
     * The video we're creating a series for
     * 
     * @var \videoViewer\Entities\Video
     */
    protected $_video=null;
    
    /**
     * The directory series banners are uploaded to temporarily
     * 
     * @var string
     */
    private static $bannerTempDir = '';
    /**
     * The directory we save banners to long term
     * 
     * @var string
     */
    private static $bannerDir = '';

    public function __construct($get, $post, $session, $cookie, \Pimple $di)
    {
        parent::__construct($get, $post, $session, $cookie, $di);
        self::$bannerTempDir = \dirname(__FILE__).'/../../temp/';
        self::$bannerDir = \dirname(__FILE__).'/../../banners/';
    }
    
    /**
     * Sets the TVDB connection object
     * 
     * @param \videoViewer\TvDBApiConn $conn 
     * 
     * @return void
     */
    public function setTvdbConn(\videoViewer\TvDBApiConn $conn)
    {
        $this->tvdbConn = $conn;
    }
    
    /**
     * Sets the file name parser object
     * 
     * @param \videoViewer\FileNameParser $parser 
     * 
     * @return void
     */
    public function setParser(\videoViewer\FileNameParser $parser)
    {
        $this->fileParser = $parser;
    }
    
    /**
     * Processes an AJAX request this page knows how to handle
     * 
     * Should generally return a json string
     * 
     * @return string
     */
    protected function _processAjax()
    {
        throw new v\PageRedirectException(501);
    }
    
    /**
     * Processes a standard GET request for the page
     * 
     * @return string
     */
    protected function _processGet()
    {
        $view = $this->_di->getView('CreateSeries');
        $this->_video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_get['videoId']);
        $view->videoId = $this->_video->getId();
        $this->fileParser->parseFileName($this->_video->getFileNameBase());
        $seriesOpts = $this->tvdbConn->findSeries($this->fileParser->series);
        foreach($seriesOpts as $option){
            //temporarily store the series banners for user display
            if(!$this->_di->fileSystem('file_exists',array(self::$bannerTempDir.$option->seriesid.'.jpg')))
            {
                $this->_di->fileSystem('file_put_contents',array(
                    self::$bannerTempDir.$option->seriesid.'.jpg', 
                    $this->tvdbConn->getBanner($option->bannerUrl)));
            }
            $view->addSeriesOpt($option);
        }
        return $view->render();
    }
    
    /**
     * Processes a POST form submission for this page
           $this->_video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_post['videoId']);
        $this->fileParser->parseFileName($this->_video->getFileNameBase());
  * 
     * @return void
     * 
     * @throws \videoViewer\PageRedirectException 
     */
    protected function _processPost()
    {
        if(!$this->verifyAccess('createSeries'))
        {
            throw new videoViewer\PageRedirectException(403);
        }
        $this->_video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_post['videoId']);
        $this->fileParser->parseFileName($this->_video->getFileNameBase());
        //process the form
        $tvdbSeries = $this->tvdbConn->getFullSeriesInformation($this->_post['seriesId']);
        $this->_di->fileSystem('rename',array(self::$bannerTempDir.$tvdbSeries->seriesid.'.jpg',
            self::$bannerDir.$tvdbSeries->seriesid.'.jpg'));
        
        $series = $this->_di->getEntity('Series');
        $series->setDescription($tvdbSeries->desc);
        $series->setImage(self::$bannerDir.$tvdbSeries->seriesid.'.jpg');
        $series->setName($tvdbSeries->name);
        $series->setSeriesId($tvdbSeries->seriesid);
        //Create a series alias based on the video file name
        $videoAlias = $this->_di->getEntity('SeriesAlias');
        $videoAlias->setAlias($parsed->series);
        $series->addAlias($videoAlias);
        if($tvdbSeries->name!=$parsed->series)
        {//if the series name parsed out of the file doesn't match the TVDB series, add the TVDB series name
            $seriesAlias =$this->_di->getEntity('SeriesAlias');
            $seriesAlias->setAlias($tvdbSeries->name);
            $series->addAlias($seriesAlias);
        }
        $this->_di['em']->persist($series);
        $this->_di['em']->flush();
        $this->_attachVideoToSeries($series,$tvdbSeries);
        new \videoViewer\PageRedirectException(303, 'index.php',$e);
    }
    
    /**
     * Verifies that the logged in user has access to perform the supplied action
     * 
     * @param string $action [Optional] Defaults to checking if the user can load the page
     * 
     * @return boolean
     */
    protected function _verifyAccess($action='load')
    {
        if($action=='load')
        {
            if(is_null($this->_user)){return false;}
            if(!isset($this->_get['videoId']) && !isset($this->_post['videoId'])){
                throw new v\PageRedirectException(303,'seriesList.php');
            }
        }
        elseif($action=='createSeries')
        {
            if(!isset($this->_post['seriesId']) || 
                    !\is_numeric($this->_post['seriesId']))
            {
                return false;
            }
        }
        return true;
    }
    /**
     *
     * Searches the series's XML for a matching episode, then populates the video information and attaches it to the series
     * @param \videoViewer\Entities\Series $series
     * @param \videoViewer\TvDBSeries $tvdbSeries
     *
     * @return void
     * 
     * @throws \RuntimeException if the video details don't find a valid TVDB episode
     */
    protected function _attachVideoToSeries(\videoViewer\Entities\Series $series,  
            \videoViewer\TvDBSeries $tvdbSeries)
    {
        //search through the xml for the episode's identifiers
        if(!empty($this->fileParser->episode)){
            $episode = $tvdbSeries->getEpisodeByEpisodeNumber($this->fileParser->season,
                    $this->fileParser->episode);
        }
        elseif(!empty($this->fileParser->airDate)){
            $episode = $tvdbSeries->getEpisodeByAirDate($this->fileParser->airDate);
        }
        //if not found throw an error
        if(empty($episode))
        {
            throw new \RuntimeException('Episode was not found');
        }
        //update the video record and attach to series
        $this->_video->setAirDate($episode->airDate);
        $this->_video->setDetails($episode->desc);
        $this->_video->setEpisodeNumber($episode->episode);
        $this->_video->setSeasonNumber($episode->season);
        $this->_video->setNotes('');
        $series->addEpisode($this->_video);
        $this->_di['em']->flush();
    }

}

?>