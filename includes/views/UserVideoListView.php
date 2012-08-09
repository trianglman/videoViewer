<?php

namespace videoViewer\views;

/**
 * View for listing of all unwatched videos
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class UserVideoListView extends PageView {
    
    public $videos = array();

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) 
    {
        $template = $di->loadTemplate('UserVideoList');
        parent::__construct($di, $template);
    }
    
    public function addVideo(\videoViewer\Entities\Video $video)
    {
        $vid = new \stdClass();
        $vid->seriesName = $video->getSeries()->getName();
        $vid->videoId = $video->getId();
        $vid->episodeName = $video->getEpisodeName();
        $vid->airDate = $video->getAirDate()->format('F d, Y');
        $vid->sortDate = $video->getAirDate()->getTimestamp();
        $this->videos[] = $vid;
    }
    
    public function hasVideos()
    {
        return count($this->videos)>0;
    }

    /**
     * Override the parent render() in order to sort the episodes for display
     * 
     * @return string
     */
    public function render($template = NULL, $view = NULL, $partials = NULL)
    {
        usort($this->videos, function($a,$b)
        {
            if($a->sortDate==$b->sortDate)
            {
                return 0;
            }
            return $a->sortDate>$b->sortDate?1:-1;//sort oldest to newest
        });
        return parent::render($template,$view,$partials);
    }
    
}

?>