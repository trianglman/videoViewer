<?php

namespace videoViewer\views;

/**
 * Listing of videos the user has already viewed
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class UserHistoryView extends PageView {
    
    public $watched=array();

    /**
     * Initializes the User History page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) 
    {
        $template = $di->loadTemplate('UserHistory');
        parent::__construct($di, $template);
    }
    
    /**
     * Adds a video to the watched list
     * 
     * @param \videoViewer\Entities\Video $vid 
     */
    public function addVideo(\videoViewer\Entities\Video $vid)
    {
        $opt = new \stdClass();
        $opt->seriesName = $vid->getSeries()->getName();
        $opt->videoId = $vid->getId();
        $opt->season = $vid->getSeasonNumber();
        $opt->episode = $vid->getEpisodeNumber();
        $opt->episodeName = $vid->getEpisodeName();
        $this->watched[]=$opt;
    }

}

?>