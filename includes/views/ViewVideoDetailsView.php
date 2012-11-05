<?php

namespace videoViewer\views;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class ViewVideoDetailsView extends PageView {
    
    /**
     *
     * @var \videoViewer\Entities\Video
     */
    public $video = null;

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) {
        $template = $di->loadTemplate('ViewVideoDetails');
        parent::__construct($di, $template);
    }
    
    public function oggPath()
    {
        return $this->video->getWebPath('ogg',$this->di);
    }
    
    public function mp4Path()
    {
        return $this->video->getWebPath('mp4',$this->di);
    }
    
    public function seriesName()
    {
        return $this->video->getSeries()->getName();
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
        return $this->video->getAirDate()->format('m-d-Y');
    }
    
    public function details()
    {
        return $this->video->getDetails();
    }
    
    public function notes()
    {
        return $this->video->getNotes();
    }
    
    public function videoId()
    {
        return $this->video->getId();
    }

}

?>