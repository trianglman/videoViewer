<?php

namespace videoViewer\views;

/**
 * Listing of the unmatched videos
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class ViewUnmatchedVideosView extends PageView {
    
    public $videos = array();

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) {
        $template = $di->loadTemplate('ViewUnmatchedVideos');
        parent::__construct($di, $template);
    }
    
    public function addVideo(\videoViewer\Entities\Video $vid)
    {
        $video = new \stdClass();
        $video->filename = $vid->getFileNameBase();
        $video->notes = $vid->getNotes();
        $video->videoId = $vid->getId();
        $this->videos[] = $video;
    }

}

?>