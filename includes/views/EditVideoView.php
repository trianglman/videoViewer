<?php

namespace videoViewer\views;

/**
 * View for the page that allows users to edit video details
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class EditVideoView extends PageView {
    
    /**
     *
     * @var \videoViewer\Entities\Video
     */
    public $video=null;
    
    /**
     * Whether to display an error message to the user
     * @var boolean
     */
    public $hasError=false;
    
    /**
     * The error message to display to the user
     * @var string
     */
    public $errorMessage='';
    
    /**
     * The list of available series, on of which is marked selected
     * @var array[int]\stdClass
     */
    public $series=array();

    /**
     * Initializes the Edit Video page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) 
    {
        $template = $di->loadTemplate('EditVideo');
        parent::__construct($di, $template);
    }
    
    public function videoId(){
        return $this->video->getId();
    }
    
    public function addSeries(\videoViewer\Entities\Series $series)
    {
        $opt = new \stdClass();
        $opt->seriesSelected = $this->video->getSeries()==$series;
        $opt->seriesId = $series->getId();
        $opt->seriesName = $series->getName();
        $this->series[]=$opt;
    }
    
    public function episodeName()
    {
        return $this->video->getEpisodeName();
    }
    
    public function season()
    {
        return $this->video->getSeasonNumber();
    }
    
    public function episodeNumber()
    {
        return $this->video->getEpisodeNumber();
    }
    
    public function airDate()
    {
        return $this->video->getAirDate()->format('m/d/Y');
    }
    
    public function episodeDetails()
    {
        return $this->video->getDetails();
    }
    
    public function notes()
    {
        return $this->video->getNotes();
    }

}

?>